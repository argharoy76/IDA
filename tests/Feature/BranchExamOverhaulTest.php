<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Student;
use App\Models\Course;
use App\Models\ExamAttempt;
use App\Models\ExamAttemptAnswer;
use App\Services\McqPdfParserService;
use Carbon\Carbon;

class BranchExamOverhaulTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        ExamAttempt::clearRankCache();
        if (Question::whereNull('exam_id')->where('branch', 'army')->count() < 3) {
            for ($i = 1; $i <= 3; $i++) {
                Question::create([
                    'branch' => 'army',
                    'exam_type' => 'iq_mcq',
                    'question_text' => "Sample Army Pool Question {$i}",
                    'options' => ['A' => 'Option A', 'B' => 'Option B', 'C' => 'Option C', 'D' => 'Option D'],
                    'correct_answer' => 'A',
                    'marks' => 2.0,
                    'negative_marks' => 0.5,
                ]);
            }
        }
    }

    public function test_admin_branch_pages_render_cleanly()
    {
        $admin = User::where('role', 'super_admin')->first();

        foreach (['army', 'navy', 'air_force', 'police'] as $branch) {
            $response = $this->actingAs($admin)->get("/admin/exams/branch/{$branch}");
            $response->assertStatus(200);
            $response->assertSee('Question Bank Master Pool');
            $response->assertSee('Upload MCQ PDF');
        }
    }

    public function test_mcq_parser_service_extracts_questions()
    {
        $parser = new McqPdfParserService();
        $sampleText = <<<TEXT
1. What is the standard military march cadence?
A) 60 paces per minute
B) 120 paces per minute
C) 180 paces per minute
D) 90 paces per minute
Ans: B
Explanation: Standard quick march cadence is 120 paces per minute.

2. In maritime navigation, what does GPS stand for?
(A) Global Positioning System
(B) Ground Patrol Scout
(C) General Position Satellite
(D) Geo Pilot System
Answer: A
TEXT;

        $res = $parser->parseText($sampleText);
        $this->assertEquals(2, $res['total_detected']);
        $this->assertEquals(2, $res['valid_count']);
        $this->assertEquals('B', $res['questions'][0]['correct_answer']);
        $this->assertEquals('A', $res['questions'][1]['correct_answer']);
    }

    public function test_admin_can_generate_random_exam_from_pool()
    {
        $admin = User::where('role', 'super_admin')->first();

        $response = $this->actingAs($admin)->post('/admin/exams/branch/army/generate', [
            'title' => 'Automated Test Army Random Exam',
            'category' => 'Verbal IQ',
            'exam_type' => 'iq_mcq',
            'duration_minutes' => 30,
            'question_count' => 3,
            'marks_per_question' => 2.0,
            'negative_marking_per_wrong' => 0.5,
            'pass_marks' => 3.0,
            'fee' => 0.0,
            'status' => 'scheduled',
            'schedule_start' => Carbon::now()->addHour()->format('Y-m-d\TH:i'),
            'is_public_for_external' => 1,
            'description' => 'Test exam generated from pool',
        ]);

        $response->assertRedirect('/admin/exams/branch/army');
        $response->assertSessionHas('success');

        $exam = Exam::where('title', 'Automated Test Army Random Exam')->latest('id')->first();
        $this->assertNotNull($exam);
        $this->assertEquals('army', $exam->branch);
        $this->assertEquals(3, $exam->questions()->count());
        $this->assertEquals(6.0, (float) $exam->total_marks);
        $this->assertTrue($exam->is_public_for_external);

        $exam->questions()->delete();
        $exam->delete();
    }

    public function test_admin_can_toggle_public_visibility()
    {
        $admin = User::where('role', 'super_admin')->first();
        $exam = Exam::create([
            'title' => 'Test Exam Public Toggle ' . time(),
            'slug' => 'test-exam-toggle-' . time(),
            'branch' => 'army',
            'category' => 'Verbal IQ',
            'exam_type' => 'iq_mcq',
            'duration_minutes' => 30,
            'total_marks' => 10,
            'pass_marks' => 5,
            'status' => 'open',
            'is_public_for_external' => true,
        ]);

        $response = $this->actingAs($admin)->post("/admin/exams/{$exam->id}/toggle-public");
        $response->assertRedirect();

        $exam->refresh();
        $this->assertFalse($exam->is_public_for_external);
        $exam->delete();
    }

    public function test_admin_can_override_start_now()
    {
        $admin = User::where('role', 'super_admin')->first();
        $exam = Exam::create([
            'title' => 'Test Exam Start Now ' . time(),
            'slug' => 'test-exam-start-now-' . time(),
            'branch' => 'army',
            'category' => 'Verbal IQ',
            'exam_type' => 'iq_mcq',
            'duration_minutes' => 30,
            'total_marks' => 10,
            'pass_marks' => 5,
            'status' => 'scheduled',
            'schedule_start' => Carbon::now()->addHours(2),
            'is_public_for_external' => true,
        ]);

        $response = $this->actingAs($admin)->post("/admin/exams/{$exam->id}/start-now");
        $response->assertRedirect();

        $exam->refresh();
        $this->assertEquals('open', $exam->status);
        $this->assertNotNull($exam->schedule_start);
        $exam->delete();
    }

    public function test_public_online_tests_branch_filter_and_visibility()
    {
        // Public gateway loads
        $response = $this->get('/online-tests');
        $response->assertStatus(200);
        $response->assertSee('Bangladesh Army');

        // Filter by branch
        $armyResponse = $this->get('/online-tests?branch=army');
        $armyResponse->assertStatus(200);
        $armyResponse->assertSee('Army');
    }

    public function test_future_scheduled_exam_is_locked_for_cadet()
    {
        $cadet = User::where('role', 'academic_student')->first();

        $futureExam = Exam::create([
            'title' => 'Future Locked Mock Exam ' . time(),
            'slug' => 'future-locked-mock-' . time(),
            'branch' => 'army',
            'category' => 'Verbal IQ',
            'exam_type' => 'iq_mcq',
            'duration_minutes' => 30,
            'total_marks' => 10,
            'pass_marks' => 5,
            'status' => 'scheduled',
            'schedule_start' => Carbon::now()->addHours(2),
            'is_public_for_external' => true,
        ]);

        // Attempting to start future locked exam should redirect with lock error
        $startResponse = $this->actingAs($cadet)->get("/cadet/exams/{$futureExam->id}/start");
        $startResponse->assertRedirect(route('cadet.exams.index'));
        $startResponse->assertSessionHas('error');
        $futureExam->delete();
    }

    public function test_admin_can_end_exam_now()
    {
        $admin = User::where('role', 'super_admin')->first();
        $exam = Exam::create([
            'title' => 'Test Exam End Now ' . time(),
            'slug' => 'test-exam-end-now-' . time(),
            'branch' => 'army',
            'category' => 'Verbal IQ',
            'exam_type' => 'iq_mcq',
            'duration_minutes' => 30,
            'total_marks' => 10,
            'pass_marks' => 5,
            'status' => 'open',
            'is_public_for_external' => true,
        ]);

        $response = $this->actingAs($admin)->post("/admin/exams/{$exam->id}/end-now");
        $response->assertRedirect();

        $exam->refresh();
        $this->assertEquals('closed', $exam->status);
        $this->assertNotNull($exam->schedule_end);
        $exam->delete();
    }

    public function test_admin_can_update_schedule_and_controls()
    {
        $admin = User::where('role', 'super_admin')->first();
        $exam = Exam::create([
            'title' => 'Test Exam Schedule Update ' . time(),
            'slug' => 'test-exam-schedule-update-' . time(),
            'branch' => 'army',
            'category' => 'Verbal IQ',
            'exam_type' => 'iq_mcq',
            'duration_minutes' => 30,
            'total_marks' => 10,
            'pass_marks' => 5,
            'status' => 'draft',
            'is_public_for_external' => false,
        ]);

        $start = Carbon::now()->addHours(3)->setSecond(0);
        $end = Carbon::now()->addHours(5)->setSecond(0);

        $response = $this->actingAs($admin)->post("/admin/exams/{$exam->id}/update-schedule", [
            'schedule_start' => $start->format('Y-m-d\TH:i'),
            'schedule_end' => $end->format('Y-m-d\TH:i'),
            'status' => 'scheduled',
            'is_public_for_external' => 1,
        ]);
        $response->assertRedirect();

        $exam->refresh();
        $this->assertEquals('scheduled', $exam->status);
        $this->assertTrue($exam->is_public_for_external);
        $this->assertNotNull($exam->schedule_start);
        $this->assertNotNull($exam->schedule_end);
        $exam->delete();
    }

    public function test_mcq_parser_supports_bangla_and_equations()
    {
        $parser = new McqPdfParserService();
        $banglaMathText = <<<'TEXT'
১. ম্যাট্রিক্স $\begin{pmatrix} 1 & 2 \\ 3 & 4 \end{pmatrix}$ এর নির্ণায়ক (determinant) কত?
ক) -2
খ) 2
গ) 0
ঘ) 4
উত্তর: ক
ব্যাখ্যা: $1 \times 4 - 2 \times 3 = 4 - 6 = -2$।

