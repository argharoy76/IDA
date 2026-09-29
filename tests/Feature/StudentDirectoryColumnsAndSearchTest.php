<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;

class StudentDirectoryColumnsAndSearchTest extends TestCase
{
    protected User $admin;
    protected Student $student;
    protected User $studentUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_test_dir@ida.com'],
            [
                'name' => 'Directory Super Admin',
                'account_id' => 'ADM-DIR-TEST',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
            ]
        );

        $uniqueCode = 'SRCH-' . strtoupper(uniqid());
        $phone = '01712345678';
        $email = 'searchable_' . uniqid() . '@example.com';
        $name = 'Tariq Al-Mansoor ' . uniqid();

        $this->studentUser = User::create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'account_id' => $uniqueCode,
            'password' => Hash::make('password123'),
            'role' => 'academic_student',
        ]);

        $this->student = Student::create([
            'user_id' => $this->studentUser->id,
            'student_id_code' => $uniqueCode,
            'target_wing' => 'Navy',
            'student_type' => 'offline',
            'gender' => 'male',
            'status' => 'active',
        ]);
    }

    /**
     * Test table columns: ONLY ID, Name, Contact, Actions are visible; Info and Courses are NOT.
     */
    public function test_directory_table_contains_only_id_name_contact_and_actions(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/student-accounts?wing=Navy');
        $response->assertStatus(200);

        // Required headers
        $response->assertSee('<th style="padding: 10px 10px;">ID</th>', false);
        $response->assertSee('<th style="padding: 10px 10px;">Name</th>', false);
        $response->assertSee('<th style="padding: 10px 10px;">Contact</th>', false);
        $response->assertSee('<th style="padding: 10px 10px; text-align: right;">Actions</th>', false);

        // Info and Courses columns must NOT exist in the table header
        $response->assertDontSee('<th style="padding: 10px 10px;">Info</th>', false);
        $response->assertDontSee('<th style="padding: 10px 10px;">Courses</th>', false);

        // Check search placeholder is clean without explanatory text
        $response->assertSee('placeholder="Search..."', false);
        $response->assertDontSee('placeholder="Search by name, ID, phone, email..."', false);
    }

    /**
     * Test search finds cadet by ID code.
     */
    public function test_search_finds_student_by_id(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/student-accounts?wing=all&search=' . $this->student->student_id_code);
        $response->assertStatus(200);
        $response->assertSee($this->student->student_id_code);
        $response->assertSee($this->studentUser->name);
    }

    /**
     * Test search finds cadet by Name.
     */
    public function test_search_finds_student_by_name(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/student-accounts?wing=all&search=' . urlencode($this->studentUser->name));
        $response->assertStatus(200);
        $response->assertSee($this->student->student_id_code);
        $response->assertSee($this->studentUser->name);
    }

    /**
     * Test search finds cadet by Phone number.
     */
    public function test_search_finds_student_by_phone(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/student-accounts?wing=all&search=' . $this->studentUser->phone);
        $response->assertStatus(200);
        $response->assertSee($this->student->student_id_code);
        $response->assertSee($this->studentUser->name);
    }

    /**
     * Test search finds cadet by Email.
     */
    public function test_search_finds_student_by_email(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/student-accounts?wing=all&search=' . $this->studentUser->email);
        $response->assertStatus(200);
        $response->assertSee($this->student->student_id_code);
        $response->assertSee($this->studentUser->name);
    }

    /**
     * Test specialized register button titles per wing:
     * - Navy: Register New Navigator
     * - Police: Register New Police Officer
     * - Army: Register New Army Cadet
     * - Air Force: Register New Air Cadet
     */
    public function test_specialized_register_button_titles_for_wings(): void
    {
        // 1. Navy
        $navyRes = $this->actingAs($this->admin)->get('/admin/student-accounts?wing=Navy');
        $navyRes->assertStatus(200);
        $navyRes->assertSee('Register New Navigator');

        // 2. Police
        $policeRes = $this->actingAs($this->admin)->get('/admin/student-accounts?wing=Police');
        $policeRes->assertStatus(200);
        $policeRes->assertSee('Register New Police Officer');

        // 3. Army
        $armyRes = $this->actingAs($this->admin)->get('/admin/student-accounts?wing=Army');
        $armyRes->assertStatus(200);
        $armyRes->assertSee('Register New Army Cadet');

        // 4. Air Force
        $airRes = $this->actingAs($this->admin)->get('/admin/student-accounts?wing=Air%20Force');
        $airRes->assertStatus(200);
        $airRes->assertSee('Register New Air Cadet');
    }

    /**
     * Test that All Cadets button is removed from the wing segment section.
     */
    public function test_all_cadets_button_removed_from_wing_segment_bar(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/student-accounts?wing=Navy');
        $response->assertStatus(200);

        // The segment action bar does not contain the old All Cadets button
        $response->assertDontSee('title="Switch to All Cadets view"', false);
    }

    /**
     * Test courses/tracks dropdown menu options for each wing:
     * - Navy: All Navy Courses, Preliminary Courses, ISSB
     * - Police: All Police Courses, Constable Courses, Sub-Inspector (SI), Assistant Sub-Inspector (ASI)
     */
    public function test_courses_tracks_dropdown_menu_options_per_wing(): void
    {
        // 1. Navy dropdown options
        $navyRes = $this->actingAs($this->admin)->get('/admin/student-accounts?wing=Navy');
        $navyRes->assertStatus(200);
        $navyRes->assertSee('<select name="track"', false);
        $navyRes->assertSee('All Navy Courses', false);
        $navyRes->assertSee('Preliminary Courses', false);
        $navyRes->assertSee('ISSB', false);

        // 2. Police dropdown options (retains police-specific career ranks)
        $policeRes = $this->actingAs($this->admin)->get('/admin/student-accounts?wing=Police');
        $policeRes->assertStatus(200);
        $policeRes->assertSee('<select name="track"', false);
        $policeRes->assertSee('All Police Courses', false);
        $policeRes->assertSee('Constable Courses', false);
        $policeRes->assertSee('Sub-Inspector (SI)', false);
        $policeRes->assertSee('Assistant Sub-Inspector (ASI)', false);

        // 3. Army dropdown options
        $armyRes = $this->actingAs($this->admin)->get('/admin/student-accounts?wing=Army');
        $armyRes->assertStatus(200);
        $armyRes->assertSee('All Army Courses', false);

        // 4. Air Force dropdown options
        $airRes = $this->actingAs($this->admin)->get('/admin/student-accounts?wing=Air%20Force');
        $airRes->assertStatus(200);
        $airRes->assertSee('All Air Force Courses', false);
    }
}

