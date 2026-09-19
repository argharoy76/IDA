<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;

class AuthFlowTest extends TestCase
{
    /**
     * Test 1: Frontend navbar does NOT have a "Dashboard" link
     */
    public function test_frontend_navbar_does_not_contain_dashboard_link(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertDontSee('>Dashboard</a>', false);
        $response->assertSee('Cadet Portal');
        $response->assertSee('Join Us');
        $response->assertDontSee('nav-btn-login');

        // When authenticated, public navbar still only displays "Join Us" and no user logged-in badges or "Sign Out"
        $candidate = User::where('role', 'external_student')->first() ?? User::factory()->create(['role' => 'external_student']);
        $authResponse = $this->actingAs($candidate)->get('/');
        $authResponse->assertStatus(200);
        $authResponse->assertDontSee('>Dashboard</a>', false);
        $authResponse->assertDontSee('Sign Out');
        $authResponse->assertSee('Join Us');
    }

    /**
     * Test 2: Login page renders properly with modern styling
     */
    public function test_login_page_renders_properly(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Cadet Portal Login');
        $response->assertSee('Sign In to Cadet Portal');
        $response->assertSee('Cadet ID or Email Address');
        $response->assertDontSee('Demo Switcher');
    }

    /**
     * Test 2b: Dedicated Admin Login page renders properly with command styling
     */
    public function test_admin_login_page_renders_properly(): void
    {
        $response = $this->get('/admin/login');
        $response->assertStatus(200);
        $response->assertSee('Admin Command Access');
        $response->assertSee('Officer / Account ID or Email');
        $response->assertDontSee('1-Click Super Admin Access');
        $response->assertSee('Registered Phone Number');
        $response->assertSee('Student Login');
    }