২. $\int_0^1 2x \, dx$ এর মান কত?
A) 0
B) 1
C) 2
D) 0.5
Ans: B
TEXT;

        $res = $parser->parseText($banglaMathText);
        $this->assertEquals(2, $res['total_detected']);
        $this->assertEquals(2, $res['valid_count']);
        $this->assertEquals('A', $res['questions'][0]['correct_answer']);
        $this->assertStringContainsString('pmatrix', $res['questions'][0]['question_text']);
        $this->assertEquals('-2', $res['questions'][0]['option_a']);
        $this->assertEquals('B', $res['questions'][1]['correct_answer']);
        $this->assertStringContainsString('\int', $res['questions'][1]['question_text']);
    }

    public function test_stem_ocr_parser_supports_advanced_latex_matrices_and_diagrams()
    {
        $parser = new McqPdfParserService();
        $stemOcrText = <<<'STEM'
### Question 1:
Solve the definite integral: $\int_0^x 2t \, dt = 16$
A) $x = 2$
B) $x = 4$
C) $x = 8$
D) $x = \pm 4$
Ans: B
Explanation: $\int_0^x 2t \, dt = [t^2]_0^x = x^2 = 16 \implies x = 4$.

**Question 2:**
Given matrix $A = \begin{pmatrix} 2 & 1 \\ 4 & 3 \end{pmatrix}$, compute $\det(A)$.
- A) 2
- B) -2
- C) 10
- D) 0
**Answer:** A
**Solution:** $\det(A) = 2(3) - 1(4) = 2$.

Question 3:
A particle travels with velocity $\vec{v}(t) = 3t^2 \hat{i} + 2t \hat{j}$.
[Diagram: Trajectory showing curve tangent at t=2s]
(A) $12\hat{i} + 2\hat{j} \, \text{m/s}^2$
(B) $6\hat{i} + 2\hat{j} \, \text{m/s}^2$
(C) $14 \, \text{m/s}^2$
(D) None of the above
Ans: A
STEM;

        $res = $parser->parseText($stemOcrText);
        $this->assertEquals(3, $res['total_detected']);
        $this->assertEquals(3, $res['valid_count']);
        $this->assertEquals('B', $res['questions'][0]['correct_answer']);
        $this->assertEquals('A', $res['questions'][1]['correct_answer']);
        $this->assertEquals('A', $res['questions'][2]['correct_answer']);
        $this->assertStringContainsString('\int_0^x', $res['questions'][0]['question_text']);
        $this->assertStringContainsString('\begin{pmatrix}', $res['questions'][1]['question_text']);
        $this->assertStringContainsString('[Diagram: Trajectory showing curve tangent at t=2s]', $res['questions'][2]['question_text']);
    }

    public function test_stem_ocr_parser_matrix_synthesis_and_bengali_linear_systems()
    {
        $parser = new McqPdfParserService();
        $rawText = <<<'TXT'
9. \lambda এর কোন মানের জন্য নিম্নের সমীকরণজোটের কোনো সমাধান নেই?
x + y + z = 1
x + 2y + 2z = 3
x + 2y + \lambda z = 4
a) 3
b) -3
c) 0
d) 11
Ans: a

