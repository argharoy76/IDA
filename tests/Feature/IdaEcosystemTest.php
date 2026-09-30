<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\Instructor;
use App\Models\Course;
use App\Models\Batch;
use App\Models\FeeType;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\FinancialTransaction;
use App\Models\FinanceCategory;
use App\Models\Exam;
use App\Models\Question;
use App\Models\WatWord;
use App\Models\ExamAttempt;
use App\Models\ContactInquiry;
use Carbon\Carbon;

class IdaEcosystemTest extends TestCase
{
    /**
     * Test 1: Public Academy Pages
     */
    public function test_public_pages_load_successfully()
    {
        $home = $this->get('/')->assertStatus(200)->assertSee('IMPERIAL DEFENCE ACADEMY');
        $this->assertTrue(str_contains($home->getContent(), 'brand-crest-3d-svg') || str_contains($home->getContent(), 'brand-crest-3d-wrap'));
        $this->get('/about')->assertStatus(200)->assertSee('Our Mission');
        $this->get('/courses')->assertStatus(200)->assertSee('Officer Cadet Preparatory Courses');
        $this->get('/gallery')->assertStatus(200)->assertSee('PHOTO ARCHIVE');
        $this->get('/notices')->assertStatus(200)->assertSee('OFFICIAL BULLETIN');
        $this->get('/contact')->assertStatus(200)->assertSee('Contact');
        $this->get('/classes')->assertStatus(200)->assertSee('Training Schedule');
        $this->get('/online-tests')->assertStatus(200)->assertSee('Assessment Platform');
        $this->get('/login')->assertStatus(200)->assertSee('Cadet Portal Login');
    }

    /**
     * Test 2: Role-based Authentication Redirection
     */
    public function test_authenticated_dashboards_redirect_according_to_role()
    {
        $admin = User::where('role', 'super_admin')->first();
        $this->actingAs($admin)->get('/dashboard')->assertRedirect(route('admin.dashboard'));

        $instructorUser = User::where('role', 'instructor')->first();
        $this->actingAs($instructorUser)->get('/dashboard')->assertRedirect(route('instructor.dashboard'));

        $cadetUser = User::where('role', 'academic_student')->first();
        $this->actingAs($cadetUser)->get('/dashboard')->assertRedirect(route('cadet.dashboard'));

        $externalUser = User::where('role', 'external_student')->first();
        if (!$externalUser) {
            $externalUser = User::create([
                'name' => 'External Student',
                'email' => 'ext_temp_' . uniqid() . '@example.com',
                'account_id' => 'EXT-TEMP-' . strtoupper(uniqid()),
                'password' => bcrypt('password'),
                'role' => 'external_student',
            ]);
        }
        $this->actingAs($externalUser)->get('/dashboard')->assertRedirect(route('external.dashboard'));
    }

