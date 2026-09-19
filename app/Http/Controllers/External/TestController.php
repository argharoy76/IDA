<?php

namespace App\Http\Controllers\External;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Question;
use App\Models\WatWord;
use App\Models\ExamAttempt;
use App\Models\ExamAttemptAnswer;
use App\Models\Payment;
use App\Models\Student;
use Carbon\Carbon;

class TestController extends Controller
{
    public function index(Request $request)
    {
        $query = Exam::where('is_public_for_external', true)
            ->whereIn('status', ['open', 'scheduled']);

        if ($request->filled('branch')) {
            $query->where('branch', $request->branch);
        }

        $exams = $query->orderBy('schedule_start', 'asc')->get();
        return view('external.tests.index', compact('exams'));
    }

    public function purchase(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);
        $user = auth()->user();
        $student = Student::where('user_id', $user->id)->first();

        $validated = $request->validate([
            'amount' => 'required|numeric',
            'payment_method' => 'required|in:bkash,nagad,rocket,bank_transfer',
            'transaction_reference' => 'required|string|max:100',
        ]);

        $count = Payment::count() + 1;
        $payNumber = sprintf('PAY-%s-%04d', date('Y'), $count);

        Payment::create([
            'payment_number' => $payNumber,
            'student_id' => $student->id ?? null,
            'user_id' => $user->id,
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'transaction_reference' => $validated['transaction_reference'],
            'payment_date' => Carbon::today(),
            'verification_status' => 'pending',
            'admin_notes' => 'External test pass request for ' . $exam->title,
        ]);