10. [[1, 2], [3, 4]] [[x, y], [z, w]] = [[1, 0], [0, 1]] হলে, (x, y, z, w) এর মান কত?
A) (-2, 1, 3, 1/2)
B) (2, -1, 3/2, -1)
C) (-2, 1, 3/2, -1/2)
D) (-2, 0, 3/2, 0)
Ans: C
TXT;

        $res = $parser->parseText($rawText);

        $this->assertEquals(2, $res['total_detected']);
        $this->assertEquals(2, $res['valid_count']);
        $this->assertEquals(0, $res['flagged_count']);

        // Question 1 assertions
        $q1 = $res['questions'][0];
        $this->assertTrue($q1['is_valid']);
        $this->assertEquals('A', $q1['correct_answer']);
        $this->assertStringContainsString('$\\lambda$', $q1['question_text']);
        $this->assertStringContainsString('কোনো সমাধান নেই?', $q1['question_text']);
        $this->assertStringContainsString('x + y + z = 1', $q1['question_text']);
        $this->assertEquals('3', $q1['option_a']);
        $this->assertEquals('-3', $q1['option_b']);
        $this->assertEquals('0', $q1['option_c']);
        $this->assertEquals('11', $q1['option_d']);
        $this->assertNull($q1['explanation']);

        // Question 2 assertions
        $q2 = $res['questions'][1];
        $this->assertTrue($q2['is_valid']);
        $this->assertEquals('C', $q2['correct_answer']);
        $this->assertStringContainsString('\\begin{pmatrix} 1 & 2 \\\\ 3 & 4 \\end{pmatrix}', $q2['question_text']);
        $this->assertStringContainsString('\\begin{pmatrix} x & y \\\\ z & w \\end{pmatrix}', $q2['question_text']);
        $this->assertStringContainsString('\\begin{pmatrix} 1 & 0 \\\\ 0 & 1 \\end{pmatrix}', $q2['question_text']);
        $this->assertEquals('(-2, 1, 3, 1/2)', $q2['option_a']);
        $this->assertEquals('(2, -1, 3/2, -1)', $q2['option_b']);
        $this->assertEquals('(-2, 1, 3/2, -1/2)', $q2['option_c']);
        $this->assertEquals('(-2, 0, 3/2, 0)', $q2['option_d']);
    }

    public function test_stem_ocr_gaussian_integral_and_unicode_greek_and_inline_options()
    {
        $parser = new McqPdfParserService();

        // Exact input with display math and options touching delimiters
        $gaussianInput = <<<'GAUSS'
7. Differentiating Under the Integral SignFor a positive parameter $\alpha > 0$, evaluate the Gaussian moment integral:$$\int_{-\infty}^{\infty} x^2 e^{-\alpha x^2} \, dx$$A) $\frac{1}{2}\sqrt{\frac{\pi}{\alpha^3}}$B) $\sqrt{\frac{\pi}{\alpha^3}}$C) $\frac{1}{4}\sqrt{\frac{\pi}{\alpha}}$D) $\frac{\sqrt{\pi}}{2\alpha}$
Ans: A
GAUSS;

        $res1 = $parser->parseText($gaussianInput);
        $this->assertEquals(1, $res1['total_detected']);
        $this->assertEquals(1, $res1['valid_count']);
        $q1 = $res1['questions'][0];
        $this->assertTrue($q1['is_valid']);
        $this->assertEquals('A', $q1['correct_answer']);
        $this->assertStringContainsString('$$\int_{-\infty}^{\infty} x^2 e^{-\alpha x^2} \, dx$$', $q1['question_text']);
        // Must NOT contain nested dollars
        $this->assertStringNotContainsString('$\frac{$\pi$}', $q1['option_a']);
        $this->assertEquals('$\frac{1}{2}\sqrt{\frac{\pi}{\alpha^3}}$', $q1['option_a']);
        $this->assertEquals('$\sqrt{\frac{\pi}{\alpha^3}}$', $q1['option_b']);
        $this->assertEquals('$\frac{1}{4}\sqrt{\frac{\pi}{\alpha}}$', $q1['option_c']);
        $this->assertEquals('$\frac{\sqrt{\pi}}{2\alpha}$', $q1['option_d']);

        // Exact input with raw Unicode Greek λ
        $unicodeInput = <<<'UNICODE'
