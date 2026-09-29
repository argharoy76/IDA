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

class TargetTrackAndClearanceTest extends TestCase
{
    protected function createCadetWithCourse(string $branch, string $courseName): array
    {
        $user = User::create([
            'name' => 'Cadet ' . $branch . ' ' . uniqid(),
            'email' => 'cadet_' . uniqid() . '@ida.com',
            'account_id' => 'CDT-' . strtoupper(uniqid()),
            'password' => Hash::make('password'),
            'role' => 'academic_student',
        ]);

        $student = Student::create([
            'user_id' => $user->id,
            'student_id_code' => 'STU-' . strtoupper(uniqid()),
            'target_wing' => $branch,
            'status' => 'active',
        ]);

        $course = Course::create([
            'title' => $courseName,
            'slug' => 'course-' . uniqid(),
            'category' => $branch,
            'fee' => 1000,
            'admission_status' => 'open',
        ]);

        $student->courses()->attach($course->id, [
            'enrolled_at' => now(),
            'status' => 'active',
        ]);

        return [$user, $student, $course];
    }

    protected function getAdminUser(): User
    {
        $admin = User::where('role', 'super_admin')->first();
        if (!$admin) {
            $admin = User::create([
                'name' => 'Super Admin',
                'email' => 'admin_track_' . uniqid() . '@ida.com',
                'account_id' => 'ADM-' . strtoupper(uniqid()),
                'password' => Hash::make('password'),
                'role' => 'super_admin',
            ]);
        }
        return $admin;
    }

    public function test_preliminary_exam_exclusive_to_enrolled_branch(): void
    {
        [$armyCadetUser, $armyStudent] = $this->createCadetWithCourse('army', 'Bangladesh Army General Course');
        [$navyCadetUser, $navyStudent] = $this->createCadetWithCourse('navy', 'Bangladesh Navy Fast Track Course');

        $armyPrelimExam = Exam::create([
            'title' => 'Army Preliminary Assessment ' . uniqid(),
            'slug' => 'army-prelim-' . uniqid(),
            'branch' => 'army',
            'category' => 'Verbal IQ',
            'target_track' => 'prelim',
            'exam_type' => 'iq_mcq',
            'access_type' => 'paid',
            'is_paid_for_external' => true,
            'status' => 'open',
            'duration_minutes' => 30,
            'total_marks' => 50,
            'pass_marks' => 25,
        ]);

        $navyPrelimExam = Exam::create([
            'title' => 'Navy Preliminary Assessment ' . uniqid(),
            'slug' => 'navy-prelim-' . uniqid(),
            'branch' => 'navy',
            'category' => 'Verbal IQ',
            'target_track' => 'prelim',
            'exam_type' => 'iq_mcq',
            'access_type' => 'paid',
            'is_paid_for_external' => true,
            'status' => 'open',
            'duration_minutes' => 30,
            'total_marks' => 50,
            'pass_marks' => 25,
        ]);

        // Army cadet can access Army Prelim, but NOT Navy Prelim
        $this->assertTrue($armyPrelimExam->canCandidateAccess($armyStudent, $armyCadetUser));
        $this->assertFalse($navyPrelimExam->canCandidateAccess($armyStudent, $armyCadetUser));

        // Navy cadet can access Navy Prelim, but NOT Army Prelim
        $this->assertTrue($navyPrelimExam->canCandidateAccess($navyStudent, $navyCadetUser));
        $this->assertFalse($armyPrelimExam->canCandidateAccess($navyStudent, $navyCadetUser));

        // Attempting unauthorized exam start redirects with error message
        $response = $this->actingAs($armyCadetUser)->get(route('cadet.exams.start', $navyPrelimExam->id));
        $response->assertRedirect(route('cadet.exams.index', ['branch' => 'navy']));
        $response->assertSessionHas('error');
    }