    /**
     * Test 3: Admin Management, Invoicing, and Payment Approval Workflow
     */
    public function test_admin_fees_payments_and_automatic_cash_ledger_inflow()
    {
        $admin = User::where('role', 'super_admin')->first();
        $student = Student::with('user')->whereHas('user', fn($q) => $q->where('role', 'academic_student'))->first();
        if (!$student) {
            $cadetUser = User::create([
                'name' => 'Cadet Auto',
                'email' => 'cadet_auto_' . uniqid() . '@ida.com',
                'account_id' => 'CAD-AUTO-' . strtoupper(uniqid()),
                'password' => bcrypt('password'),
                'role' => 'academic_student',
            ]);
            $student = Student::create([
                'user_id' => $cadetUser->id,
                'student_id_code' => $cadetUser->account_id,
                'roll_number' => '01',
                'student_type' => 'academic',
                'current_batch_id' => Batch::first()->id ?? null,
                'admission_date' => now(),
                'status' => 'active',
            ]);
        }
        $feeType = FeeType::first();

        // 1. Visit Admin Dashboard & Dossier
        $this->actingAs($admin)->get('/admin/dashboard')->assertStatus(200)->assertSee('Executive Command Dashboard');
        $this->actingAs($admin)->get('/admin/students/' . $student->id)->assertStatus(200)->assertSee('Digital Student Dossier');

        // 2. Generate an invoice with unique title
        $initialBalance = FinancialTransaction::getCurrentBalance();
        $testTitle = 'Test Auto Tuition ' . uniqid();

        $invoiceResponse = $this->actingAs($admin)->post('/admin/fees/invoices', [
            'student_id' => $student->id,
            'fee_type_id' => $feeType->id,
            'title' => $testTitle,
            'gross_amount' => 15000,
            'discount_amount' => 1000,
            'waiver_amount' => 0,
            'due_date' => Carbon::today()->addDays(10)->format('Y-m-d'),
        ]);
        $invoiceResponse->assertRedirect(route('admin.fees.index'));

        $createdInvoice = Invoice::where('title', $testTitle)->first();
        $this->assertNotNull($createdInvoice);
        $this->assertEquals(14000.0, (float) $createdInvoice->net_amount);
        $this->assertEquals('pending', $createdInvoice->status);

        // 3. Cadet submits payment for this invoice
        $cadetUser = $student->user;
        $paySubmitResponse = $this->actingAs($cadetUser)->post('/cadet/payments', [
            'invoice_id' => $createdInvoice->id,
            'amount' => 14000,
            'payment_method' => 'bkash',
            'transaction_reference' => 'BKASH_TEST_TRX_' . time(),
        ]);
        $paySubmitResponse->assertSessionHas('success');

        $payment = Payment::where('invoice_id', $createdInvoice->id)->latest()->first();
        $this->assertEquals('pending', $payment->verification_status);

        // 4. Admin approves payment -> verifies automatic cash ledger inflow
        $approveResponse = $this->actingAs($admin)->post('/admin/payments/' . $payment->id . '/approve', [
            'admin_notes' => 'Verified through automated test runner',
        ]);
        $approveResponse->assertSessionHas('success');

        $payment->refresh();
        $createdInvoice->refresh();

        $this->assertEquals('approved', $payment->verification_status);
        $this->assertEquals('paid', $createdInvoice->status);
        $this->assertEquals(0.0, (float) $createdInvoice->due_amount);

        // Verify Automatic Inflow Entry
        $newBalance = FinancialTransaction::getCurrentBalance();
        $this->assertEquals($initialBalance + 14000.0, $newBalance);

        // 5. Admin Finance Ledger & Reports load cleanly
        $this->actingAs($admin)->get('/admin/finance')->assertStatus(200)->assertSee('Academy Financial Ledger');
        $this->actingAs($admin)->get('/admin/finance/reports')->assertStatus(200)->assertSee('Financial Reports');
    }

    /**
     * Test 4: Instructor Workflow — Dossier Observations & Attendance
     */
    public function test_instructor_observations_and_muster_attendance()
    {
        $instructorUser = User::where('role', 'instructor')->first();
        $instructor = Instructor::where('user_id', $instructorUser->id)->first();
        $batch = $instructor->batches->first() ?? Batch::first();
        $student = Student::where('current_batch_id', $batch->id ?? 1)->first();
        if (!$student) {
            $cadetUser = User::create([
                'name' => 'Cadet Instructor Test',
                'email' => 'cadet_inst_' . uniqid() . '@ida.com',
                'account_id' => 'CAD-INST-' . strtoupper(uniqid()),
                'password' => bcrypt('password'),
                'role' => 'academic_student',
            ]);
            $student = Student::create([
                'user_id' => $cadetUser->id,
                'student_id_code' => $cadetUser->account_id,
                'roll_number' => '02',
                'student_type' => 'academic',
                'current_batch_id' => $batch->id ?? 1,
                'admission_date' => now(),
                'status' => 'active',
            ]);
        }

        // 1. View Instructor flight deck & assigned cadet
        $this->actingAs($instructorUser)->get('/instructor/dashboard')->assertStatus(200)->assertSee('Assigned Cadets');
        $this->actingAs($instructorUser)->get('/instructor/students/' . $student->id)->assertStatus(200)->assertSee('5-Axis Competence Radar');

        // 2. Log formal observation
        $obsResponse = $this->actingAs($instructorUser)->post('/instructor/observations', [
            'student_id' => $student->id,
            'category' => 'Leadership & Initiative',
            'observation_text' => 'Demonstrated exceptional situational poise during mock obstacle course exercise.',
            'rating' => 5,
            'recommended_action' => 'Nominate for squadron cadet commander',
            'visibility' => 'student_visible',
        ]);
        $obsResponse->assertSessionHas('success');

        // 3. Mark Parade Muster Attendance
        $attResponse = $this->actingAs($instructorUser)->post('/instructor/attendance', [
            'batch_id' => $student->current_batch_id,
            'date' => Carbon::today()->format('Y-m-d'),
            'attendance' => [
                $student->id => [
                    'status' => 'present',
                    'remarks' => 'On parade drill time',
                ]
            ]
        ]);
        $attResponse->assertSessionHas('success');
    }

