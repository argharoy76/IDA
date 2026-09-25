<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\Course;
use App\Models\Batch;
use Illuminate\Support\Facades\Hash;

class CadetHierarchyAndTaxonomyTest extends TestCase
{
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure ArghaRoy developer admin account is present
        $admin = User::where('account_id', 'ArghaRoy')->first();
        if (!$admin) {
            $admin = User::create([
                'name' => 'ArghaRoy',
                'email' => 'argharoy@ida.com',
                'account_id' => 'ArghaRoy',
                'phone' => '+880 1711-001122',
                'password' => Hash::make('ArghaArghaGTA6'),
                'role' => 'super_admin',
                'status' => 'active',
                'permissions' => ['can_manage_cms', 'can_manage_exams', 'can_manage_students', 'can_manage_finance', 'can_view_audit'],
            ]);
        }
        $this->admin = $admin;
    }

    /**
     * Test 1: Cadet management index page renders successfully with Cadet terminology and wings.
     */
    public function test_cadet_management_index_renders_with_cadet_terminology(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.student_accounts.index'));

        $response->assertStatus(200);
        $response->assertSee('Cadet Management');
        $response->assertSee('Army');
        $response->assertSee('Navy');
        $response->assertSee('Air Force');
        $response->assertSee('Police');
        $response->assertSee('All Cadets');
    }

    /**
     * Test 2: Wing and track filtering on cadet accounts.
     */
    public function test_cadet_wing_and_track_filtering(): void
    {
        // Create an Army Prelim Cadet
        $armyUser = User::create([
            'name' => 'Army Cadet Test',
            'email' => 'army_cadet_' . uniqid() . '@example.com',
            'phone' => '01700' . rand(100000, 999999),
            'password' => Hash::make('secret123'),
            'role' => 'student',
            'status' => 'active',
        ]);
        $armyStudent = Student::create([
            'user_id' => $armyUser->id,
            'student_id_code' => 'AC' . rand(1000, 9999),
            'target_wing' => 'Army - Preliminary',
            'student_type' => 'academic',
        ]);

        // Create a Police SI Cadet
        $policeUser = User::create([
            'name' => 'Police SI Cadet Test',
            'email' => 'police_si_' . uniqid() . '@example.com',
            'phone' => '01800' . rand(100000, 999999),
            'password' => Hash::make('secret123'),
            'role' => 'student',
            'status' => 'active',
        ]);
        $policeStudent = Student::create([
            'user_id' => $policeUser->id,
            'student_id_code' => 'PSI' . rand(1000, 9999),
            'target_wing' => 'Police - Sub-Inspector (SI)',
            'student_type' => 'academic',
        ]);

        // Filter Army with prelim track
        $resArmy = $this->actingAs($this->admin)->get(route('admin.student_accounts.index', [
            'wing' => 'Army',
            'track' => 'prelim',
        ]));
        $resArmy->assertStatus(200);
        $resArmy->assertSee($armyUser->name);
        $resArmy->assertDontSee($policeUser->name);

        // Filter Police with SI track
        $resPolice = $this->actingAs($this->admin)->get(route('admin.student_accounts.index', [
            'wing' => 'Police',
            'track' => 'si',
        ]));
        $resPolice->assertStatus(200);
        $resPolice->assertSee($policeUser->name);
        $resPolice->assertDontSee($armyUser->name);
    }

    /**
     * Test 3: Offline registration with target_wing and target_program creates cadet account.
     */
    public function test_offline_registration_stores_cadet_with_wing_and_program(): void
    {
        $uniquePhone = '019' . rand(10000000, 99999999);
        $uniqueCustomId = 'CADET' . rand(1000, 9999);

        // Find or create a course
        $course = Course::firstOrCreate(
            ['slug' => 'police-si-test-course'],
            [
                'title' => 'Police SI Special Training Course',
                'category' => 'Police',
                'duration' => '3 Months',
                'fee' => 12000,
                'admission_status' => 'open',
            ]
        );

        $response = $this->actingAs($this->admin)->post(route('admin.student_accounts.store_offline'), [
            'name' => 'Sgt Mahmud Cadet',
            'phone' => $uniquePhone,
            'email' => 'mahmud_' . uniqid() . '@example.com',
            'custom_id' => $uniqueCustomId,
            'password' => 'pass12345',
            'target_wing' => 'Police',
            'target_program' => 'Sub-Inspector (SI)',
            'course_id' => $course->id,
            'paid_amount' => 5000,
            'payment_method' => 'Cash',
            'age' => 23,
            'gender' => 'male',
            'address' => 'Dhaka Cantonment, Bangladesh',
        ]);

        $response->assertRedirect(route('admin.student_accounts.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('students', [
            'student_id_code' => $uniqueCustomId,
            'target_wing' => 'Police - Sub-Inspector (SI)',
        ]);
    }

    /**
     * Test 4: Courses catalog renders and includes Police category.
     */
    public function test_courses_catalog_includes_police_and_cadet_headers(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.courses.index'));

        $response->assertStatus(200);
        $response->assertSee('Cadets');
        $response->assertSee('Police Service');
    }
}
