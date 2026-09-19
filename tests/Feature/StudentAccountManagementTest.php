<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\Course;
use Illuminate\Support\Facades\Hash;

class StudentAccountManagementTest extends TestCase
{
    /**
     * Test 1: Anyone can create a free account with Name, Age, Phone, Email, Gender, Address
     * and initially NO courses are assigned.
     */
    public function test_free_signup_creates_account_with_no_initial_courses(): void
    {
        $email = 'free_student_' . uniqid() . '@example.com';

        $response = $this->post('/register', [
            'name' => 'Mehedi Hasan',
            'age' => 19,
            'phone' => '01811223344',
            'email' => $email,
            'gender' => 'male',
            'address' => 'House 42, Road 7, Sector 3, Uttara, Dhaka',
            'password' => 'pass1234',
            'password_confirmation' => 'pass1234',
        ]);

        $response->assertRedirect(route('online_tests'));

        $user = User::where('email', $email)->first();
        $this->assertNotNull($user);
        $this->assertNotNull($user->account_id);

        $student = $user->student;
        $this->assertNotNull($student);
        $this->assertEquals(19, $student->age);
        $this->assertEquals('male', $student->gender);
        $this->assertStringContainsString('Uttara, Dhaka', $student->address);

        // Crucial requirement: Initially NO courses are assigned!
        $this->assertNull($student->current_course_id);
        $this->assertEquals(0, $student->courses()->count());

        // Can log in with their generated ID and password
        $this->post('/logout');
        $loginRes = $this->post('/login', [
            'login_id' => $user->account_id,
            'password' => 'pass1234',
        ]);
        $loginRes->assertRedirect(route('online_tests'));
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test 2: Admin/Staff can access the Student Accounts Control page
     */
    public function test_admin_can_access_student_accounts_page(): void
    {
        $admin = User::where('role', 'super_admin')->first();
        if (!$admin) {
            $admin = User::create([
                'name' => 'Super Admin',
                'email' => 'admin_' . uniqid() . '@ida.com',
                'account_id' => 'ADM-' . strtoupper(uniqid()),
                'password' => Hash::make('password'),
                'role' => 'super_admin',
            ]);
        }

        $response = $this->actingAs($admin)->get('/admin/student-accounts');
        $response->assertStatus(200);
        $response->assertSee('Student Management');
        $response->assertSee('Navy');
        $response->assertSee('Police');
        $response->assertSee('Army');
        $response->assertSee('Air Force');
        $response->assertSee('All Students');

        // Subpage contains registration capability
        $dirResponse = $this->actingAs($admin)->get('/admin/student-accounts?wing=all');
        $dirResponse->assertStatus(200);
        $dirResponse->assertSee('Register Offline Student');
    }

    /**
     * Test 3: Staff can register an offline student and assign their own custom ID
     */
    public function test_staff_can_register_offline_student_with_custom_id(): void
    {
        $admin = User::where('role', 'super_admin')->first();
        $customId = 'OFF-' . strtoupper(uniqid());
        $email = 'offline_' . uniqid() . '@ida.com';

        $response = $this->actingAs($admin)->post('/admin/student-accounts/offline', [
            'name' => 'Kamrul Islam',
            'custom_id' => $customId,
            'age' => 20,
            'phone' => '01700112233',
            'email' => $email,
            'gender' => 'male',
            'address' => 'Boyra Main Road, Khulna',
            'password' => 'offline123',
        ]);

        $response->assertRedirect(route('admin.student_accounts.index'));
        $this->assertDatabaseHas('users', [
            'email' => $email,
            'account_id' => $customId,
        ]);

        $student = Student::where('student_id_code', $customId)->first();
        $this->assertNotNull($student);
        $this->assertEquals('offline', $student->student_type);
        $this->assertEquals(20, $student->age);
        $this->assertEquals('Boyra Main Road, Khulna', $student->address);

        // Initially no courses assigned unless explicitly checked
        $this->assertEquals(0, $student->courses()->count());

        // Student can immediately log in with this custom ID
        $this->post('/logout');
        $loginRes = $this->post('/login', [
            'login_id' => $customId,
            'password' => 'offline123',
        ]);
        $this->assertAuthenticated();
    }

    /**
     * Test 4: Staff can assign 1 course or 2 courses to a student, controlling their access
     */
    public function test_staff_can_assign_one_or_multiple_courses_to_student(): void
    {
        $admin = User::where('role', 'super_admin')->first();

        // Ensure 2 courses exist
        $course1 = Course::first();
        if (!$course1) {
            $course1 = Course::create([
                'title' => 'BMA Long Course Preparation',
                'slug' => 'bma-long-course-' . uniqid(),
                'fee' => 15000,
                'category' => 'Army',
                'admission_status' => 'open',
            ]);
        }
        $course2 = Course::where('id', '!=', $course1->id)->first();
        if (!$course2) {
            $course2 = Course::create([
                'title' => 'Navy Officer Cadet Course',
                'slug' => 'navy-cadet-course-' . uniqid(),
                'fee' => 14000,
                'category' => 'Navy',
                'admission_status' => 'open',
            ]);
        }

        // Create a student with 0 courses
        $user = User::create([
            'name' => 'Anisur Rahman',
            'email' => 'anis_' . uniqid() . '@example.com',
            'account_id' => 'STU-' . strtoupper(uniqid()),
            'password' => Hash::make('password'),
            'role' => 'external_student',
        ]);
        $student = Student::create([
            'user_id' => $user->id,
            'student_id_code' => $user->account_id,
            'student_type' => 'online',
            'gender' => 'male',
            'status' => 'active',
        ]);

        $this->assertFalse($student->hasCourse($course1->id));
        $this->assertFalse($student->hasCourse($course2->id));

        // Step A: Student pays for 1 course -> employee assigns 1 course
        $this->actingAs($admin)->post("/admin/student-accounts/{$student->id}/assign-courses", [
            'course_ids' => [$course1->id],
        ]);

        $student->refresh();
        $this->assertTrue($student->hasCourse($course1->id));
        $this->assertFalse($student->hasCourse($course2->id));
        $this->assertEquals(1, $student->courses()->count());

        // Step B: Student pays for second course -> employee assigns 2 courses
        $this->actingAs($admin)->post("/admin/student-accounts/{$student->id}/assign-courses", [
            'course_ids' => [$course1->id, $course2->id],
        ]);

        $student->refresh();
        $this->assertTrue($student->hasCourse($course1->id));
        $this->assertTrue($student->hasCourse($course2->id));
        $this->assertEquals(2, $student->courses()->count());
    }

    /**
     * Test 5: Staff can update the custom Login ID number of a student
     */
    public function test_staff_can_update_custom_login_id_for_student(): void
    {
        $admin = User::where('role', 'super_admin')->first();

        $originalId = 'OLD-' . strtoupper(uniqid());
        $newId = 'CUSTOM-KHL-' . strtoupper(uniqid());

        $user = User::create([
            'name' => 'Farhan Ahmed',
            'email' => 'farhan_' . uniqid() . '@example.com',
            'account_id' => $originalId,
            'password' => Hash::make('mypassword'),
            'role' => 'academic_student',
        ]);
        $student = Student::create([
            'user_id' => $user->id,
            'student_id_code' => $originalId,
            'student_type' => 'offline',
            'gender' => 'male',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->post("/admin/student-accounts/{$student->id}/update-id", [
            'custom_id' => $newId,
        ]);

        $response->assertSessionHas('success');

        $user->refresh();
        $student->refresh();

        $this->assertEquals($newId, $user->account_id);
        $this->assertEquals($newId, $student->student_id_code);

        // Student can now log in using the newly assigned custom ID
        $this->post('/logout');
        $this->post('/login', [
            'login_id' => $newId,
            'password' => 'mypassword',
        ]);
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test 6: Admin can delete a student account permanently
     */
    public function test_admin_can_delete_student_account(): void
    {
        $admin = User::where('role', 'super_admin')->first();

        $user = User::create([
            'name' => 'To Be Deleted',
            'email' => 'delete_me_' . uniqid() . '@example.com',
            'account_id' => 'DEL-' . strtoupper(uniqid()),
            'password' => Hash::make('password123'),
            'role' => 'academic_student',
        ]);

        $student = Student::create([
            'user_id' => $user->id,
            'student_id_code' => $user->account_id,
            'student_type' => 'offline',
            'gender' => 'male',
            'status' => 'active',
        ]);

        $course = Course::first();
        if ($course) {
            $student->courses()->attach($course->id, ['enrolled_at' => now(), 'status' => 'active']);
        }

        // 1. Attempt to delete without confirmation phrase MUST FAIL with warning
        $unconfirmedResponse = $this->actingAs($admin)->delete("/admin/student-accounts/{$student->id}", [
            'confirm_delete' => 'wrong_code',
        ]);
        $unconfirmedResponse->assertSessionHas('error');
        $this->assertDatabaseHas('students', ['id' => $student->id]);
        $this->assertDatabaseHas('users', ['id' => $user->id]);

        // 2. Supplying exact confirmation phrase (or ID) successfully deletes
        $response = $this->actingAs($admin)->delete("/admin/student-accounts/{$student->id}", [
            'confirm_delete' => $student->student_id_code,
        ]);
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('students', ['id' => $student->id]);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        if ($course) {
            $this->assertDatabaseMissing('course_student', ['student_id' => $student->id]);
        }
    }

    /**
     * Test 7: Student directory displays 'View Details', 'Edit Details', and 'Delete' buttons with ID and Name columns
     */
    public function test_student_directory_displays_view_and_update_buttons(): void
    {
        $admin = User::where('role', 'super_admin')->first();
        $student = Student::first();

        // Check directory table in unified all-students view
        $response = $this->actingAs($admin)->get('/admin/student-accounts?wing=all');
        $response->assertStatus(200);
        $response->assertSee('ID');
        $response->assertSee('Name');
        if ($student) {
            $response->assertSee('View Details');
            $response->assertSee('Edit Details');
            $response->assertSee('Delete');
        }

        // Check landing page has the 5 command cards
        $landingResponse = $this->actingAs($admin)->get('/admin/student-accounts');
        $landingResponse->assertStatus(200);
        $landingResponse->assertSee('Navy');
        $landingResponse->assertSee('Police');
        $landingResponse->assertSee('Army');
        $landingResponse->assertSee('Air Force');
        $landingResponse->assertSee('All Students');
    }

    /**
     * Test 8: Admin can access View Details standalone page
     */
    public function test_admin_can_view_student_account_details_page(): void
    {
        $admin = User::where('role', 'super_admin')->first();

        $user = User::create([
            'name' => 'Details Test Cadet',
            'email' => 'details_cadet_' . uniqid() . '@example.com',
            'account_id' => 'CAD-VIEW-' . strtoupper(uniqid()),
            'password' => Hash::make('password123'),
            'role' => 'academic_student',
        ]);

        $student = Student::create([
            'user_id' => $user->id,
            'student_id_code' => $user->account_id,
            'student_type' => 'offline',
            'gender' => 'male',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->get("/admin/student-accounts/{$student->id}");
        $response->assertStatus(200);
        $response->assertSee('Student Details');
        $response->assertSee($user->name);
        $response->assertSee($user->account_id);
        $response->assertSee('Fee Invoices & Payment Records');
        $response->assertSee('Total Verified Paid');
    }

    /**
     * Test 9: Admin can access Update Details standalone page
     */
    public function test_admin_can_view_student_account_edit_page(): void
    {
        $admin = User::where('role', 'super_admin')->first();

        $user = User::create([
            'name' => 'Edit Test Cadet',
            'email' => 'edit_cadet_' . uniqid() . '@example.com',
            'account_id' => 'CAD-EDIT-' . strtoupper(uniqid()),
            'password' => Hash::make('password123'),
            'role' => 'academic_student',
        ]);

        $student = Student::create([
            'user_id' => $user->id,
            'student_id_code' => $user->account_id,
            'student_type' => 'offline',
            'gender' => 'male',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->get("/admin/student-accounts/{$student->id}/edit");
        $response->assertStatus(200);
        $response->assertSee('Update Student Details');
        $response->assertSee($user->name);
    }

    /**
     * Test 10: Admin can update all student details and course assignments from edit page
     */
    public function test_admin_can_update_student_details_and_courses_via_edit_page(): void
    {
        $admin = User::where('role', 'super_admin')->first();

        $user = User::create([
            'name' => 'Original Name',
            'email' => 'orig_cadet_' . uniqid() . '@example.com',
            'account_id' => 'ORIG-' . strtoupper(uniqid()),
            'password' => Hash::make('password123'),
            'role' => 'academic_student',
            'phone' => '01711000000',
        ]);

        $student = Student::create([
            'user_id' => $user->id,
            'student_id_code' => $user->account_id,
            'student_type' => 'offline',
            'gender' => 'male',
            'address' => 'Old Address',
            'status' => 'active',
        ]);

        $course = Course::first();
        $newCustomId = 'UPDATED-ID-' . strtoupper(uniqid());

        $response = $this->actingAs($admin)->put("/admin/student-accounts/{$student->id}", [
            'name' => 'Updated Cadet Name',
            'custom_id' => $newCustomId,
            'age' => 22,
            'phone' => '01899112233',
            'email' => $user->email,
            'gender' => 'female',
            'address' => 'New Dhaka Address',
            'student_type' => 'offline',
            'course_ids' => $course ? [$course->id] : [],
        ]);

        $response->assertRedirect(route('admin.student_accounts.show', $student->id));
        $response->assertSessionHas('success');

        $user->refresh();
        $student->refresh();

        $this->assertEquals('Updated Cadet Name', $user->name);
        $this->assertEquals($newCustomId, $user->account_id);
        $this->assertEquals($newCustomId, $student->student_id_code);
        $this->assertEquals('female', $student->gender);
        $this->assertEquals('New Dhaka Address', $student->address);

        if ($course) {
            $this->assertTrue($student->courses->contains($course->id));
        }
    }
}