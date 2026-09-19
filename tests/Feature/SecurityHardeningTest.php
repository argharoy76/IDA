<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\Instructor;
use App\Models\Batch;
use App\Models\Course;
use App\Models\AuditLog;
use App\Models\ContactInquiry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class SecurityHardeningTest extends TestCase
{
    /**
     * 1. Test HTTP Security Headers are set across all responses.
     */
    public function test_security_headers_are_present_on_all_responses(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
    }

    /**
     * 2. Test Login Rate Limiting & Failed Login Audit Logging.
     */
    public function test_login_rate_limiting_and_audit_logging(): void
    {
        RateLimiter::clear('student_login|test_rate_cadet|127.0.0.1');

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'login_id' => 'test_rate_cadet',
                'password' => 'wrong_password_attempt',
            ]);
        }

        // 6th attempt should be blocked by rate limiter
        $blockedResponse = $this->post('/login', [
            'login_id' => 'test_rate_cadet',
            'password' => 'wrong_password_attempt',
        ]);

        $blockedResponse->assertSessionHasErrors('login_id');
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'login_failed',
        ]);
    }

    /**
     * 3. Test Registration Password Policy Enforces Minimum 8 chars with letters & numbers.
     */
    public function test_registration_password_policy_enforces_strength(): void
    {
        // Weak password (only 5 chars)
        $weakRes = $this->post('/register', [
            'name' => 'Weak User',
            'email' => 'weak_' . uniqid() . '@example.com',
            'phone' => '01700000000',
            'password' => '12345',
            'password_confirmation' => '12345',
        ]);
        $weakRes->assertSessionHasErrors('password');

        // Missing numbers (only letters)
        $alphaOnlyRes = $this->post('/register', [
            'name' => 'Alpha User',
            'email' => 'alpha_' . uniqid() . '@example.com',
            'phone' => '01700000001',
            'password' => 'abcdefghij',
            'password_confirmation' => 'abcdefghij',
        ]);
        $alphaOnlyRes->assertSessionHasErrors('password');

        // Strong password (letters + numbers, 8+ chars)
        $validEmail = 'strong_' . uniqid() . '@example.com';
        $strongRes = $this->post('/register', [
            'name' => 'Strong User',
            'email' => $validEmail,
            'phone' => '01700000002',
            'password' => 'SecurityPass2026',
            'password_confirmation' => 'SecurityPass2026',
        ]);
        $strongRes->assertRedirect(route('online_tests'));
        $this->assertDatabaseHas('users', ['email' => $validEmail]);
    }

    /**
     * 4. Test File Upload Protection (Disallow PHP & Executable files in CMS).
     */
    public function test_cms_disallows_malicious_script_uploads(): void
    {
        $admin = User::where('role', 'super_admin')->first();

        // 1. Attempt to upload a .php script disguised as an image
        $maliciousFile = UploadedFile::fake()->create('shell.php', 20, 'application/x-php');

        $response = $this->actingAs($admin)->post('/admin/cms/settings', [
            'hero_bg_image' => $maliciousFile,
        ]);
        $response->assertSessionHasErrors('hero_bg_image');

        // 2. Attempt to upload a .php file in hero slides
        $slideResponse = $this->actingAs($admin)->post('/admin/cms/hero-slides', [
            'slide_image' => $maliciousFile,
        ]);
        $slideResponse->assertSessionHasErrors('slide_image');

        // 3. Verify .htaccess exists in public/uploads/
        $this->assertFileExists(public_path('uploads/.htaccess'));
        $htaccessContent = file_get_contents(public_path('uploads/.htaccess'));
        $this->assertStringContainsString('php', $htaccessContent);
        $this->assertStringContainsString('Deny from all', $htaccessContent);
    }

    /**
     * 5. Test Finance Manager Role Isolation (Cannot access Accounts, CMS, or Exams).
     */
    public function test_finance_manager_cannot_access_accounts_or_cms_or_exams(): void
    {
        $financeUser = User::where('role', 'finance_manager')->first();
        if (!$financeUser) {
            $financeUser = User::create([
                'name' => 'Finance Controller Test',
                'email' => 'fin_test_' . uniqid() . '@ida.com',
                'account_id' => 'FIN-TEST-001',
                'password' => Hash::make('password123'),
                'role' => 'finance_manager',
                'status' => 'active',
            ]);
        }

        // Finance manager CAN access finance & dashboard
        $this->actingAs($financeUser)->get('/admin/dashboard')->assertStatus(200);
        $this->actingAs($financeUser)->get('/admin/fees')->assertStatus(200);
        $this->actingAs($financeUser)->get('/admin/finance')->assertStatus(200);

        // Finance manager CANNOT access accounts, student accounts, CMS, or exams
        $this->actingAs($financeUser)->get('/admin/accounts')->assertStatus(403);
        $this->actingAs($financeUser)->get('/admin/student-accounts')->assertStatus(403);
        $this->actingAs($financeUser)->get('/admin/cms')->assertStatus(403);
        $this->actingAs($financeUser)->get('/admin/exams')->assertStatus(403);
    }

    /**
     * 6. Test IDOR Protection on Instructor Student Dossiers & Observations.
     */
    public function test_instructor_cannot_access_cadet_from_unassigned_batch(): void
    {
        $instructorUser = User::where('role', 'instructor')->first();
        $instructor = Instructor::where('user_id', $instructorUser->id)->first();
        $assignedBatchIds = $instructor->batches->pluck('id')->toArray();

        // Create a separate unassigned batch and student
        $course = Course::first();
        $unassignedBatch = Batch::create([
            'course_id' => $course->id,
            'batch_name' => 'Unassigned Squad ' . uniqid(),
            'batch_code' => 'BAT-' . strtoupper(substr(uniqid(), -4)),
            'start_date' => now(),
            'end_date' => now()->addMonths(3),
            'status' => 'active',
        ]);

        $unassignedUser = User::create([
            'name' => 'Alien Cadet',
            'email' => 'alien_' . uniqid() . '@ida.com',
            'account_id' => 'CAD-ALIEN-' . uniqid(),
            'password' => Hash::make('password123'),
            'role' => 'academic_student',
            'status' => 'active',
        ]);

        $unassignedStudent = Student::create([
            'user_id' => $unassignedUser->id,
            'student_id_code' => $unassignedUser->account_id,
            'roll_number' => '99',
            'student_type' => 'academic',
            'current_batch_id' => $unassignedBatch->id,
            'admission_date' => now(),
            'status' => 'active',
        ]);

        // Instructor attempting to view unassigned cadet dossier receives 403 Forbidden
        $this->actingAs($instructorUser)
            ->get('/instructor/students/' . $unassignedStudent->id)
            ->assertStatus(403);

        // Instructor attempting to post observation for unassigned cadet receives 403 Forbidden
        $obsRes = $this->actingAs($instructorUser)->post('/instructor/observations', [
            'student_id' => $unassignedStudent->id,
            'category' => 'Leadership',
            'observation_text' => 'Attempted unauthorized observation',
            'rating' => 3,
            'visibility' => 'student_visible',
        ]);
        $obsRes->assertStatus(403);
    }

    /**
     * 7. Test Privilege Escalation Defense: Standard Admin cannot create or reset Super Admin.
     */
    public function test_standard_admin_cannot_create_or_reset_super_admin(): void
    {
        $adminUser = User::where('role', 'admin')->first();
        if (!$adminUser) {
            $adminUser = User::create([
                'name' => 'Standard Admin Test',
                'email' => 'admin_test_' . uniqid() . '@ida.com',
                'account_id' => 'ADM-TEST-002',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'status' => 'active',
            ]);
        }

        $superAdminUser = User::where('role', 'super_admin')->first();

        // 1. Standard admin attempts to create a super_admin account -> 403
        $createRes = $this->actingAs($adminUser)->post('/admin/accounts', [
            'name' => 'Rogue Super Admin',
            'email' => 'rogue_' . uniqid() . '@ida.com',
            'role' => 'super_admin',
            'password' => 'password123',
            'status' => 'active',
        ]);
        $createRes->assertStatus(403);

        // 2. Standard admin attempts to reset super_admin's password -> 403
        $resetRes = $this->actingAs($adminUser)->post('/admin/accounts/' . $superAdminUser->id . '/password', [
            'password' => 'new_hacked_pass123',
            'password_confirmation' => 'new_hacked_pass123',
        ]);
        $resetRes->assertStatus(403);
    }

    /**
     * 8. Test Honeypot Spam Prevention on Contact Inquiries.
     */
    public function test_contact_inquiry_honeypot_silently_discards_bot_submissions(): void
    {
        $initialCount = ContactInquiry::count();

        // Bot submission with honeypot filled
        $botRes = $this->post('/contact', [
            'name' => 'Spam Bot 3000',
            'email' => 'spambot@spam.com',
            'phone' => '1234567890',
            'message' => 'Buy cheap links now please visit our website http://spam.xyz',
            'website_hp' => 'http://spam.xyz', // Bot filled the hidden honeypot
        ]);

        $botRes->assertSessionHas('success'); // Returns success so bot does not retry
        $this->assertEquals($initialCount, ContactInquiry::count()); // No record created

        // Legitimate human submission without honeypot
        $humanRes = $this->post('/contact', [
            'name' => 'Genuine Candidate',
            'email' => 'genuine_' . uniqid() . '@gmail.com',
            'phone' => '01711223344',
            'message' => 'I would like to inquire about the upcoming Army BMA Long Course admission.',
        ]);

        $humanRes->assertSessionHas('success');
        $this->assertEquals($initialCount + 1, ContactInquiry::count());
    }
}