    /**
     * Test 5: Assessment Engine — MCQ with Server-Side Negative Marking
     */
    public function test_cadet_mcq_examination_with_negative_marking()
    {
        $cadetUser = User::where('role', 'academic_student')->first();
        $student = Student::where('user_id', $cadetUser->id)->first();
        $mcqExam = Exam::with('questions')->where('exam_type', 'iq_mcq')->where('status', 'open')->where('branch', 'army')->first();
        $isTempMcq = false;
        if (!$mcqExam || $mcqExam->questions->count() < 2) {
            $isTempMcq = true;
            $mcqExam = Exam::create([
                'title' => 'Test Cadet MCQ Exam ' . uniqid(),
                'slug' => 'test-cadet-mcq-exam-' . uniqid(),
                'exam_type' => 'iq_mcq',
                'category' => 'Verbal IQ',
                'branch' => 'army',
                'duration_minutes' => 30,
                'total_marks' => 10,
                'pass_marks' => 5,
                'negative_marking_per_wrong' => 0.25,
                'status' => 'open',
                'schedule_start' => Carbon::now()->subMinutes(5),
            ]);
            Question::create([
                'exam_id' => $mcqExam->id,
                'branch' => 'army',
                'exam_type' => 'iq_mcq',
                'question_text' => 'What is the speed of light in vacuum?',
                'options' => ['A' => '3x10^8 m/s', 'B' => '100 m/s', 'C' => '500 m/s', 'D' => 'None'],
                'correct_answer' => 'A',
                'marks' => 2.0,
                'negative_marks' => 0.5,
            ]);
            Question::create([
                'exam_id' => $mcqExam->id,
                'branch' => 'army',
                'exam_type' => 'iq_mcq',
                'question_text' => 'What is the capital of Bangladesh?',
                'options' => ['A' => 'Dhaka', 'B' => 'Chittagong', 'C' => 'Sylhet', 'D' => 'Khulna'],
                'correct_answer' => 'A',
                'marks' => 2.0,
                'negative_marks' => 0.5,
            ]);
            $mcqExam->load('questions');
        }

        $this->assertNotNull($mcqExam);
        $this->assertTrue($mcqExam->questions->count() >= 2);

        // Start Exam Session
        $startResponse = $this->actingAs($cadetUser)->get('/cadet/exams/' . $mcqExam->id . '/start');
        $startResponse->assertStatus(200)->assertSee('Question Palette');

        $questions = $mcqExam->questions;
        $q1 = $questions[0];
        $q2 = $questions[1];

        // Intentionally answer Q1 correctly and Q2 incorrectly to verify negative marking
        $correctKey = $q1->correct_answer;
        $wrongKey = ($q2->correct_answer === 'A') ? 'B' : 'A';

        $submitResponse = $this->actingAs($cadetUser)->post('/cadet/exams/' . $mcqExam->id . '/submit', [
            'answers' => [
                $q1->id => $correctKey,
                $q2->id => $wrongKey,
            ]
        ]);

        $submitResponse->assertRedirect();

        // Check latest attempt
        $attempt = ExamAttempt::where('student_id', $student->id)->where('exam_id', $mcqExam->id)->latest()->first();
        $this->assertNotNull($attempt);
        $this->assertEquals('submitted', $attempt->status);

        // Expected score: +q1 marks - q2 negative_marks
        $expectedScore = max(0, $q1->marks - $q2->negative_marks);
        $this->assertEquals($expectedScore, (float) $attempt->score);

        // Result Scorecard
        $this->actingAs($cadetUser)->get('/cadet/exams/attempts/' . $attempt->id . '/result')
            ->assertStatus(200)
            ->assertSee('Assessment Scorecard');

        if ($isTempMcq) {
            $attempt->answers()->delete();
            $attempt->delete();
            $mcqExam->questions()->delete();
            $mcqExam->delete();
        }
    }

