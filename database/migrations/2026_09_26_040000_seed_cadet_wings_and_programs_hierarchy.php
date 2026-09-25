<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Course;
use App\Models\Batch;

return new class extends Migration
{
    public function up(): void
    {
        $courses = [
            // Army
            [
                'title' => 'Bangladesh Army Preliminary Medical & Written Prep',
                'slug' => 'army-preliminary-prep',
                'description' => 'Comprehensive preliminary screening coaching for Bangladesh Army BMA Long Course covering initial medical examination, IQ tests, and preliminary written papers.',
                'duration' => '3 Months',
                'eligibility' => 'HSC / Equivalent with minimum GPA 4.00; Age 17-21 Years',
                'fee' => 15000.00,
                'category' => 'Army',
                'features' => ['Preliminary Medical Guidelines', 'Initial Verbal & Non-Verbal IQ Drills', 'Written Exam Mastery (Math, Eng, GK)', 'Preliminary Viva Voce Simulation'],
                'syllabus' => [
                    'Stage 1: Preliminary Medical Parameters & Physical Baseline',
                    'Stage 2: Verbal & Abstract Matrix IQ Drills',
                    'Stage 3: Written English, Mathematics, and General Knowledge',
                    'Stage 4: Preliminary Viva Board Facing Technique',
                ],
                'admission_status' => 'open',
                'schedule_info' => 'Morning & Evening Batches (Sun, Tue, Thu)',
                'is_featured' => true,
            ],
            [
                'title' => 'Bangladesh Army ISSB Special Grooming Course',
                'slug' => 'army-issb-special',
                'description' => 'Advanced 45-day intensive grooming for candidates appearing before Inter Services Selection Board (ISSB) for Bangladesh Army BMA commission.',
                'duration' => '45 Days',
                'eligibility' => 'Preliminary Passed or ISSB Call Letter Holders',
                'fee' => 18000.00,
                'category' => 'Army',
                'features' => ['Complete Ground Tasks (PGT, HGT, Command Task)', '15s Word Association Test (WAT) Live Flash', 'Psychological TAT & WAT Dossier Review', '1-on-1 Deputy President (DP) Mock Interview'],
                'syllabus' => [
                    'Day 1: Intelligence Testing, Picture Perception (PPDT)',
                    'Day 2: Group Discussion, Extempore Lecturette, PGT, HGT',
                    'Day 3: Command Task, Individual Obstacles, Mutual Assessment',
                    'Day 4: Conference Board Protocol & Final Recommendation Review',
                ],
                'admission_status' => 'open',
                'schedule_info' => 'Full-Day Tactical Lab & Ground Immersion',
                'is_featured' => true,
            ],

            // Navy
            [
                'title' => 'Bangladesh Navy Preliminary Officer Cadet Prep',
                'slug' => 'navy-preliminary-prep',
                'description' => 'Specialized naval preparatory module for Bangladesh Naval Academy (BNA) Officer Cadet preliminary screening, maritime aptitude, and written testing.',
                'duration' => '3 Months',
                'eligibility' => 'HSC Science with Physics & Mathematics; Minimum GPA 4.00',
                'fee' => 15000.00,
                'category' => 'Navy',
                'features' => ['Naval Medical Screening Standards', 'Naval Aptitude & Analytical Tests', 'Science & Mathematics Focused Written Prep', 'Preliminary Board Grooming'],
                'syllabus' => [
                    'Naval Medical Standards & Eyesight Guidelines',
                    'IQ & Maritime Situation Problem Solving',
                    'Written Physics, Mathematics, English & Current Affairs',
                    'Naval Preliminary Viva Voce',
                ],
                'admission_status' => 'open',
                'schedule_info' => 'Afternoon Batches (Mon, Wed, Sat)',
                'is_featured' => true,
            ],
            [
                'title' => 'Bangladesh Navy ISSB Executive & Technical Prep',
                'slug' => 'navy-issb-prep',
                'description' => 'Tailored ISSB simulation program focusing on naval leadership in crisis, maritime emergency group planning, and psychological endurance for naval cadets.',
                'duration' => '45 Days',
                'eligibility' => 'Candidates appearing for BNA Officer Cadet ISSB',
                'fee' => 17500.00,
                'category' => 'Navy',
                'features' => ['Maritime Crisis Group Planning', 'Psychological Profile Evaluation', 'Outdoor Obstacle Handling', 'Naval Interview Coaching'],
                'syllabus' => [
                    'Psychological Screening & Self-Appraisal (Bio-Data)',
                    'Naval Situation Reaction Tests (SRT) & WAT',
                    'Group Planning Exercise (GPE) with Water Obstacle Scenarios',
                    'Final Board Facing with Former Military Officers',
                ],
                'admission_status' => 'open',
                'schedule_info' => 'Intensive Weekend & Evening Sessions',
                'is_featured' => true,
            ],

            // Air Force (AF)
            [
                'title' => 'Bangladesh Air Force Preliminary Pilot & Ground Prep',
                'slug' => 'airforce-preliminary-prep',
                'description' => 'Focused training for Bangladesh Air Force Academy (BAFA) flight cadet and ground branch preliminary selection, spatial dial reading, and written examination.',
                'duration' => '3 Months',
                'eligibility' => 'HSC Science (Physics & Mathematics compulsory) GPA 4.50+',
                'fee' => 16000.00,
                'category' => 'Airforce',
                'features' => ['Pilot Aptitude & Spatial Orientation Drills', 'Dial & Instrument Reading Comprehension', 'Physics & Mathematics Written Drills', 'Medical Fitness Coaching (Vision 6/6)'],
                'syllabus' => [
                    'Air Force Preliminary Medical & Anthropometric Checks',
                    'Instrument Comprehension & Cockpit Spatial Tests',
                    'Written Physics, Mathematics, and General English',
                    'Preliminary Interview Techniques',
                ],
                'admission_status' => 'open',
                'schedule_info' => 'Morning Shift (Sun, Tue, Thu)',
                'is_featured' => true,
            ],
            [
                'title' => 'Bangladesh Air Force ISSB Aviation & Psych Masterclass',
                'slug' => 'airforce-issb-prep',
                'description' => 'Rigorous ISSB preparation course emphasizing rapid decision making, high-pressure psychological resilience, and Officer Like Qualities (OLQ) for aspiring pilots.',
                'duration' => '45 Days',
                'eligibility' => 'BAFA Preliminary Qualified Candidates',
                'fee' => 18000.00,
                'category' => 'Airforce',
                'features' => ['Aviation Leadership Simulations', 'Speed WAT & TAT Reaction Tests', 'Rapid Problem Resolution under Time Constraint', 'Aero-Officer Mock DP Interviews'],
                'syllabus' => [
                    'Day 1: PPDT & Advanced Mechanical Comprehension',
                    'Day 2: Outdoor Ground Tasks & Rapid GPE Solutions',
                    'Day 3: Individual Agility Obstacles & Command Tasks',
                    'Day 4: Conference Board Preparation',
                ],
                'admission_status' => 'open',
                'schedule_info' => 'Intensive Daily Immersion',
                'is_featured' => true,
            ],

            // Police
            [
                'title' => 'Bangladesh Police Constable (Con) Recruitment Coaching',
                'slug' => 'police-constable-prep',
                'description' => 'Targeted coaching for direct Constable recruitment examinations covering physical endurance preparation, Bengali, English, General Mathematics, and Viva Voce.',
                'duration' => '2 Months',
                'eligibility' => 'SSC / Equivalent with minimum GPA 2.50; Required Height & Physical Standards',
                'fee' => 9500.00,
                'category' => 'Police',
                'features' => ['Physical Screening & Field Drill Guidelines', 'Written Exam Crash Program (Bangla, Eng, Math, GK)', 'Basic Situation Reaction Tests', 'Viva Voce Simulation'],
                'syllabus' => [
                    'Physical Measurement & Endurance Run Guidelines',
                    'Written Examination: Bangla Composition & Grammar',
                    'Written Examination: English & Elementary Mathematics',
                    'General Knowledge, Police Ethics & Mock Viva',
                ],
                'admission_status' => 'open',
                'schedule_info' => 'Morning & Evening Sessions (Mon, Wed, Fri)',
                'is_featured' => true,
            ],
            [
                'title' => 'Bangladesh Police Sub-Inspector (SI) Comprehensive Prep',
                'slug' => 'police-si-prep',
                'description' => 'Premier preparatory program for Bangladesh Police Sub-Inspector (SI) and Sergeant competitive examinations covering physical tests, 3-part written exam, and viva.',
                'duration' => '3.5 Months',
                'eligibility' => 'Graduation degree in any discipline; Height: 5\'6" (Male), 5\'4" (Female)',
                'fee' => 16500.00,
                'category' => 'Police',
                'features' => ['Complete 3-Paper Written Syllabus Mastery', 'Mental Ability & Analytical Reasoning Drills', 'Physical Endurance Tracking', 'Mock Viva with Former Police Officials'],
                'syllabus' => [
                    'Paper 1: English & Bengali Language, Composition & Essay',
                    'Paper 2: General Knowledge & Bangladesh/International Affairs',
                    'Paper 3: Mathematical Reasoning & Mental Ability',
                    'Physical Agility Test (PET) Protocol & Final Viva Voce',
                ],
                'admission_status' => 'open',
                'schedule_info' => 'Morning & Evening Batches (Sun, Tue, Thu)',
                'is_featured' => true,
            ],
            [
                'title' => 'Bangladesh Police Assistant Sub-Inspector (ASI) Prep',
                'slug' => 'police-asi-prep',
                'description' => 'Focused curriculum for Assistant Sub-Inspector (ASI) departmental and direct competitive selection covering law enforcement basics, written papers, and viva.',
                'duration' => '2.5 Months',
                'eligibility' => 'HSC / Degree with standard physical measurements',
                'fee' => 12500.00,
                'category' => 'Police',
                'features' => ['Written Syllabus Crash Course', 'Constitutional & General Knowledge Modules', 'Analytical Speed Tests', 'Viva Board Facing Grooming'],
                'syllabus' => [
                    'Bengali & English Comprehension and Report Writing',
                    'General Science, Bangladesh Geography & History',
                    'Applied Arithmetic & Mental Agility',
                    'Medical Screening & Official Viva Voce Preparation',
                ],
                'admission_status' => 'open',
                'schedule_info' => 'Evening Batches (Tue, Thu, Sat)',
                'is_featured' => true,
            ],
        ];

        foreach ($courses as $cData) {
            $course = Course::firstOrCreate(
                ['slug' => $cData['slug']],
                $cData
            );

            // Create initial active batch if course has none
            if ($course->batches()->count() === 0) {
                $batchCode = strtoupper(str_replace('-', '_', $course->slug)) . '_B1';
                Batch::create([
                    'course_id' => $course->id,
                    'batch_name' => $course->title . ' - Squad Alpha',
                    'batch_code' => $batchCode,
                    'start_date' => now()->addDays(7),
                    'max_students' => 40,
                    'schedule_summary' => $course->schedule_info ?? 'Regular Batch Sessions',
                    'status' => 'active',
                ]);
            }
        }
    }

    public function down(): void
    {
        // Safe down: do not delete user courses
    }
};
