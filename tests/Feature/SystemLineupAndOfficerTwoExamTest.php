<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use App\Models\Exam;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;

class SystemLineupAndOfficerTwoExamTest extends TestCase
{
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $admin = User::where('role', 'super_admin')->first();
        if (!$admin) {
            $admin = User::create([
                'name' => 'Super Admin',
                'email' => 'superadmin_' . uniqid() . '@ida.com',
                'account_id' => 'ADM-' . strtoupper(uniqid()),
                'password' => Hash::make('password123'),
                'role' => 'super_admin',
                'status' => 'active',
                'permissions' => ['can_manage_cms', 'can_manage_exams', 'can_manage_students'],
            ]);
        }
        $this->admin = $admin;
    }

    /**
     * Test 1: Exam Management displays the standardized 4-wing lineup (Navy -> Police -> Army -> Air Force).
     */
    public function test_exam_management_displays_standardized_wing_lineup_order(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.exam_management.index', ['type' => 'paid']));

        $response->assertStatus(200);

        $content = $response->getContent();
        $navyPos = strpos($content, 'Navy (');
        $policePos = strpos($content, 'Police (');
        $armyPos = strpos($content, 'Army (');
        $airForcePos = strpos($content, 'Air Force (');

        $this->assertNotFalse($navyPos, 'Navy tab missing');
        $this->assertNotFalse($policePos, 'Police tab missing');
        $this->assertNotFalse($armyPos, 'Army tab missing');
        $this->assertNotFalse($airForcePos, 'Air Force tab missing');

        $this->assertTrue($navyPos < $policePos, 'Navy must appear before Police');
        $this->assertTrue($policePos < $armyPos, 'Police must appear before Army');
        $this->assertTrue($armyPos < $airForcePos, 'Army must appear before Air Force');
    }

    /**
     * Test 2: Exam Management under a military wing (Navy) displays strictly Officer Two-Exam architecture.
     */
    public function test_exam_management_military_wing_shows_officer_two_exam_tracks_and_hides_police(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.exam_management.index', [
            'type' => 'paid',
            'branch' => 'navy',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Preliminary (Officer 1)');
        $response->assertSee('ISSB Masterclass (Officer 2)');

        // Police tracks must NOT appear in the track stage selector for Navy
        $response->assertDontSee('Constable (');
        $response->assertDontSee('Sub-Inspector SI (');
        $response->assertDontSee('Assistant SI ASI (');
    }

    /**
     * Test 3: Exam Management under Police displays Police rank tracks and hides ISSB.
     */
    public function test_exam_management_police_wing_shows_police_tracks_and_hides_issb(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.exam_management.index', [
            'type' => 'paid',
            'branch' => 'police',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Constable (');
        $response->assertSee('Sub-Inspector SI (');
        $response->assertSee('Assistant SI ASI (');

        // ISSB must NOT appear for Police
        $response->assertDontSee('ISSB Masterclass (Officer 2)');
    }

    /**
     * Test 4: Course Management displays standardized 4-wing lineup (Navy -> Police -> Army -> Air Force).
     */
    public function test_course_management_displays_standardized_wing_lineup_order(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.courses.index'));

        $response->assertStatus(200);

        $content = $response->getContent();
        $navyPos = strpos($content, 'Navy (');
        $policePos = strpos($content, 'Police (');
        $armyPos = strpos($content, 'Army (');
        $airForcePos = strpos($content, 'Air Force (');

        $this->assertNotFalse($navyPos, 'Navy tab missing in courses');
        $this->assertNotFalse($policePos, 'Police tab missing in courses');
        $this->assertNotFalse($armyPos, 'Army tab missing in courses');
        $this->assertNotFalse($airForcePos, 'Air Force tab missing in courses');

        $this->assertTrue($navyPos < $policePos, 'Navy must appear before Police in courses');
        $this->assertTrue($policePos < $armyPos, 'Police must appear before Army in courses');
        $this->assertTrue($armyPos < $airForcePos, 'Army must appear before Air Force in courses');
    }

    /**
     * Test 5: Course Management military wing shows Officer Two-Exam stages (Preliminary & ISSB).
     */
    public function test_course_management_military_wing_filters_officer_two_exam_tracks(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.courses.index', ['branch' => 'navy']));

        $response->assertStatus(200);
        $response->assertSee('Preliminary (Officer 1)');
        $response->assertSee('ISSB Masterclass (Officer 2)');

        // Police courses should not appear in track filter for Navy
        $response->assertDontSee('Constable Courses (');
    }

    /**
     * Test 6: Course Management Police wing shows Police tracks and hides ISSB.
     */
    public function test_course_management_police_wing_filters_police_tracks(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.courses.index', ['branch' => 'police']));

        $response->assertStatus(200);
        $response->assertSee('Constable Courses');
        $response->assertSee('Sub-Inspector SI');
        $response->assertSee('Assistant SI ASI');

        // ISSB must not appear for Police courses
        $response->assertDontSee('ISSB Masterclass (Officer 2)');
    }

    /**
     * Test 7: Admin can create a course with branch and officer track.
     */
    public function test_admin_can_create_course_with_branch_and_officer_track(): void
    {
        $uniqueCode = 'NAV-OFF-' . rand(100, 999);
        $response = $this->actingAs($this->admin)->post(route('admin.courses.store'), [
            'title' => 'Navy Officer Cadet Special Batch ' . uniqid(),
            'course_code' => $uniqueCode,
            'branch' => 'navy',
            'target_track' => 'preliminary',
            'duration' => '4 Months',
            'fee' => 19500,
            'admission_status' => 'open',
            'description' => 'Official naval officer preliminary screening course.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('courses', [
            'course_code' => $uniqueCode,
            'branch' => 'navy',
            'target_track' => 'preliminary',
        ]);
    }

    /**
     * Test 8: Admin can update a course branch and target track.
     */
    public function test_admin_can_update_course_branch_and_track(): void
    {
        $course = Course::create([
            'title' => 'Police SI Fast Track ' . uniqid(),
            'slug' => 'police-si-fast-' . uniqid(),
            'branch' => 'police',
            'target_track' => 'si',
            'category' => 'Police Service',
            'duration' => '3 Months',
            'fee' => 14000,
            'admission_status' => 'open',
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.courses.update', $course->id), [
            'title' => 'Police Assistant SI (ASI) Fast Track Updated',
            'course_code' => 'ASI-99',
            'branch' => 'police',
            'target_track' => 'asi',
            'duration' => '2 Months',
            'fee' => 11000,
            'admission_status' => 'upcoming',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'branch' => 'police',
            'target_track' => 'asi',
            'course_code' => 'ASI-99',
            'duration' => '2 Months',
        ]);
    }

    /**
     * Test 9: Course search finds courses by title or code.
     */
    public function test_course_search_finds_courses_by_title_or_code(): void
    {
        $code = 'AIR-SPEC-' . rand(100, 999);
        $course = Course::create([
            'title' => 'Air Force Pilot Aptitude Unique Course ' . uniqid(),
            'slug' => 'af-pilot-' . uniqid(),
            'course_code' => $code,
            'branch' => 'air_force',
            'target_track' => 'preliminary',
            'duration' => '3 Months',
            'fee' => 16000,
            'admission_status' => 'open',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.courses.index', ['search' => $code]));

        $response->assertStatus(200);
        $response->assertSee($course->title);
    }

    /**
     * Test 10: Exam Management header cleanup and cascading dropdown hierarchy.
     */
    public function test_exam_management_header_cleanup_and_cascading_dropdowns(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.exam_management.index', ['type' => 'paid']));

        $response->assertStatus(200);
        // Header cleanup: Subtitle removed
        $response->assertDontSee('Comprehensive access control, exam creation, condition statuses');
        // Top action buttons removed from header row
        $response->assertDontSee('Sync DB Tracks');
        // Type pills present
        $response->assertSee('Free Exams');
        $response->assertSee('Cadet Exams');
        $response->assertSee('All Exams');
        // Dropdown hierarchy elements present
        $response->assertSee('id="examBranchSelect"', false);
        $response->assertSee('All Branches');
        $response->assertSee('Search exam title...');
        $response->assertSee('Create Exam');
    }

    /**
     * Test 11: Exam Management cascading dropdowns for Navy (Officer vs Sailor, Officer stages).
     */
    public function test_exam_management_navy_cascading_cadre_and_officer_stages(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.exam_management.index', [
            'type' => 'paid',
            'branch' => 'navy',
        ]));

        $response->assertStatus(200);
        $response->assertSee('id="examCadreSelect"', false);
        $response->assertSee('Officer (');
        $response->assertSee('Sailor (');
        $response->assertSee('Preliminary (Officer 1)');
        $response->assertSee('ISSB Masterclass (Officer 2)');
        $response->assertDontSee('Constable (');
        $response->assertDontSee('Sub-Inspector SI (');
    }

    /**
     * Test 12: Exam Management cascading dropdowns for Police (Constable, SI, ASI, and NO ISSB).
     */
    public function test_exam_management_police_cascading_tracks_without_issb(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.exam_management.index', [
            'type' => 'paid',
            'branch' => 'police',
        ]));

        $response->assertStatus(200);
        $response->assertSee('id="examPoliceTrackSelect"', false);
        $response->assertSee('Constable (');
        $response->assertSee('Sub-Inspector SI (');
        $response->assertSee('Assistant SI ASI (');
        $response->assertDontSee('ISSB Masterclass (Officer 2)');
        $response->assertDontSee('id="examCadreSelect"', false);
    }

    /**
     * Test 13: Create Exam page implements the cascading dropdown hierarchy (Branch -> Post -> Exam Type) and clean UI without small font notes.
     */
    public function test_create_exam_displays_cascading_dropdown_hierarchy_and_presets(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.exam_management.create'));

        $response->assertStatus(200);
        $response->assertSee('id="examBranchSelect"', false);
        $response->assertSee('id="cadreSelectContainer"', false);
        $response->assertSee('id="examCadreSelect"', false);
        $response->assertSee('id="examTypeContainer"', false);
        $response->assertSee('id="examTargetTrackSelect"', false);
        $response->assertSee('id="finalTargetTrackInput"', false);
        
        // Post and Exam Type labels
        $response->assertSee('2. Post *');
        $response->assertSee('3. Exam Type *');
        $response->assertDontSee('Officer Examination Stage');

        // Presets include Sailor and Soldier
        $response->assertSee('Navy Sailor');
        $response->assertSee('Army Soldier');
        $response->assertSee('Air Force Airman');
        $response->assertSee('color-scheme: dark !important;', false);

        // Verification of removal of badge and explanatory small text
        $response->assertDontSee('id="trackRuleBadge"', false);
        $response->assertDontSee('Official examination title visible to candidates across all platforms.');
        $response->assertDontSee('Select Officer or Non-Commissioned Post.');
    }

    /**
     * Test 14: Edit Exam page implements the clean cascading dropdown hierarchy without small font notes.
     */
    public function test_edit_exam_displays_cascading_dropdown_hierarchy(): void
    {
        $exam = Exam::create([
            'title' => 'Navy Officer Aptitude Test ' . uniqid(),
            'slug' => 'navy-aptitude-' . uniqid(),
            'branch' => 'navy',
            'category' => 'Verbal IQ',
            'target_track' => 'prelim',
            'exam_type' => 'iq_mcq',
            'duration_minutes' => 45,
            'total_marks' => 50,
            'pass_marks' => 25,
            'status' => 'open',
            'is_paid_for_external' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.exam_management.edit', $exam->id));

        $response->assertStatus(200);
        $response->assertSee('id="examBranchSelect"', false);
        $response->assertSee('id="cadreSelectContainer"', false);
        $response->assertSee('id="examCadreSelect"', false);
        $response->assertSee('id="examTypeContainer"', false);
        $response->assertSee('id="examTargetTrackSelect"', false);
        $response->assertSee('id="finalTargetTrackInput"', false);

        // Post and Exam Type labels
        $response->assertSee('2. Post *');
        $response->assertSee('3. Exam Type *');
        $response->assertDontSee('Officer Examination Stage');
        $response->assertDontSee('id="trackRuleBadge"', false);
        $response->assertDontSee('Official examination title visible to candidates across all platforms.');

        $response->assertSee('color-scheme: dark !important;', false);
    }

    /**
     * Test 15: Exam Management index has soldier stage wrapper and dark color-scheme on selects.
     */
    public function test_exam_management_soldier_stage_wrapper_and_dark_select_styles(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.exam_management.index', [
            'type' => 'paid',
            'branch' => 'navy',
        ]));

        $response->assertStatus(200);
        $response->assertSee('id="examSoldierStageWrapper"', false);
        $response->assertSee('id="examSoldierTrackSelect"', false);
        $response->assertSee('quickEditExamSelect');
        $response->assertSee('color-scheme: dark !important;', false);
    }

    /**
     * Test 16: Navy cadet has "Other ISSB Exams" option under Exam History and sees sister wings ISSB exams.
     */
    public function test_navy_cadet_has_other_issb_option_and_sees_sister_wings_exams(): void
    {
        $navyUser = User::factory()->create(['role' => 'academic_student']);
        $navyCourse = Course::create([
            'title' => 'Navy Officer Cadet Course ' . uniqid(),
            'slug' => 'navy-cadet-' . uniqid(),
            'course_code' => 'NAVY-CADET-' . rand(100, 999),
            'branch' => 'navy',
            'target_track' => 'prelim',
            'fee' => 15000,
            'admission_status' => 'open',
        ]);
        $navyStudent = Student::create([
            'user_id' => $navyUser->id,
            'current_course_id' => $navyCourse->id,
            'target_wing' => 'Navy',
            'student_id_code' => 'NAVY-STU-' . rand(1000, 9999),
        ]);
        $navyStudent->courses()->attach($navyCourse->id);

        $armyIssbExam = Exam::create([
            'title' => 'Army ISSB Special Intelligence Assessment ' . uniqid(),
            'slug' => 'army-issb-' . uniqid(),
            'branch' => 'army',
            'category' => 'ISSB Masterclass',
            'target_track' => 'issb',
            'exam_type' => 'mcq',
            'duration_minutes' => 30,
            'total_marks' => 50,
            'pass_marks' => 25,
            'status' => 'open',
            'is_paid_for_external' => true,
        ]);

        $response = $this->actingAs($navyUser)->get(route('cadet.exams.index'));
        $response->assertStatus(200);
        $response->assertSee('Exam History');
        $response->assertSee('Other ISSB');

        // Visit Army sector (not enrolled in Army): sees Army ISSB exam at top, and available Army courses to buy
        $armyResponse = $this->actingAs($navyUser)->get(route('cadet.exams.index', ['branch' => 'army']));
        $armyResponse->assertStatus(200);
        $armyResponse->assertSee($armyIssbExam->title);
        $armyResponse->assertSee('Ongoing Courses in Bangladesh Army');
        $armyResponse->assertSee('Buy / Enroll');

        // Visit Other ISSB view
        $otherResponse = $this->actingAs($navyUser)->get(route('cadet.exams.index', ['view' => 'other_issb']));
        $otherResponse->assertStatus(200);
        $otherResponse->assertSee('Other Branches ISSB', false);
        $otherResponse->assertSee($armyIssbExam->title);
    }

    /**
     * Test 17: Navy cadet can conduct Army ISSB exam, and Army cadet can conduct Navy ISSB exam.
     */
    public function test_cross_branch_issb_exam_conduct_authorization(): void
    {
        $navyUser = User::factory()->create(['role' => 'academic_student']);
        $navyCourse = Course::create([
            'title' => 'Navy BNA Course ' . uniqid(),
            'slug' => 'navy-bna-' . uniqid(),
            'course_code' => 'NAVY-BNA-' . rand(100, 999),
            'branch' => 'navy',
            'target_track' => 'prelim',
            'fee' => 15000,
            'admission_status' => 'open',
        ]);
        $navyStudent = Student::create([
            'user_id' => $navyUser->id,
            'current_course_id' => $navyCourse->id,
            'target_wing' => 'Navy',
            'student_id_code' => 'NAVY-' . rand(1000, 9999),
        ]);
        $navyStudent->courses()->attach($navyCourse->id);

        $armyIssbExam = Exam::create([
            'title' => 'Bangladesh Army ISSB Test ' . uniqid(),
            'slug' => 'army-issb-test-' . uniqid(),
            'branch' => 'army',
            'category' => 'ISSB Masterclass',
            'target_track' => 'issb',
            'exam_type' => 'mcq',
            'duration_minutes' => 30,
            'total_marks' => 50,
            'pass_marks' => 25,
            'status' => 'open',
            'is_paid_for_external' => true,
        ]);

        $this->assertTrue($navyStudent->hasIssbPrivilege());
        $this->assertTrue($armyIssbExam->canCandidateAccess($navyStudent, $navyUser));

        // Navy student starts Army ISSB exam
        $response = $this->actingAs($navyUser)->get(route('cadet.exams.start', $armyIssbExam->id));
        $response->assertStatus(200);
    }

    /**
     * Test 18: Police cadet does not have Other ISSB option and is denied access to military ISSB exams.
     */
    public function test_police_cadet_has_no_issb_access(): void
    {
        $policeUser = User::factory()->create(['role' => 'academic_student']);
        $policeCourse = Course::create([
            'title' => 'Police SI Training Course ' . uniqid(),
            'slug' => 'police-si-' . uniqid(),
            'course_code' => 'POLICE-SI-' . rand(100, 999),
            'branch' => 'police',
            'target_track' => 'si',
            'fee' => 10000,
            'admission_status' => 'open',
        ]);
        $policeStudent = Student::create([
            'user_id' => $policeUser->id,
            'current_course_id' => $policeCourse->id,
            'target_wing' => 'Police',
            'student_id_code' => 'POLICE-' . rand(1000, 9999),
        ]);
        $policeStudent->courses()->attach($policeCourse->id);

        $armyIssbExam = Exam::create([
            'title' => 'Army ISSB Confidential ' . uniqid(),
            'slug' => 'army-issb-conf-' . uniqid(),
            'branch' => 'army',
            'category' => 'ISSB Masterclass',
            'target_track' => 'issb',
            'exam_type' => 'mcq',
            'duration_minutes' => 30,
            'total_marks' => 50,
            'pass_marks' => 25,
            'status' => 'open',
            'is_paid_for_external' => true,
        ]);

        $this->assertFalse($policeStudent->hasIssbPrivilege());
        $this->assertFalse($armyIssbExam->canCandidateAccess($policeStudent, $policeUser));

        // Police portal should not show Other ISSB Exams option
        $response = $this->actingAs($policeUser)->get(route('cadet.exams.index'));
        $response->assertStatus(200);
        $response->assertDontSee('btnOtherIssbOption');

        // Trying to start Army ISSB exam must be blocked and redirected
        $startResponse = $this->actingAs($policeUser)->get(route('cadet.exams.start', $armyIssbExam->id));
        $startResponse->assertRedirect(route('cadet.exams.index', ['branch' => 'army']));
        $startResponse->assertSessionHas('error');
    }

    /**
     * Test 19: When admin unchecks ISSB Masterclass (revoking ISSB permission), cadet strictly loses ISSB access.
     */
    public function test_revoked_issb_permission_strictly_denies_issb_access_and_hides_exams(): void
    {
        $navyUser = User::factory()->create(['role' => 'academic_student']);
        $navyCourse = Course::create([
            'title' => 'Navy Officer Cadet Course ' . uniqid(),
            'slug' => 'navy-officer-' . uniqid(),
            'course_code' => 'NAVY-OFF-' . rand(100, 999),
            'branch' => 'navy',
            'target_track' => 'prelim',
            'fee' => 15000,
            'admission_status' => 'open',
        ]);

        // Explicitly set target_tracks to only ['prelim'] (Admin unchecked ISSB Masterclass)
        $navyStudent = Student::create([
            'user_id' => $navyUser->id,
            'current_course_id' => $navyCourse->id,
            'target_wing' => 'Navy - Preliminary',
            'target_tracks' => ['prelim'],
            'student_id_code' => 'NAVY-NOPRIV-' . rand(1000, 9999),
        ]);
        $navyStudent->courses()->attach($navyCourse->id);

        $armyCourse = Course::create([
            'title' => 'Bangladesh Army Officer Program ' . uniqid(),
            'slug' => 'army-officer-' . uniqid(),
            'course_code' => 'ARMY-OFF-' . rand(100, 999),
            'branch' => 'army',
            'fee' => 12000,
            'admission_status' => 'open',
        ]);

        $armyIssbExam = Exam::create([
            'title' => 'Army ISSB Special Intelligence Assessment ' . uniqid(),
            'slug' => 'army-issb-spec-' . uniqid(),
            'branch' => 'army',
            'category' => 'ISSB Masterclass',
            'target_track' => 'issb',
            'exam_type' => 'mcq',
            'duration_minutes' => 30,
            'total_marks' => 50,
            'pass_marks' => 25,
            'status' => 'open',
            'is_paid_for_external' => true,
        ]);

        // Authorization checks
        $this->assertFalse($navyStudent->hasIssbPrivilege(), 'Cadet with explicit prelim-only track must not have ISSB privilege');
        $this->assertFalse($navyStudent->hasIssbTrack(), 'Cadet with explicit prelim-only track must not have ISSB track');
        $this->assertTrue($navyStudent->hasPrelimTrack('navy'), 'Cadet must have prelim track for their enrolled branch');
        $this->assertFalse($armyIssbExam->canCandidateAccess($navyStudent, $navyUser), 'Cadet without ISSB privilege must not access Army ISSB exam');

        // Cadet portal index: Other ISSB tab should NOT appear
        $response = $this->actingAs($navyUser)->get(route('cadet.exams.index'));
        $response->assertStatus(200);
        $response->assertDontSee('Other ISSB');

        // Visiting Army sector: cadet is not enrolled in Army and has NO ISSB privilege.
        // Therefore, Army ISSB exam must NOT appear, and available courses to buy must appear.
        $armySectorResponse = $this->actingAs($navyUser)->get(route('cadet.exams.index', ['branch' => 'army']));
        $armySectorResponse->assertStatus(200);
        $armySectorResponse->assertDontSee($armyIssbExam->title);
        $armySectorResponse->assertSee('Ongoing Courses in Bangladesh Army');
        $armySectorResponse->assertSee('Buy / Enroll');

        // Attempting to directly start the Army ISSB exam should be blocked and redirected
        $startResponse = $this->actingAs($navyUser)->get(route('cadet.exams.start', $armyIssbExam->id));
        $startResponse->assertRedirect(route('cadet.exams.index', ['branch' => 'army']));
        $startResponse->assertSessionHas('error');
    }
}