    /**
     * Test 6: Assessment Engine — 15-Second WAT Simulation Execution
     */
    public function test_cadet_wat_examination_simulation()
    {
        $cadetUser = User::where('role', 'academic_student')->first();
        $student = Student::where('user_id', $cadetUser->id)->first();
        $watExam = Exam::with('watWords')->where('exam_type', 'word_association')->first();
        $isTempWat = false;
        if (!$watExam || $watExam->watWords->count() === 0) {
            $isTempWat = true;
            $watExam = Exam::create([
                'title' => 'Test Cadet WAT Exam ' . uniqid(),
                'slug' => 'test-cadet-wat-exam-' . uniqid(),
                'exam_type' => 'word_association',
                'category' => 'Psychological WAT',
                'branch' => 'army',
                'duration_minutes' => 15,
                'total_marks' => 10,
                'pass_marks' => 5,
                'status' => 'open',
            ]);
            WatWord::create([
                'exam_id' => $watExam->id,
                'word' => 'HONOR',
                'display_seconds' => 15,
                'order_seq' => 1,
            ]);
            WatWord::create([
                'exam_id' => $watExam->id,
                'word' => 'COURAGE',
                'display_seconds' => 15,
                'order_seq' => 2,
            ]);
            $watExam->load('watWords');
        }

        $this->assertNotNull($watExam);

        // Start WAT Session
        $this->actingAs($cadetUser)->get('/cadet/exams/' . $watExam->id . '/start')
            ->assertStatus(200)
            ->assertSee('Prompt Word');

        // Submit Spontaneous Reactions
        $responses = [];
        $times = [];
        foreach ($watExam->watWords as $w) {
            $responses[$w->id] = 'A true soldier leads with courage.';
            $times[$w->id] = 4;
        }

        $watSubmitResponse = $this->actingAs($cadetUser)->post('/cadet/exams/' . $watExam->id . '/submit', [
            'wat_responses' => $responses,
            'wat_times' => $times,
        ]);
        $watSubmitResponse->assertRedirect();

        $attempt = ExamAttempt::where('student_id', $student->id)->where('exam_id', $watExam->id)->latest()->first();
        $this->assertNotNull($attempt);
        $this->assertEquals('submitted', $attempt->status);
        $this->assertEquals(count($responses), $attempt->answers()->count());

        if ($isTempWat) {
            $attempt->answers()->delete();
            $attempt->delete();
            $watExam->watWords()->delete();
            $watExam->delete();
        }
    }