    /**
     * Test 3: Candidate registration creates user and student record with extra fields and account_id
     */
    public function test_candidate_registration_creates_records_and_redirects_to_online_tests(): void
    {
        $email = 'candidate_' . uniqid() . '@example.com';

        $response = $this->post('/register', [
            'name' => 'Tanvir Hossain',
            'email' => $email,
            'phone' => '01712349999',
            'student_type' => 'external',
            'target_wing' => 'Army',
            'institution' => 'Notre Dame College, Dhaka',
            'hsc_year' => '2025',
            'district' => 'Dhaka',
            'gender' => 'male',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect(route('online_tests'));

        $this->assertDatabaseHas('users', [
            'email' => $email,
            'role' => 'external_student',
        ]);

        $user = User::where('email', $email)->first();
        $this->assertNotNull($user);
        $this->assertNotNull($user->account_id);
        $this->assertStringStartsWith('EXT-', $user->account_id);

        $this->assertDatabaseHas('students', [
            'user_id' => $user->id,
            'student_type' => 'external',
            'institution' => 'Notre Dame College, Dhaka',
            'hsc_year' => '2025',
            'district' => 'Dhaka',
            'target_wing' => 'Army',
        ]);

        $this->assertStringStartsWith('EXT-', $user->student->student_id_code);
    }

    /**
     * Test 4: External candidate login with account_id redirects to online_tests on frontend
     */
    public function test_external_candidate_login_with_account_id(): void
    {
        $email = 'logintest_' . uniqid() . '@example.com';
        $testId = 'EXT-' . strtoupper(uniqid());
        $user = User::create([
            'name' => 'Rafiqul Islam',
            'email' => $email,
            'account_id' => $testId,
            'password' => Hash::make('password123'),
            'role' => 'external_student',
            'phone' => '01799887766',
        ]);

        $response = $this->post('/login', [
            'login_id' => $testId,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('online_tests'));
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test 5: Admin can access account & ID control page
     */
    public function test_admin_can_access_account_management(): void
    {
        $admin = User::where('role', 'super_admin')->first();
        if (!$admin) {
            $admin = User::create([
                'name' => 'Super Admin',
                'email' => 'admin_test_' . uniqid() . '@ida.com',
                'account_id' => 'ADM-999',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
            ]);
        }

        $response = $this->actingAs($admin)->get('/admin/accounts');
        $response->assertStatus(200);
        $response->assertSee('Account & ID Control');
    }

    /**
     * Test 6: Admin CANNOT log in from frontend student login page
     */
    public function test_admin_cannot_login_from_frontend_student_login(): void
    {
        $admin = User::where('role', 'super_admin')->first();

        $response = $this->post('/login', [
            'login_id' => $admin->account_id,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('login_id');
        $this->assertGuest();
    }

    /**
     * Test 7: Student CANNOT log in from backend admin command login page
     */
    public function test_student_cannot_login_from_backend_admin_login(): void
    {
        $studentUser = User::create([
            'name' => 'Candidate Isolation Check',
            'email' => 'candidate_iso_' . uniqid() . '@example.com',
            'account_id' => 'EXT-' . strtoupper(uniqid()),
            'password' => Hash::make('password123'),
            'role' => 'external_student',
        ]);

        $response = $this->post('/admin/login', [
            'login_id' => $studentUser->account_id,
            'phone' => '01711998877',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('login_id');
        $this->assertGuest();
    }

    /**
     * Test 8: Admin CAN log in from backend admin login page
     */
    public function test_admin_can_login_from_backend_admin_login(): void
    {
        $admin = User::where('role', 'super_admin')->first();

        $response = $this->post('/admin/login', [
            'login_id' => $admin->account_id,
            'phone' => $admin->phone,
            'password' => 'ArghaArghaGTA6',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    /**
     * Test 9: Unauthenticated request to /admin/* redirects to /admin/login (NOT /login)
     */
    public function test_unauthenticated_admin_request_redirects_to_admin_login(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect(route('admin.login'));
    }

    /**
     * Test 10: Cadet Dashboard displays clean top header, 6 neat boxes, no clutter below, and update details
     */
    public function test_cadet_dashboard_clean_layout_and_details_update(): void
    {
        $cadet = User::where('role', 'academic_student')->first();
        if (!$cadet) {
            $cadet = User::create([
                'name' => 'Cadet Argha Roy',
                'email' => 'argharoy_test_' . uniqid() . '@ida.com',
                'account_id' => '250236',
                'password' => Hash::make('password'),
                'role' => 'academic_student',
            ]);
            Student::create([
                'user_id' => $cadet->id,
                'student_id_code' => '250236',
                'roll_number' => '01',
                'student_type' => 'academic',
                'admission_date' => now(),
                'status' => 'active',
            ]);
        }

        $response = $this->actingAs($cadet)->get('/cadet/dashboard');
        $response->assertStatus(200);

        // Standalone 3D Dashboard title rendered on dashboard page ONLY (outside the topbar slim box)
        $response->assertSee('cadet-3d-page-title');
        $response->assertSee('Dashboard');
        $response->assertDontSee('btn-cadet-top-dashboard-3d');

        // Top right: Candidate name in place of account text, with dropdown menu (Name, ID #, Manage Account, Logout)
        $response->assertSee($cadet->name);
        $response->assertSee('ID #');
        $response->assertSee('Manage Account');
        $response->assertSee('Logout');

        // Deleted ticks / elements
        $response->assertDontSee('Preview Site');
        $response->assertDontSee('<div class="tactical-clock">', false);
        $response->assertDontSee('fa-fire');
        $response->assertDontSee('Active Cadet</span>', false);

        // Active Boxes: Exam and Exam History
        $response->assertSee('Exam');
        $response->assertSee('Exam History');

        // Unfinished modules are hidden per user requirement (code preserved)
        $response->assertDontSee(route('cadet.routine'));
        $response->assertDontSee(route('cadet.fees'));
        $response->assertDontSee('Previous Year Question');

        // Verify old verbose labels and old sidebar links are removed
        $response->assertDontSee('Exam and Test');
        $response->assertDontSee('Payment & Leftover Fee');
        $response->assertDontSee('Visit Our Other Courses');
        $response->assertDontSee('Assessment Center');
        $response->assertDontSee('Class Routine');
        $response->assertDontSee('Cadet Dossier');
        $response->assertDontSee('Exams & Tests');

        // Clean below boxes (no schedule table or development focus)
        $response->assertDontSee("Today's Schedule & Parade");
        $response->assertDontSee('Current Development Focus');

        // Test Update Details POST action
        $newPhone = '01700998877';
        $updateResponse = $this->actingAs($cadet)->post('/cadet/update-details', [
            'name' => $cadet->name,
            'email' => $cadet->email,
            'phone' => $newPhone,
            'institution' => 'Notre Dame College, Dhaka',
            'hsc_year' => '2025',
            'district' => 'Dhaka',
            'target_wing' => 'Army',
        ]);
        $updateResponse->assertSessionHas('success');
        $this->assertEquals($newPhone, $cadet->fresh()->phone);

        // Verify that visiting other pages (e.g. routine) shows Routine section badge in topbar and candidate account
        $routineResponse = $this->actingAs($cadet)->get('/cadet/routine');
        $routineResponse->assertStatus(200);
        $routineResponse->assertSee($cadet->name);
        $routineResponse->assertSee('Manage Account');
        $routineResponse->assertSee('cadet-3d-page-title');
        $routineResponse->assertSee('Routine');

        // Verify that visiting exams page shows Exam badge in topbar
        $examsResponse = $this->actingAs($cadet)->get('/cadet/exams');
        $examsResponse->assertStatus(200);
        $examsResponse->assertSee('cadet-3d-page-title');
        $examsResponse->assertSee('Exam');
        $examsResponse->assertDontSee('Exam Date');

        // Verify that visiting exams history page shows Exam History badge in topbar
        $historyResponse = $this->actingAs($cadet)->get('/cadet/exam-history');
        $historyResponse->assertStatus(200);
        $historyResponse->assertSee('cadet-3d-page-title');
        $historyResponse->assertSee('Exam History');

        // Verify that visiting fees / payment page shows Payment badge in topbar
        $feesResponse = $this->actingAs($cadet)->get('/cadet/fees');
        $feesResponse->assertStatus(200);
        $feesResponse->assertSee('cadet-3d-page-title');
        $feesResponse->assertSee('Payment');
    }

    /**
     * Test 12: Admin can log in using the name 'ArghaRoy', phone and password 'ArghaArghaGTA6'
     */
    public function test_admin_can_login_with_name_argharoy_and_password(): void
    {
        $admin = User::where('role', 'super_admin')->first();
        $this->assertNotNull($admin);
        $this->assertEquals('ArghaRoy', $admin->name);

        $response = $this->post('/admin/login', [
            'login_id' => 'ArghaRoy',
            'phone' => $admin->phone,
            'password' => 'ArghaArghaGTA6',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    /**
     * Test 13: Admin can log in using lowercase 'argharoy'
     */
    public function test_admin_can_login_with_lowercase_argharoy(): void
    {
        $admin = User::where('role', 'super_admin')->first();

        $response = $this->post('/admin/login', [
            'login_id' => 'argharoy',
            'phone' => $admin->phone,
            'password' => 'ArghaArghaGTA6',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }
}