9. λ এর কোন মানের জন্য নিম্নের সমীকরণজোটের কোনো সমাধান নেই? x + y + z = 1; x + 2y + 2z = 3; x + 2y + λz = 4
A) 3
B) -3
C) 0
D) 11
Ans: A
UNICODE;

        $res2 = $parser->parseText($unicodeInput);
        $this->assertEquals(1, $res2['total_detected']);
        $this->assertEquals(1, $res2['valid_count']);
        $q2 = $res2['questions'][0];
        $this->assertTrue($q2['is_valid']);
        $this->assertStringContainsString('$\\lambda$', $q2['question_text']);
        $this->assertStringContainsString('কোনো সমাধান নেই?', $q2['question_text']);
        $this->assertNull($q2['explanation']);
        $this->assertEquals('3', $q2['option_a']);
        $this->assertEquals('-3', $q2['option_b']);
        $this->assertEquals('0', $q2['option_c']);
        $this->assertEquals('11', $q2['option_d']);
    }

    public function test_bulk_handwritten_stem_latex_mcq_splitting_and_option_preservation()
    {
        $parser = new McqPdfParserService();
        $path = base_path('storage/logs/last_raw_input.txt');
        if (!file_exists($path)) {
            $this->markTestSkipped('LaTeX sample log file not present.');
        }
        $rawInput = file_get_contents($path);
        if (!str_contains($rawInput, '\det(\lambda I - A)')) {
            $this->markTestSkipped('LaTeX sample log not present in last_raw_input.txt.');
        }

        $res = $parser->parseText($rawInput);

        // Assert all 10 questions were successfully split and detected
        $this->assertEquals(10, $res['total_detected']);
        $this->assertCount(10, $res['questions']);

        // Assert Q1 correctly extracted its own options instead of confusing \det(\lambda I - A) with Option A
        $q1 = $res['questions'][0];
        $this->assertStringContainsString('\det(\lambda I - A)', $q1['question_text']);
        $this->assertStringContainsString('**Characteristic Polynomial and Matrix Trace**', $q1['question_text']);
        $this->assertEquals('$0$', $q1['option_a']);
        $this->assertEquals('$\frac{3}{4}$', $q1['option_b']);
        $this->assertEquals('$-1$', $q1['option_c']);
        $this->assertEquals('$1$', $q1['option_d']);

        // Assert Q2 correctly split across '---' markdown divider
        $q2 = $res['questions'][1];
        $this->assertStringContainsString('\oint_{\vert{}z\vert{}=2}', $q2['question_text']);
        $this->assertEquals('$0$', $q2['option_a']);
        $this->assertEquals('$2\pi i$', $q2['option_b']);
        $this->assertEquals('$2\pi i (e - 1)$', $q2['option_c']);
        $this->assertEquals('$2\pi i e$', $q2['option_d']);

        // Assert Q7 contains display Gaussian integral
        $q7 = $res['questions'][6];
        $this->assertStringContainsString('\int_{-\infty}^{\infty} x^2 e^{-\alpha x^2} \, dx', $q7['question_text']);
        $this->assertEquals('$\frac{1}{2}\sqrt{\frac{\pi}{\alpha^3}}$', $q7['option_a']);

        // Assert Q8 contains piecewise cases environment
        $q8 = $res['questions'][7];
        $this->assertStringContainsString('\begin{cases}', $q8['option_a']);
        $this->assertStringContainsString('\begin{cases}', $q8['option_b']);

        // Assert Q10 is extracted as the 10th question
        $q10 = $res['questions'][9];
        $this->assertStringContainsString('Jacobi\'s Formula in Lie Theory', $q10['question_text']);
        $this->assertEquals('$\exp(\operatorname{tr}(M))$', $q10['option_a']);
        $this->assertEquals('$\operatorname{tr}(\exp(M))$', $q10['option_b']);
        $this->assertEquals('$\exp(\det(M))$', $q10['option_c']);
        $this->assertEquals('$\det(M) \cdot \operatorname{tr}(M)$', $q10['option_d']);
    }

    public function test_exam_attempt_and_answer_accessors()
    {
        $exam = Exam::create([
            'title' => 'Test Exam Attempt Accessors ' . uniqid(),
            'slug' => 'test-exam-attempt-accessors-' . uniqid(),
            'exam_type' => 'iq_mcq',
            'category' => 'Verbal IQ',
            'branch' => 'army',
            'duration_minutes' => 60,
            'total_marks' => 50,
            'pass_marks' => 20,
            'status' => 'open',
        ]);
        $user = User::where('role', 'academic_student')->first() ?? User::first();

        $attempt = ExamAttempt::create([
            'exam_id' => $exam->id,
            'user_id' => $user->id,
            'attempt_number' => 999,
            'started_at' => Carbon::now()->subMinutes(20),
            'completed_at' => Carbon::now(),
            'time_spent_seconds' => 1200,
            'score' => 45.0,
            'total_marks' => 50.0,
            'percentage' => 90.0,
            'result_status' => 'passed',
            'status' => 'submitted',
            'answers_summary' => [
                'correct' => 18,
                'incorrect' => 2,
                'unanswered' => 0,
            ],
        ]);

        $this->assertTrue($attempt->is_passed);
        $this->assertEquals(18, $attempt->correct_answers);
        $this->assertEquals(2, $attempt->wrong_answers);
        $this->assertNotNull($attempt->submitted_at);

        $answer = ExamAttemptAnswer::create([
            'attempt_id' => $attempt->id,
            'user_answer' => 'C',
            'is_correct' => false,
            'marks_awarded' => -0.5,
        ]);

        $this->assertEquals('C', $answer->selected_option);
        $this->assertEquals('C', $answer->text_answer);
        $this->assertEquals(0.5, $answer->marks_deducted);

        $attempt->delete();
        $exam->delete();
    }

    public function test_admin_can_update_exam_name_and_settings()
    {
        $admin = User::where('role', 'super_admin')->first();
        $exam = Exam::create([
            'title' => 'Original Army Exam Name ' . time(),
            'slug' => 'original-army-exam-name-' . time(),
            'branch' => 'army',
            'category' => 'Verbal IQ',
            'exam_type' => 'iq_mcq',
            'duration_minutes' => 30,
            'total_marks' => 20,
            'pass_marks' => 10,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($admin)->put("/admin/exams/{$exam->id}", [
            'title' => 'Renamed Special Forces IQ Exam',
            'branch' => 'army',
            'category' => 'Advanced Spatial IQ',
            'duration_minutes' => 45,
            'total_marks' => 50,
            'pass_marks' => 25,
            'negative_marking_per_wrong' => 0.5,
            'fee' => 0,
            'status' => 'open',
            'description' => 'Updated exam description',
            'instructions' => 'Updated instructions',
        ]);

        $response->assertRedirect(route('admin.exams.branch', 'army'));

        $exam->refresh();
        $this->assertEquals('Renamed Special Forces IQ Exam', $exam->title);
        $this->assertEquals('Advanced Spatial IQ', $exam->category);
        $this->assertEquals(45, $exam->duration_minutes);
        $this->assertEquals('open', $exam->status);
        $exam->delete();
    }

    public function test_admin_can_scan_questions_via_json_endpoint()
    {
        $admin = User::where('role', 'super_admin')->first();
        $sampleText = <<<'TEXT'
1. What is the military rank directly above Major?
A) Captain
B) Lieutenant Colonel
C) Colonel
D) Brigadier
Ans: B

2. What is the standard military march cadence in paces per minute?
A) 60
B) 120
C) 180
D) 90
Ans: B
TEXT;

        $response = $this->actingAs($admin)->postJson('/admin/exams/branch/army/scan-pdf', [
            'raw_text' => $sampleText,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'total_detected' => 2,
            'valid_count' => 2,
        ]);
    }

    public function test_admin_can_scan_stem_math_with_matrices_and_gaussian_integral()
    {
        $admin = User::where('role', 'super_admin')->first();
        $sampleText = <<<'TEXT'
1. For a positive parameter $\alpha > 0$, evaluate the Gaussian moment integral:$$\int_{-\infty}^{\infty} x^2 e^{-\alpha x^2} \, dx$$A) $\frac{1}{2}\sqrt{\frac{\pi}{\alpha^3}}$B) $\sqrt{\frac{\pi}{\alpha^3}}$C) $\frac{1}{4}\sqrt{\frac{\pi}{\alpha}}$D) $\frac{\sqrt{\pi}}{2\alpha}$
Ans: A