    /**
     * Test 7: External Candidate Isolation
     */
    public function test_external_candidate_isolation_and_testing()
    {
        $externalUser = User::where('role', 'external_student')->first();
        if (!$externalUser) {
            $externalUser = User::create([
                'name' => 'Isolation External Candidate',
                'email' => 'iso_cand_' . uniqid() . '@example.com',
                'account_id' => 'EXT-ISO-' . strtoupper(uniqid()),
                'password' => bcrypt('password'),
                'role' => 'external_student',
            ]);
        }

        // 1. External candidate cannot access internal admin or instructor areas
        $this->actingAs($externalUser)->get('/admin/dashboard')->assertStatus(403);
        $this->actingAs($externalUser)->get('/instructor/dashboard')->assertStatus(403);
        $this->actingAs($externalUser)->get('/cadet/dashboard')->assertStatus(403);

        // 2. Can access candidate flight deck & tests
        $this->actingAs($externalUser)->get('/external/dashboard')->assertStatus(200)->assertSee('Candidate Flight Deck');
        $this->actingAs($externalUser)->get('/external/tests')->assertStatus(200)->assertSee('Military Online Assessment Catalog');
    }

    /**
     * Test 8: Admin Examination & Question Bank Management
     */
    public function test_admin_can_manage_exam_question_bank_and_wat_sequence()
    {
        $admin = User::where('role', 'super_admin')->first();
        $exam = Exam::where('exam_type', 'iq_mcq')->first();
        $isTempExam = false;
        if (!$exam) {
            $isTempExam = true;
            $exam = Exam::create([
                'title' => 'Test Admin Manage Exam ' . uniqid(),
                'slug' => 'test-admin-manage-exam-' . uniqid(),
                'exam_type' => 'iq_mcq',
                'category' => 'Aeronautical IQ',
                'branch' => 'air_force',
                'duration_minutes' => 45,
                'total_marks' => 50,
                'pass_marks' => 25,
                'status' => 'draft',
            ]);
        }

        $watExam = Exam::where('exam_type', 'word_association')->first();
        $isTempWat = false;
        if (!$watExam) {
            $isTempWat = true;
            $watExam = Exam::create([
                'title' => 'Test WAT Admin Manage Exam ' . uniqid(),
                'slug' => 'test-wat-admin-manage-' . uniqid(),
                'exam_type' => 'word_association',
                'category' => 'Psychological WAT',
                'branch' => 'army',
                'duration_minutes' => 15,
                'total_marks' => 20,
                'pass_marks' => 10,
                'status' => 'draft',
            ]);
        }

        // 1. Admin can access exam question bank
        $this->actingAs($admin)->get('/admin/exams/' . $exam->id . '/questions')
            ->assertStatus(200)
            ->assertSee('Question Bank');

        // 2. Admin can add a new question
        $response = $this->actingAs($admin)->post('/admin/exams/' . $exam->id . '/questions', [
            'question_text' => 'What is the standard cruising speed of a military trainer aircraft in knots?',
            'option_a' => '120 knots',
            'option_b' => '180 knots',
            'option_c' => '240 knots',
            'option_d' => '300 knots',
            'correct_answer' => 'B',
            'marks' => 1.0,
            'negative_marks' => 0.25,
            'difficulty' => 'medium',
            'explanation' => 'Standard trainer cruising speed operates around 180 knots.',
        ]);
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('questions', [
            'exam_id' => $exam->id,
            'correct_answer' => 'B',
        ]);

        // 3. Admin can access WAT sequence and add a word
        $this->actingAs($admin)->get('/admin/exams/' . $watExam->id . '/wat-words')
            ->assertStatus(200)
            ->assertSee('WAT Flash Sequence');

        $watResponse = $this->actingAs($admin)->post('/admin/exams/' . $watExam->id . '/wat-words', [
            'word' => 'HONOR',
            'display_seconds' => 15,
        ]);
        $watResponse->assertSessionHas('success');

        $this->assertDatabaseHas('wat_words', [
            'exam_id' => $watExam->id,
            'word' => 'HONOR',
        ]);

        // 4. Admin can inspect attempts overview
        $this->actingAs($admin)->get('/admin/exams-attempts')
            ->assertStatus(200)
            ->assertSee('Exam Attempts');

        if ($isTempExam) {
            $exam->questions()->delete();
            $exam->delete();
        }
        if ($isTempWat) {
            $watExam->watWords()->delete();
            $watExam->delete();
        }
    }