    public function test_cadet_with_any_military_course_can_access_any_issb_exam(): void
    {
        [$armyCadetUser, $armyStudent] = $this->createCadetWithCourse('army', 'Army Core Cadet Program');

        $navyIssbExam = Exam::create([
            'title' => 'Navy ISSB Psychological Test ' . uniqid(),
            'slug' => 'navy-issb-' . uniqid(),
            'branch' => 'navy',
            'category' => 'ISSB Psychological',
            'target_track' => 'issb',
            'exam_type' => 'iq_mcq',
            'access_type' => 'paid',
            'is_paid_for_external' => true,
            'status' => 'open',
            'duration_minutes' => 30,
            'total_marks' => 50,
            'pass_marks' => 25,
        ]);

        $airForceIssbExam = Exam::create([
            'title' => 'Air Force ISSB Situational Test ' . uniqid(),
            'slug' => 'airforce-issb-' . uniqid(),
            'branch' => 'air_force',
            'category' => 'ISSB Situational',
            'target_track' => 'issb',
            'exam_type' => 'iq_mcq',
            'access_type' => 'paid',
            'is_paid_for_external' => true,
            'status' => 'open',
            'duration_minutes' => 30,
            'total_marks' => 50,
            'pass_marks' => 25,
        ]);

        // Even though cadet is only enrolled in Army, they CAN conduct Navy and Air Force ISSB exams!
        $this->assertTrue($navyIssbExam->canCandidateAccess($armyStudent, $armyCadetUser));
        $this->assertTrue($airForceIssbExam->canCandidateAccess($armyStudent, $armyCadetUser));

        // In cadet index under "All Branches", military cadet sees all ISSB exams
        $response = $this->actingAs($armyCadetUser)->get(route('cadet.exams.index'));
        $response->assertStatus(200);
        $response->assertSee($navyIssbExam->title);
        $response->assertSee($airForceIssbExam->title);
    }

    public function test_police_subtracks_access_control(): void
    {
        [$constableCadetUser, $constableStudent] = $this->createCadetWithCourse('police', 'Bangladesh Police Constable Trainee Program');
        [$siCadetUser, $siStudent] = $this->createCadetWithCourse('police', 'Bangladesh Police Sub-Inspector SI Cadet Course');
        [$asiCadetUser, $asiStudent] = $this->createCadetWithCourse('police', 'Bangladesh Police Assistant Sub-Inspector ASI Course');

        $constableExam = Exam::create([
            'title' => 'Police Constable Aptitude Test ' . uniqid(),
            'slug' => 'police-constable-' . uniqid(),
            'branch' => 'police',
            'category' => 'Constable Recruitment',
            'target_track' => 'constable',
            'exam_type' => 'iq_mcq',
            'access_type' => 'paid',
            'is_paid_for_external' => true,
            'status' => 'open',
            'duration_minutes' => 30,
            'total_marks' => 50,
            'pass_marks' => 25,
        ]);

        $siExam = Exam::create([
            'title' => 'Police Sub-Inspector Advanced IQ ' . uniqid(),
            'slug' => 'police-si-' . uniqid(),
            'branch' => 'police',
            'category' => 'SI Recruitment',
            'target_track' => 'si',
            'exam_type' => 'iq_mcq',
            'access_type' => 'paid',
            'is_paid_for_external' => true,
            'status' => 'open',
            'duration_minutes' => 30,
            'total_marks' => 50,
            'pass_marks' => 25,
        ]);

        $asiExam = Exam::create([
            'title' => 'Police Assistant Sub-Inspector Exam ' . uniqid(),
            'slug' => 'police-asi-' . uniqid(),
            'branch' => 'police',
            'category' => 'ASI Recruitment',
            'target_track' => 'asi',
            'exam_type' => 'iq_mcq',
            'access_type' => 'paid',
            'is_paid_for_external' => true,
            'status' => 'open',
            'duration_minutes' => 30,
            'total_marks' => 50,
            'pass_marks' => 25,
        ]);

        // Constable student can access Constable, but NOT SI or ASI
        $this->assertTrue($constableExam->canCandidateAccess($constableStudent, $constableCadetUser));
        $this->assertFalse($siExam->canCandidateAccess($constableStudent, $constableCadetUser));
        $this->assertFalse($asiExam->canCandidateAccess($constableStudent, $constableCadetUser));

        // SI student can access SI, but NOT Constable or ASI
        $this->assertTrue($siExam->canCandidateAccess($siStudent, $siCadetUser));
        $this->assertFalse($constableExam->canCandidateAccess($siStudent, $siCadetUser));
        $this->assertFalse($asiExam->canCandidateAccess($siStudent, $siCadetUser));

        // ASI student can access ASI, but NOT Constable or SI
        $this->assertTrue($asiExam->canCandidateAccess($asiStudent, $asiCadetUser));
        $this->assertFalse($constableExam->canCandidateAccess($asiStudent, $asiCadetUser));
        $this->assertFalse($siExam->canCandidateAccess($asiStudent, $asiCadetUser));
    }