2. [[1, 2], [3, 4]] [[x, y], [z, w]] = [[1, 0], [0, 1]] হলে, (x, y, z, w) এর মান কত?
A) (-2, 1, 3, 1/2)
B) (2, -1, 3/2, -1)
C) (-2, 1, 3/2, -1/2)
D) (-2, 0, 3/2, 0)
Ans: C
TEXT;

        $response = $this->actingAs($admin)->postJson('/admin/exams/branch/army/scan-pdf', [
            'raw_text' => $sampleText,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'total_detected' => 2,
            'valid_count' => 2,
        ]);
        $data = $response->json();
        $this->assertStringContainsString('$$\int_{-\infty}^{\infty} x^2 e^{-\alpha x^2} \, dx$$', $data['questions'][0]['question_text']);
        $this->assertEquals('$\frac{1}{2}\sqrt{\frac{\pi}{\alpha^3}}$', $data['questions'][0]['option_a']);
        $this->assertStringContainsString('\begin{pmatrix} 1 & 2 \\\\ 3 & 4 \end{pmatrix}', $data['questions'][1]['question_text']);
        $this->assertEquals('C', $data['questions'][1]['correct_answer']);
    }

    public function test_admin_can_create_exam_from_scanned_pdf_questions_with_random_selection()
    {
        $admin = User::where('role', 'super_admin')->first();
        
        // Generate 15 sample questions
        $sampleQuestions = [];
        for ($i = 1; $i <= 15; $i++) {
            $sampleQuestions[] = [
                'index' => $i,
                'question_text' => "Sample Scanned Question #{$i} for Testing?",
                'option_a' => "Option A {$i}",
                'option_b' => "Option B {$i}",
                'option_c' => "Option C {$i}",
                'option_d' => "Option D {$i}",
                'correct_answer' => 'A',
                'explanation' => "Explanation for question {$i}",
                'is_valid' => true,
            ];
        }

        // Request 5 random questions out of 15
        $response = $this->actingAs($admin)->post('/admin/exams/branch/army/create-from-pdf', [
            'title' => 'Random 5 from 15 Scanned Exam ' . time(),
            'category' => 'Verbal IQ',
            'exam_type' => 'iq_mcq',
            'duration_minutes' => 60, // 1 hour
            'question_count' => 5,
            'marks_per_question' => 1.0,
            'negative_marking_per_wrong' => 0.25,
            'pass_marks' => 3,
            'fee' => 0,
            'status' => 'scheduled',
            'schedule_start' => Carbon::now()->addHours(2)->format('Y-m-d\TH:i'),
            'schedule_end' => Carbon::now()->addHours(4)->format('Y-m-d\TH:i'),
            'description' => 'Test exam created via PDF wizard',
            'instructions' => 'No calculators allowed',
            'questions_json' => json_encode($sampleQuestions),
            'is_public_for_external' => 1,
        ]);

        $response->assertRedirect(route('admin.exams.branch', ['branch' => 'army', 'tab' => 'control']));

        $createdExam = Exam::where('title', 'like', 'Random 5 from 15 Scanned Exam%')->latest('id')->first();
        $this->assertNotNull($createdExam);
        $this->assertEquals(60, $createdExam->duration_minutes);
        $this->assertEquals(5, $createdExam->questions()->count()); // exactly 5 questions selected!
        $this->assertEquals(5.0, $createdExam->total_marks);
        $this->assertTrue($createdExam->is_public_for_external);
        $this->assertEquals('scheduled', $createdExam->status);

        $createdExam->questions()->delete();
        $createdExam->delete();
    }

    public function test_admin_can_delete_exam_asynchronously_without_page_reload()
    {
        $admin = User::where('role', 'super_admin')->first() ?? User::first();

        $exam = Exam::create([
            'title' => 'Test Exam for Instant Delete ' . uniqid(),
            'slug' => 'test-exam-delete-' . uniqid(),
            'exam_type' => 'iq_mcq',
            'category' => 'Verbal IQ',
            'branch' => 'army',
            'duration_minutes' => 30,
            'total_marks' => 20,
            'pass_marks' => 10,
            'status' => 'open',
        ]);

        $examId = $exam->id;

        // AJAX JSON Delete Request (zero page reload)
        $response = $this->actingAs($admin)
            ->deleteJson(route('admin.exams.destroy', $examId));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'deleted_id' => $examId,
            ]);

        $this->assertDatabaseMissing('exams', ['id' => $examId]);
    }

    public function test_cadet_exam_submission_by_admin_and_student_without_404()
    {
        $admin = User::where('role', 'super_admin')->first() ?? User::first();

        $exam = Exam::create([
            'title' => 'Test Exam Submission 404 Prevention ' . uniqid(),
            'slug' => 'test-submission-' . uniqid(),
            'exam_type' => 'academic_mcq',
            'category' => 'Mathematics',
            'branch' => 'army',
            'duration_minutes' => 30,
            'total_marks' => 10,
            'pass_marks' => 5,
            'status' => 'open',
            'is_paid_for_external' => true,
        ]);

        $q = Question::create([
            'exam_id' => $exam->id,
            'question_text' => "**Matrix Determinant**\n\nFind determinant of:\n\\begin{pmatrix} 2 & 0 \\\\ 0 & 3 \\end{pmatrix}",
            'options' => [
                ['key' => 'A', 'text' => '$6$'],
                ['key' => 'B', 'text' => '$5$'],
                ['key' => 'C', 'text' => '$0$'],
                ['key' => 'D', 'text' => '$1$'],
            ],
            'correct_answer' => 'A',
            'marks' => 10.00,
            'negative_marks' => 0.25,
            'order_index' => 1,
        ]);

        // Start exam as admin (user without a student model record)
        $startResponse = $this->actingAs($admin)->get("/cadet/exams/{$exam->id}/start");
        $startResponse->assertStatus(200);

        // Submit exam answers
        $submitResponse = $this->actingAs($admin)->post("/cadet/exams/{$exam->id}/submit", [
            'answers' => [$q->id => 'A'],
        ]);

        // Must redirect to result page and NOT 404
        // Must redirect to cadet.exams.index with success message
        $submitResponse->assertStatus(302);
        $submitResponse->assertRedirect(route('cadet.exams.index', ['branch' => $exam->branch]));

        $attempt = ExamAttempt::where('exam_id', $exam->id)->latest('id')->first();
        $this->assertNotNull($attempt);
        $this->assertEquals('submitted', $attempt->status);
        $this->assertEquals(10.0, $attempt->score);
        $this->assertEquals(1, $attempt->rank);
        $this->assertEquals(1, $attempt->total_candidates);

        // While exam is live: rank is pending on index and not published on history
        $indexResponse = $this->actingAs($admin)->get(route('cadet.exams.index', ['branch' => 'army']));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Scorecard');
        $indexResponse->assertSee('Retake Test');
        $indexResponse->assertSee('Rank Pending');

        // Cadet Exam History page lists the submitted attempt with Not Published while live
        $historyResponse = $this->actingAs($admin)->get(route('cadet.exams.history'));
        $historyResponse->assertStatus(200);
        $historyResponse->assertSee($exam->title);
        $historyResponse->assertSee('PASSED');
        $historyResponse->assertSee('Not Published');
        $historyResponse->assertSee('All Exam History');

        // Once exam is closed/ended: rank IS published
        $exam->update(['status' => 'closed']);
        $exam->refresh();
        $attempt->refresh();
        ExamAttempt::clearRankCache();

        $historyEnded = $this->actingAs($admin)->get(route('cadet.exams.history'));
        $historyEnded->assertStatus(200);
        $historyEnded->assertSee('Rank #1');

        // View result page directly
        $resultResponse = $this->actingAs($admin)->get("/cadet/exams/attempts/{$attempt->id}/result");
        $resultResponse->assertStatus(200);
        $resultResponse->assertSee('Matrix Determinant');
        $resultResponse->assertSee('QUALIFIED');
        $resultResponse->assertSee('Candidate Rank');

        // Test idempotency: re-submitting an already submitted exam redirects to cadet.exams.index without 404
        $reSubmitResponse = $this->actingAs($admin)->post("/cadet/exams/{$exam->id}/submit", [
            'answers' => [$q->id => 'A'],
        ]);
        $reSubmitResponse->assertStatus(302);
        $reSubmitResponse->assertRedirect(route('cadet.exams.index', ['branch' => $exam->branch]));

        $q->delete();
        $attempt->answers()->delete();
        $attempt->delete();
        $exam->delete();
    }

    public function test_stem_matrix_and_markdown_heading_formatting()
    {
        $raw = "**Matrix Eigenvalues**\n\nFind the determinant and trace:\n\\begin{pmatrix} 1 & 2 \\\\ 3 & 4 \\end{pmatrix}\n\nwhere $\\det(A) = -2$.";
        $rendered = McqPdfParserService::renderStemContent($raw);

        // Bold heading converted to clean styled block
        $this->assertStringContainsString('Matrix Eigenvalues', $rendered);
        $this->assertStringNotContainsString('**Matrix Eigenvalues**', $rendered);
        $this->assertStringContainsString('display: block;', $rendered);

        // Bare matrix converted to math mode
        $this->assertStringContainsString('begin{pmatrix}', $rendered);

        // Operator spacing converted \det( -> \det\,(
        $this->assertStringContainsString('\\det\\,(A)', $rendered);
    }

    public function test_cadet_exam_history_branch_tabs_and_unenrolled_course_view()
    {
        $user = User::factory()->create(['role' => 'academic_student']);
        $armyCourse = Course::create([
            'title' => 'Army Preliminary Course ' . uniqid(),
            'slug' => 'army-course-' . uniqid(),
            'category' => 'Army Preliminary',
            'admission_status' => 'open',
            'fee' => 5000.00,
        ]);
        $navyCourse = Course::create([
            'title' => 'Navy Officer Course ' . uniqid(),
            'slug' => 'navy-course-' . uniqid(),
            'category' => 'Navy Officer',
            'admission_status' => 'open',
            'fee' => 6000.00,
        ]);

        $student = Student::create([
            'user_id' => $user->id,
            'student_id_code' => 'IDA-TEST-' . uniqid(),
            'current_course_id' => $armyCourse->id,
            'target_wing' => 'army',
        ]);
        $student->courses()->attach($armyCourse->id, ['enrolled_at' => now(), 'status' => 'active', 'payment_status' => 'paid']);

        $exam = Exam::create([
            'title' => 'Cadet History Test ' . uniqid(),
            'slug' => 'history-test-' . uniqid(),
            'exam_type' => 'academic_mcq',
            'category' => 'Army Preliminary',
            'branch' => 'army',
            'duration_minutes' => 30,
            'total_marks' => 20,
            'pass_marks' => 10,
            'status' => 'open',
            'is_paid_for_external' => true,
        ]);

        $q = Question::create([
            'exam_id' => $exam->id,
            'question_text' => "Sample Question",
            'options' => [
                ['key' => 'A', 'text' => 'Option 1'],
                ['key' => 'B', 'text' => 'Option 2'],
            ],
            'correct_answer' => 'A',
            'marks' => 10.00,
            'negative_marks' => 2.00,
            'order_index' => 1,
        ]);

        // Submit attempt with a wrong answer to test negative marking
        $attempt = ExamAttempt::create([
            'exam_id' => $exam->id,
            'user_id' => $user->id,
            'student_id' => $student->id,
            'attempt_number' => 1,
            'started_at' => Carbon::now()->subMinutes(15),
            'completed_at' => Carbon::now(),
            'time_spent_seconds' => 900,
            'score' => 8.00,
            'total_marks' => 20.00,
            'percentage' => 40.0,
            'result_status' => 'failed',
            'status' => 'submitted',
            'answers_summary' => [
                'correct' => 1,
                'incorrect' => 1,
                'unanswered' => 0,
                'negative_marks' => 2.00,
            ],
        ]);

        // Create a 2nd newer attempt to verify chronological ordering (latest on top)
        $newerExam = Exam::create([
            'title' => 'Newer Cadet Test ' . uniqid(),
            'slug' => 'newer-test-' . uniqid(),
            'exam_type' => 'academic_mcq',
            'category' => 'Army Preliminary',
            'branch' => 'army',
            'duration_minutes' => 30,
            'total_marks' => 20,
            'pass_marks' => 10,
            'status' => 'open',
            'is_paid_for_external' => true,
        ]);

        $attempt2 = ExamAttempt::create([
            'exam_id' => $newerExam->id,
            'user_id' => $user->id,
            'student_id' => $student->id,
            'attempt_number' => 2,
            'started_at' => Carbon::now(),
            'completed_at' => Carbon::now(),
            'time_spent_seconds' => 600,
            'score' => 18.00,
            'total_marks' => 20.00,
            'percentage' => 90.0,
            'result_status' => 'passed',
            'status' => 'submitted',
            'created_at' => Carbon::now(),
        ]);
        \DB::table('exam_attempts')->where('id', $attempt->id)->update([
            'created_at' => Carbon::now()->subDays(2),
            'started_at' => Carbon::now()->subDays(2),
        ]);

        // 1. All Exam History: shows the attempt, marks (10/50 format), Details button, and ranking status
        $allHistory = $this->actingAs($user)->get(route('cadet.exams.history'));
        $allHistory->assertStatus(200);
        $allHistory->assertSee('All Exam History');
        $allHistory->assertSee($exam->title);
        $allHistory->assertSee($newerExam->title);
        $allHistory->assertSee('Mark');
        $allHistory->assertSee('8/20'); // Mark format (score/total_marks)
        $allHistory->assertSee('Details'); // Details button
        $allHistory->assertSee('Not Published'); // candidate rank hidden while live

        // Verify Total Mark, Mark Achieved, and Minus Marking headers are removed from history table
        $allHistory->assertDontSee('Total Mark');
        $allHistory->assertDontSee('Mark Achieved');
        $allHistory->assertDontSee('Minus Marking');

        // Check Details view shows question paper, selected answers, corrected answers, and minus markings
        $detailsRes = $this->actingAs($user)->get(route('cadet.exams.result', $attempt->id));
        $detailsRes->assertStatus(200);
        $detailsRes->assertSee('Exam Details');
        $detailsRes->assertSee('-2.00'); // minus marking inside Details
        $detailsRes->assertSee('Correct Answer');
        $detailsRes->assertSee('Your Selection');

        // Verify Assessment Hub is NOT shown
        $allHistory->assertDontSee('Assessment Hub');
        // Verify summary metric cards (Total Attempts, Average Score) are NOT shown
        $allHistory->assertDontSee('Total Attempts');
        $allHistory->assertDontSee('Average Score');
        // Verify branches selector & filter results
        $allHistory->assertSee('Branches:');
        $allHistory->assertSee('Army');
        $allHistory->assertSee('Navy');
        $allHistory->assertSee('Air Force');
        $allHistory->assertSee('Police');
        // Verify filter exam results buttons are removed
        $allHistory->assertDontSee('Filter Results:');
        $allHistory->assertDontSee('Passed Only');

        // Verify Find Exam option with Exam Name search, OR separator, and Find by Date
        $allHistory->assertSee('Find Exam');
        $allHistory->assertSee('Search by exam name...');
        $allHistory->assertSee('OR');
        $allHistory->assertSee('Find by Date:');

        // Verify latest exam appears on top (chronological timing ordering)
        $html = $allHistory->getContent();
        $this->assertTrue(strpos($html, $newerExam->title) < strpos($html, $exam->title), 'Latest exam should appear before earlier exam');

        // 2. Date filtering: query by today's date should return newer exam and not older exam
        $todayStr = Carbon::now()->toDateString();
        $dateFiltered = $this->actingAs($user)->get(route('cadet.exams.history', ['date' => $todayStr]));
        $dateFiltered->assertStatus(200);
        $dateFiltered->assertSee($newerExam->title);
        $dateFiltered->assertDontSee($exam->title);

        // 3. Topic filtering: searching by topic keyword returns matching exam and not others
        $keyword = explode(' ', $newerExam->title)[0];
        $topicFiltered = $this->actingAs($user)->get(route('cadet.exams.history', ['topic' => $newerExam->title]));
        $topicFiltered->assertStatus(200);
        $topicFiltered->assertSee($newerExam->title);
        $topicFiltered->assertDontSee($exam->title);

        // 3. Enrolled sector (Army): shows attempt
        $armyHistory = $this->actingAs($user)->get(route('cadet.exams.history', ['branch' => 'army']));
        $armyHistory->assertStatus(200);
        $armyHistory->assertSee($exam->title);
        $armyHistory->assertSee($newerExam->title);
        $armyHistory->assertSee('8/20');
        $armyHistory->assertSee('Details');

        // 4. Unenrolled sector (Navy): shows Ongoing Courses promo banner & Navy courses
        $navyHistory = $this->actingAs($user)->get(route('cadet.exams.history', ['branch' => 'navy']));
        $navyHistory->assertStatus(200);
        $navyHistory->assertSee('Not Enrolled');
        $navyHistory->assertSee('Ongoing Courses in Bangladesh Navy');
        $navyHistory->assertSee($navyCourse->title);

        $attempt2->delete();
        $newerExam->delete();
        $attempt->delete();
        $q->delete();
        $exam->delete();
        $student->courses()->detach();
        $student->delete();
        $armyCourse->delete();
        $navyCourse->delete();
        $user->delete();
    }

    public function test_cadet_modules_hidden_without_removing_code()
    {
        $user = User::factory()->create([
            'role' => 'academic_student',
            'email' => 'hide_test_cadet@example.com',
        ]);
        $student = Student::create([
            'user_id' => $user->id,
            'student_id_code' => 'STU-HIDE-99',
            'roll_number' => 'HIDE99',
            'target_wing' => 'Army',
        ]);

        // Dashboard shows Exam & Exam History, but hides Routine, Payment, Question Bank, and Other Courses
        $dashRes = $this->actingAs($user)->get(route('cadet.dashboard'));
        $dashRes->assertStatus(200);
        $dashRes->assertSee(route('cadet.exams.index'));
        $dashRes->assertSee(route('cadet.exams.history'));
        $dashRes->assertDontSee(route('cadet.routine'));
        $dashRes->assertDontSee(route('cadet.fees'));
        $dashRes->assertDontSee('Previous Year Question');

        // Routine page renders clean placeholder; table is hidden
        $routineRes = $this->actingAs($user)->get(route('cadet.routine'));
        $routineRes->assertStatus(200);
        $routineRes->assertSee('Routine Section Currently Hidden');
        $routineRes->assertDontSee('Today\'s Drills', false);

        // Fees page renders clean placeholder; invoice table is hidden
        $feesRes = $this->actingAs($user)->get(route('cadet.fees'));
        $feesRes->assertStatus(200);
        $feesRes->assertSee('Payment Section Currently Hidden');
        $feesRes->assertDontSee('Issued Fee Invoices', false);

        // Attendance page renders clean placeholder; table is hidden
        $attRes = $this->actingAs($user)->get(route('cadet.attendance'));
        $attRes->assertStatus(200);
        $attRes->assertSee('Attendance Section Currently Hidden');

        // Dossier page renders clean placeholder; radar canvas is hidden
        $dosRes = $this->actingAs($user)->get(route('cadet.dossier'));
        $dosRes->assertStatus(200);
        $dosRes->assertSee('Cadet Dossier Currently Hidden');

        $student->delete();
        $user->delete();
    }

    public function test_candidate_rank_published_only_after_schedule_ends_and_minimum_qualification_setting()
    {
        $admin = User::where('role', 'super_admin')->first();
        $user = User::factory()->create(['role' => 'academic_student']);
        $student = Student::create([
            'user_id' => $user->id,
            'student_id_code' => 'STU-RANK-' . uniqid(),
            'target_wing' => 'Army',
        ]);

        // 1. Create an exam scheduled between 3:00 PM and 8:00 PM (end time in future)
        $exam = Exam::create([
            'title' => 'Scheduled Flight Aptitude ' . uniqid(),
            'slug' => 'flight-aptitude-' . uniqid(),
            'branch' => 'army',
            'category' => 'Aeronautics',
            'duration_minutes' => 30,
            'total_marks' => 30,
            'pass_marks' => 15, // Minimum result for qualification
            'status' => 'open',
            'schedule_start' => Carbon::now()->subHours(2),
            'schedule_end' => Carbon::now()->addHours(3), // Still ongoing!
            'is_paid_for_external' => true,
        ]);

        $attempt = ExamAttempt::create([
            'exam_id' => $exam->id,
            'user_id' => $user->id,
            'student_id' => $student->id,
            'attempt_number' => 1,
            'started_at' => Carbon::now()->subMinutes(20),
            'completed_at' => Carbon::now(),
            'time_spent_seconds' => 1200,
            'score' => 20.00, // Meets pass_marks -> Qualified
            'total_marks' => 30.00,
            'percentage' => 66.7,
            'result_status' => 'passed',
            'status' => 'submitted',
        ]);

        // Before schedule ends: rank is NOT published, scorecard is locked
        $this->assertFalse($exam->isRankPublished());
        $this->assertFalse($attempt->isRankPublished());

        $historyRes = $this->actingAs($user)->get(route('cadet.exams.history'));
        $historyRes->assertStatus(200);
        $historyRes->assertSee('Not Published');
        $historyRes->assertSee('Details');

        // Scorecard access while exam is live shows pending rank without rank number
        $scorecardRes = $this->actingAs($user)->get(route('cadet.exams.result', $attempt->id));
        $scorecardRes->assertStatus(200);
        $scorecardRes->assertSee('Rank: Pending Exam Schedule Conclusion');
        $scorecardRes->assertDontSee('Rank #1');

        // Add a second candidate to test multi-candidate mark comparison
        $stamp2 = uniqid();
        $user2 = User::factory()->create([
            'role' => 'academic_student',
            'name' => 'Candidate Second ' . $stamp2,
        ]);
        $student2 = Student::create([
            'user_id' => $user2->id,
            'student_id_code' => 'STU-RANK-' . $stamp2,
            'target_wing' => 'Army',
        ]);
        $attempt2 = ExamAttempt::create([
            'exam_id' => $exam->id,
            'user_id' => $user2->id,
            'student_id' => $student2->id,
            'attempt_number' => 1,
            'started_at' => Carbon::now()->subMinutes(15),
            'completed_at' => Carbon::now(),
            'time_spent_seconds' => 900,
            'score' => 14.00,
            'total_marks' => 30.00,
            'percentage' => 46.7,
            'result_status' => 'failed',
            'status' => 'submitted',
        ]);

        // 2. After schedule ends (past 8:00 PM): rank IS published, scorecard is visible
        $exam->update(['schedule_end' => Carbon::now()->subMinutes(5)]);
        $exam->refresh();
        $attempt->refresh();
        $attempt2->refresh();
        $this->assertTrue($exam->isRankPublished());
        $this->assertTrue($attempt->isRankPublished());
        $this->assertTrue($attempt2->isRankPublished());

        ExamAttempt::clearRankCache();
        $historyEndedRes = $this->actingAs($user)->get(route('cadet.exams.history'));
        $historyEndedRes->assertStatus(200);
        $historyEndedRes->assertSee('Rank #1');
        $historyEndedRes->assertDontSee('Not Published');
        $historyEndedRes->assertSee('Details');

        $scorecardEndedRes = $this->actingAs($user)->get(route('cadet.exams.result', $attempt->id));
        $scorecardEndedRes->assertStatus(200);
        $scorecardEndedRes->assertSee('Rank #1 of 2 Candidates');
        $scorecardEndedRes->assertSee('All Candidates Scorecard & Merit Position');
        // Candidate 1 (user) with score 20.00 and Rank 1
        $scorecardEndedRes->assertSee($user->name);
        $scorecardEndedRes->assertSee('20.00');
        $scorecardEndedRes->assertSee('Rank #1');
        $scorecardEndedRes->assertSee('YOU');
        // Candidate 2 (user2) with score 14.00 and Rank 2
        $scorecardEndedRes->assertSee($user2->name);
        $scorecardEndedRes->assertSee('14.00');
        $scorecardEndedRes->assertSee('Rank #2');

        // 3. Backend Create & Edit exam page displays Minimum Result for Qualification
        $createRes = $this->actingAs($admin)->get('/admin/exam-management/create');
        $createRes->assertStatus(200);
        $createRes->assertSee('Minimum Result for Qualification (Pass Mark) *');
        $createRes->assertSee('Result Connector');
        $createRes->assertSee('QUALIFIED');
        $createRes->assertSee('NOT QUALIFIED');

        $editRes = $this->actingAs($admin)->get("/admin/exam-management/{$exam->id}/edit");
        $editRes->assertStatus(200);
        $editRes->assertSee('Minimum Result for Qualification (Pass Mark) *');
        $editRes->assertSee('Result Connector');

        $attempt->delete();
        $attempt2->delete();
        $exam->delete();
        $student->delete();
        $student2->delete();
        $user->delete();
        $user2->delete();
    }
}