    /**
     * Test 9: Public Contact Inquiry & CMS Management
     */
    public function test_contact_inquiry_submission_and_admin_cms_management()
    {
        // 1. Submit public inquiry
        $inquiryData = [
            'name' => 'Tariqul Islam',
            'email' => 'tariqul@gmail.com',
            'phone' => '+880 1711-223344',
            'subject' => 'Admission into BMA 95 Long Course',
            'message' => 'I would like to inquire about the hostel facilities and class schedule for the upcoming batch.',
        ];

        $res = $this->post('/contact', $inquiryData);
        $res->assertSessionHas('success');

        $this->assertDatabaseHas('contact_inquiries', [
            'email' => 'tariqul@gmail.com',
            'status' => 'unread',
        ]);

        // 2. Admin views inquiry in CMS
        $admin = User::where('role', 'super_admin')->first();
        $this->actingAs($admin)->get('/admin/cms')
            ->assertStatus(200)
            ->assertSee('Tariqul Islam')
            ->assertSee('Admission Inquiries');

        $inquiry = ContactInquiry::where('email', 'tariqul@gmail.com')->first();

        // 3. Admin updates inquiry status
        $statusRes = $this->actingAs($admin)->post('/admin/cms/inquiries/' . $inquiry->id . '/status', [
            'status' => 'read',
        ]);
        $statusRes->assertSessionHas('success');

        $this->assertEquals('read', $inquiry->fresh()->status);
    }

    public function test_quick_admin_access_and_unauthorized_role_handling(): void
    {
        // 1. Quick admin authenticates and redirects to dashboard
        $res = $this->get('/quick-admin');
        $res->assertRedirect('/admin/dashboard');
        $this->assertAuthenticated();
        $this->assertEquals('super_admin', auth()->user()->role);

        // 2. A cadet trying to access admin receives friendly 403 with switch option
        $cadetUser = User::where('role', 'academic_student')->first();
        $cadetRes = $this->actingAs($cadetUser)->get('/admin/dashboard');
        $cadetRes->assertStatus(403);
        $cadetRes->assertSee('Administrative Privileges Required');
        $cadetRes->assertSee('Access Restricted');
        $cadetRes->assertDontSee('quick_admin');
    }

    /**
     * Test 11: Separated CMS Pages Load Successfully
     */
    public function test_separated_cms_pages_load_successfully(): void
    {
        $admin = User::where('role', 'super_admin')->first();

        $routes = [
            '/admin/cms' => 'Overview Hub',
            '/admin/cms/home' => 'Home Page Editor',
            '/admin/cms/courses' => 'Courses Page Editor',
            '/admin/cms/classes' => 'Classes & Routines Editor',
            '/admin/cms/online-tests' => 'Online Tests Portal Editor',
            '/admin/cms/about' => 'About Page Editor',
            '/admin/cms/contact' => 'Contact & Footer Editor',
            '/admin/cms/gallery' => 'Campus Gallery Editor',
            '/admin/cms/notices' => 'Notices & Circulars Publisher',
            '/admin/cms/branding' => 'Identity & Theme Colors Customizer',
            '/admin/cms/inquiries' => 'Admission Inquiries & Leads',
        ];

        foreach ($routes as $route => $expectedText) {
            $this->actingAs($admin)->get($route)
                ->assertStatus(200)
                ->assertSee($expectedText)
                ->assertSee('Web Management');
        }

        // Test updating settings from separated page
        $updateRes = $this->actingAs($admin)->post('/admin/cms/settings', [
            'hero_heading_prefix' => 'Lead With Honor at the',
        ]);
        $updateRes->assertSessionHas('success');
        $this->assertEquals('Lead With Honor at the', cms('hero_heading_prefix'));

        // Revert to original
        $this->actingAs($admin)->post('/admin/cms/settings', [
            'hero_heading_prefix' => 'Forge Your Legacy in the',
        ]);
        $this->assertEquals('Forge Your Legacy in the', cms('hero_heading_prefix'));
    }

