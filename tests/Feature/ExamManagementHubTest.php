<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\Exam;
use App\Models\Course;
use App\Models\Question;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ExamManagementHubTest extends TestCase
{
    protected function getAdminUser(): User
    {
        $admin = User::where('role', 'super_admin')->first();
        if (!$admin) {
            $admin = User::create([
                'name' => 'Super Admin',
                'email' => 'admin_test_' . uniqid() . '@ida.com',
                'account_id' => 'ADM-' . strtoupper(uniqid()),
                'password' => Hash::make('password'),
                'role' => 'super_admin',
            ]);
        }
        return $admin;
    }

    protected function getCadetUser(): array
    {
        $user = User::where('role', 'academic_student')->first();
        if (!$user) {
            $user = User::create([
                'name' => 'Cadet Tester',
                'email' => 'cadet_' . uniqid() . '@ida.com',
                'account_id' => 'CDT-' . strtoupper(uniqid()),
                'password' => Hash::make('password'),
                'role' => 'academic_student',
            ]);
        }
        $student = Student::where('user_id', $user->id)->first();
        if (!$student) {
            $student = Student::create([
                'user_id' => $user->id,
                'student_id_code' => 'STU-' . strtoupper(uniqid()),
                'target_wing' => 'army',
                'status' => 'active',
            ]);
        }
        return [$user, $student];
    }

    public function test_sidebar_contains_exam_management_under_student_management(): void
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Cadet Management');
        $response->assertSee('Exam Management');
        $response->assertSee(route('admin.exam_management.index'));
    }

    public function test_exam_management_hub_displays_two_command_boxes(): void
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin)->get(route('admin.exam_management.index'));
        $response->assertStatus(200);

        // Verify title and landing elements
        $response->assertSee('Exam Management');
        $response->assertSee('Free Exams');
        $response->assertSee('Cadet Exams');
        $response->assertDontSee('Paid Exams');
        $response->assertSee('Total Exams');
        $response->assertSee('View All Exams');
        $response->assertSee('Create a New Exam');
        $response->assertDontSee('Frontend Online Tests • Public Access');
        $response->assertDontSee('Cadet Assessment Portal • Enrolled Cadets');

        // Check links to filtered views
        $response->assertSee(route('admin.exam_management.index', ['type' => 'free']));
        $response->assertSee(route('admin.exam_management.index', ['type' => 'paid']));
    }

    public function test_filter_free_exams_view(): void
    {
        $admin = $this->getAdminUser();

        Exam::firstOrCreate(
            ['is_paid_for_external' => false, 'status' => 'open', 'access_type' => 'free'],
            [
                'title' => 'Test Free Assessment Exam ' . uniqid(),
                'slug' => 'test-free-exam-' . uniqid(),
                'branch' => 'army',
                'category' => 'Verbal IQ',
                'exam_type' => 'iq_mcq',
                'access_type' => 'free',
                'duration_minutes' => 20,
                'total_marks' => 10,
                'pass_marks' => 5,
                'fee' => 0,
            ]
        );

        $response = $this->actingAs($admin)->get(route('admin.exam_management.index', ['type' => 'free']));
        $response->assertStatus(200);

        $response->assertSee('Back to Exam Management');
        $response->assertSee('Free Exams');
        $response->assertSee('FREE EXAM');
        $response->assertDontSee('Paid Exams');
    }

    public function test_filter_paid_exams_view(): void
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin)->get(route('admin.exam_management.index', ['type' => 'paid']));
        $response->assertStatus(200);

        $response->assertSee('Back to Exam Management');
        $response->assertSee('Cadet Exams');
        $response->assertDontSee('Paid Exams');
        $response->assertDontSee('Convert to Free');
    }

    public function test_admin_can_toggle_exam_payment_status(): void
    {
        $admin = $this->getAdminUser();

        $exam = Exam::create([
            'title' => 'Toggle Test Assessment ' . uniqid(),
            'branch' => 'army',
            'slug' => 'toggle-test-' . uniqid(),
            'exam_type' => 'iq_mcq',
            'category' => 'Testing',
            'fee' => 0.00,
            'is_paid_for_external' => false,
            'is_public_for_external' => true,
            'status' => 'open',
            'duration_minutes' => 20,
            'total_marks' => 20,
            'pass_marks' => 10,
        ]);

        $this->assertFalse((bool)$exam->is_paid_for_external);

        // Toggle to Paid
        $response = $this->actingAs($admin)->post(route('admin.exam_management.toggle_payment', $exam->id), [
            'fee' => 450.00,
        ]);
        $response->assertRedirect();

        $exam->refresh();
        $this->assertTrue((bool)$exam->is_paid_for_external);
        $this->assertEquals(450.00, (float)$exam->fee);

        // Toggle back to Free
        $response2 = $this->actingAs($admin)->post(route('admin.exam_management.toggle_payment', $exam->id));
        $response2->assertRedirect();

        $exam->refresh();
        $this->assertFalse((bool)$exam->is_paid_for_external);
        $this->assertEquals(0.00, (float)$exam->fee);
    }

    public function test_admin_can_update_exam_fee(): void
    {
        $admin = $this->getAdminUser();

        $exam = Exam::create([
            'title' => 'Fee Test Assessment ' . uniqid(),
            'branch' => 'navy',
            'slug' => 'fee-test-' . uniqid(),
            'exam_type' => 'iq_mcq',
            'category' => 'Testing',
            'fee' => 300.00,
            'is_paid_for_external' => true,
            'is_public_for_external' => true,
            'status' => 'open',
            'duration_minutes' => 20,
            'total_marks' => 20,
            'pass_marks' => 10,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.exam_management.update_fee', $exam->id), [
            'fee' => 750.00,
        ]);
        $response->assertRedirect();

        $exam->refresh();
        $this->assertEquals(750.00, (float)$exam->fee);
        $this->assertTrue((bool)$exam->is_paid_for_external);
    }

    public function test_frontend_online_tests_only_shows_free_exams(): void
    {
        $freeExam = Exam::where('is_paid_for_external', false)
            ->where('is_public_for_external', true)
            ->where('status', '!=', 'archived')
            ->first();

        $paidExam = Exam::where('is_paid_for_external', true)
            ->where('status', '!=', 'archived')
            ->first();

        $response = $this->get(route('online_tests'));
        $response->assertStatus(200);

        if ($freeExam) {
            $response->assertSee($freeExam->title);
        }
        if ($paidExam) {
            $response->assertDontSee($paidExam->title);
        }
    }

    public function test_cadet_exam_page_only_shows_paid_exams(): void
    {
        [$cadetUser, $student] = $this->getCadetUser();

        $freeExam = Exam::where('is_paid_for_external', false)
            ->where('is_public_for_external', true)
            ->where('status', '!=', 'archived')
            ->first();

        $paidExam = Exam::where('is_paid_for_external', true)
            ->whereIn('status', ['open', 'scheduled'])
            ->first();

        $response = $this->actingAs($cadetUser)->get(route('cadet.exams.index'));
        $response->assertStatus(200);

        if ($paidExam) {
            $response->assertSee($paidExam->title);
        }
        if ($freeExam) {
            $response->assertDontSee($freeExam->title);
        }
    }

    public function test_admin_can_update_exam_details_and_condition_from_management(): void
    {
        $admin = $this->getAdminUser();

        $exam = Exam::create([
            'title' => 'Initial Title ' . uniqid(),
            'branch' => 'army',
            'slug' => 'edit-mgmt-test-' . uniqid(),
            'exam_type' => 'iq_mcq',
            'category' => 'Initial Topic',
            'description' => 'Initial Description',
            'fee' => 0.00,
            'is_paid_for_external' => false,
            'is_public_for_external' => true,
            'status' => 'draft',
            'duration_minutes' => 30,
            'total_marks' => 30,
            'pass_marks' => 15,
            'negative_marking_per_wrong' => 0.25,
        ]);

        $returnUrl = route('admin.exam_management.index', ['type' => 'paid']);

        // 1. Update to LIVE with custom topic, description, duration, marks, fee, return_to
        $response = $this->actingAs($admin)->put(route('admin.exams.update', $exam->id), [
            'title' => 'Updated Army Command Assessment',
            'branch' => 'army',
            'category' => 'Advanced Situational Logic',
            'description' => 'Comprehensive timed military decision making assessment under pressure.',
            'status' => 'LIVE',
            'duration_minutes' => 45,
            'total_marks' => 60,
            'pass_marks' => 30,
            'negative_marking_per_wrong' => 0.50,
            'fee' => 600.00,
            'is_paid_for_external' => 1,
            'return_to' => $returnUrl,
        ]);

        $response->assertRedirect($returnUrl);

        $exam->refresh();
        $this->assertEquals('Updated Army Command Assessment', $exam->title);
        $this->assertEquals('Advanced Situational Logic', $exam->category);
        $this->assertEquals('Comprehensive timed military decision making assessment under pressure.', $exam->description);
        $this->assertEquals('open', $exam->status);
        $this->assertTrue($exam->isLiveNow());
        $this->assertEquals(45, $exam->duration_minutes);
        $this->assertEquals(60.00, (float)$exam->total_marks);
        $this->assertEquals(30.00, (float)$exam->pass_marks);
        $this->assertEquals(0.50, (float)$exam->negative_marking_per_wrong);
        $this->assertEquals(600.00, (float)$exam->fee);
        $this->assertTrue((bool)$exam->is_paid_for_external);

        // 2. Update to SCHEDULED
        $futureStart = \Carbon\Carbon::now()->addDay();
        $futureEnd = \Carbon\Carbon::now()->addDays(2);
        $response2 = $this->actingAs($admin)->put(route('admin.exams.update', $exam->id), [
            'title' => $exam->title,
            'branch' => 'army',
            'category' => $exam->category,
            'status' => 'SCHEDULED',
            'schedule_start' => $futureStart->format('Y-m-d H:i:s'),
            'schedule_end' => $futureEnd->format('Y-m-d H:i:s'),
            'duration_minutes' => 45,
            'total_marks' => 60,
            'pass_marks' => 30,
            'return_to' => $returnUrl,
        ]);
        $response2->assertRedirect($returnUrl);
        $exam->refresh();
        $this->assertEquals('scheduled', $exam->status);
        $this->assertTrue($exam->isScheduledFuture());

        // 3. Update to ENDED
        $response3 = $this->actingAs($admin)->put(route('admin.exams.update', $exam->id), [
            'title' => $exam->title,
            'branch' => 'army',
            'category' => $exam->category,
            'status' => 'ENDED',
            'duration_minutes' => 45,
            'total_marks' => 60,
            'pass_marks' => 30,
            'return_to' => $returnUrl,
        ]);
        $response3->assertRedirect($returnUrl);
        $exam->refresh();
        $this->assertEquals('closed', $exam->status);
        $this->assertTrue($exam->isEnded());

        $exam->delete();
    }

    public function test_cadet_exam_displays_correct_status_and_time_rules(): void
    {
        [$cadetUser, $student] = $this->getCadetUser();

        // 1. Create a Scheduled Paid Exam
        $scheduledExam = Exam::create([
            'title' => 'Scheduled Cadet Test ' . uniqid(),
            'branch' => 'army',
            'slug' => 'sched-cadet-' . uniqid(),
            'exam_type' => 'iq_mcq',
            'category' => 'Scheduled Logic',
            'description' => 'Scheduled assessment test for cadets',
            'fee' => 500.00,
            'is_paid_for_external' => true,
            'status' => 'scheduled',
            'schedule_start' => \Carbon\Carbon::now()->addHours(5),
            'schedule_end' => \Carbon\Carbon::now()->addHours(7),
            'duration_minutes' => 30,
            'total_marks' => 50,
            'pass_marks' => 25,
        ]);

        // 2. Create an Ended Paid Exam
        $endedExam = Exam::create([
            'title' => 'Ended Cadet Test ' . uniqid(),
            'branch' => 'army',
            'slug' => 'ended-cadet-' . uniqid(),
            'exam_type' => 'iq_mcq',
            'category' => 'Concluded Logic',
            'description' => 'Past assessment test for cadets',
            'fee' => 500.00,
            'is_paid_for_external' => true,
            'status' => 'closed',
            'schedule_start' => \Carbon\Carbon::now()->subHours(5),
            'schedule_end' => \Carbon\Carbon::now()->subHours(3),
            'duration_minutes' => 30,
            'total_marks' => 50,
            'pass_marks' => 25,
        ]);

        $response = $this->actingAs($cadetUser)->get(route('cadet.exams.index'));
        $response->assertStatus(200);

        // Scheduled: Displays starting time in place of Start Assessment Test
        $response->assertSee('SCHEDULED');
        $response->assertSee('Starts at ' . $scheduledExam->schedule_start->format('h:i A'));

        // Ended: Displays started and ended times in place of Start Assessment Test
        $response->assertSee('ENDED');
        $response->assertSee('Exam Concluded');
        $response->assertSee('Started: ' . $endedExam->schedule_start->format('h:i A'));
        $response->assertSee('Ended: ' . $endedExam->schedule_end->format('h:i A'));

        $scheduledExam->delete();
        $endedExam->delete();
    }

    public function test_admin_can_access_dedicated_create_exam_page(): void
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin)->get(route('admin.exam_management.create'));
        $response->assertStatus(200);

        // Verify full page elements & exact user requirements
        $response->assertSee('Create New Assessment Module');
        $response->assertSee('Access Type');
        $response->assertDontSee('Access Type (Free vs Paid)');
        $response->assertSee('Free Exam');
        $response->assertSee('Cadet Exam');
        $response->assertDontSee('Paid Exam');
        $response->assertSee('Both');
        $response->assertSee('Category');
        $response->assertSee('Negative Marking');
        $response->assertSee('Schedule Start Date & Time', false);
        $response->assertSee('Schedule End Date & Time', false);
        $response->assertSee('Brief Description');
        $response->assertDontSee('Exam Fee');

        // Verify redirect from legacy exams.create
        $redirectResponse = $this->actingAs($admin)->get(route('admin.exams.create'));
        $redirectResponse->assertRedirect(route('admin.exam_management.create'));
    }

    public function test_admin_can_access_dedicated_edit_exam_page_matching_create_page(): void
    {
        $admin = $this->getAdminUser();

        $exam = Exam::create([
            'title' => 'Dedicated Edit Page Test ' . uniqid(),
            'branch' => 'army',
            'slug' => 'edit-page-test-' . uniqid(),
            'exam_type' => 'iq_mcq',
            'category' => 'Analytical Matrix',
            'duration_minutes' => 35,
            'total_marks' => 50,
            'pass_marks' => 25,
            'negative_marking_per_wrong' => 0.25,
            'access_type' => 'paid',
            'status' => 'open',
            'description' => 'Test description for dedicated full edit page verification.',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.exams.edit', $exam->id));
        $response->assertStatus(200);

        // Verify full 3-section page layout identical to create page
        $response->assertSee('Edit Assessment Module');
        $response->assertSee('Assessment Identity & Access Control', false);
        $response->assertSee('Engine Configuration & Scoring Rules', false);
        $response->assertSee('Scheduling & Candidate Overview', false);
        $response->assertSee('Access Type');
        $response->assertDontSee('Access Type (Free vs Paid)');
        $response->assertSee('Free Exam');
        $response->assertSee('Cadet Exam');
        $response->assertDontSee('Paid Exam');
        $response->assertSee('Both');
        $response->assertSee('Analytical Matrix');
        $response->assertSee('Negative Marking');
        $response->assertSee('Schedule Start Date & Time', false);
        $response->assertSee('Schedule End Date & Time', false);
        $response->assertSee('Brief Description');
        $response->assertDontSee('Exam Fee');
        $response->assertSee('Save Question Paper');

        $exam->delete();
    }

    public function test_admin_can_store_exam_with_access_types_and_scheduling(): void
    {
        $admin = $this->getAdminUser();

        $title = 'Aeronautical Space Matrix ' . uniqid();
        $payload = [
            'title' => $title,
            'branch' => 'air_force',
            'category' => 'Aero Space Custom Topic',
            'exam_type' => 'iq_mcq',
            'duration_minutes' => 45,
            'total_marks' => 60,
            'pass_marks' => 30,
            'access_type' => 'both',
            // No fee sent in form
            'negative_marking_per_wrong' => 0.35,
            'status' => 'scheduled',
            'schedule_start' => '2026-10-01T10:00',
            'schedule_end' => '2026-10-01T12:00',
            'description' => 'Comprehensive spatial and aerodynamic intelligence testing.',
        ];

        $response = $this->actingAs($admin)->post(route('admin.exam_management.store'), $payload);
        $response->assertRedirect(route('admin.exam_management.index', ['type' => 'paid']));

        $exam = Exam::where('title', $title)->first();
        $this->assertNotNull($exam);
        $this->assertEquals('both', $exam->access_type);
        $this->assertEquals(1, $exam->is_paid_for_external);
        $this->assertEquals(500, (float)$exam->fee);
        $this->assertEquals('Aero Space Custom Topic', $exam->category);
        $this->assertEquals(0.35, (float)$exam->negative_marking_per_wrong);
        $this->assertEquals('scheduled', $exam->status);
        $this->assertEquals('Comprehensive spatial and aerodynamic intelligence testing.', $exam->description);

        // Also test creating a free exam
        $freeTitle = 'Free Demo Test ' . uniqid();
        $freePayload = [
            'title' => $freeTitle,
            'branch' => 'navy',
            'category' => 'Open Naval IQ',
            'exam_type' => 'iq_mcq',
            'duration_minutes' => 20,
            'total_marks' => 25,
            'pass_marks' => 12,
            'access_type' => 'free',
            'negative_marking_per_wrong' => 0.20,
            'status' => 'open',
            'description' => 'Free entrance practice for naval candidates.',
        ];

        $freeResponse = $this->actingAs($admin)->post(route('admin.exam_management.store'), $freePayload);
        $freeResponse->assertRedirect(route('admin.exam_management.index', ['type' => 'free']));

        $freeExam = Exam::where('title', $freeTitle)->first();
        $this->assertNotNull($freeExam);
        $this->assertEquals('free', $freeExam->access_type);
        $this->assertEquals(0, $freeExam->is_paid_for_external);
        $this->assertEquals(0, $freeExam->fee);

        $exam->delete();
        $freeExam->delete();
    }

    public function test_cadet_enrolled_in_army_only_sees_army_exams_and_ongoing_courses_for_other_branches(): void
    {
        [$cadetUser, $student] = $this->getCadetUser();

        // Ensure student is enrolled only in Army course
        $armyCourse = Course::where('category', 'Army')->first();
        if (!$armyCourse) {
            $armyCourse = Course::create([
                'title' => 'BMA Army Test Prep ' . uniqid(),
                'category' => 'Army',
                'admission_status' => 'open',
                'fee' => 15000,
            ]);
        }
        $student->courses()->sync([$armyCourse->id => ['enrolled_at' => now(), 'status' => 'active', 'payment_status' => 'paid']]);
        $student->update(['current_course_id' => $armyCourse->id, 'target_wing' => 'Army']);

        $armyExam = Exam::where('branch', 'army')->where('is_paid_for_external', true)->where('status', 'open')->first();
        if (!$armyExam) {
            $armyExam = Exam::create([
                'title' => 'Test Army Cadet Exam ' . uniqid(),
                'slug' => 'test-army-exam-' . uniqid(),
                'branch' => 'army',
                'category' => 'Army Aptitude',
                'exam_type' => 'iq_mcq',
                'duration_minutes' => 30,
                'total_marks' => 20,
                'pass_marks' => 10,
                'status' => 'open',
                'is_paid_for_external' => true,
            ]);
        }

        $navyExam = Exam::where('branch', 'navy')->where('is_paid_for_external', true)->where('status', 'open')->first();
        if (!$navyExam) {
            $navyExam = Exam::create([
                'title' => 'Test Navy Cadet Exam ' . uniqid(),
                'slug' => 'test-navy-exam-' . uniqid(),
                'branch' => 'navy',
                'category' => 'Naval Aptitude',
                'exam_type' => 'iq_mcq',
                'duration_minutes' => 30,
                'total_marks' => 20,
                'pass_marks' => 10,
                'status' => 'open',
                'is_paid_for_external' => true,
            ]);
        }

        $this->assertNotNull($armyExam);
        $this->assertNotNull($navyExam);

        // 1. Visit All Branches (default): Sees Army exam, does NOT see Navy exam
        $allResponse = $this->actingAs($cadetUser)->get(route('cadet.exams.index'));
        $allResponse->assertStatus(200);
        $allResponse->assertSee($armyExam->title);
        $allResponse->assertDontSee($navyExam->title);

        // 2. Visit Army branch: Sees Army exam
        $armyResponse = $this->actingAs($cadetUser)->get(route('cadet.exams.index', ['branch' => 'army']));
        $armyResponse->assertStatus(200);
        $armyResponse->assertSee($armyExam->title);

        // 3. Visit Navy branch: Does NOT see Navy exams, sees ongoing courses in Navy
        $navyResponse = $this->actingAs($cadetUser)->get(route('cadet.exams.index', ['branch' => 'navy']));
        $navyResponse->assertStatus(200);
        $navyResponse->assertDontSee($navyExam->title);
        $navyResponse->assertSee('Ongoing Courses in Bangladesh Navy');
        $navyResponse->assertSee('Active Preparatory Programs');

        // 4. Visit Air Force branch: Sees ongoing courses in Air Force
        $airResponse = $this->actingAs($cadetUser)->get(route('cadet.exams.index', ['branch' => 'air_force']));
        $airResponse->assertStatus(200);
        $airResponse->assertSee('Ongoing Courses in Bangladesh Air Force');

        // 5. Visit Police branch: Sees ongoing courses in Police
        $policeResponse = $this->actingAs($cadetUser)->get(route('cadet.exams.index', ['branch' => 'police']));
        $policeResponse->assertStatus(200);
        $policeResponse->assertSee('Ongoing Courses in Bangladesh Police');

        // 6. Direct access attempt to start Navy exam redirects with notice
        $startResponse = $this->actingAs($cadetUser)->get(route('cadet.exams.start', $navyExam->id));
        $startResponse->assertRedirect(route('cadet.exams.index', ['branch' => 'navy']));
        $startResponse->assertSessionHas('error');
    }

    public function test_create_exam_page_displays_mandatory_question_ingestion_with_two_options(): void
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin)->get(route('admin.exam_management.create'));
        $response->assertStatus(200);

        // Verify Question Paper section exists
        $response->assertSee('Question Paper');

        // Verify strictly 2 options: Bulk Upload and Single Upload
        $response->assertSee('Bulk Upload');
        $response->assertSee('Single Upload');

        // Verify legacy scanning / upload tabs are removed
        $response->assertDontSee('Upload from PDF');
        $response->assertDontSee('Upload from JSON');
        $response->assertDontSee('STEM OCR & LaTeX Digitizer');
        $response->assertDontSee('Missing \\end{pmatrix}');

        // Verify bulk and single action triggers
        $response->assertSee('Load Question');
        $response->assertSee('Create Question Paper');
    }

    public function test_storing_exam_with_questions_json_creates_linked_questions(): void
    {
        $admin = $this->getAdminUser();

        $title = 'Question Ingestion Test ' . uniqid();
        $sampleQuestions = [
            [
                'question_text' => 'What is the capital of Bangladesh?',
                'option_a' => 'Dhaka',
                'option_b' => 'Chittagong',
                'option_c' => 'Khulna',
                'option_d' => 'Rajshahi',
                'correct_answer' => 'A',
                'explanation' => 'Dhaka is the national capital.'
            ],
            [
                'question_text' => 'Which is the primary military academy of Bangladesh?',
                'option_a' => 'BMA',
                'option_b' => 'BNA',
                'option_c' => 'BAFA',
                'option_d' => 'BPA',
                'correct_answer' => 'A',
                'explanation' => 'Bangladesh Military Academy (BMA) in Bhatiary.'
            ]
        ];

        $payload = [
            'title' => $title,
            'branch' => 'army',
            'category' => 'General Knowledge',
            'exam_type' => 'iq_mcq',
            'duration_minutes' => 30,
            'total_marks' => 50,
            'pass_marks' => 25,
            'access_type' => 'paid',
            'status' => 'open',
            'questions_json' => json_encode($sampleQuestions),
        ];

        $response = $this->actingAs($admin)->post(route('admin.exam_management.store'), $payload);
        $response->assertRedirect(route('admin.exam_management.index', ['type' => 'paid']));

        $exam = Exam::where('title', $title)->first();
        $this->assertNotNull($exam);
        $this->assertEquals(2, $exam->questions()->count());

        $firstQ = $exam->questions()->first();
        $this->assertEquals('What is the capital of Bangladesh?', $firstQ->question_text);
        $this->assertEquals('A', $firstQ->correct_answer);

        $exam->delete();
    }

    public function test_edit_exam_page_displays_questions_and_add_question_methods(): void
    {
        $admin = $this->getAdminUser();

        $exam = Exam::create([
            'title' => 'Edit Exam Question Test ' . uniqid(),
            'branch' => 'army',
            'slug' => 'edit-q-test-' . uniqid(),
            'exam_type' => 'iq_mcq',
            'category' => 'Verbal IQ',
            'duration_minutes' => 30,
            'total_marks' => 50,
            'pass_marks' => 25,
            'access_type' => 'paid',
            'status' => 'open',
        ]);

        $q = Question::create([
            'exam_id' => $exam->id,
            'branch' => 'army',
            'exam_type' => 'iq_mcq',
            'question_text' => 'Sample Existing Question 101?',
            'options' => [
                ['key' => 'A', 'text' => 'Alpha'],
                ['key' => 'B', 'text' => 'Bravo'],
                ['key' => 'C', 'text' => 'Charlie'],
                ['key' => 'D', 'text' => 'Delta'],
            ],
            'correct_answer' => 'B',
            'marks' => 1,
            'order_seq' => 1,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.exams.edit', $exam->id));
        $response->assertStatus(200);

        // Verify Section 4 of 4
        $response->assertSee('Section 4 of 4');
        $response->assertSee('Question Bank & Add Questions', false);

        // Verify existing question is listed
        $response->assertSee('Sample Existing Question 101?');
        $response->assertSee('Ans: B');

        // Verify strictly 2 options for adding questions: Bulk Upload and Single Upload
        $response->assertSee('Add to Question Paper');
        $response->assertSee('Bulk Upload');
        $response->assertSee('Single Upload');
        $response->assertSee('Load Question');
        $response->assertSee('Save Question Paper');

        // Verify legacy scanning / upload tabs are removed
        $response->assertDontSee('Upload from PDF');
        $response->assertDontSee('Upload from JSON');
        $response->assertDontSee('STEM OCR & LaTeX Digitizer');

        // Test deleting existing question via AJAX JSON
        $delResponse = $this->actingAs($admin)->deleteJson(route('admin.exams.delete_exam_question', [
            'examId' => $exam->id,
            'questionId' => $q->id
        ]));
        $delResponse->assertStatus(200);
        $delResponse->assertJson(['success' => true]);
        $this->assertEquals(0, $exam->questions()->count());

        $exam->delete();
    }

    public function test_update_exam_question_endpoint(): void
    {
        $admin = $this->getAdminUser();

        $exam = Exam::create([
            'title' => 'Update Q Test ' . uniqid(),
            'branch' => 'army',
            'slug' => 'update-q-test-' . uniqid(),
            'exam_type' => 'iq_mcq',
            'category' => 'Verbal IQ',
            'duration_minutes' => 30,
            'total_marks' => 50,
            'pass_marks' => 25,
            'access_type' => 'paid',
            'status' => 'open',
        ]);

        $q = Question::create([
            'exam_id' => $exam->id,
            'branch' => 'army',
            'exam_type' => 'iq_mcq',
            'question_text' => 'Original Question Text?',
            'options' => [
                ['key' => 'A', 'text' => 'Opt 1'],
                ['key' => 'B', 'text' => 'Opt 2'],
                ['key' => 'C', 'text' => 'Opt 3'],
                ['key' => 'D', 'text' => 'Opt 4'],
            ],
            'correct_answer' => 'A',
            'marks' => 1.0,
            'order_seq' => 1,
        ]);

        $response = $this->actingAs($admin)->putJson(route('admin.exams.update_exam_question', [
            'examId' => $exam->id,
            'questionId' => $q->id,
        ]), [
            'question_text' => 'Updated Question Statement After In-Place Edit?',
            'option_a' => 'Updated Opt A',
            'option_b' => 'Updated Opt B',
            'option_c' => 'Updated Opt C',
            'option_d' => 'Updated Opt D',
            'correct_answer' => 'C',
            'marks' => 2.0,
            'explanation' => 'Reasoning for option C.',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $q->refresh();
        $this->assertEquals('Updated Question Statement After In-Place Edit?', $q->question_text);
        $this->assertEquals('C', $q->correct_answer);
        $this->assertEquals(2.0, (float)$q->marks);
        $this->assertEquals('Reasoning for option C.', $q->explanation);
        $this->assertEquals('Updated Opt C', $q->options[2]['text']);

        $exam->delete();
    }

    public function test_add_single_question_directly_to_exam_endpoint(): void
    {
        $admin = $this->getAdminUser();

        $exam = Exam::create([
            'title' => 'Single Add Q Test ' . uniqid(),
            'branch' => 'navy',
            'slug' => 'single-add-q-test-' . uniqid(),
            'exam_type' => 'iq_mcq',
            'category' => 'Naval General IQ',
            'duration_minutes' => 30,
            'total_marks' => 50,
            'pass_marks' => 25,
            'access_type' => 'paid',
            'status' => 'open',
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.exams.add_single_question', [
            'examId' => $exam->id,
        ]), [
            'question_text' => 'Directly Added Naval Question?',
            'option_a' => 'Knot',
            'option_b' => 'Mile',
            'option_c' => 'Foot',
            'option_d' => 'Meter',
            'correct_answer' => 'A',
            'marks' => 1.5,
            'explanation' => 'Knot is a unit of speed.',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'total_questions' => 1]);

        $this->assertEquals(1, $exam->questions()->count());
        $newQ = $exam->questions()->first();
        $this->assertEquals('Directly Added Naval Question?', $newQ->question_text);
        $this->assertEquals('A', $newQ->correct_answer);
        $this->assertEquals('navy', $newQ->branch);

        $exam->delete();
    }

    public function test_create_exam_with_automatic_random_selection(): void
    {
        $admin = $this->getAdminUser();

        // 10 loaded questions
        $pool = [];
        for ($i = 1; $i <= 10; $i++) {
            $pool[] = [
                'question_text' => "Question Pool Number {$i}?",
                'option_a' => "A{$i}",
                'option_b' => "B{$i}",
                'option_c' => "C{$i}",
                'option_d' => "D{$i}",
                'correct_answer' => 'A',
            ];
        }

        // Automatic mode with 4 selected questions
        $postData = [
            '_token' => csrf_token(),
            'title' => 'Random 4 of 10 Assessment ' . uniqid(),
            'branch' => 'air_force',
            'category' => 'Aero IQ',
            'exam_type' => 'iq_mcq',
            'duration_minutes' => 25,
            'total_marks' => 40,
            'pass_marks' => 20,
            'access_type' => 'paid',
            'fee' => 350,
            'status' => 'open',
            'selection_mode' => 'automatic',
            'selected_question_count' => 4,
            'questions_json' => json_encode($pool),
        ];

        $response = $this->actingAs($admin)->post(route('admin.exam_management.store'), $postData);
        $response->assertRedirect();

        $exam = Exam::where('title', $postData['title'])->first();
        $this->assertNotNull($exam);
        // Exam should only have 4 questions selected from the 10
        $this->assertEquals(4, $exam->questions()->count());
        $this->assertEquals(4, $exam->random_question_count);

        $exam->delete();
    }

    public function test_create_exam_with_manual_selection(): void
    {
        $admin = $this->getAdminUser();

        // 5 questions, 2 selected, 3 unselected
        $pool = [
            ['question_text' => 'Selected Q1', 'option_a' => '1', 'option_b' => '2', 'option_c' => '3', 'option_d' => '4', 'correct_answer' => 'A', 'selected' => true],
            ['question_text' => 'Unselected Q2', 'option_a' => '1', 'option_b' => '2', 'option_c' => '3', 'option_d' => '4', 'correct_answer' => 'B', 'selected' => false],
            ['question_text' => 'Selected Q3', 'option_a' => '1', 'option_b' => '2', 'option_c' => '3', 'option_d' => '4', 'correct_answer' => 'C', 'selected' => true],
            ['question_text' => 'Unselected Q4', 'option_a' => '1', 'option_b' => '2', 'option_c' => '3', 'option_d' => '4', 'correct_answer' => 'D', 'selected' => false],
            ['question_text' => 'Unselected Q5', 'option_a' => '1', 'option_b' => '2', 'option_c' => '3', 'option_d' => '4', 'correct_answer' => 'A', 'selected' => false],
        ];

        $postData = [
            '_token' => csrf_token(),
            'title' => 'Manual Selected Exam ' . uniqid(),
            'branch' => 'police',
            'category' => 'Police Aptitude',
            'exam_type' => 'iq_mcq',
            'duration_minutes' => 30,
            'total_marks' => 20,
            'pass_marks' => 10,
            'access_type' => 'free',
            'status' => 'open',
            'selection_mode' => 'manual',
            'questions_json' => json_encode($pool),
        ];

        $response = $this->actingAs($admin)->post(route('admin.exam_management.store'), $postData);
        $response->assertRedirect();

        $exam = Exam::where('title', $postData['title'])->first();
        $this->assertNotNull($exam);
        // Should only have 2 questions
        $this->assertEquals(2, $exam->questions()->count());
        $this->assertEquals(2, $exam->random_question_count);

        $savedTexts = $exam->questions()->pluck('question_text')->toArray();
        $this->assertContains('Selected Q1', $savedTexts);
        $this->assertContains('Selected Q3', $savedTexts);
        $this->assertNotContains('Unselected Q2', $savedTexts);

        $exam->delete();
    }

    public function test_exam_management_hub_displays_edit_exam_options_and_modal(): void
    {
        $admin = $this->getAdminUser();

        $exam = Exam::create([
            'title' => 'Modal Edit Test Exam ' . uniqid(),
            'branch' => 'navy',
            'slug' => 'modal-edit-test-' . uniqid(),
            'exam_type' => 'iq_mcq',
            'category' => 'Naval Tactics',
            'duration_minutes' => 40,
            'total_marks' => 50,
            'pass_marks' => 25,
            'access_type' => 'paid',
            'status' => 'open',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.exam_management.index'));
        $response->assertStatus(200);

        // Verify edit options on landing page
        $response->assertSee('Edit an Exam');
        $response->assertSee('Edit Exam from Edit Page');
        $response->assertSee('selectExamModal');
        $response->assertSee('quickEditExamSelect');
        $response->assertSee('goToExamEditPage');
        $response->assertSee($exam->title);

        $exam->delete();
    }

    public function test_admin_can_access_edit_page_via_exam_management_route(): void
    {
        $admin = $this->getAdminUser();

        $exam = Exam::create([
            'title' => 'Route Edit Test Exam ' . uniqid(),
            'branch' => 'air_force',
            'slug' => 'route-edit-test-' . uniqid(),
            'exam_type' => 'iq_mcq',
            'category' => 'Aeronautical Reasoning',
            'duration_minutes' => 45,
            'total_marks' => 60,
            'pass_marks' => 30,
            'access_type' => 'paid',
            'status' => 'open',
            'description' => 'Aviation situational assessment.',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.exam_management.edit', $exam->id));
        $response->assertStatus(200);

        $response->assertSee('Edit Assessment Module');
        $response->assertSee('Route Edit Test Exam');
        $response->assertSee('Aeronautical Reasoning');
        $response->assertSee('Engine Configuration & Scoring Rules', false);
        $response->assertSee('Assessment Identity & Access Control', false);

        $exam->delete();
    }

    public function test_exam_management_card_contains_edit_link(): void
    {
        $admin = $this->getAdminUser();

        $exam = Exam::create([
            'title' => 'Card Link Edit Test ' . uniqid(),
            'branch' => 'army',
            'slug' => 'card-link-edit-' . uniqid(),
            'exam_type' => 'iq_mcq',
            'category' => 'Command Strategy',
            'duration_minutes' => 30,
            'total_marks' => 40,
            'pass_marks' => 20,
            'access_type' => 'paid',
            'status' => 'open',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.exam_management.index', ['type' => 'all']));
        $response->assertStatus(200);

        $response->assertSee(route('admin.exam_management.edit', $exam->id));
        $response->assertSee('Edit Exam from Edit Page');

        $exam->delete();
    }
}