    public function test_admin_can_create_and_edit_exam_with_target_track(): void
    {
        $admin = $this->getAdminUser();

        // 1. Create with target_track
        $postData = [
            '_token' => csrf_token(),
            'title' => 'Special SI Examination ' . uniqid(),
            'branch' => 'police',
            'category' => 'Police SI Training',
            'target_track' => 'si',
            'exam_type' => 'iq_mcq',
            'duration_minutes' => 45,
            'total_marks' => 50,
            'pass_marks' => 25,
            'access_type' => 'paid',
            'status' => 'open',
            'selection_mode' => 'manual',
            'questions_json' => json_encode([
                ['question_text' => 'Sample Police Question 1', 'option_a' => 'A', 'option_b' => 'B', 'option_c' => 'C', 'option_d' => 'D', 'correct_answer' => 'A', 'selected' => true]
            ]),
        ];

        $response = $this->actingAs($admin)->post(route('admin.exam_management.store'), $postData);
        $response->assertRedirect();

        $exam = Exam::where('title', $postData['title'])->first();
        $this->assertNotNull($exam);
        $this->assertEquals('si', $exam->target_track);

        // 2. Edit exam and change target_track to 'asi'
        $updateData = [
            '_token' => csrf_token(),
            'title' => $exam->title . ' Updated',
            'branch' => 'police',
            'category' => 'Police ASI Training',
            'target_track' => 'asi',
            'exam_type' => 'iq_mcq',
            'duration_minutes' => 40,
            'total_marks' => 50,
            'pass_marks' => 20,
            'access_type' => 'paid',
            'status' => 'open',
        ];

        $updateResponse = $this->actingAs($admin)->put(route('admin.exams.update', $exam->id), $updateData);
        $updateResponse->assertRedirect();

        $exam->refresh();
        $this->assertEquals('asi', $exam->target_track);
        $this->assertEquals('Police ASI Training', $exam->category);
    }

