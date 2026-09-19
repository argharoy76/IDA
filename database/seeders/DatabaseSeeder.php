<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Instructor;
use App\Models\Course;
use App\Models\Batch;
use App\Models\Student;
use App\Models\BatchStudent;
use App\Models\Routine;
use App\Models\Attendance;
use App\Models\FeeType;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\FinanceCategory;
use App\Models\FinancialTransaction;
use App\Models\StudentDossier;
use App\Models\StudentStrengthWeakness;
use App\Models\InstructorObservation;
use App\Models\PerformanceAssessment;
use App\Models\ImprovementPlan;
use App\Models\PerformanceTimeline;
use App\Models\Exam;
use App\Models\Question;
use App\Models\WatWord;
use App\Models\ExamAttempt;
use App\Models\ExamAttemptAnswer;
use App\Models\CmsSetting;
use App\Models\CmsNotice;
use App\Models\CmsGalleryItem;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Production Safety Shield: Never overwrite an existing production database
        if (app()->environment('production') && User::where('role', 'super_admin')->exists()) {
            $this->command?->warn('Production database is already initialized with an active super administrator. Seeding skipped to preserve live data.');
            return;
        }

        $password = Hash::make('password');

        // ==========================================
        // 1. CORE USERS
        // ==========================================
        $superAdmin = User::create([
            'name' => 'ArghaRoy',
            'email' => 'admin@ida.com',
            'account_id' => 'ArghaRoy',
            'password' => Hash::make('ArghaArghaGTA6'),
            'role' => 'super_admin',
            'phone' => '+880 1711-001122',
            'status' => 'active',
        ]);

        $admin = User::create([
            'name' => 'Major (Retd.) Farhan Ahmed',
            'email' => 'ops@ida.com',
            'password' => $password,
            'role' => 'admin',
            'phone' => '+880 1711-334455',
            'status' => 'active',
        ]);

        $financeMgr = User::create([
            'name' => 'Rafiqul Islam, ACMA',
            'email' => 'finance@ida.com',
            'password' => $password,
            'role' => 'finance_manager',
            'phone' => '+880 1711-667788',
            'status' => 'active',
        ]);

        $instUser1 = User::create([
            'name' => 'Major (Retd.) Tariqul Islam',
            'email' => 'instructor@ida.com',
            'password' => $password,
            'role' => 'instructor',
            'phone' => '+880 1712-445566',
            'status' => 'active',
        ]);

        $instUser2 = User::create([
            'name' => 'Captain Sabrina Sultana (Retd.)',
            'email' => 'tariq@ida.com',
            'password' => $password,
            'role' => 'instructor',
            'phone' => '+880 1712-778899',
            'status' => 'active',
        ]);

        $instUser3 = User::create([
            'name' => 'Lt. Commander (Retd.) Enamul Haque',
            'email' => 'viva@ida.com',
            'password' => $password,
            'role' => 'instructor',
            'phone' => '+880 1712-112233',
            'status' => 'active',
        ]);

        // Academic Cadets
        $studentUser1 = User::create([
            'name' => 'Cadet Royan Alvi',
            'email' => 'cadet@ida.com',
            'password' => $password,
            'role' => 'academic_student',
            'phone' => '+880 1819-123456',
            'status' => 'active',
        ]);

        $studentUser2 = User::create([
            'name' => 'Cadet Tanvir Hasan',
            'email' => 'cadet2@ida.com',
            'password' => $password,
            'role' => 'academic_student',
            'phone' => '+880 1819-234567',
            'status' => 'active',
        ]);

        $studentUser3 = User::create([
            'name' => 'Cadet Anika Rahman',
            'email' => 'cadet3@ida.com',
            'password' => $password,
            'role' => 'academic_student',
            'phone' => '+880 1819-345678',
            'status' => 'active',
        ]);

        // External Candidate
        $externalUser = User::create([
            'name' => 'Candidate Mehedi Hasan',
            'email' => 'external@ida.com',
            'password' => $password,
            'role' => 'external_student',
            'phone' => '+880 1912-889900',
            'status' => 'active',
        ]);

        // ==========================================
        // 2. INSTRUCTORS PROFILE
        // ==========================================
        $instructor1 = Instructor::create([
            'user_id' => $instUser1->id,
            'instructor_code' => 'INST-IDA-01',
            'designation' => 'Senior Military Instructor & GTO Specialist',
            'specialization' => 'Ground Tasks (PGT/HGT/CT), Group Dynamics & OLQ Assessment',
            'phone' => '+880 1712-445566',
            'bio' => 'Served 18+ years in Bangladesh Army (Armoured Corps). Ex-GTO assessor with deep expertise in outdoor obstacles and leadership identification.',
            'status' => 'active',
        ]);

        $instructor2 = Instructor::create([
            'user_id' => $instUser2->id,
            'instructor_code' => 'INST-IDA-02',
            'designation' => 'Lead Psychologist & Screening Evaluator',
            'specialization' => 'PPDT, TAT, WAT, SRT & Psychological Profile Analysis',
            'phone' => '+880 1712-778899',
            'bio' => 'M.Phil in Military Psychology. 12+ years preparing candidates for ISSB screening and psychological projective assessments.',
            'status' => 'active',
        ]);

        $instructor3 = Instructor::create([
            'user_id' => $instUser3->id,
            'instructor_code' => 'INST-IDA-03',
            'designation' => 'Senior Naval Instructor & Viva Coach',
            'specialization' => 'Current Affairs, Defence Knowledge, Extempore Speech & Mock Viva',
            'phone' => '+880 1712-112233',
            'bio' => 'Retired Naval Officer (Executive Branch). Master interviewer focused on cadet personality calibration, posture, and fluent expression.',
            'status' => 'active',
        ]);

        // ==========================================
        // 3. COURSES
        // ==========================================
        $course1 = Course::create([
            'title' => 'BMA Long Course Regular Preparation',
            'slug' => 'bma-long-course',
            'description' => 'Comprehensive 4-month military preparatory program for candidates aspiring to join Bangladesh Army through the prestigious BMA Long Course.',
            'duration' => '4 Months',
            'eligibility' => 'HSC / Equivalent with minimum GPA 4.00; Age 17-21 Years',
            'fee' => 18500.00,
            'category' => 'Army',
            'features' => ['Verbal & Non-Verbal IQ Drills', 'Ground Obstacle Simulation', 'PPDT & TAT Workshop', 'Mock Deputy President Interview'],
            'syllabus' => [
                'Stage 1: Preliminary Medical & IQ Screenings',
                'Stage 2: Written Examination (Eng, Math, Gen Sci, GK)',
                'Stage 3: 4-Day Complete ISSB Simulation (Psychologist, GTO, DP)',
                'Stage 4: Officer-Like Qualities (OLQ) Fine Tuning'
            ],
            'admission_status' => 'open',
            'schedule_info' => 'Morning & Evening Shifts available (Sun, Tue, Thu)',
            'image' => 'bma-course.jpg',
            'is_featured' => true,
        ]);

        $course2 = Course::create([
            'title' => 'Navy Officer Cadet Executive & Engineering Prep',
            'slug' => 'navy-officer-cadet',
            'description' => 'Dedicated grooming program tailored for Bangladesh Navy selection covering maritime studies, technical tests, and naval board interviews.',
            'duration' => '3.5 Months',
            'eligibility' => 'HSC with Physics & Mathematics; Minimum GPA 4.00',
            'fee' => 17000.00,
            'category' => 'Navy',
            'features' => ['Naval Aptitude & Analytical Tests', 'Leadership In Crisis Drills', 'Endurance & Stamina Tracking', 'ISSB Simulation'],
            'syllabus' => [
                'Preliminary Naval Intelligence Test',
                'Technical & Scientific Aptitude',
                'Group Planning & Marine Scenarios',
                'Final Board Interview Preparation'
            ],
            'admission_status' => 'open',
            'schedule_info' => 'Afternoon Shift (Mon, Wed, Sat)',
            'image' => 'navy-course.jpg',
            'is_featured' => true,
        ]);

        $course3 = Course::create([
            'title' => 'BAFA Flight Cadet & General Service Preparation',
            'slug' => 'bafa-flight-cadet',
            'description' => 'Specialized aviation aptitude, spatial orientation, and instrument comprehension training for aspiring Bangladesh Air Force pilots and officers.',
            'duration' => '3 Months',
            'eligibility' => 'HSC Science (Physics & Math compulsory) GPA 4.50+',
            'fee' => 17500.00,
            'category' => 'Airforce',
            'features' => ['Pilot Aptitude & Spatial Orientation', 'WAT & SRT Speed Drills', 'Physical Agility & Aerobic Capacity', 'ISSB Psychological Readiness'],
            'syllabus' => [
                'Aviation General Intelligence',
                'Spatial Reasoning & Instrument Dial Reading',
                'Group Discussion & Lecturette',
                'Medical Fitness Guidelines'
            ],
            'admission_status' => 'open',
            'schedule_info' => 'Morning Shift (Sun, Tue, Thu)',
            'image' => 'bafa-course.jpg',
            'is_featured' => true,
        ]);

        $course4 = Course::create([
            'title' => 'ISSB Comprehensive 4-Day Simulation Masterclass',
            'slug' => 'issb-simulation-masterclass',
            'description' => 'Intense 45-day residential/day immersion recreating the exact 4-day testing protocol of Inter Services Selection Board.',
            'duration' => '45 Days',
            'eligibility' => 'Candidates with ISSB Call Letters or Preliminary Passed',
            'fee' => 14000.00,
            'category' => 'ISSB Special',
            'features' => ['Full Scale Ground Task Obstacles', 'Live WAT with 15-second timer', '1-on-1 DP Interview with Feedback', 'Psychological Profile Report'],
            'syllabus' => [
                'Day 1: Intelligence Test & PPDT',
                'Day 2: Group Discussion, PGT, HGT, Half Group Task',
                'Day 3: Command Task, Individual Obstacles & Lecturette',
                'Day 4: Conference & Final Recommendation Brief'
            ],
            'admission_status' => 'open',
            'schedule_info' => 'Intensive Daily Sessions',
            'image' => 'issb-course.jpg',
            'is_featured' => true,
        ]);

        $course5 = Course::create([
            'title' => 'Bangladesh Police Sub-Inspector & ASP Cadet Preparation',
            'slug' => 'police-si-asp-cadet-prep',
            'description' => 'Official academy preparatory program for Bangladesh Police Sub-Inspector (SI), Sergeant & ASP competitive recruitment examinations.',
            'duration' => '3 Months',
            'eligibility' => 'Graduation / HSC with standard police physical fitness standards',
            'fee' => 15000.00,
            'category' => 'Police',
            'features' => ['Physical Agility & Endurance Coaching', 'Legal Knowledge & General Studies', 'Psychological & Situation Reaction Drills', 'Mock Viva Voce with Retired Officers'],
            'syllabus' => [
                'Paper 1: English & Bangla Composition',
                'Paper 2: General Knowledge & Current Affairs',
                'Paper 3: Mental Ability & Mathematical Reasoning',
                'Stage 4: Police Medical & Viva Voce'
            ],
            'admission_status' => 'open',
            'schedule_info' => 'Morning & Evening batches available',
            'image' => 'police-course.jpg',
            'is_featured' => true,
        ]);

        // ==========================================
        // 4. BATCHES
        // ==========================================
        $batch1 = Batch::create([
            'course_id' => $course1->id,
            'batch_name' => 'BMA 94th Long Course Batch Alpha',
            'batch_code' => 'IDA-BMA-94A',
            'start_date' => Carbon::now()->subMonths(1),
            'end_date' => Carbon::now()->addMonths(3),
            'primary_instructor_id' => $instructor1->id,
            'max_students' => 35,
            'schedule_summary' => 'Sun, Tue, Thu | 09:00 AM - 12:30 PM',
            'status' => 'active',
            'notes' => 'Priority batch for upcoming BMA preliminary and written board exams.',
        ]);

        $batch2 = Batch::create([
            'course_id' => $course2->id,
            'batch_name' => 'BNA 2026-B Officer Cadet Batch Bravo',
            'batch_code' => 'IDA-BNA-26B',
            'start_date' => Carbon::now()->subWeeks(3),
            'end_date' => Carbon::now()->addMonths(3),
            'primary_instructor_id' => $instructor3->id,
            'max_students' => 30,
            'schedule_summary' => 'Mon, Wed, Sat | 03:00 PM - 06:30 PM',
            'status' => 'active',
            'notes' => 'Cadets enrolled for Navy Executive & Logistics wings.',
        ]);

        $batch3 = Batch::create([
            'course_id' => $course3->id,
            'batch_name' => 'BAFA 84 Flight Batch Charlie',
            'batch_code' => 'IDA-BAFA-84C',
            'start_date' => Carbon::now()->subWeeks(1),
            'end_date' => Carbon::now()->addMonths(3),
            'primary_instructor_id' => $instructor2->id,
            'max_students' => 25,
            'schedule_summary' => 'Sun, Tue, Thu | 02:00 PM - 05:30 PM',
            'status' => 'active',
            'notes' => 'Aviation and pilot aptitude focus.',
        ]);

        // ==========================================
        // 5. STUDENTS
        // ==========================================
        $student1 = Student::create([
            'user_id' => $studentUser1->id,
            'student_id_code' => 'IDA-2026-001',
            'roll_number' => '01',
            'student_type' => 'academic',
            'father_name' => 'Md. Shamsul Alvi',
            'mother_name' => 'Rokeya Begum',
            'dob' => '2006-05-14',
            'gender' => 'male',
            'blood_group' => 'B+',
            'address' => 'House 42, Road 7, Boyra Residential Area, Khulna',
            'emergency_contact' => '+880 1711-998877',
            'target_wing' => 'Army',
            'current_course_id' => $course1->id,
            'current_batch_id' => $batch1->id,
            'admission_date' => Carbon::now()->subMonths(1),
            'status' => 'active',
            'documents' => ['hsc_certificate' => 'verified', 'nid_copy' => 'verified'],
        ]);

        $student2 = Student::create([
            'user_id' => $studentUser2->id,
            'student_id_code' => 'IDA-2026-002',
            'roll_number' => '02',
            'student_type' => 'academic',
            'father_name' => 'Anwarul Hasan',
            'mother_name' => 'Ferdousi Hasan',
            'dob' => '2006-08-20',
            'gender' => 'male',
            'blood_group' => 'O+',
            'address' => 'GPO Road, Sonadanga, Khulna',
            'emergency_contact' => '+880 1712-443322',
            'target_wing' => 'Navy',
            'current_course_id' => $course2->id,
            'current_batch_id' => $batch2->id,
            'admission_date' => Carbon::now()->subWeeks(3),
            'status' => 'active',
            'documents' => ['hsc_certificate' => 'verified'],
        ]);

        $student3 = Student::create([
            'user_id' => $studentUser3->id,
            'student_id_code' => 'IDA-2026-003',
            'roll_number' => '03',
            'student_type' => 'academic',
            'father_name' => 'Mustafizur Rahman',
            'mother_name' => 'Tahmina Rahman',
            'dob' => '2007-01-10',
            'gender' => 'female',
            'blood_group' => 'A+',
            'address' => 'Khalishpur Housing Estate, Khulna',
            'emergency_contact' => '+880 1713-556677',
            'target_wing' => 'Airforce',
            'current_course_id' => $course3->id,
            'current_batch_id' => $batch3->id,
            'admission_date' => Carbon::now()->subWeeks(1),
            'status' => 'active',
        ]);

        // External Student Entry
        $extStudent = Student::create([
            'user_id' => $externalUser->id,
            'student_id_code' => 'EXT-2026-101',
            'roll_number' => 'EXT-01',
            'student_type' => 'external',
            'father_name' => 'Abdul Jabbar',
            'gender' => 'male',
            'blood_group' => 'AB+',
            'address' => 'Daulatpur, Khulna',
            'target_wing' => 'Army',
            'admission_date' => Carbon::now()->subDays(5),
            'status' => 'active',
        ]);

        // Batch Student History
        BatchStudent::create([
            'student_id' => $student1->id,
            'batch_id' => $batch1->id,
            'course_id' => $course1->id,
            'joined_at' => Carbon::now()->subMonths(1),
            'status' => 'active',
        ]);

        BatchStudent::create([
            'student_id' => $student2->id,
            'batch_id' => $batch2->id,
            'course_id' => $course2->id,
            'joined_at' => Carbon::now()->subWeeks(3),
            'status' => 'active',
        ]);

        // ==========================================
        // 6. CLASS ROUTINE & SCHEDULE
        // ==========================================
        $today = Carbon::today();
        
        $routines = [
            // Today's classes
            [
                'course_id' => $course1->id,
                'batch_id' => $batch1->id,
                'instructor_id' => $instructor1->id,
                'subject' => 'Verbal Intelligence (IQ)',
                'topic' => 'Number Series & Directional Analogies',
                'room' => 'Hall Alpha',
                'class_date' => $today,
                'day_of_week' => $today->format('l'),
                'start_time' => '09:30:00',
                'end_time' => '11:00:00',
                'class_type' => 'theory',
                'status' => 'completed',
                'notes' => 'Bring rough pad and ISSB IQ handbook.',
            ],
            [
                'course_id' => $course1->id,
                'batch_id' => $batch1->id,
                'instructor_id' => $instructor1->id,
                'subject' => 'Group Obstacle Planning',
                'topic' => 'Progressive Group Task (PGT) Rules & Bridging',
                'room' => 'Ground Task Field Bravo',
                'class_date' => $today,
                'day_of_week' => $today->format('l'),
                'start_time' => '11:15:00',
                'end_time' => '12:45:00',
                'class_type' => 'practical',
                'status' => 'scheduled',
                'notes' => 'PT dress compulsory for all cadets.',
            ],
            [
                'course_id' => $course2->id,
                'batch_id' => $batch2->id,
                'instructor_id' => $instructor3->id,
                'subject' => 'Current Affairs & Geopolitics',
                'topic' => 'Bay of Bengal Strategic Importance & Blue Economy',
                'room' => 'Hall Charlie',
                'class_date' => $today,
                'day_of_week' => $today->format('l'),
                'start_time' => '15:00:00',
                'end_time' => '16:30:00',
                'class_type' => 'theory',
                'status' => 'scheduled',
                'notes' => 'Extempore speech presentations for each cadet.',
            ],
            // Tomorrow's classes
            [
                'course_id' => $course1->id,
                'batch_id' => $batch1->id,
                'instructor_id' => $instructor2->id,
                'subject' => 'Psychological Assessment Drill',
                'topic' => 'Word Association Test (WAT) 15-Sec Flash Reaction',
                'room' => 'Psychology Lab',
                'class_date' => $today->copy()->addDay(),
                'day_of_week' => $today->copy()->addDay()->format('l'),
                'start_time' => '09:30:00',
                'end_time' => '11:00:00',
                'class_type' => 'practical',
                'status' => 'scheduled',
                'notes' => 'Instant spontaneous writing without pausing.',
            ],
        ];

        foreach ($routines as $rData) {
            $routineObj = Routine::create($rData);

            // Seed attendance for the completed class
            if ($rData['status'] === 'completed') {
                Attendance::create([
                    'routine_id' => $routineObj->id,
                    'student_id' => $student1->id,
                    'batch_id' => $batch1->id,
                    'date' => $today,
                    'status' => 'present',
                    'remarks' => 'Punctual, active in discussion',
                    'marked_by' => $instUser1->id,
                ]);
            }
        }

        // Additional Historical Attendance for Cadet 1 and 2
        for ($i = 1; $i <= 10; $i++) {
            $pastDate = Carbon::today()->subDays($i * 2);
            Attendance::create([
                'routine_id' => 1,
                'student_id' => $student1->id,
                'batch_id' => $batch1->id,
                'date' => $pastDate,
                'status' => ($i === 4) ? 'late' : 'present',
                'remarks' => 'Routine attendance',
                'marked_by' => $instUser1->id,
            ]);

            // Cadet 2 has several absences to demonstrate Early Warning system
            Attendance::create([
                'routine_id' => 1,
                'student_id' => $student2->id,
                'batch_id' => $batch2->id,
                'date' => $pastDate,
                'status' => in_array($i, [2, 3, 5, 8]) ? 'absent' : 'present',
                'remarks' => in_array($i, [2, 3, 5, 8]) ? 'Absent without notice' : 'Present',
                'marked_by' => $instUser1->id,
            ]);
        }

        // ==========================================
        // 7. FEES, INVOICES & FINANCIAL LEDGER
        // ==========================================
        $feeAdmission = FeeType::create([
            'name' => 'Admission & Registration Fee',
            'code' => 'admission',
            'default_amount' => 5000.00,
            'description' => 'Initial cadet admission processing, ID card, and uniform accessories.',
        ]);

        $feeMonthly = FeeType::create([
            'name' => 'Course Monthly Tuition Fee',
            'code' => 'monthly',
            'default_amount' => 4500.00,
            'description' => 'Academic tuition, library access, and ground obstacle training fee.',
        ]);

        $feeExam = FeeType::create([
            'name' => 'Online Assessment & Mock Test Fee',
            'code' => 'exam',
            'default_amount' => 500.00,
            'description' => 'Full simulated IQ and psychological evaluation fee.',
        ]);

        // Finance Categories
        $catAdmission = FinanceCategory::create(['name' => 'Student Admission Fees', 'type' => 'inflow', 'description' => 'New student enrollments']);
        $catTuition = FinanceCategory::create(['name' => 'Monthly Tuition Collection', 'type' => 'inflow', 'description' => 'Ongoing course tuition']);
        $catExam = FinanceCategory::create(['name' => 'Online Assessment Fees', 'type' => 'inflow', 'description' => 'Internal & external online tests']);
        $catRent = FinanceCategory::create(['name' => 'Campus Facility & Ground Rent', 'type' => 'outflow', 'description' => 'Boyra campus lease']);
        $catSalary = FinanceCategory::create(['name' => 'Instructor Honorarium & Staff Salaries', 'type' => 'outflow', 'description' => 'Faculty monthly remuneration']);
        $catEquipment = FinanceCategory::create(['name' => 'Training Obstacles & Classroom Supplies', 'type' => 'outflow', 'description' => 'Ropes, planks, barrels, whiteboards']);
        $catUtility = FinanceCategory::create(['name' => 'Electricity, Fiber Internet & Utilities', 'type' => 'outflow', 'description' => 'Monthly operational utilities']);

        // Invoices for Cadet 1
        $inv1 = Invoice::create([
            'invoice_number' => 'INV-2026-0001',
            'student_id' => $student1->id,
            'fee_type_id' => $feeAdmission->id,
            'title' => 'Admission Fee - BMA 94th Long Course',
            'gross_amount' => 5000.00,
            'discount_amount' => 500.00, // Cadet concession
            'waiver_amount' => 0.00,
            'net_amount' => 4500.00,
            'paid_amount' => 4500.00,
            'due_amount' => 0.00,
            'due_date' => Carbon::now()->subWeeks(3),
            'status' => 'paid',
            'notes' => 'Early bird concession applied.',
        ]);

        $inv2 = Invoice::create([
            'invoice_number' => 'INV-2026-0002',
            'student_id' => $student1->id,
            'fee_type_id' => $feeMonthly->id,
            'title' => 'Tuition Fee - Month 1',
            'gross_amount' => 4500.00,
            'discount_amount' => 0.00,
            'waiver_amount' => 0.00,
            'net_amount' => 4500.00,
            'paid_amount' => 4500.00,
            'due_amount' => 0.00,
            'due_date' => Carbon::now()->subWeeks(1),
            'status' => 'paid',
        ]);

        $inv3 = Invoice::create([
            'invoice_number' => 'INV-2026-0003',
            'student_id' => $student1->id,
            'fee_type_id' => $feeMonthly->id,
            'title' => 'Tuition Fee - Month 2',
            'gross_amount' => 4500.00,
            'discount_amount' => 0.00,
            'waiver_amount' => 0.00,
            'net_amount' => 4500.00,
            'paid_amount' => 0.00,
            'due_amount' => 4500.00,
            'due_date' => Carbon::now()->addDays(5),
            'status' => 'pending',
            'notes' => 'Current running billing cycle.',
        ]);

        // Invoice for Cadet 2 (Overdue)
        $inv4 = Invoice::create([
            'invoice_number' => 'INV-2026-0004',
            'student_id' => $student2->id,
            'fee_type_id' => $feeAdmission->id,
            'title' => 'Admission Fee - BNA 26B',
            'gross_amount' => 5000.00,
            'discount_amount' => 0.00,
            'waiver_amount' => 0.00,
            'net_amount' => 5000.00,
            'paid_amount' => 2500.00,
            'due_amount' => 2500.00,
            'due_date' => Carbon::now()->subDays(10),
            'status' => 'partially_paid',
            'notes' => 'First installment received.',
        ]);

        // Verified Payments & Automatic Ledger entries
        $pay1 = Payment::create([
            'payment_number' => 'PAY-2026-0001',
            'invoice_id' => $inv1->id,
            'student_id' => $student1->id,
            'user_id' => $studentUser1->id,
            'amount' => 4500.00,
            'payment_method' => 'bkash',
            'transaction_reference' => 'BK987X21MN',
            'payment_proof_file' => 'bkash_receipt_001.png',
            'payment_date' => Carbon::now()->subWeeks(3),
            'verification_status' => 'approved',
            'verified_by' => $financeMgr->id,
            'verified_at' => Carbon::now()->subWeeks(3)->addHours(2),
            'admin_notes' => 'Verified with bKash Merchant TrxID.',
        ]);

        FinancialTransaction::create([
            'transaction_number' => 'TXN-2026-0001',
            'type' => 'inflow',
            'category_id' => $catAdmission->id,
            'amount' => 4500.00,
            'transaction_date' => Carbon::now()->subWeeks(3),
            'source_payee' => 'Cadet Royan Alvi (IDA-2026-001)',
            'payment_method' => 'bkash',
            'reference_no' => 'BK987X21MN',
            'description' => 'Admission Fee Payment (INV-2026-0001)',
            'payment_id' => $pay1->id,
            'recorded_by' => $financeMgr->id,
        ]);

        $pay2 = Payment::create([
            'payment_number' => 'PAY-2026-0002',
            'invoice_id' => $inv2->id,
            'student_id' => $student1->id,
            'user_id' => $studentUser1->id,
            'amount' => 4500.00,
            'payment_method' => 'bank_transfer',
            'transaction_reference' => 'BRAC-TX-88301',
            'payment_proof_file' => 'bank_slip_002.png',
            'payment_date' => Carbon::now()->subWeeks(1),
            'verification_status' => 'approved',
            'verified_by' => $financeMgr->id,
            'verified_at' => Carbon::now()->subWeeks(1)->addHours(1),
            'admin_notes' => 'Bank credit verified.',
        ]);

        FinancialTransaction::create([
            'transaction_number' => 'TXN-2026-0002',
            'type' => 'inflow',
            'category_id' => $catTuition->id,
            'amount' => 4500.00,
            'transaction_date' => Carbon::now()->subWeeks(1),
            'source_payee' => 'Cadet Royan Alvi (IDA-2026-001)',
            'payment_method' => 'bank_transfer',
            'reference_no' => 'BRAC-TX-88301',
            'description' => 'Tuition Fee Month 1 (INV-2026-0002)',
            'payment_id' => $pay2->id,
            'recorded_by' => $financeMgr->id,
        ]);

        // Outflows (Expenses)
        FinancialTransaction::create([
            'transaction_number' => 'TXN-2026-0003',
            'type' => 'outflow',
            'category_id' => $catRent->id,
            'amount' => 25000.00,
            'transaction_date' => Carbon::now()->subWeeks(2),
            'source_payee' => 'Khulna Boyra Campus Landlord',
            'payment_method' => 'bank_transfer',
            'reference_no' => 'RENT-SEP-2026',
            'description' => 'Monthly Academy Premises & Ground Lease',
            'recorded_by' => $financeMgr->id,
        ]);

        FinancialTransaction::create([
            'transaction_number' => 'TXN-2026-0004',
            'type' => 'outflow',
            'category_id' => $catEquipment->id,
            'amount' => 8500.00,
            'transaction_date' => Carbon::now()->subDays(6),
            'source_payee' => 'Bengal Sports & Tactical Gear Ltd.',
            'payment_method' => 'cash',
            'reference_no' => 'REC-TACTICAL-441',
            'description' => 'Ground Task bridging planks, ropes and obstacle markers',
            'recorded_by' => $financeMgr->id,
        ]);

        FinancialTransaction::create([
            'transaction_number' => 'TXN-2026-0005',
            'type' => 'outflow',
            'category_id' => $catUtility->id,
            'amount' => 4200.00,
            'transaction_date' => Carbon::now()->subDays(2),
            'source_payee' => 'AmberIT Fiber & DESCO',
            'payment_method' => 'bkash',
            'reference_no' => 'UTIL-SEP-192',
            'description' => 'High speed dedicated broadband & campus electricity',
            'recorded_by' => $financeMgr->id,
        ]);

        // External Test Registration Fee Payment
        $payExt = Payment::create([
            'payment_number' => 'PAY-2026-0003',
            'student_id' => $extStudent->id,
            'user_id' => $externalUser->id,
            'amount' => 500.00,
            'payment_method' => 'nagad',
            'transaction_reference' => 'NAG9923881',
            'payment_date' => Carbon::now()->subDays(4),
            'verification_status' => 'approved',
            'verified_by' => $financeMgr->id,
            'verified_at' => Carbon::now()->subDays(4)->addMinutes(45),
            'admin_notes' => 'External mock test pass activated.',
        ]);

        FinancialTransaction::create([
            'transaction_number' => 'TXN-2026-0006',
            'type' => 'inflow',
            'category_id' => $catExam->id,
            'amount' => 500.00,
            'transaction_date' => Carbon::now()->subDays(4),
            'source_payee' => 'Candidate Mehedi Hasan (External)',
            'payment_method' => 'nagad',
            'reference_no' => 'NAG9923881',
            'description' => 'Online Verbal IQ Assessment Pass',
            'payment_id' => $payExt->id,
            'recorded_by' => $financeMgr->id,
        ]);

        // ==========================================
        // 8. STUDENT DOSSIER & DEVELOPMENT SYSTEM
        // ==========================================
        StudentDossier::create([
            'student_id' => $student1->id,
            'readiness_score' => 82,
            'overall_status' => 'High Potential',
            'physical_grade' => 'A-',
            'communication_grade' => 'B+',
            'leadership_grade' => 'A',
            'iq_grade' => 'A+',
            'discipline_grade' => 'A',
            'summary_notes' => 'Excellent analytical capacity and natural authority during group tasks. Focused mentorship on spontaneous English expression underway.',
        ]);

        StudentDossier::create([
            'student_id' => $student2->id,
            'readiness_score' => 58,
            'overall_status' => 'Needs Attention',
            'physical_grade' => 'B',
            'communication_grade' => 'C+',
            'leadership_grade' => 'B-',
            'iq_grade' => 'B',
            'discipline_grade' => 'C',
            'summary_notes' => 'Attendance irregularity impacting group cohesion. Needs urgent counselling regarding discipline and punctuality.',
        ]);

        // Strengths & Weaknesses for Cadet 1
        $sw1 = StudentStrengthWeakness::create([
            'student_id' => $student1->id,
            'type' => 'strength',
            'category' => 'Leadership',
            'title' => 'Tactical Command & Group Coordination',
            'description' => 'Demonstrated exceptional calm under pressure in PGT obstacles, assigning clear roles to group members.',
            'priority' => 'high',
            'status' => 'improved',
            'identified_date' => Carbon::now()->subWeeks(3),
            'instructor_id' => $instructor1->id,
        ]);

        $sw2 = StudentStrengthWeakness::create([
            'student_id' => $student1->id,
            'type' => 'strength',
            'category' => 'IQ',
            'title' => 'Rapid Numerical & Non-Verbal Matrix Solving',
            'description' => 'Consistently finishes intelligence tests in the top 5th percentile with 90%+ accuracy.',
            'priority' => 'medium',
            'status' => 'resolved',
            'identified_date' => Carbon::now()->subWeeks(4),
            'instructor_id' => $instructor2->id,
        ]);

        $sw3 = StudentStrengthWeakness::create([
            'student_id' => $student1->id,
            'type' => 'weakness',
            'category' => 'Communication',
            'title' => 'Spontaneous English Vocabulary Under Time Stress',
            'description' => 'Tends to hesitate and search for words during extempore lecturette and rapid WAT rounds.',
            'priority' => 'critical',
            'status' => 'improving',
            'identified_date' => Carbon::now()->subWeeks(2),
            'follow_up_date' => Carbon::now()->addDays(7),
            'instructor_id' => $instructor3->id,
            'notes' => 'Given 100 military speaking idioms and 10-minute daily speech assignment.',
        ]);

        // Weakness for Cadet 2 (Triggering Early Warning)
        StudentStrengthWeakness::create([
            'student_id' => $student2->id,
            'type' => 'weakness',
            'category' => 'Discipline',
            'title' => 'Chronic Late Arrival & Class Absences',
            'description' => 'Missed 4 scheduled ground obstacle training sessions in the last 2 weeks.',
            'priority' => 'critical',
            'status' => 'identified',
            'identified_date' => Carbon::now()->subWeeks(1),
            'follow_up_date' => Carbon::now()->addDays(2),
            'instructor_id' => $instructor1->id,
            'notes' => 'Guardian notified. Mandatory counseling meeting with Lead Instructor.',
        ]);

        // Instructor Observations for Cadet 1
        InstructorObservation::create([
            'student_id' => $student1->id,
            'instructor_id' => $instructor1->id,
            'observation_date' => Carbon::now()->subDays(5),
            'category' => 'Command Task',
            'observation_text' => 'Cadet Royan displayed commanding vocal projection and logical solution to the rope-cantilever bridge obstacle. Maintained full group cooperation.',
            'rating' => 5,
            'recommended_action' => 'Maintain leadership initiative without dominating passive teammates.',
            'follow_up_date' => Carbon::now()->addWeeks(2),
            'visibility' => 'student_visible',
        ]);

        InstructorObservation::create([
            'student_id' => $student1->id,
            'instructor_id' => $instructor2->id,
            'observation_date' => Carbon::now()->subDays(3),
            'category' => 'Psychological Screening',
            'observation_text' => 'WAT responses indicate strong patriotic conviction, mental resilience, and positive orientation toward duty and adversity.',
            'rating' => 4,
            'recommended_action' => 'Avoid overly repetitive thematic sentences on consecutive flash words.',
            'visibility' => 'student_visible',
        ]);

        // Periodic Structured Performance Assessments for Cadet 1
        PerformanceAssessment::create([
            'student_id' => $student1->id,
            'instructor_id' => $instructor1->id,
            'assessment_date' => Carbon::now()->subMonths(1),
            'period_label' => 'Month 1 Baseline Assessment',
            'category' => 'Academic & Physical Baseline',
            'ratings' => [
                'Verbal IQ' => 74,
                'English Expression' => 58,
                'General Knowledge' => 65,
                'Group Dynamics' => 70,
                'Physical Obstacles' => 78,
            ],
            'overall_rating' => 3,
            'remarks' => 'Good initial potential; requires speech grooming and confidence building in public lecturette.',
        ]);

        PerformanceAssessment::create([
            'student_id' => $student1->id,
            'instructor_id' => $instructor3->id,
            'assessment_date' => Carbon::now()->subDays(7),
            'period_label' => 'Month 2 Mid-Term Development Review',
            'category' => 'OLQ & Mock ISSB Assessment',
            'ratings' => [
                'Verbal IQ' => 88,
                'English Expression' => 74,
                'General Knowledge' => 80,
                'Group Dynamics' => 86,
                'Physical Obstacles' => 85,
            ],
            'overall_rating' => 4,
            'remarks' => 'Tangible progress across all five core military domains. Communication fluidity significantly refined.',
        ]);

        // Actionable Improvement Plan for Cadet 1
        ImprovementPlan::create([
            'student_id' => $student1->id,
            'strength_weakness_id' => $sw3->id,
            'problem_description' => 'Hesitation and pauses during 3-minute extempore military lecturette topics.',
            'target_objective' => 'Deliver uninterrupted 3-minute speech in fluent English with structured opening, analysis, and conclusion.',
            'recommended_activity' => 'Daily recorded speech on assigned international geopolitical topic evaluated by Instructor.',
            'assigned_task' => 'Record and submit 3-minute speech on "Artificial Intelligence in Modern Warfare" by Friday.',
            'responsible_instructor_id' => $instructor3->id,
            'deadline' => Carbon::now()->addDays(6),
            'progress_percentage' => 65,
            'status' => 'in_progress',
            'follow_up_date' => Carbon::now()->addDays(7),
            'review_notes' => 'Tone and gesture improved; vocabulary flow needs 1 more week of targeted practice.',
        ]);

        // Cadet Development Timeline
        $timelineEvents = [
            [
                'student_id' => $student1->id,
                'event_type' => 'admission',
                'title' => 'Enrolled at Imperial Defence Academy',
                'description' => 'Admitted to BMA 94th Long Course Batch Alpha under ID IDA-2026-001.',
                'event_date' => Carbon::now()->subMonths(1),
                'badge_color' => 'emerald',
                'icon' => 'fa-id-badge',
            ],
            [
                'student_id' => $student1->id,
                'event_type' => 'assessment',
                'title' => 'Completed Month 1 Baseline Assessment',
                'description' => 'Overall rating: 3/5 with High Potential citation in Physical and Verbal IQ.',
                'event_date' => Carbon::now()->subWeeks(3),
                'badge_color' => 'blue',
                'icon' => 'fa-chart-simple',
            ],
            [
                'student_id' => $student1->id,
                'event_type' => 'weakness_identified',
                'title' => 'Targeted English Expression Plan Initiated',
                'description' => 'Lead Instructor assigned daily speaking regimen to eliminate hesitation.',
                'event_date' => Carbon::now()->subWeeks(2),
                'badge_color' => 'amber',
                'icon' => 'fa-bullseye',
            ],
            [
                'student_id' => $student1->id,
                'event_type' => 'exam_completed',
                'title' => 'Verbal IQ Mock Screening: Scored 92%',
                'description' => 'Achieved 46 out of 50 in timed simulated screening test.',
                'event_date' => Carbon::now()->subDays(6),
                'badge_color' => 'emerald',
                'icon' => 'fa-award',
            ],
            [
                'student_id' => $student1->id,
                'event_type' => 'observation',
                'title' => 'Command Task Leadership Citation',
                'description' => 'Rated 5/5 by Major (Retd.) Tariqul Islam for bridging obstacle mastery.',
                'event_date' => Carbon::now()->subDays(5),
                'badge_color' => 'purple',
                'icon' => 'fa-medal',
            ],
        ];

        foreach ($timelineEvents as $event) {
            PerformanceTimeline::create($event);
        }

        // ==========================================
        // 9. EXAMINATION ENGINE DATA
        // ==========================================
        $examIQ = Exam::create([
            'title' => 'Preliminary Verbal Intelligence (IQ) Screening Test 01',
            'slug' => 'prelim-verbal-iq-01',
            'exam_type' => 'iq_mcq',
            'category' => 'Verbal IQ',
            'description' => 'Standard 30-minute simulated preliminary intelligence test for Bangladesh Army (BMA), Navy, and Airforce officer cadet selections.',
            'duration_minutes' => 25,
            'total_marks' => 20.00,
            'pass_marks' => 10.00,
            'negative_marking_per_wrong' => 0.25,
            'fee' => 500.00,
            'is_paid_for_external' => true,
            'is_public_for_external' => true,
            'status' => 'open',
            'instructions' => 'Read each question carefully. Select one best option. Incorrect answers carry 0.25 negative marks. Do not refresh or exit fullscreen during the examination.',
        ]);

        $examWAT = Exam::create([
            'title' => 'ISSB Psychological Word Association Test (WAT) - Set Alpha',
            'slug' => 'wat-set-alpha',
            'exam_type' => 'word_association',
            'category' => 'WAT',
            'description' => 'Simulated ISSB Word Association Test. 15 words presented sequentially for exactly 15 seconds each. Input spontaneous, meaningful, positive sentences.',
            'duration_minutes' => 5,
            'total_marks' => 30.00,
            'pass_marks' => 15.00,
            'negative_marking_per_wrong' => 0.00,
            'fee' => 600.00,
            'is_paid_for_external' => true,
            'is_public_for_external' => true,
            'status' => 'open',
            'instructions' => 'A word will appear on screen for 15 seconds. Type your immediate spontaneous sentence before the timer expires. The screen will automatically transition to the next word.',
        ]);

        $examNonVerbal = Exam::create([
            'title' => 'Non-Verbal Spatial & Pattern Reasoning Diagnostic',
            'slug' => 'non-verbal-pattern-diagnostic',
            'exam_type' => 'non_verbal_iq',
            'category' => 'Non-Verbal IQ',
            'description' => 'Architectural framework test for pattern recognition, shape rotation, and visual analogies.',
            'duration_minutes' => 20,
            'total_marks' => 15.00,
            'pass_marks' => 8.00,
            'negative_marking_per_wrong' => 0.25,
            'fee' => 500.00,
            'is_paid_for_external' => true,
            'is_public_for_external' => true,
            'status' => 'open',
            'instructions' => 'Analyze the sequence of figures and identify the logical missing component.',
        ]);

        // Seed Authentic Military IQ Questions for Exam 1
        $questions = [
            [
                'question_text' => 'Find the next numbers in the series: 12, 13, 18, 19, 24, 25, ?',
                'options' => [
                    ['key' => 'A', 'text' => '33, 35'],
                    ['key' => 'B', 'text' => '30, 31'],
                    ['key' => 'C', 'text' => '25, 26'],
                    ['key' => 'D', 'text' => '41, 42'],
                ],
                'correct_answer' => 'B',
                'explanation' => 'The pattern alternates: +1, then +5. (12+1=13; 13+5=18; 18+1=19; 19+5=24; 24+1=25; 25+5=30; 30+1=31). Hence, 30, 31.',
                'marks' => 1.00,
                'negative_marks' => 0.25,
                'order_seq' => 1,
            ],
            [
                'question_text' => 'Rearrange the scrambled letters "R A B G E N I D" to form an army rank. What is the fourth letter from the beginning?',
                'options' => [
                    ['key' => 'A', 'text' => 'G'],
                    ['key' => 'B', 'text' => 'A'],
                    ['key' => 'C', 'text' => 'R'],
                    ['key' => 'D', 'text' => 'I'],
                ],
                'correct_answer' => 'A',
                'explanation' => 'The unscrambled word is "BRIGADIER". 1st: B, 2nd: R, 3rd: I, 4th: G. Hence, G.',
                'marks' => 1.00,
                'negative_marks' => 0.25,
                'order_seq' => 2,
            ],
            [
                'question_text' => 'If SOLDIER is coded as JOKZEAN, which word is coded as QEFOP?',
                'options' => [
                    ['key' => 'A', 'text' => 'BRAVE'],
                    ['key' => 'B', 'text' => 'HONOR'],
                    ['key' => 'C', 'text' => 'VALOR'],
                    ['key' => 'D', 'text' => 'PEACE'],
                ],
                'correct_answer' => 'A',
                'explanation' => 'In reverse alphabet offset displacement, QEFOP decrypts into BRAVE.',
                'marks' => 1.00,
                'negative_marks' => 0.25,
                'order_seq' => 3,
            ],
            [
                'question_text' => 'A soldier starts from post Alpha, walks 4 km North, turns right and walks 3 km East. How far and in which direction is he from post Alpha?',
                'options' => [
                    ['key' => 'A', 'text' => '7 km North-East'],
                    ['key' => 'B', 'text' => '5 km North-East'],
                    ['key' => 'C', 'text' => '5 km South-East'],
                    ['key' => 'D', 'text' => '6 km North-West'],
                ],
                'correct_answer' => 'B',
                'explanation' => 'By Pythagoras theorem: sqrt(4^2 + 3^2) = sqrt(16 + 9) = sqrt(25) = 5 km North-East.',
                'marks' => 1.00,
                'negative_marks' => 0.25,
                'order_seq' => 4,
            ],
            [
                'question_text' => 'Which word does NOT belong to the group: Tank, Frigate, Fighter Jet, Submarine, Corvette?',
                'options' => [
                    ['key' => 'A', 'text' => 'Tank'],
                    ['key' => 'B', 'text' => 'Fighter Jet'],
                    ['key' => 'C', 'text' => 'Submarine'],
                    ['key' => 'D', 'text' => 'Frigate'],
                ],
                'correct_answer' => 'A',
                'explanation' => 'Frigate, Submarine, and Corvette are naval vessels; Fighter Jet operates in air; but among combat platforms Tank is strictly ground-armoured without naval integration. Hence Tank is distinct.',
                'marks' => 1.00,
                'negative_marks' => 0.25,
                'order_seq' => 5,
            ],
            [
                'question_text' => 'Pointing to a military portrait, Cadet Rahat says: "His mother is the only daughter of my mother." How is Rahat related to the man in the portrait?',
                'options' => [
                    ['key' => 'A', 'text' => 'Father'],
                    ['key' => 'B', 'text' => 'Brother'],
                    ['key' => 'C', 'text' => 'Maternal Uncle'],
                    ['key' => 'D', 'text' => 'Grandfather'],
                ],
                'correct_answer' => 'C',
                'explanation' => 'Only daughter of Rahat\'s mother is Rahat\'s sister. Her son makes Rahat his maternal uncle.',
                'marks' => 1.00,
                'negative_marks' => 0.25,
                'order_seq' => 6,
            ],
            [
                'question_text' => 'Complete the analogy: Compass is to Direction as Altimeter is to ______?',
                'options' => [
                    ['key' => 'A', 'text' => 'Speed'],
                    ['key' => 'B', 'text' => 'Altitude / Height'],
                    ['key' => 'C', 'text' => 'Pressure'],
                    ['key' => 'D', 'text' => 'Depth'],
                ],
                'correct_answer' => 'B',
                'explanation' => 'A compass measures direction; an altimeter measures altitude/height above sea level.',
                'marks' => 1.00,
                'negative_marks' => 0.25,
                'order_seq' => 7,
            ],
            [
                'question_text' => 'If today is Saturday, what day will it be after 65 days?',
                'options' => [
                    ['key' => 'A', 'text' => 'Sunday'],
                    ['key' => 'B', 'text' => 'Monday'],
                    ['key' => 'C', 'text' => 'Tuesday'],
                    ['key' => 'D', 'text' => 'Wednesday'],
                ],
                'correct_answer' => 'C',
                'explanation' => '65 divided by 7 has a remainder of 2. Saturday + 2 days = Monday (wait, 63 is divisible by 7; remainder is 2: Saturday + 1 = Sunday, + 2 = Monday). Hence Monday.',
                'marks' => 1.00,
                'negative_marks' => 0.25,
                'order_seq' => 8,
            ],
            [
                'question_text' => 'Select the missing number in the matrix: [3, 5, 15], [4, 6, 24], [5, 7, ?]',
                'options' => [
                    ['key' => 'A', 'text' => '30'],
                    ['key' => 'B', 'text' => '35'],
                    ['key' => 'C', 'text' => '42'],
                    ['key' => 'D', 'text' => '45'],
                ],
                'correct_answer' => 'B',
                'explanation' => 'The third number is the product of the first two numbers: 5 * 7 = 35.',
                'marks' => 1.00,
                'negative_marks' => 0.25,
                'order_seq' => 9,
            ],
            [
                'question_text' => 'Water is to Thirst as Victory is to ______?',
                'options' => [
                    ['key' => 'A', 'text' => 'Medal'],
                    ['key' => 'B', 'text' => 'Battle'],
                    ['key' => 'C', 'text' => 'Ambition'],
                    ['key' => 'D', 'text' => 'Glory'],
                ],
                'correct_answer' => 'C',
                'explanation' => 'Water satisfies thirst; victory satisfies ambition or aspiration.',
                'marks' => 1.00,
                'negative_marks' => 0.25,
                'order_seq' => 10,
            ],
        ];

        foreach ($questions as $q) {
            Question::create(array_merge($q, ['exam_id' => $examIQ->id, 'exam_type' => 'iq_mcq']));
        }

        // Seed 15 Authentic ISSB WAT Words
        $watWords = [
            'DUTY', 'WAR', 'LEADER', 'FAILURE', 'NIGHT',
            'SOLDIER', 'CHALLENGE', 'ALONE', 'BLOOD', 'WEAK',
            'ENEMY', 'PEACE', 'RISK', 'COMPASSION', 'BRAVERY'
        ];

        foreach ($watWords as $idx => $word) {
            WatWord::create([
                'exam_id' => $examWAT->id,
                'word' => $word,
                'display_seconds' => 15,
                'order_seq' => $idx + 1,
            ]);
        }

        // Seed Sample Attempt for Cadet 1 (IQ Test)
        $attempt = ExamAttempt::create([
            'exam_id' => $examIQ->id,
            'user_id' => $studentUser1->id,
            'student_id' => $student1->id,
            'attempt_number' => 1,
            'started_at' => Carbon::now()->subDays(6)->subMinutes(25),
            'completed_at' => Carbon::now()->subDays(6),
            'time_spent_seconds' => 1140,
            'score' => 9.25,
            'total_marks' => 10.00,
            'percentage' => 92.50,
            'result_status' => 'passed',
            'status' => 'submitted',
            'answers_summary' => ['correct' => 9, 'incorrect' => 1, 'unanswered' => 0],
        ]);

        // ==========================================
        // 10. CMS DATA & WEBSITE SETTINGS
        // ==========================================
        $cmsSettings = [
            ['key' => 'site_name', 'value' => 'Imperial Defence Academy (IDA)', 'group' => 'general'],
            ['key' => 'site_tagline', 'value' => 'Courage • Character • Commission', 'group' => 'general'],
            ['key' => 'academy_location', 'value' => 'Boyra Main Road (Near Medical College), Khulna - 9000', 'group' => 'contact'],
            ['key' => 'academy_phone', 'value' => '+880 1712-345678, +880 1911-987654', 'group' => 'contact'],
            ['key' => 'academy_email', 'value' => 'info@ida.com.bd, admissions@ida.com.bd', 'group' => 'contact'],
            ['key' => 'office_hours', 'value' => 'Saturday - Thursday: 08:00 AM - 08:00 PM (Friday: 03:00 PM - 08:00 PM)', 'group' => 'contact'],
            ['key' => 'hero_heading', 'value' => 'Forging Tomorrow\'s Defence Leaders with Precision & Discipline', 'group' => 'hero'],
            ['key' => 'hero_subheading', 'value' => 'Khulna\'s premier academy for BMA Long Course, Navy Officer Cadet, BAFA Flight Wing, and comprehensive ISSB preparation guided by retired armed forces officers.', 'group' => 'hero'],
            ['key' => 'about_mission', 'value' => 'To instill unwavering discipline, moral integrity, physical robustness, and leadership attributes in candidates aspiring to serve Bangladesh Armed Forces as commissioned officers.', 'group' => 'about'],
            ['key' => 'about_vision', 'value' => 'To be the nation\'s most scientifically structured and technologically advanced defence preparatory academy.', 'group' => 'about'],
        ];

        foreach ($cmsSettings as $st) {
            CmsSetting::create($st);
        }

        // Notices
        CmsNotice::create([
            'title' => 'Admissions Open: BMA 95th Long Course Special Preparatory Batch',
            'slug' => 'admissions-bma-95-open',
            'content' => 'Registrations are now actively accepted for the upcoming intensive batch. Limited to 35 cadets per squad. Early seat confirmation entitles cadets to preliminary medical fitness screening at our campus clinic.',
            'category' => 'Admission',
            'is_urgent' => true,
            'is_pinned' => true,
            'is_published' => true,
            'publish_date' => Carbon::today()->subDays(2),
        ]);

        CmsNotice::create([
            'title' => 'Weekly Full-Length Verbal & Non-Verbal Mock Examination Notice',
            'slug' => 'weekly-mock-exam-sep',
            'content' => 'All enrolled academic cadets and verified external test takers are notified that the central mock exam portal will open Friday at 09:00 AM. Please ensure desktop/tablet devices are ready.',
            'category' => 'Exam',
            'is_urgent' => false,
            'is_pinned' => false,
            'is_published' => true,
            'publish_date' => Carbon::today()->subDays(4),
        ]);

        // Gallery Items
        $gallery = [
            ['title' => 'Cadets Tackling Progressive Ground Task (PGT) Obstacle', 'category' => 'Training', 'image_path' => 'gallery-pgt.jpg', 'caption' => 'Hands-on bridging exercises on regulation ground task field.'],
            ['title' => 'Morning Physical Conditioning & Endurance Run', 'category' => 'Drills', 'image_path' => 'gallery-drill.jpg', 'caption' => 'Aerobic capacity and calisthenics training at sunrise.'],
            ['title' => 'Interactive Lecturette & Group Discussion Session', 'category' => 'Classroom', 'image_path' => 'gallery-gd.jpg', 'caption' => 'Cadets presenting geopolitical topics under instructor review.'],
            ['title' => 'Passing Out Batch Celebration & Commission Accolades', 'category' => 'Achievements', 'image_path' => 'gallery-success.jpg', 'caption' => 'Celebrating 14 recommended cadets from the previous ISSB board.'],
        ];

        foreach ($gallery as $g) {
            CmsGalleryItem::create($g);
        }
    }
}
