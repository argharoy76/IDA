<?php

namespace App\Http\Controllers\Cadet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Course;
use App\Models\Question;
use App\Models\WatWord;
use App\Models\ExamAttempt;
use App\Models\ExamAttemptAnswer;
use App\Models\Student;
use App\Models\PerformanceTimeline;
use Carbon\Carbon;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        if ($request->query('view') === 'history' || $request->has('history')) {
            return redirect()->route('cadet.exams.history');
        }

        $student = Student::with(['courses', 'currentCourse'])->where('user_id', auth()->id())->first();
        if (!$student && in_array(auth()->user()->role, ['super_admin', 'admin', 'instructor'])) {
            $student = Student::with(['courses', 'currentCourse'])->first();
        }
        if (!$student) {
            abort(404, 'No cadet student record found.');
        }

        $user = auth()->user();

        // Determine cadet's enrolled branches (Admins get all branches unlocked)
        if ($user->isAdmin() || in_array($user->role, ['super_admin', 'admin', 'instructor'])) {
            $enrolledBranches = ['army', 'navy', 'air_force', 'police'];
        } else {
            $enrolledBranches = $student->getEnrolledBranches();
        }

        // Selected branch tab (e.g. null for 'All Branches', or 'army', 'navy', 'air_force', 'police')
        $selectedBranch = $request->query('branch');
        $isEnrolledInSelectedBranch = true;
        $ongoingCourses = collect();

        if ($selectedBranch) {
            // Cadet selected a specific branch
            if (!in_array($selectedBranch, $enrolledBranches) && !$user->isAdmin()) {
                // Not enrolled in this sector: show ongoing courses for this sector
                $isEnrolledInSelectedBranch = false;
                $ongoingCourses = Course::forBranch($selectedBranch)
                    ->where('admission_status', 'open')
                    ->get();
                if ($ongoingCourses->isEmpty()) {
                    $ongoingCourses = Course::forBranch($selectedBranch)->get();
                }
                $exams = collect();
            } else {
                // Enrolled in this sector: show exams for this branch
                $exams = Exam::where('is_paid_for_external', true)
                    ->whereIn('status', ['open', 'scheduled', 'closed'])
                    ->where('branch', $selectedBranch)
                    ->orderBy('schedule_start', 'asc')
                    ->get();
            }
        } else {
            // "All Branches" or "All Exams"
            $query = Exam::where('is_paid_for_external', true)
                ->whereIn('status', ['open', 'scheduled', 'closed']);

            if (!$user->isAdmin() && !empty($enrolledBranches)) {
                $query->where(function ($q) use ($enrolledBranches) {
                    $q->whereIn('branch', $enrolledBranches)
                      ->orWhereNull('branch')
                      ->orWhere('branch', '')
                      ->orWhere('branch', 'general');
                });
            }

            $exams = $query->orderBy('schedule_start', 'asc')->get();
        }

        $user = auth()->user();
        $userAttempts = ExamAttempt::where(function ($q) use ($user, $student) {
            $q->where('user_id', $user->id);
            if ($student && $student->user_id === $user->id) {
                $q->orWhere('student_id', $student->id);
            }
        })
        ->whereIn('status', ['submitted', 'auto_submitted'])
        ->latest('id')
        ->get()
        ->groupBy('exam_id');

        return view('cadet.exams.index', compact(
            'student',
            'exams',
            'enrolledBranches',
            'selectedBranch',
            'isEnrolledInSelectedBranch',
            'ongoingCourses',
            'userAttempts'
        ));
    }

    public function history(Request $request)
    {
        $user = auth()->user();
        $student = Student::with(['courses', 'currentCourse'])->where('user_id', $user->id)->first();
        if (!$student && in_array($user->role, ['super_admin', 'admin', 'instructor'])) {
            $student = Student::with(['courses', 'currentCourse'])->first();
        }
        if (!$student) {
            abort(404, 'No cadet student record found.');
        }

        if ($user->isAdmin() || in_array($user->role, ['super_admin', 'admin', 'instructor'])) {
            $enrolledBranches = ['army', 'navy', 'air_force', 'police'];
        } else {
            $enrolledBranches = $student->getEnrolledBranches();
        }
        $selectedBranch = $request->query('branch');
        $isEnrolledInSelectedBranch = true;
        $ongoingCourses = collect();

        if ($selectedBranch && $selectedBranch !== 'all') {
            $hasBranchAttempts = ExamAttempt::where(function ($q) use ($user, $student) {
                    $q->where('user_id', $user->id);
                    if ($student && $student->user_id === $user->id) {
                        $q->orWhere('student_id', $student->id);
                    }
                })
                ->whereIn('status', ['submitted', 'auto_submitted'])
                ->whereHas('exam', function ($q) use ($selectedBranch) {
                    $q->where('branch', $selectedBranch);
                })
                ->exists();

            if (!in_array($selectedBranch, $enrolledBranches) && !$hasBranchAttempts && !$user->isAdmin() && !in_array($user->role, ['super_admin', 'admin', 'instructor'])) {
                $isEnrolledInSelectedBranch = false;
                $ongoingCourses = Course::forBranch($selectedBranch)
                    ->where('admission_status', 'open')
                    ->get();
                if ($ongoingCourses->isEmpty()) {
                    $ongoingCourses = Course::forBranch($selectedBranch)->get();
                }
            }
        }

        if (!$isEnrolledInSelectedBranch) {
            $attempts = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
            $totalAttempts = 0;
            $passedCount = 0;
            $failedCount = 0;
            $avgScore = 0.0;
        } else {
            $baseQuery = ExamAttempt::with('exam')
                ->where(function ($q) use ($user, $student) {
                    $q->where('user_id', $user->id);
                    if ($student && $student->user_id === $user->id) {
                        $q->orWhere('student_id', $student->id);
                    }
                })
                ->whereIn('status', ['submitted', 'auto_submitted']);

            if ($selectedBranch && $selectedBranch !== 'all') {
                $baseQuery->whereHas('exam', function ($q) use ($selectedBranch) {
                    $q->where('branch', $selectedBranch);
                });
            }

            $query = clone $baseQuery;

            if ($request->filled('result')) {
                $query->where('result_status', $request->result);
            }

            $searchTopic = $request->input('topic') ?? $request->input('search');
            if (!empty($searchTopic)) {
                $topic = trim($searchTopic);
                $query->whereHas('exam', function ($q) use ($topic) {
                    $q->where(function ($sub) use ($topic) {
                        $sub->where('title', 'like', "%{$topic}%")
                            ->orWhere('category', 'like', "%{$topic}%")
                            ->orWhere('description', 'like', "%{$topic}%")
                            ->orWhere('slug', 'like', "%{$topic}%");
                    });
                });
            }

            if ($request->filled('date')) {
                try {
                    $filterDate = Carbon::parse($request->input('date'))->toDateString();
                    $query->where(function ($q) use ($filterDate) {
                        $q->whereDate('started_at', $filterDate)
                          ->orWhereDate('created_at', $filterDate);
                    });
                } catch (\Exception $e) {}
            }

            $attempts = $query->orderBy('created_at', 'desc')->orderBy('id', 'desc')->paginate(15)->withQueryString();

            $totalAttempts = (clone $baseQuery)->count();
            $passedCount = (clone $baseQuery)->where('result_status', 'passed')->count();
            $failedCount = (clone $baseQuery)->where('result_status', 'failed')->count();
            $avgScore = $totalAttempts > 0 ? (float)(clone $baseQuery)->avg('percentage') : 0.0;
        }

        return view('cadet.exams.history', compact(
            'student',
            'attempts',
            'totalAttempts',
            'passedCount',
            'failedCount',
            'avgScore',
            'enrolledBranches',
            'selectedBranch',
            'isEnrolledInSelectedBranch',
            'ongoingCourses'
        ));
    }

    public function start($id)
    {
        $exam = Exam::with(['questions', 'watWords'])->findOrFail($id);
        $user = auth()->user();
        $student = Student::with(['courses', 'currentCourse'])->where('user_id', $user->id)->first();

        // Enrolled branch enforcement: if cadet is not enrolled in this sector, redirect to branch ongoing courses
        if ($student && !empty($exam->branch) && !$user->isAdmin() && !in_array($user->role, ['super_admin', 'admin', 'instructor']) && !$student->isEnrolledInBranch($exam->branch)) {
            return redirect()->route('cadet.exams.index', ['branch' => $exam->branch])
                ->with('error', "You are not enrolled in the {$exam->branchLabel()} sector. Please explore the ongoing courses below.");
        }

        // Concluded enforcement: if closed/ended, deny access (unless admin previewing)
        if ($exam->isEnded() && !$user->isAdmin()) {
            return redirect()->route('cadet.exams.index')
                ->with('error', "This examination module has concluded.");
        }

        // Lock enforcement: if scheduled in future, deny access (unless admin previewing)
        if ($exam->isScheduledFuture() && !$user->isAdmin()) {
            return redirect()->route('cadet.exams.index')
                ->with('error', "Exam is scheduled to start at {$exam->schedule_start->format('h:i A')}. Please wait for the countdown timer.");
        }

        // Check or create in-progress attempt
        $attemptQuery = ExamAttempt::where('exam_id', $exam->id)
            ->where('status', 'in_progress');
        if ($student) {
            $attemptQuery->where('student_id', $student->id);
        } else {
            $attemptQuery->where('user_id', $user->id);
        }
        $attempt = $attemptQuery->first();

        if (!$attempt) {
            $attemptCountQuery = ExamAttempt::where('exam_id', $exam->id);
            if ($student) {
                $attemptCountQuery->where('student_id', $student->id);
            } else {
                $attemptCountQuery->where('user_id', $user->id);
            }
            $attemptNumber = $attemptCountQuery->count() + 1;

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
            return view('cadet.exams.wat_exam', compact('exam', 'attempt', 'student', 'remainingSeconds'));
        }

        return view('cadet.exams.mcq_exam', compact('exam', 'attempt', 'student', 'remainingSeconds'));
    }

    public function submit(Request $request, $id)
    {
        $exam = Exam::with(['questions', 'watWords'])->findOrFail($id);
        $user = auth()->user();
        $student = Student::where('user_id', $user->id)->first();
        if (!$student && in_array($user->role, ['super_admin', 'admin', 'instructor'])) {
            $student = Student::first();
        }

        $attemptQuery = ExamAttempt::where('exam_id', $exam->id)
            ->where(function ($q) use ($user, $student) {
                $q->where('user_id', $user->id);
                if ($student) {
                    $q->orWhere('student_id', $student->id);
                }
            });

        $attempt = (clone $attemptQuery)->where('status', 'in_progress')->latest()->first();

        if (!$attempt) {
            $alreadySubmitted = (clone $attemptQuery)->whereIn('status', ['submitted', 'auto_submitted'])->latest()->first();
            if ($alreadySubmitted) {
                return redirect()->route('cadet.exams.index', ['branch' => $exam->branch])
                    ->with('info', "Exam '{$exam->title}' has already been submitted. You can review your result in Exam History or below.")
                    ->with('submitted_attempt_id', $alreadySubmitted->id);
            }
            abort(404, 'No active exam attempt found.');
        }

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
            // Handle WAT word responses
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
                    'marks_awarded' => 1.00, // standard completion mark
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
                'answers_summary' => [
                    'completed_words' => count($responses),
                    'negative_marks' => 0.00,
                ],
            ]);

            ExamAttempt::clearRankCache();
            $rankInfo = $attempt->getRankInfo();
            $summaryData = [
                'attempt_id' => $attempt->id,
                'exam_title' => $exam->title,
                'score' => count($responses),
                'minus_marks' => 0.00,
                'total_marks' => $exam->watWords->count(),
                'percentage' => round((count($responses) / max(1, $exam->watWords->count())) * 100, 1),
                'rank' => $attempt->isRankPublished() ? $rankInfo['rank'] : null,
                'total_candidates' => $attempt->isRankPublished() ? $rankInfo['total_candidates'] : null,
                'rank_published' => $attempt->isRankPublished(),
                'result_status' => 'under_evaluation',
            ];

            if ($student) {
                PerformanceTimeline::create([
                    'student_id' => $student->id,
                    'event_type' => 'exam_completed',
                    'title' => 'Completed WAT Simulation: ' . $exam->title,
                    'description' => 'Submitted ' . count($responses) . ' spontaneous associative sentences for psychological review.',
                    'event_date' => Carbon::today(),
                    'badge_color' => 'purple',
                    'icon' => 'fa-brain',
                    'source_id' => $attempt->id,
                ]);
            }

            $watRankMsg = $attempt->isRankPublished()
                ? "Rank #{$rankInfo['rank']} of {$rankInfo['total_candidates']} candidates"
                : "Official ranking and scorecard will unlock after the examination schedule concludes";

            return redirect()->route('cadet.exams.index', ['branch' => $exam->branch])
                ->with('success', "Word Association Test '{$exam->title}' submitted successfully! You completed " . count($responses) . " / " . $exam->watWords->count() . " prompts ({$watRankMsg}).")
                ->with('submitted_attempt_id', $attempt->isRankPublished() ? $attempt->id : null)
                ->with('exam_submission_summary', $summaryData);
        }

        // Handle MCQ Examination Scoring
        $userAnswers = $request->input('answers', []);
        $correctCount = 0;
        $wrongCount = 0;
        $unansweredCount = 0;
        $earnedScore = 0.0;
        $totalMarksPossible = 0.0;
        $negativeMarksDeducted = 0.0;

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
                $negativeVal = (float) ($question->negative_marks ?? $exam->negative_marking_per_wrong ?? 0.25);
                $marksAwarded = - $negativeVal;
                $negativeMarksDeducted += $negativeVal;
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
                'negative_marks' => round($negativeMarksDeducted, 2),
            ],
        ]);

        ExamAttempt::clearRankCache();
        $rankInfo = $attempt->getRankInfo();
        $isRankPublished = $attempt->isRankPublished();

        $summaryData = [
            'attempt_id' => $attempt->id,
            'exam_title' => $exam->title,
            'score' => $finalScore,
            'minus_marks' => round($negativeMarksDeducted, 2),
            'total_marks' => $totalMarksPossible,
            'percentage' => $percentage,
            'rank' => $isRankPublished ? $rankInfo['rank'] : null,
            'total_candidates' => $isRankPub = $isRankPublished ? $rankInfo['total_candidates'] : null,
            'rank_published' => $isRankPublished,
            'result_status' => $resultStatus,
        ];

        if ($student) {
            $timelineDesc = $isRankPublished
                ? "Scored {$finalScore} / {$totalMarksPossible} marks (Minus: -{$negativeMarksDeducted}, Rank: #{$rankInfo['rank']} / {$rankInfo['total_candidates']}). Status: " . ucfirst($resultStatus)
                : "Scored {$finalScore} / {$totalMarksPossible} marks. Official ranking & scorecard will be published after the examination concludes.";

            PerformanceTimeline::create([
                'student_id' => $student->id,
                'event_type' => 'exam_completed',
                'title' => "Exam Result: {$exam->title} ({$percentage}%)",
                'description' => $timelineDesc,
                'event_date' => Carbon::today(),
                'badge_color' => ($resultStatus === 'passed') ? 'emerald' : 'red',
                'icon' => 'fa-award',
                'source_id' => $attempt->id,
            ]);
        }

        $rankSuccessMsg = $isRankPublished
            ? "Rank #{$rankInfo['rank']} of {$rankInfo['total_candidates']} candidates"
            : "Official ranking and scorecard will unlock after the examination schedule concludes";

        return redirect()->route('cadet.exams.index', ['branch' => $exam->branch])
            ->with('success', "Exam '{$exam->title}' submitted successfully! You scored {$finalScore} / {$totalMarksPossible} marks ({$rankSuccessMsg}).")
            ->with('submitted_attempt_id', $isRankPublished ? $attempt->id : null)
            ->with('exam_submission_summary', $summaryData);
    }

    public function result($attemptId)
    {
        $user = auth()->user();
        $student = Student::where('user_id', $user->id)->first();
        if (!$student && in_array($user->role, ['super_admin', 'admin', 'instructor'])) {
            $student = Student::first();
        }

        $attemptQuery = ExamAttempt::with(['exam.questions', 'exam.watWords', 'answers.question', 'answers.watWord'])
            ->where('id', $attemptId);
        if (!in_array($user->role, ['super_admin', 'admin', 'instructor'])) {
            $attemptQuery->where(function ($q) use ($user, $student) {
                $q->where('user_id', $user->id);
                if ($student) {
                    $q->orWhere('student_id', $student->id);
                }
            });
        }
        $attempt = $attemptQuery->firstOrFail();

        $leaderboard = ($attempt->isRankPublished() && $attempt->exam) 
            ? $attempt->exam->getLeaderboard() 
            : collect();

        return view('cadet.exams.result', compact('attempt', 'student', 'leaderboard'));
    }
}
