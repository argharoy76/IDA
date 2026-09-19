<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\Exam;
use App\Models\Question;
use App\Models\WatWord;
use Carbon\Carbon;

class FreshExamDataSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            throw new \RuntimeException('Destructive operation blocked: FreshExamDataSeeder truncates exam tables and is strictly prohibited in production.');
        }

        // 1. Wipe all existing exam tables cleanly
        Schema::disableForeignKeyConstraints();
        DB::table('exam_attempt_answers')->truncate();
        DB::table('exam_attempts')->truncate();
        DB::table('wat_words')->truncate();
        DB::table('questions')->truncate();
        DB::table('exams')->truncate();
        Schema::enableForeignKeyConstraints();

        $now = Carbon::now();

        // 2. Define Questions for Question Bank Pools across all 4 Branches
        $branchQuestions = [
            'army' => [
                [
                    'text' => 'Which is the oldest military academy in Bangladesh, established at Bhatiary, Chattogram?',
                    'a' => 'Bangladesh Naval Academy (BNA)',
                    'b' => 'Bangladesh Military Academy (BMA)',
                    'c' => 'Bangladesh Air Force Academy (BAFA)',
                    'd' => 'Defence Services Command and Staff College (DSCSC)',
                    'ans' => 'B',
                    'diff' => 'easy',
                    'marks' => 1.0,
                    'neg' => 0.25,
                    'exp' => 'Bangladesh Military Academy (BMA) is located at Bhatiary, Chattogram.'
                ],
                [
                    'text' => 'What is the motto of the Bangladesh Army?',
                    'a' => 'In War, In Peace, Everywhere For Our Country',
                    'b' => 'Ever Vigilant at Sea',
                    'c' => 'Free Shall We Keep the Sky of Bangladesh',
                    'd' => 'Discipline, Security, Progress',
                    'ans' => 'A',
                    'diff' => 'easy',
                    'marks' => 1.0,
                    'neg' => 0.25,
                    'exp' => '"In War, In Peace, Everywhere For Our Country" (সমরে আমরা শান্তিতে আমরা সর্বত্র আমরা দেশের তরে) is the motto of the Bangladesh Army.'
                ],
                [
                    'text' => 'In verbal reasoning: If SOLDIER is coded as JFLSIRE in reverse order grouping, how is DEFENCE coded?',
                    'a' => 'ECNEFED',
                    'b' => 'FEDCNEE',
                    'c' => 'CNEEDEF',
                    'd' => 'EFNEDEC',
                    'ans' => 'A',
                    'diff' => 'medium',
                    'marks' => 1.0,
                    'neg' => 0.25,
                    'exp' => 'DEFENCE spelled in reverse is ECNEFED.'
                ],
                [
                    'text' => 'Complete the number sequence: 4, 9, 19, 39, 79, ... ?',
                    'a' => '159',
                    'b' => '149',
                    'c' => '169',
                    'd' => '139',
                    'ans' => 'A',
                    'diff' => 'medium',
                    'marks' => 1.0,
                    'neg' => 0.25,
                    'exp' => 'Each step is (n * 2) + 1: 79 * 2 + 1 = 159.'
                ],
                [
                    'text' => 'What is the equivalent rank of an Army Captain in the Bangladesh Navy?',
                    'a' => 'Lieutenant',
                    'b' => 'Lieutenant Commander',
                    'c' => 'Sub-Lieutenant',
                    'd' => 'Commander',
                    'ans' => 'A',
                    'diff' => 'hard',
                    'marks' => 1.0,
                    'neg' => 0.25,
                    'exp' => 'In Bangladesh Armed Forces, an Army Captain is equivalent to a Navy Lieutenant and Air Force Flight Lieutenant.'
                ],
                [
                    'text' => 'A tactical squad marches 12 km North, turns East and marches 5 km. What is the direct displacement from start?',
                    'a' => '17 km',
                    'b' => '13 km',
                    'c' => '15 km',
                    'd' => '14 km',
                    'ans' => 'B',
                    'diff' => 'medium',
                    'marks' => 1.0,
                    'neg' => 0.25,
                    'exp' => 'Pythagoras theorem: sqrt(12^2 + 5^2) = sqrt(144 + 25) = sqrt(169) = 13 km.'
                ],
            ],
            'navy' => [
                [
                    'text' => 'Where is the Bangladesh Naval Academy (BNA) situated?',
                    'a' => 'Bhatiary, Chattogram',
                    'b' => 'Patenga, Chattogram',
                    'c' => 'Mongla, Khulna',
                    'd' => 'Kaptai, Rangamati',
                    'ans' => 'B',
                    'diff' => 'easy',
                    'marks' => 1.0,
                    'neg' => 0.25,
                    'exp' => 'Bangladesh Naval Academy is located at Patenga, Chattogram on the Karnaphuli river mouth.'
                ],
                [
                    'text' => 'What is the official motto of the Bangladesh Navy?',
                    'a' => 'Ever Vigilant at Sea',
                    'b' => 'Fight to the End',
                    'c' => 'Peace and Security',
                    'd' => 'Guardians of the Coast',
                    'ans' => 'A',
                    'diff' => 'easy',
                    'marks' => 1.0,
                    'neg' => 0.25,
                    'exp' => '"Ever Vigilant at Sea" (শান্তিতে সংগ্রামে সমুদ্রে দুর্জয়) is the proud motto of the Bangladesh Navy.'
                ],
                [
                    'text' => 'What is a nautical mile equivalent to in kilometers?',
                    'a' => '1.609 km',
                    'b' => '1.852 km',
                    'c' => '2.000 km',
                    'd' => '1.450 km',
                    'ans' => 'B',
                    'diff' => 'medium',
                    'marks' => 1.0,
                    'neg' => 0.25,
                    'exp' => 'One International Nautical Mile is defined as exactly 1,852 meters (1.852 km).'
                ],
                [
                    'text' => 'A naval frigate sails at 20 knots. How many nautical miles will it cover in 4 hours 30 minutes?',
                    'a' => '80 nm',
                    'b' => '90 nm',
                    'c' => '100 nm',
                    'd' => '85 nm',
                    'ans' => 'B',
                    'diff' => 'medium',
                    'marks' => 1.0,
                    'neg' => 0.25,
                    'exp' => 'Distance = Speed * Time = 20 knots * 4.5 hours = 90 nautical miles.'
                ],
            ],
            'air_force' => [
                [
                    'text' => 'What is the standard cruising speed of a military trainer aircraft in knots?',
                    'a' => '120 knots',
                    'b' => '180 knots',
                    'c' => '240 knots',
                    'd' => '300 knots',
                    'ans' => 'B',
                    'diff' => 'medium',
                    'marks' => 1.0,
                    'neg' => 0.25,
                    'exp' => 'Standard trainer cruising speed operates around 180 knots.'
                ],
                [
                    'text' => 'Where is the Bangladesh Air Force Academy (BAFA) located?',
                    'a' => 'Kurmitola, Dhaka',
                    'b' => 'Zahurul Haque Base, Chattogram',
                    'c' => 'Matiur Rahman Base, Jashore',
                    'd' => 'Paharkanchanpur, Tangail',
                    'ans' => 'C',
                    'diff' => 'easy',
                    'marks' => 1.0,
                    'neg' => 0.25,
                    'exp' => 'BAFA is situated at BAF Base Matiur Rahman in Jashore.'
                ],
                [
                    'text' => 'What is the motto of the Bangladesh Air Force?',
                    'a' => 'Free Shall We Keep the Sky of Bangladesh',
                    'b' => 'Sky Is the Limit',
                    'c' => 'Valor in Flight',
                    'd' => 'Supreme in Defence',
                    'ans' => 'A',
                    'diff' => 'easy',
                    'marks' => 1.0,
                    'neg' => 0.25,
                    'exp' => '"Free Shall We Keep the Sky of Bangladesh" (বাংলার আকাশ রাখিব মুক্ত) is the motto of BAF.'
                ],
                [
                    'text' => 'Which instrument in a fighter cockpit measures altitude above mean sea level?',
                    'a' => 'Tachometer',
                    'b' => 'Altimeter',
                    'c' => 'Machmeter',
                    'd' => 'Variometer',
                    'ans' => 'B',
                    'diff' => 'easy',
                    'marks' => 1.0,
                    'neg' => 0.25,
                    'exp' => 'The barometric altimeter displays aircraft altitude relative to mean sea level.'
                ],
            ],
            'police' => [
                [
                    'text' => 'Where is the Bangladesh Police Academy (BPA) located?',
                    'a' => 'Mirpur, Dhaka',
                    'b' => 'Sarda, Rajshahi',
                    'c' => 'Sitakunda, Chattogram',
                    'd' => 'Chandpur',
                    'ans' => 'B',
                    'diff' => 'easy',
                    'marks' => 1.0,
                    'neg' => 0.25,
                    'exp' => 'The historical Bangladesh Police Academy is located at Sarda in Rajshahi.'
                ],
                [
                    'text' => 'What is the core motto of the Bangladesh Police?',
                    'a' => 'Discipline, Security, Progress',
                    'b' => 'Courage and Devotion',
                    'c' => 'Service and Vigilance',
                    'd' => 'Law and Order First',
                    'ans' => 'A',
                    'diff' => 'easy',
                    'marks' => 1.0,
                    'neg' => 0.25,
                    'exp' => '"Discipline, Security, Progress" (শৃঙ্খলা নিরাপত্তা প্রগতি) is the motto of Bangladesh Police.'
                ],
                [
                    'text' => 'In a crime scene investigation, what does the forensics acronym "CCTV" stand for?',
                    'a' => 'Central Circuit Television',
                    'b' => 'Closed-Circuit Television',
                    'c' => 'Command Control Television',
                    'd' => 'Crime Capture Television',
                    'ans' => 'B',
                    'diff' => 'easy',
                    'marks' => 1.0,
                    'neg' => 0.25,
                    'exp' => 'CCTV stands for Closed-Circuit Television.'
                ],
                [
                    'text' => 'Under Bangladesh laws, which rank is the head of the district police force?',
                    'a' => 'Superintendent of Police (SP)',
                    'b' => 'Deputy Inspector General (DIG)',
                    'c' => 'Officer-in-Charge (OC)',
                    'd' => 'Additional SP',
                    'ans' => 'A',
                    'diff' => 'medium',
                    'marks' => 1.0,
                    'neg' => 0.25,
                    'exp' => 'The Superintendent of Police (SP) is the chief commanding officer of a district police administration.'
                ],
            ],
        ];

        // 3. Create questions in the question bank pool (exam_id = null)
        foreach ($branchQuestions as $branch => $questions) {
            foreach ($questions as $idx => $qData) {
                Question::create([
                    'exam_id' => null,
                    'branch' => $branch,
                    'exam_type' => 'iq_mcq',
                    'question_text' => $qData['text'],
                    'options' => [
                        ['key' => 'A', 'text' => $qData['a']],
                        ['key' => 'B', 'text' => $qData['b']],
                        ['key' => 'C', 'text' => $qData['c']],
                        ['key' => 'D', 'text' => $qData['d']],
                    ],
                    'correct_answer' => $qData['ans'],
                    'marks' => $qData['marks'],
                    'negative_marks' => $qData['neg'],
                    'explanation' => $qData['exp'],
                    'difficulty' => $qData['diff'],
                    'order_seq' => $idx + 1,
                ]);
            }
        }

        // 4. Create Baseline Active/Scheduled Exams across Branches
        // A) Army Exam - LIVE OPEN
        $armyLive = Exam::create([
            'title' => 'Bangladesh Army BMA Long Course Verbal IQ Assessment',
            'slug' => 'bangladesh-army-bma-long-course-verbal-iq',
            'branch' => 'army',
            'exam_type' => 'iq_mcq',
            'category' => 'Verbal IQ',
            'description' => 'Comprehensive preliminary screening containing situational problem solving, series completion, and general knowledge for BMA officer cadets.',
            'duration_minutes' => 30,
            'total_marks' => 50.00,
            'pass_marks' => 25.00,
            'negative_marking_per_wrong' => 0.25,
            'fee' => 0.00,
            'is_paid_for_external' => false,
            'is_public_for_external' => true,
            'status' => 'open',
            'schedule_start' => $now->copy()->subMinutes(10), // already started
            'schedule_end' => $now->copy()->addHours(6),
            'random_question_count' => 6,
            'instructions' => 'Calculators and external smart devices are strictly prohibited. Negative marking of 0.25 per incorrect answer is enabled.',
        ]);

        // Attach Army questions to this live exam
        $armyPool = Question::where('branch', 'army')->whereNull('exam_id')->get();
        foreach ($armyPool as $i => $pq) {
            Question::create([
                'exam_id' => $armyLive->id,
                'branch' => 'army',
                'exam_type' => $pq->exam_type,
                'question_text' => $pq->question_text,
                'options' => $pq->options,
                'correct_answer' => $pq->correct_answer,
                'marks' => $pq->marks,
                'negative_marks' => $pq->negative_marks,
                'explanation' => $pq->explanation,
                'difficulty' => $pq->difficulty,
                'order_seq' => $i + 1,
            ]);
        }

        // B) Army Exam - SCHEDULED (Starts at 1:00 PM / 30 mins in future)
        $scheduledTime = $now->copy()->addMinutes(45)->setSecond(0);
        Exam::create([
            'title' => 'BMA 95 Long Course Preliminary Mock Exam (Scheduled)',
            'slug' => 'bma-95-long-course-preliminary-mock-scheduled',
            'branch' => 'army',
            'exam_type' => 'iq_mcq',
            'category' => 'General Aptitude',
            'description' => 'Server-timed scheduled mock exam for enrolled cadets and external aspirants.',
            'duration_minutes' => 45,
            'total_marks' => 100.00,
            'pass_marks' => 50.00,
            'negative_marking_per_wrong' => 0.25,
            'fee' => 0.00,
            'is_paid_for_external' => false,
            'is_public_for_external' => true,
            'status' => 'scheduled',
            'schedule_start' => $scheduledTime,
            'schedule_end' => $scheduledTime->copy()->addHours(2),
            'random_question_count' => 20,
            'instructions' => 'Exam access unlocks exactly at the scheduled start time.',
        ]);

        // C) Navy Exam - LIVE OPEN
        $navyLive = Exam::create([
            'title' => 'Bangladesh Navy Officer Cadet Maritime Intelligence Test',
            'slug' => 'bangladesh-navy-officer-cadet-maritime-intelligence-test',
            'branch' => 'navy',
            'exam_type' => 'iq_mcq',
            'category' => 'Non-Verbal IQ',
            'description' => 'Aptitude screening covering naval navigation calculations, speed-time-distance, and spatial awareness.',
            'duration_minutes' => 30,
            'total_marks' => 40.00,
            'pass_marks' => 20.00,
            'negative_marking_per_wrong' => 0.25,
            'fee' => 0.00,
            'is_paid_for_external' => false,
            'is_public_for_external' => true,
            'status' => 'open',
            'schedule_start' => $now->copy()->subMinutes(5),
            'schedule_end' => $now->copy()->addHours(8),
            'random_question_count' => 4,
            'instructions' => 'Read each maritime scenario carefully.',
        ]);
        $navyPool = Question::where('branch', 'navy')->whereNull('exam_id')->get();
        foreach ($navyPool as $i => $pq) {
            Question::create([
                'exam_id' => $navyLive->id,
                'branch' => 'navy',
                'exam_type' => $pq->exam_type,
                'question_text' => $pq->question_text,
                'options' => $pq->options,
                'correct_answer' => $pq->correct_answer,
                'marks' => $pq->marks,
                'negative_marks' => $pq->negative_marks,
                'explanation' => $pq->explanation,
                'difficulty' => $pq->difficulty,
                'order_seq' => $i + 1,
            ]);
        }

        // D) Air Force Exam - LIVE OPEN
        $airForceLive = Exam::create([
            'title' => 'Bangladesh Air Force BAFA Flight Cadet Screening Test',
            'slug' => 'bafa-flight-cadet-screening-test',
            'branch' => 'air_force',
            'exam_type' => 'iq_mcq',
            'category' => 'Aviation Aptitude',
            'description' => 'High-velocity decision making, aeronautical navigation, and pilot aptitude screening assessment.',
            'duration_minutes' => 35,
            'total_marks' => 50.00,
            'pass_marks' => 25.00,
            'negative_marking_per_wrong' => 0.25,
            'fee' => 0.00,
            'is_paid_for_external' => false,
            'is_public_for_external' => true,
            'status' => 'open',
            'schedule_start' => $now->copy()->subMinutes(15),
            'schedule_end' => $now->copy()->addHours(12),
            'random_question_count' => 4,
            'instructions' => 'Pacing is essential. Do not linger on a single problem.',
        ]);
        $airPool = Question::where('branch', 'air_force')->whereNull('exam_id')->get();
        foreach ($airPool as $i => $pq) {
            Question::create([
                'exam_id' => $airForceLive->id,
                'branch' => 'air_force',
                'exam_type' => $pq->exam_type,
                'question_text' => $pq->question_text,
                'options' => $pq->options,
                'correct_answer' => $pq->correct_answer,
                'marks' => $pq->marks,
                'negative_marks' => $pq->negative_marks,
                'explanation' => $pq->explanation,
                'difficulty' => $pq->difficulty,
                'order_seq' => $i + 1,
            ]);
        }

        // E) Police Exam - LIVE OPEN
        $policeLive = Exam::create([
            'title' => 'Bangladesh Police Sub-Inspector / ASP Screening Test',
            'slug' => 'bangladesh-police-si-asp-screening-test',
            'branch' => 'police',
            'exam_type' => 'iq_mcq',
            'category' => 'General Aptitude',
            'description' => 'Law enforcement aptitude, analytical deduction, legal basics, and situation reaction evaluation.',
            'duration_minutes' => 30,
            'total_marks' => 50.00,
            'pass_marks' => 25.00,
            'negative_marking_per_wrong' => 0.25,
            'fee' => 0.00,
            'is_paid_for_external' => false,
            'is_public_for_external' => true,
            'status' => 'open',
            'schedule_start' => $now->copy()->subMinutes(20),
            'schedule_end' => $now->copy()->addHours(24),
            'random_question_count' => 4,
            'instructions' => 'Answer all questions objectively.',
        ]);
        $policePool = Question::where('branch', 'police')->whereNull('exam_id')->get();
        foreach ($policePool as $i => $pq) {
            Question::create([
                'exam_id' => $policeLive->id,
                'branch' => 'police',
                'exam_type' => $pq->exam_type,
                'question_text' => $pq->question_text,
                'options' => $pq->options,
                'correct_answer' => $pq->correct_answer,
                'marks' => $pq->marks,
                'negative_marks' => $pq->negative_marks,
                'explanation' => $pq->explanation,
                'difficulty' => $pq->difficulty,
                'order_seq' => $i + 1,
            ]);
        }

        // F) Seed Word Association Test (WAT) Exam for Psychological testing
        $watExam = Exam::create([
            'title' => 'ISSB 15-Second Word Association Test (WAT)',
            'slug' => 'issb-15-second-word-association-test',
            'branch' => 'army',
            'exam_type' => 'word_association',
            'category' => 'WAT',
            'description' => 'Spontaneous association protocol where 15 prompt words flash for 15 seconds each.',
            'duration_minutes' => 15,
            'total_marks' => 15.00,
            'pass_marks' => 8.00,
            'negative_marking_per_wrong' => 0.00,
            'fee' => 0.00,
            'is_paid_for_external' => false,
            'is_public_for_external' => true,
            'status' => 'open',
            'schedule_start' => $now->copy()->subHour(),
            'schedule_end' => $now->copy()->addDays(7),
            'instructions' => 'Type the very first meaningful thought that comes into your mind.',
        ]);

        $watWords = ['COURAGE', 'DUTY', 'LEADERSHIP', 'SACRIFICE', 'INTEGRITY', 'HONOR'];
        foreach ($watWords as $idx => $word) {
            WatWord::create([
                'exam_id' => $watExam->id,
                'word' => $word,
                'display_seconds' => 15,
                'order_seq' => $idx + 1,
            ]);
        }
    }
}