    /**
     * Test 12: Dynamic Hero Slider Management (Add, Delete, Reset Any Number of Slides)
     */
    public function test_dynamic_hero_slides_management_add_delete_reset(): void
    {
        $admin = User::where('role', 'super_admin')->first();

        // 0. Reset to default 5 slides before testing
        $this->actingAs($admin)->post('/admin/cms/hero-slides-reset');

        // 1. Initial count of slides (default 5)
        $initialSlides = cms_hero_slides();
        $this->assertCount(5, $initialSlides);

        // 2. Add a new slide via URL
        $testUrl = 'https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?w=1600';
        $addRes = $this->actingAs($admin)->post('/admin/cms/hero-slides', [
            'slide_url' => $testUrl,
        ]);
        $addRes->assertSessionHas('success');

        $slidesAfterAdd = cms_hero_slides();
        $this->assertCount(6, $slidesAfterAdd);
        $this->assertEquals($testUrl, end($slidesAfterAdd));

        // 3. Verify public home page renders 6 slides and 6 indicator dots
        $homeRes = $this->get('/');
        $homeRes->assertStatus(200);
        $homeRes->assertSee($testUrl);

        // 4. Delete the added slide (index 5)
        $delRes = $this->actingAs($admin)->delete('/admin/cms/hero-slides/5');
        $delRes->assertSessionHas('success');

        $slidesAfterDel = cms_hero_slides();
        $this->assertCount(5, $slidesAfterDel);

        // 5. Reset to default
        $resetRes = $this->actingAs($admin)->post('/admin/cms/hero-slides-reset');
        $resetRes->assertSessionHas('success');
        $this->assertCount(5, cms_hero_slides());
    }

    /**
     * Test 13: Official Logo Upload from Backend Replaces Sitewide Emblem
     */
    public function test_admin_can_upload_official_logo_and_replaces_sitewide(): void
    {
        $admin = User::where('role', 'super_admin')->first();
        $savedLogo = cms('site_logo');

        // Reset logo to verify default 3D emblem state
        \App\Models\CmsSetting::whereIn('key', ['site_logo', 'site_crest'])->update(['value' => '']);
        if (function_exists('cms_clear_cache')) {
            cms_clear_cache();
        }

        // 1. Initial state: official default logo renders (3D SVG crest is completely absent)
        $initHome = $this->get('/');
        $initHome->assertStatus(200);
        $initHome->assertDontSee('brand-crest-3d-svg');
        $initHome->assertSee('images/logo.png');

        // 2. Upload official academy logo via backend
        $fakeLogo = \Illuminate\Http\UploadedFile::fake()->create('ida_official_logo.png', 100, 'image/png');
        $uploadRes = $this->actingAs($admin)->post('/admin/cms/settings', [
            'site_logo' => $fakeLogo,
        ]);
        $uploadRes->assertSessionHas('success');

        $activeLogo = cms('site_logo');
        $this->assertNotEmpty($activeLogo);
        $this->assertStringContainsString('site_logo_', $activeLogo);

        // 3. Verify public pages render the uploaded image asset
        $homeWithLogo = $this->get('/');
        $homeWithLogo->assertStatus(200);
        $homeWithLogo->assertSee($activeLogo);
        $homeWithLogo->assertDontSee('brand-crest-3d-svg');

        // 4. Remove custom logo and restore default official logo
        $removeRes = $this->actingAs($admin)->post('/admin/cms/settings', [
            'remove_site_logo' => 1,
        ]);
        $removeRes->assertSessionHas('success');
        $this->assertEmpty(cms('site_logo'));

        // 5. Verify default official logo is restored and 3D SVG is not present
        $restoredHome = $this->get('/');
        $restoredHome->assertStatus(200);
        $restoredHome->assertDontSee('brand-crest-3d-svg');
        $restoredHome->assertSee('images/logo.png');

        // Restore previous logo if present
        if ($savedLogo) {
            \App\Models\CmsSetting::updateOrCreate(['key' => 'site_logo'], ['value' => $savedLogo]);
            \App\Models\CmsSetting::updateOrCreate(['key' => 'site_crest'], ['value' => $savedLogo]);
            if (function_exists('cms_clear_cache')) {
                cms_clear_cache();
            }
        }
    }