        return redirect()->route('external.dashboard')->with('success', "Payment submitted for {$exam->title}. Once verified by our team, your test access will be unlocked!");
    }

    public function start($id)
    {
        $exam = Exam::with(['questions', 'watWords'])->findOrFail($id);
        $user = auth()->user();
        $student = Student::where('user_id', $user->id)->first();

        // Lock enforcement: if scheduled in future, deny access (unless admin previewing)
        if ($exam->isScheduledFuture() && !$user->isAdmin()) {
            return redirect()->route('online_tests')
                ->with('error', "Exam is scheduled to start at {$exam->schedule_start->format('h:i A')}. Please wait for the countdown timer.");
        }

        $attempt = ExamAttempt::where('exam_id', $exam->id)
            ->where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->first();

        if (!$attempt) {
            $attemptNumber = ExamAttempt::where('exam_id', $exam->id)
                ->where('user_id', $user->id)
                ->count() + 1;

            $attempt = ExamAttempt::create([
                'exam_id' => $exam->id,
                'user_id' => $user->id,
                'student_id' => $student->id ?? null,
                'attempt_number' => $attemptNumber,
                'started_at' => Carbon::now(),
                'status' => 'in_progress',
            ]);
        }

        $remainingSeconds = max(0, ($exam->duration_minutes * 60) - Carbon::now()->diffInSeconds($attempt->started_at));

        if ($remainingSeconds <= 0) {
            return $this->submit(request(), $id);
        }

        if (($exam->status === 'closed' || ($exam->status !== 'open' && $exam->schedule_end && Carbon::now()->isAfter($exam->schedule_end))) && !$user->isAdmin()) {
            return redirect()->route('online_tests')
                ->with('error', "This exam's scheduled time has already ended.");
        }

        if ($exam->exam_type === 'word_association') {
            return view('external.tests.wat_exam', compact('exam', 'attempt', 'remainingSeconds'));
        }

        return view('external.tests.mcq_exam', compact('exam', 'attempt', 'remainingSeconds'));
    }

    public function submit(Request $request, $id)
    {
        $exam = Exam::with(['questions', 'watWords'])->findOrFail($id);
        $user = auth()->user();

        $attempt = ExamAttempt::where('exam_id', $exam->id)
            ->where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->latest()
            ->firstOrFail();

        $completedAt = Carbon::now();
        $timeSpentSeconds = $completedAt->diffInSeconds($attempt->started_at);

        $attemptStatus = 'submitted';
        if ($timeSpentSeconds > ($exam->duration_minutes * 60) + 60) {
            $attemptStatus = 'auto_submitted';
        }
        if ($exam->status !== 'open' && $exam->schedule_end && Carbon::now()->isAfter($exam->schedule_end)) {
            $attemptStatus = 'auto_submitted';
        }

        if ($exam->exam_type === 'word_association') {
            $responses = $request->input('wat_responses', []);
            $responseTimes = $request->input('wat_times', []);

            foreach ($exam->watWords as $watWord) {
                $userText = $responses[$watWord->id] ?? '';
                $spent = (int) ($responseTimes[$watWord->id] ?? 15);

                ExamAttemptAnswer::create([
                    'attempt_id' => $attempt->id,
                    'wat_word_id' => $watWord->id,
                    'user_answer' => $userText,
                    'response_time_seconds' => $spent,
                    'marks_awarded' => 1.00,
                ]);
            }

            $attempt->update([
                'completed_at' => $completedAt,
                'time_spent_seconds' => $timeSpentSeconds,
                'score' => count($responses),
                'total_marks' => $exam->watWords->count(),
                'percentage' => round((count($responses) / max(1, $exam->watWords->count())) * 100, 1),
                'result_status' => 'under_evaluation',
                'status' => $attemptStatus,
                'answers_summary' => ['completed_words' => count($responses)],
            ]);

            return redirect()->route('external.tests.result', $attempt->id)->with('success', 'Word Association Test responses submitted successfully!');
        }

        $userAnswers = $request->input('answers', []);
        $correctCount = 0;
        $wrongCount = 0;
        $unansweredCount = 0;
        $earnedScore = 0.0;
        $totalMarksPossible = 0.0;

        foreach ($exam->questions as $question) {
            $totalMarksPossible += (float) $question->marks;
            $selectedChoice = $userAnswers[$question->id] ?? null;

            if (empty($selectedChoice)) {
                $unansweredCount++;
                $isCorrect = null;
                $marksAwarded = 0.0;
            } elseif ($selectedChoice === $question->correct_answer) {
                $correctCount++;
                $isCorrect = true;
                $marksAwarded = (float) $question->marks;
                $earnedScore += $marksAwarded;
            } else {
                $wrongCount++;
                $isCorrect = false;
                $marksAwarded = - (float) $question->negative_marks;
                $earnedScore += $marksAwarded;
            }

            ExamAttemptAnswer::create([
                'attempt_id' => $attempt->id,
                'question_id' => $question->id,
                'user_answer' => $selectedChoice,
                'is_correct' => $isCorrect,
                'marks_awarded' => $marksAwarded,
            ]);
        }

        $finalScore = max(0.0, round($earnedScore, 2));
        $percentage = ($totalMarksPossible > 0) ? round(($finalScore / $totalMarksPossible) * 100, 1) : 0.0;
        $resultStatus = ($finalScore >= (float) $exam->pass_marks) ? 'passed' : 'failed';

        $attempt->update([
            'completed_at' => $completedAt,
            'time_spent_seconds' => $timeSpentSeconds,
            'score' => $finalScore,
            'total_marks' => $totalMarksPossible,
            'percentage' => $percentage,
            'result_status' => $resultStatus,
            'status' => $attemptStatus,
            'answers_summary' => [
                'correct' => $correctCount,
                'incorrect' => $wrongCount,
                'unanswered' => $unansweredCount,
            ],
        ]);

        return redirect()->route('external.tests.result', $attempt->id)->with('success', 'Examination submitted! Review your scorecard below.');
    }

    public function result($attemptId)
    {
        $attempt = ExamAttempt::with(['exam.questions', 'exam.watWords', 'answers.question', 'answers.watWord'])
            ->where('id', $attemptId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('external.tests.result', compact('attempt'));
    }
}