    public function test_cadet_management_displays_clearance_badges(): void
    {
        $admin = $this->getAdminUser();
        [$armyCadetUser, $armyStudent] = $this->createCadetWithCourse('army', 'Bangladesh Army Commando Prep');

        $response = $this->actingAs($admin)->get(route('admin.student_accounts.index', ['wing' => 'all']));
        $response->assertStatus(200);

        $showResponse = $this->actingAs($admin)->get(route('admin.student_accounts.show', $armyStudent->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Exam Clearance & Testing Entitlement');
        $showResponse->assertSee('Tri-Services ISSB Clearance Granted');
    }

    public function test_admin_can_update_cadet_to_navy_prelim_only(): void
    {
        $admin = $this->getAdminUser();
        [$cadetUser, $cadetStudent] = $this->createCadetWithCourse('navy', 'Bangladesh Navy Specialized Prep');

        $updateData = [
            '_token' => csrf_token(),
            'name' => $cadetUser->name,
            'custom_id' => $cadetStudent->student_id_code,
            'phone' => '01712345678',
            'email' => $cadetUser->email,
            'gender' => 'male',
            'address' => 'Naval Academy Area, Chattogram',
            'student_type' => 'academic',
            'branch_wing' => 'Navy',
            'category_tracks' => ['prelim'],
        ];

        $response = $this->actingAs($admin)->put(route('admin.student_accounts.update', $cadetStudent->id), $updateData);
        $response->assertRedirect(route('admin.student_accounts.show', $cadetStudent->id));
        $response->assertSessionHas('success');

        $cadetStudent->refresh();
        $this->assertEquals('Navy - Preliminary', $cadetStudent->target_wing);
        $this->assertEquals(['prelim'], $cadetStudent->target_tracks);
        $this->assertTrue($cadetStudent->hasPrelimTrack('navy'));
        $this->assertFalse($cadetStudent->hasPrelimTrack('army'));
    }

    public function test_admin_can_update_cadet_to_navy_both_prelim_and_issb(): void
    {
        $admin = $this->getAdminUser();
        [$cadetUser, $cadetStudent] = $this->createCadetWithCourse('navy', 'Navy Officer Fast Track');

        $updateData = [
            '_token' => csrf_token(),
            'name' => $cadetUser->name,
            'custom_id' => $cadetStudent->student_id_code,
            'phone' => '01812345678',
            'email' => $cadetUser->email,
            'gender' => 'male',
            'address' => 'Naval Base, Chattogram',
            'student_type' => 'academic',
            'branch_wing' => 'Navy',
            'category_tracks' => ['prelim', 'issb'], // Both can be selected!
        ];

        $response = $this->actingAs($admin)->put(route('admin.student_accounts.update', $cadetStudent->id), $updateData);
        $response->assertRedirect(route('admin.student_accounts.show', $cadetStudent->id));

        $cadetStudent->refresh();
        $this->assertEquals('Navy - Prelim & ISSB', $cadetStudent->target_wing);
        $this->assertContains('prelim', $cadetStudent->target_tracks);
        $this->assertContains('issb', $cadetStudent->target_tracks);

        $this->assertTrue($cadetStudent->hasPrelimTrack('navy'));
        $this->assertTrue($cadetStudent->hasIssbTrack());

        // Verify exam access for both Prelim and ISSB
        $navyPrelimExam = Exam::create([
            'title' => 'Navy Prelim Test ' . uniqid(),
            'slug' => 'navy-prelim-' . uniqid(),
            'branch' => 'navy',
            'target_track' => 'prelim',
            'exam_type' => 'iq_mcq',
            'access_type' => 'paid',
            'is_paid_for_external' => true,
            'status' => 'open',
            'duration_minutes' => 30,
            'total_marks' => 50,
            'pass_marks' => 25,
        ]);

        $armyIssbExam = Exam::create([
            'title' => 'Army ISSB Dynamic Exam ' . uniqid(),
            'slug' => 'army-issb-' . uniqid(),
            'branch' => 'army',
            'target_track' => 'issb',
            'exam_type' => 'iq_mcq',
            'access_type' => 'paid',
            'is_paid_for_external' => true,
            'status' => 'open',
            'duration_minutes' => 30,
            'total_marks' => 50,
            'pass_marks' => 25,
        ]);

        // Student can access both the Navy prelim exam and the Army ISSB exam!
        $this->assertTrue($navyPrelimExam->canCandidateAccess($cadetStudent, $cadetUser));
        $this->assertTrue($armyIssbExam->canCandidateAccess($cadetStudent, $cadetUser));
    }

    public function test_admin_can_update_cadet_to_police_si_and_asi(): void
    {
        $admin = $this->getAdminUser();
        [$cadetUser, $cadetStudent] = $this->createCadetWithCourse('police', 'Police SI Cadre Comprehensive');

        $updateData = [
            '_token' => csrf_token(),
            'name' => $cadetUser->name,
            'custom_id' => $cadetStudent->student_id_code,
            'phone' => '01912345678',
            'email' => $cadetUser->email,
            'gender' => 'female',
            'address' => 'Police Lines, Dhaka',
            'student_type' => 'academic',
            'branch_wing' => 'Police',
            'category_tracks' => ['si', 'asi'], // Both SI and ASI selected!
        ];

        $response = $this->actingAs($admin)->put(route('admin.student_accounts.update', $cadetStudent->id), $updateData);
        $response->assertRedirect(route('admin.student_accounts.show', $cadetStudent->id));

        $cadetStudent->refresh();
        $this->assertEquals('Police - SI & ASI', $cadetStudent->target_wing);
        $this->assertTrue($cadetStudent->hasPoliceTrack('si'));
        $this->assertTrue($cadetStudent->hasPoliceTrack('asi'));
        $this->assertFalse($cadetStudent->hasPoliceTrack('constable'));
    }

    public function test_cadet_with_explicit_issb_course_can_access_any_issb_exam(): void
    {
        $user = User::create([
            'name' => 'ISSB Candidate ' . uniqid(),
            'email' => 'candidate_issb_' . uniqid() . '@ida.com',
            'account_id' => 'ISSB-' . strtoupper(uniqid()),
            'password' => Hash::make('password'),
            'role' => 'academic_student',
        ]);

        $student = Student::create([
            'user_id' => $user->id,
            'student_id_code' => 'STU-ISSB-' . strtoupper(uniqid()),
            'target_wing' => 'General',
            'status' => 'active',
        ]);

        // Course explicitly designated for ISSB
        $issbCourse = Course::create([
            'title' => 'ISSB Special Masterclass 2026',
            'slug' => 'issb-masterclass-' . uniqid(),
            'category' => 'issb',
            'fee' => 3500,
            'admission_status' => 'open',
        ]);

        $student->courses()->attach($issbCourse->id, [
            'enrolled_at' => now(),
            'status' => 'active',
        ]);

        $this->assertTrue($student->hasIssbTrack());

        $armyIssbExam = Exam::create([
            'title' => 'Army ISSB PGT & Interview ' . uniqid(),
            'slug' => 'army-issb-pgt-' . uniqid(),
            'branch' => 'army',
            'target_track' => 'issb',
            'exam_type' => 'iq_mcq',
            'access_type' => 'paid',
            'status' => 'open',
            'duration_minutes' => 30,
            'total_marks' => 50,
            'pass_marks' => 25,
        ]);

        $navyIssbExam = Exam::create([
            'title' => 'Navy ISSB Progressive Tasks ' . uniqid(),
            'slug' => 'navy-issb-pgt-' . uniqid(),
            'branch' => 'navy',
            'target_track' => 'issb',
            'exam_type' => 'iq_mcq',
            'access_type' => 'paid',
            'status' => 'open',
            'duration_minutes' => 30,
            'total_marks' => 50,
            'pass_marks' => 25,
        ]);

        // Student with ISSB course can access any ISSB exam across all branches
        $this->assertTrue($armyIssbExam->canCandidateAccess($student, $user));
        $this->assertTrue($navyIssbExam->canCandidateAccess($student, $user));
    }

    public function test_sync_database_tracks_artisan_command_and_web_route(): void
    {
        $admin = $this->getAdminUser();

        // Test artisan command
        $this->artisan('ida:sync-tracks')
            ->expectsOutputToContain('Starting Safe Database Track Synchronization')
            ->assertExitCode(0);

        // Test admin web route
        $response = $this->actingAs($admin)->post(route('admin.system.sync_database_tracks'));
        $response->assertRedirect();
        $response->assertSessionHas('success');
    }
}