    public function test_khulna_campus_footer_editing_from_homepage_and_attractive_bulletin(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin_test_campus@ida.test'],
            ['name' => 'Campus Admin', 'password' => bcrypt('password'), 'role' => 'admin', 'rank' => 'Wing Commander']
        );

        // 1. Check that backend home page editor includes the Khulna Campus & Footer section
        $homeCmsRes = $this->actingAs($admin)->get('/admin/cms/home');
        $homeCmsRes->assertStatus(200);
        $homeCmsRes->assertSee('Khulna Campus & Sitewide Footer', false);
        $homeCmsRes->assertSee('name="footer_campus_heading"', false);
        $homeCmsRes->assertSee('name="academy_location"', false);
        $homeCmsRes->assertSee('name="academy_phone"', false);
        $homeCmsRes->assertSee('name="academy_email"', false);
        $homeCmsRes->assertSee('name="office_hours"', false);
        $homeCmsRes->assertSee('name="footer_bio"', false);
        $homeCmsRes->assertSee('name="footer_tag1"', false);

        // 2. Update Khulna Campus and footer information from homepage CMS
        $updateRes = $this->actingAs($admin)->post('/admin/cms/settings', [
            'footer_campus_heading' => 'KHULNA CAMPUS COMMAND',
            'academy_location' => 'Boyra Main Road (Near Medical College), Khulna - 9000',
            'academy_phone' => '+880 1712-345678, +880 1911-987654',
            'academy_email' => 'info@ida.com.bd, admissions@ida.com.bd',
            'office_hours' => 'Saturday - Thursday: 08:00 AM - 08:00 PM (Friday: 03:00 PM - 08:00 PM)',
            'footer_tag1' => 'Valor',
            'footer_tag2' => 'Leadership',
            'footer_tag3' => 'Integrity',
            'footer_copyright' => 'Imperial Defence Academy (IDA), Khulna. All rights reserved. Precision • Character • Commission.',
            'marquee_is_live' => '1',
            'marquee_label' => 'COMMAND BULLETIN:',
            'marquee_text' => 'New BMA and Air Force Sessions Open',
        ]);
        $updateRes->assertSessionHas('success');

        // 3. Verify on live frontend homepage
        $homeRes = $this->get('/');
        $homeRes->assertStatus(200);
        $homeRes->assertSee('KHULNA CAMPUS COMMAND');
        $homeRes->assertSee('Boyra Main Road (Near Medical College), Khulna - 9000');
        $homeRes->assertSee('+880 1712-345678, +880 1911-987654');
        $homeRes->assertSee('info@ida.com.bd, admissions@ida.com.bd');
        $homeRes->assertSee('Saturday - Thursday: 08:00 AM - 08:00 PM (Friday: 03:00 PM - 08:00 PM)');
        $homeRes->assertSee('Valor');
        $homeRes->assertSee('Integrity');
        $homeRes->assertSee('Precision • Character • Commission.');

        // 4. Verify attractive bulletin styling classes exist
        $homeRes->assertSee('ida-bulletin-container');
        $homeRes->assertSee('ida-bulletin-card');
        $homeRes->assertSee('New BMA and Air Force Sessions Open');

        // 5. Test clean removal when cleared
        $clearRes = $this->actingAs($admin)->post('/admin/cms/settings', [
            'footer_campus_heading' => '',
            'academy_location' => '',
            'academy_phone' => '',
            'academy_email' => '',
            'office_hours' => '',
            'footer_tag1' => '',
            'footer_tag2' => '',
            'footer_tag3' => '',
        ]);
        $clearRes->assertSessionHas('success');

        $cleanHome = $this->get('/');
        $cleanHome->assertDontSee('KHULNA CAMPUS COMMAND');
        $cleanHome->assertDontSee('Boyra Main Road (Near Medical College)');
    }
}

