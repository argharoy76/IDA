<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Exam;
use App\Models\Question;
use App\Models\WatWord;
use App\Models\ExamAttempt;
use App\Services\McqPdfParserService;
use Carbon\Carbon;

class ExamController extends Controller
{
    protected array $branches = [
        'army' => 'Bangladesh Army',
        'navy' => 'Bangladesh Navy',
        'air_force' => 'Bangladesh Air Force',
        'police' => 'Bangladesh Police',
    ];

    public function index(Request $request)
    {
        $query = Exam::withCount(['questions', 'watWords', 'attempts']);

        if ($request->filled('branch')) {
            $query->where('branch', $request->branch);
        }

        $exams = $query->orderBy('id', 'desc')->get();

        $stats = [
            'total_exams' => Exam::count(),
            'army_count' => Exam::where('branch', 'army')->count(),
            'navy_count' => Exam::where('branch', 'navy')->count(),
            'air_force_count' => Exam::where('branch', 'air_force')->count(),
            'police_count' => Exam::where('branch', 'police')->count(),
            'total_pool_questions' => Question::whereNull('exam_id')->count(),
            'total_attempts' => ExamAttempt::count(),
        ];

        return view('backend.exams.index', compact('exams', 'stats'));
    }

    public function branch(Request $request, string $branch)
    {
        if (!array_key_exists($branch, $this->branches)) {
            abort(404, 'Branch not found');
        }

        $branchName = $this->branches[$branch];

        $exams = Exam::where('branch', $branch)
            ->withCount(['questions', 'watWords', 'attempts'])
            ->orderBy('id', 'desc')
            ->get();

        $poolQuery = Question::where('branch', $branch)->whereNull('exam_id');
        if ($request->filled('q')) {
            $poolQuery->where('question_text', 'like', '%' . $request->q . '%');
        }
        $poolQuestions = $poolQuery->orderBy('id', 'desc')->paginate(12)->withQueryString();

        $activeTab = $request->get('tab', 'control');

        $stats = [
            'total_exams' => $exams->count(),
            'scheduled_exams' => $exams->where('status', 'scheduled')->count(),
            'live_exams' => $exams->where('status', 'open')->count(),
            'closed_exams' => $exams->where('status', 'closed')->count(),
            'draft_exams' => $exams->where('status', 'draft')->count(),
            'pool_count' => Question::where('branch', $branch)->whereNull('exam_id')->count(),
            'total_questions_in_exams' => Question::where('branch', $branch)->whereNotNull('exam_id')->count(),
            'total_attempts' => $exams->sum('attempts_count'),
        ];

        return view('backend.exams.branch', compact('branch', 'branchName', 'exams', 'poolQuestions', 'stats', 'activeTab'));
    }

    public function scanPdf(Request $request, string $branch)
    {
        if (!array_key_exists($branch, $this->branches)) {
            return response()->json(['success' => false, 'message' => 'Branch not found.'], 404);
        }

        $request->validate([
            'pdf_file' => 'nullable|file|mimes:pdf|max:51200',
            'raw_text' => 'nullable|string',
        ]);

        if (!$request->hasFile('pdf_file') && empty($request->raw_text)) {
            return response()->json(['success' => false, 'message' => 'Please upload a PDF file or paste MCQ text.'], 422);
        }

        $parser = new McqPdfParserService();

        try {
            if ($request->hasFile('pdf_file')) {
                $parseResult = $parser->parsePdfFile($request->file('pdf_file')->getRealPath());
            } else {
                $parseResult = $parser->parseText($request->raw_text);
            }
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Parsing error: ' . $e->getMessage()], 500);
        }

        if ($parseResult['total_detected'] === 0) {
            return response()->json([
                'success' => false,
                'message' => 'No MCQ questions could be detected. Please ensure format includes questions followed by options (A/B/C/D or ক/খ/গ/ঘ) and Answer keys.'
            ], 422);
        }

        return response()->json([
            'success' => true,
            'total_detected' => $parseResult['total_detected'],
            'valid_count' => $parseResult['valid_count'],
            'flagged_count' => $parseResult['flagged_count'],
            'questions' => $parseResult['questions'],
        ]);
    }

    public function scanQuestionsGlobal(Request $request)
    {
        $request->validate([
            'pdf_file' => 'nullable|file|mimes:pdf|max:51200',
            'raw_text' => 'nullable|string',
            'json_file' => 'nullable|file|max:10240',
            'json_text' => 'nullable|string',
        ]);

        if (!$request->hasFile('pdf_file') && empty($request->raw_text) && !$request->hasFile('json_file') && empty($request->json_text)) {
            return response()->json(['success' => false, 'message' => 'Please provide a PDF file, MCQ text, or JSON questions.'], 422);
        }

        // 1. JSON parsing mode
        if ($request->hasFile('json_file') || !empty($request->json_text)) {
            $jsonContent = $request->hasFile('json_file')
                ? file_get_contents($request->file('json_file')->getRealPath())
                : $request->json_text;

            $decoded = json_decode($jsonContent, true);
            if (!is_array($decoded)) {
                return response()->json(['success' => false, 'message' => 'Invalid JSON syntax. Please verify JSON format.'], 422);
            }

            $rawQuestions = isset($decoded['questions']) && is_array($decoded['questions']) ? $decoded['questions'] : $decoded;
            $normalizedQuestions = [];
            $idx = 1;

            foreach ($rawQuestions as $q) {
                if (!is_array($q)) continue;

                $qText = $q['question_text'] ?? $q['question'] ?? $q['text'] ?? '';
                if (empty(trim($qText))) continue;

                $optA = ''; $optB = ''; $optC = ''; $optD = '';
                if (isset($q['options']) && is_array($q['options'])) {
                    foreach ($q['options'] as $o) {
                        $key = strtoupper($o['key'] ?? '');
                        $text = $o['text'] ?? '';
                        if ($key === 'A') $optA = $text;
                        elseif ($key === 'B') $optB = $text;
                        elseif ($key === 'C') $optC = $text;
                        elseif ($key === 'D') $optD = $text;
                    }
                }
                $optA = $optA ?: ($q['option_a'] ?? $q['A'] ?? ($q['options'][0] ?? ''));
                $optB = $optB ?: ($q['option_b'] ?? $q['B'] ?? ($q['options'][1] ?? ''));
                $optC = $optC ?: ($q['option_c'] ?? $q['C'] ?? ($q['options'][2] ?? ''));
                $optD = $optD ?: ($q['option_d'] ?? $q['D'] ?? ($q['options'][3] ?? ''));

                $correct = strtoupper($q['correct_answer'] ?? $q['answer'] ?? $q['ans'] ?? 'A');
                $explanation = $q['explanation'] ?? $q['note'] ?? null;

                $normalizedQuestions[] = [
                    'index' => $idx++,
                    'question_text' => trim($qText),
                    'option_a' => trim($optA),
                    'option_b' => trim($optB),
                    'option_c' => trim($optC),
                    'option_d' => trim($optD),
                    'correct_answer' => in_array($correct, ['A', 'B', 'C', 'D']) ? $correct : 'A',
                    'explanation' => $explanation ? trim($explanation) : null,
                    'is_valid' => true,
                    'errors' => [],
                ];
            }

            if (empty($normalizedQuestions)) {
                return response()->json(['success' => false, 'message' => 'No valid MCQ questions detected in JSON.'], 422);
            }

            return response()->json([
                'success' => true,
                'total_detected' => count($normalizedQuestions),
                'valid_count' => count($normalizedQuestions),
                'flagged_count' => 0,
                'questions' => $normalizedQuestions,
            ]);
        }

        // 2. PDF or Text parsing mode
        $parser = new McqPdfParserService();
        try {
            if ($request->hasFile('pdf_file')) {
                $uploadedPath = $request->file('pdf_file')->getRealPath();
                @copy($uploadedPath, storage_path('logs/last_uploaded_exam.pdf'));
                $parseResult = $parser->parsePdfFile($uploadedPath);
            } else {
                @file_put_contents(storage_path('logs/last_raw_input.txt'), (string)$request->raw_text);
                $parseResult = $parser->parseText($request->raw_text);
            }
            @file_put_contents(storage_path('logs/last_scan_response.json'), json_encode($parseResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } catch (\Throwable $e) {
            @file_put_contents(storage_path('logs/last_scan_error.txt'), $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json(['success' => false, 'message' => 'Parsing error: ' . $e->getMessage()], 500);
        }

        if ($parseResult['total_detected'] === 0) {
            return response()->json([
                'success' => false,
                'message' => 'No MCQ questions could be detected. Please ensure format includes questions followed by options (A/B/C/D) and Answer keys (Ans: A).'
            ], 422);
        }

        return response()->json([
            'success' => true,
            'total_detected' => $parseResult['total_detected'],
            'valid_count' => $parseResult['valid_count'],
            'flagged_count' => $parseResult['flagged_count'],
            'questions' => $parseResult['questions'],
        ]);
    }

    public function createFromPdf(Request $request, string $branch)
    {
        if (!array_key_exists($branch, $this->branches)) {
            abort(404, 'Branch not found');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'target_track' => 'nullable|in:soldier,prelim,issb,constable,si,asi,general',
            'exam_type' => 'required|in:iq_mcq,word_association,non_verbal_iq,general_aptitude',
            'duration_minutes' => 'required|integer|min:1|max:720',
            'question_count' => 'required|integer|min:1',
            'marks_per_question' => 'required|numeric|min:0.1',
            'negative_marking_per_wrong' => 'nullable|numeric|min:0',
            'pass_marks' => 'required|numeric|min:0',
            'fee' => 'nullable|numeric|min:0',
            'schedule_start' => 'nullable|date',
            'schedule_end' => 'nullable|date',
            'status' => 'required|in:draft,scheduled,open,closed',
            'is_public_for_external' => 'nullable',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'questions_json' => 'required|string',
        ]);

        $allQuestions = json_decode($validated['questions_json'], true);
        if (!is_array($allQuestions) || empty($allQuestions)) {
            return back()->with('error', 'No valid questions were supplied from the scanned PDF.');
        }

        $requestedCount = min(count($allQuestions), (int) $validated['question_count']);
        $marksPerQuestion = (float) $validated['marks_per_question'];

        // Randomly pick N questions from the uploaded PDF
        $shuffled = $allQuestions;
        shuffle($shuffled);
        $selectedQuestions = array_slice($shuffled, 0, $requestedCount);

        $totalMarks = $requestedCount * $marksPerQuestion;

        $slug = Str::slug($validated['title']);
        $origSlug = $slug;
        $c = 1;
        while (Exam::where('slug', $slug)->exists()) {
            $slug = "{$origSlug}-" . (++$c);
        }

        $scheduleStart = !empty($validated['schedule_start']) ? Carbon::parse($validated['schedule_start']) : null;
        $scheduleEnd = !empty($validated['schedule_end']) 
            ? Carbon::parse($validated['schedule_end']) 
            : ($scheduleStart ? $scheduleStart->copy()->addMinutes($validated['duration_minutes'] + 60) : null);

        $targetTrack = $request->input('target_track');
        if (empty($targetTrack)) {
            $catLower = strtolower($validated['category'] . ' ' . $validated['title']);
            if (in_array($branch, ['army', 'navy', 'air_force'], true)) {
                $targetTrack = str_contains($catLower, 'issb') ? 'issb' : (str_contains($catLower, 'soldier') || str_contains($catLower, 'sainik') || str_contains($catLower, 'sailor') || str_contains($catLower, 'airman') ? 'soldier' : 'prelim');
            } elseif ($branch === 'police') {
                if (str_contains($catLower, 'constable')) {
                    $targetTrack = 'constable';
                } elseif (str_contains($catLower, 'asi') || str_contains($catLower, 'assistant')) {
                    $targetTrack = 'asi';
                } elseif (str_contains($catLower, 'si') || str_contains($catLower, 'sub-inspector')) {
                    $targetTrack = 'si';
                } else {
                    $targetTrack = 'si';
                }
            } else {
                $targetTrack = 'general';
            }
        }

        $exam = Exam::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'branch' => $branch,
            'category' => $validated['category'],
            'target_track' => $targetTrack,
            'exam_type' => $validated['exam_type'],
            'duration_minutes' => $validated['duration_minutes'],
            'total_marks' => $totalMarks,
            'pass_marks' => $validated['pass_marks'],
            'random_question_count' => $requestedCount,
            'negative_marking_per_wrong' => $validated['negative_marking_per_wrong'] ?? 0.25,
            'fee' => $validated['fee'] ?? 0.00,
            'is_paid_for_external' => ($validated['fee'] ?? 0) > 0,
            'is_public_for_external' => $request->has('is_public_for_external'),
            'status' => $validated['status'],
            'schedule_start' => $scheduleStart,
            'schedule_end' => $scheduleEnd,
            'description' => $validated['description'] ?? null,
            'instructions' => $validated['instructions'] ?? null,
        ]);

        // Save selected questions linked to this exam
        foreach ($selectedQuestions as $idx => $q) {
            Question::create([
                'exam_id' => $exam->id,
                'branch' => $branch,
                'exam_type' => $exam->exam_type,
                'question_text' => $q['question_text'],
                'options' => [
                    ['key' => 'A', 'text' => $q['option_a']],
                    ['key' => 'B', 'text' => $q['option_b']],
                    ['key' => 'C', 'text' => $q['option_c']],
                    ['key' => 'D', 'text' => $q['option_d']],
                ],
                'correct_answer' => $q['correct_answer'] ?? 'A',
                'explanation' => $q['explanation'] ?? null,
                'marks' => $marksPerQuestion,
                'negative_marks' => $validated['negative_marking_per_wrong'] ?? 0.25,
                'difficulty' => 'medium',
                'order_seq' => $idx + 1,
            ]);
        }

        // Also save all scanned questions into the branch pool for future re-use
        foreach ($allQuestions as $q) {
            Question::create([
                'exam_id' => null,
                'branch' => $branch,
                'exam_type' => $exam->exam_type,
                'question_text' => $q['question_text'],
                'options' => [
                    ['key' => 'A', 'text' => $q['option_a']],
                    ['key' => 'B', 'text' => $q['option_b']],
                    ['key' => 'C', 'text' => $q['option_c']],
                    ['key' => 'D', 'text' => $q['option_d']],
                ],
                'correct_answer' => $q['correct_answer'] ?? 'A',
                'explanation' => $q['explanation'] ?? null,
                'marks' => $marksPerQuestion,
                'negative_marks' => $validated['negative_marking_per_wrong'] ?? 0.25,
                'difficulty' => 'medium',
                'order_seq' => 1,
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'redirect' => route('admin.exams.branch', ['branch' => $branch, 'tab' => 'control']),
                'message' => "Exam '{$exam->title}' successfully created with {$requestedCount} questions!",
            ]);
        }

        return redirect()->route('admin.exams.branch', ['branch' => $branch, 'tab' => 'control'])
            ->with('success', "Exam '{$exam->title}' successfully created with {$requestedCount} questions from your PDF!");
    }

    public function uploadPdf(Request $request, string $branch)
    {
        if (!array_key_exists($branch, $this->branches)) {
            abort(404, 'Branch not found');
        }

        $request->validate([
            'pdf_file' => 'nullable|file|mimes:pdf|max:51200',
            'raw_text' => 'nullable|string',
            'action' => 'required|in:preview,import',
        ]);

        if (!$request->hasFile('pdf_file') && empty($request->raw_text)) {
            return back()->with('error', 'Please upload a PDF file or paste MCQ text.');
        }

        $parser = new McqPdfParserService();

        try {
            if ($request->hasFile('pdf_file')) {
                $parseResult = $parser->parsePdfFile($request->file('pdf_file')->getRealPath());
            } else {
                $parseResult = $parser->parseText($request->raw_text);
            }
        } catch (\Throwable $e) {
            return back()->with('error', 'Parsing error: ' . $e->getMessage());
        }

        if ($parseResult['total_detected'] === 0) {
            return back()->with('error', 'No MCQ questions could be detected. Please ensure format includes questions followed by A/B/C/D options and Ans: A/B/C/D.');
        }

        // If direct import requested
        if ($request->action === 'import') {
            $importedCount = 0;
            foreach ($parseResult['questions'] as $q) {
                Question::create([
                    'exam_id' => null,
                    'branch' => $branch,
                    'exam_type' => 'iq_mcq',
                    'question_text' => $q['question_text'],
                    'options' => [
                        ['key' => 'A', 'text' => $q['option_a']],
                        ['key' => 'B', 'text' => $q['option_b']],
                        ['key' => 'C', 'text' => $q['option_c']],
                        ['key' => 'D', 'text' => $q['option_d']],
                    ],
                    'correct_answer' => $q['correct_answer'],
                    'explanation' => $q['explanation'],
                    'marks' => 1.00,
                    'negative_marks' => 0.25,
                    'difficulty' => 'medium',
                    'order_seq' => 1,
                ]);
                $importedCount++;
            }

            return redirect()->route('admin.exams.branch', $branch)
                ->with('success', "Successfully imported {$importedCount} questions into {$this->branches[$branch]} Question Bank Pool!");
        }

        // Preview Screen
        $branchName = $this->branches[$branch];
        return view('backend.exams.upload_preview', compact('parseResult', 'branch', 'branchName'));
    }

    public function confirmImport(Request $request, string $branch)
    {
        if (!array_key_exists($branch, $this->branches)) {
            abort(404, 'Branch not found');
        }

        $rawQuestions = $request->input('questions_json');
        $questions = json_decode($rawQuestions, true) ?? [];

        if (empty($questions)) {
            return redirect()->route('admin.exams.branch', $branch)->with('error', 'No questions to import.');
        }

        $imported = 0;
        foreach ($questions as $q) {
            Question::create([
                'exam_id' => null,
                'branch' => $branch,
                'exam_type' => 'iq_mcq',
                'question_text' => $q['question_text'],
                'options' => [
                    ['key' => 'A', 'text' => $q['option_a']],
                    ['key' => 'B', 'text' => $q['option_b']],
                    ['key' => 'C', 'text' => $q['option_c']],
                    ['key' => 'D', 'text' => $q['option_d']],
                ],
                'correct_answer' => $q['correct_answer'],
                'explanation' => $q['explanation'] ?? null,
                'marks' => 1.00,
                'negative_marks' => 0.25,
                'difficulty' => 'medium',
                'order_seq' => 1,
            ]);
            $imported++;
        }

        return redirect()->route('admin.exams.branch', $branch)
            ->with('success', "Confirmed and imported {$imported} questions into {$this->branches[$branch]} Question Bank!");
    }

    public function generateRandomExam(Request $request, string $branch)
    {
        if (!array_key_exists($branch, $this->branches)) {
            abort(404, 'Branch not found');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'target_track' => 'nullable|in:soldier,prelim,issb,constable,si,asi,general',
            'exam_type' => 'required|in:iq_mcq,word_association,non_verbal_iq,general_aptitude',
            'duration_minutes' => 'required|integer|min:5|max:360',
            'question_count' => 'required|integer|min:1',
            'marks_per_question' => 'required|numeric|min:0.1',
            'negative_marking_per_wrong' => 'nullable|numeric|min:0',
            'pass_marks' => 'required|numeric|min:0',
            'fee' => 'nullable|numeric|min:0',
            'schedule_start' => 'nullable|date',
            'status' => 'required|in:draft,scheduled,open,closed',
            'is_public_for_external' => 'nullable',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
        ]);

        $requestedCount = (int) $validated['question_count'];
        $marksPerQuestion = (float) $validated['marks_per_question'];

        // Pull random questions from pool for this branch
        $poolQuestions = Question::where('branch', $branch)
            ->whereNull('exam_id')
            ->inRandomOrder()
            ->limit($requestedCount)
            ->get();

        // If pool has fewer, also pull from other questions of this branch
        if ($poolQuestions->count() < $requestedCount) {
            $needed = $requestedCount - $poolQuestions->count();
            $additional = Question::where('branch', $branch)
                ->whereNotIn('id', $poolQuestions->pluck('id'))
                ->inRandomOrder()
                ->limit($needed)
                ->get();
            $poolQuestions = $poolQuestions->merge($additional);
        }

        if ($poolQuestions->isEmpty()) {
            return back()->with('error', "No questions available in {$this->branches[$branch]} Question Bank. Please upload a PDF or add questions first.");
        }

        $totalMarks = $poolQuestions->count() * $marksPerQuestion;
        $slug = Str::slug($validated['title']);
        $count = Exam::where('slug', 'like', "{$slug}%")->count();
        if ($count > 0) {
            $slug = "{$slug}-" . ($count + 1);
        }

        $scheduleStart = !empty($validated['schedule_start']) ? Carbon::parse($validated['schedule_start']) : null;
        $scheduleEnd = $scheduleStart ? $scheduleStart->copy()->addMinutes($validated['duration_minutes'] + 60) : null;

        $targetTrack = $request->input('target_track');
        if (empty($targetTrack)) {
            $catLower = strtolower($validated['category'] . ' ' . $validated['title']);
            if (in_array($branch, ['army', 'navy', 'air_force'], true)) {
                $targetTrack = str_contains($catLower, 'issb') ? 'issb' : (str_contains($catLower, 'soldier') || str_contains($catLower, 'sainik') || str_contains($catLower, 'sailor') || str_contains($catLower, 'airman') ? 'soldier' : 'prelim');
            } elseif ($branch === 'police') {
                if (str_contains($catLower, 'constable')) {
                    $targetTrack = 'constable';
                } elseif (str_contains($catLower, 'asi') || str_contains($catLower, 'assistant')) {
                    $targetTrack = 'asi';
                } elseif (str_contains($catLower, 'si') || str_contains($catLower, 'sub-inspector')) {
                    $targetTrack = 'si';
                } else {
                    $targetTrack = 'si';
                }
            } else {
                $targetTrack = 'general';
            }
        }

        $exam = Exam::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'branch' => $branch,
            'category' => $validated['category'],
            'target_track' => $targetTrack,
            'exam_type' => $validated['exam_type'],
            'duration_minutes' => $validated['duration_minutes'],
            'total_marks' => $totalMarks,
            'pass_marks' => $validated['pass_marks'],
            'random_question_count' => $poolQuestions->count(),
            'negative_marking_per_wrong' => $validated['negative_marking_per_wrong'] ?? 0.25,
            'fee' => $validated['fee'] ?? 0.00,
            'is_paid_for_external' => ($validated['fee'] ?? 0) > 0,
            'is_public_for_external' => $request->has('is_public_for_external'),
            'status' => $validated['status'],
            'schedule_start' => $scheduleStart,
            'schedule_end' => $scheduleEnd,
            'description' => $validated['description'] ?? null,
            'instructions' => $validated['instructions'] ?? null,
        ]);

        // Attach selected questions to the exam
        foreach ($poolQuestions as $order => $pq) {
            Question::create([
                'exam_id' => $exam->id,
                'branch' => $branch,
                'exam_type' => $pq->exam_type,
                'question_text' => $pq->question_text,
                'question_image' => $pq->question_image,
                'options' => $pq->options,
                'correct_answer' => $pq->correct_answer,
                'marks' => $marksPerQuestion,
                'negative_marks' => $validated['negative_marking_per_wrong'] ?? 0.25,
                'explanation' => $pq->explanation,
                'difficulty' => $pq->difficulty,
                'order_seq' => $order + 1,
            ]);
        }

        return redirect()->route('admin.exams.branch', $branch)
            ->with('success', "Exam '{$exam->title}' successfully generated with {$poolQuestions->count()} random questions! " . ($exam->schedule_start ? "Scheduled to start at {$exam->schedule_start->format('M d, Y h:i A')}." : ''));
    }

    public function togglePublic(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);
        $exam->is_public_for_external = !$exam->is_public_for_external;
        $exam->save();

        $state = $exam->is_public_for_external ? 'PUBLIC on Frontend' : 'HIDDEN from Frontend';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_public' => (bool)$exam->is_public_for_external,
                'message' => "Exam '{$exam->title}' is now {$state}.",
            ]);
        }

        return back()->with('success', "Exam '{$exam->title}' is now {$state}.");
    }

    public function startNow(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);
        $exam->status = 'open';
        $exam->schedule_start = Carbon::now();
        $exam->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Exam '{$exam->title}' is now LIVE! Scheduled countdown timer unlocked.",
                'exam' => $exam,
                'status' => 'open',
            ]);
        }

        return back()->with('success', "Exam '{$exam->title}' is now LIVE! Scheduled countdown timer unlocked.");
    }

    public function endNow(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);
        $exam->status = 'closed';
        $exam->schedule_end = Carbon::now();
        $exam->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Exam '{$exam->title}' has been ENDED & CLOSED. Students can no longer submit new attempts.",
                'exam' => $exam,
                'status' => 'closed',
            ]);
        }

        return back()->with('success', "Exam '{$exam->title}' has been ENDED & CLOSED. Students can no longer submit new attempts.");
    }

    public function updateSchedule(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);

        $validated = $request->validate([
            'schedule_start' => 'nullable|date',
            'schedule_end' => 'nullable|date',
            'status' => 'required|in:draft,scheduled,open,closed',
            'is_public_for_external' => 'nullable',
        ]);

        $exam->schedule_start = !empty($validated['schedule_start']) ? Carbon::parse($validated['schedule_start']) : null;
        $exam->schedule_end = !empty($validated['schedule_end']) ? Carbon::parse($validated['schedule_end']) : null;
        $exam->status = $validated['status'];
        $exam->is_public_for_external = $request->has('is_public_for_external');
        $exam->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Exam schedule & operational controls for '{$exam->title}' have been updated.",
                'exam' => $exam,
            ]);
        }

        return back()->with('success', "Exam schedule & operational controls for '{$exam->title}' have been updated.");
    }

    public function deleteQuestionFromPool(Request $request, $id)
    {
        $question = Question::whereNull('exam_id')->findOrFail($id);
        $question->delete();

        return back()->with('success', 'Question deleted from question bank pool.');
    }

    public function clearBranchPool(Request $request, string $branch)
    {
        if (!array_key_exists($branch, $this->branches)) {
            abort(404, 'Branch not found');
        }

        $count = Question::where('branch', $branch)->whereNull('exam_id')->delete();

        return back()->with('success', "Cleared {$count} questions from {$this->branches[$branch]} Question Bank Pool.");
    }

    public function deleteExamQuestion(Request $request, $examId, $questionId)
    {
        $exam = Exam::findOrFail($examId);
        $question = Question::where('exam_id', $exam->id)->findOrFail($questionId);
        $question->delete();

        $remaining = Question::where('exam_id', $exam->id)->count();
        $exam->random_question_count = $remaining;
        $exam->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Question deleted successfully.',
                'remaining_count' => $remaining,
            ]);
        }

        return back()->with('success', 'Question removed from examination module.');
    }

    public function updateExamQuestion(Request $request, $examId, $questionId)
    {
        $exam = Exam::findOrFail($examId);
        $question = Question::where('exam_id', $exam->id)->findOrFail($questionId);

        $validated = $request->validate([
            'question_text' => 'required|string',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'correct_answer' => 'required|in:A,B,C,D',
            'marks' => 'nullable|numeric|min:0',
            'negative_marks' => 'nullable|numeric|min:0',
            'explanation' => 'nullable|string',
            'difficulty' => 'nullable|in:easy,medium,hard',
        ]);

        $options = [
            ['key' => 'A', 'text' => $validated['option_a']],
            ['key' => 'B', 'text' => $validated['option_b']],
            ['key' => 'C', 'text' => $validated['option_c']],
            ['key' => 'D', 'text' => $validated['option_d']],
        ];

        $question->update([
            'question_text' => $validated['question_text'],
            'options' => $options,
            'correct_answer' => $validated['correct_answer'],
            'marks' => array_key_exists('marks', $validated) && $validated['marks'] !== null ? $validated['marks'] : $question->marks,
            'negative_marks' => array_key_exists('negative_marks', $validated) && $validated['negative_marks'] !== null ? $validated['negative_marks'] : $question->negative_marks,
            'explanation' => $validated['explanation'] ?? null,
            'difficulty' => $validated['difficulty'] ?? $question->difficulty ?? 'medium',
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Question updated successfully.',
                'question' => [
                    'id' => $question->id,
                    'question_text' => $question->question_text,
                    'option_a' => $validated['option_a'],
                    'option_b' => $validated['option_b'],
                    'option_c' => $validated['option_c'],
                    'option_d' => $validated['option_d'],
                    'correct_answer' => $question->correct_answer,
                    'marks' => $question->marks,
                    'explanation' => $question->explanation,
                ]
            ]);
        }

        return back()->with('success', 'Question updated successfully.');
    }

    public function storeSingleExamQuestion(Request $request, $examId)
    {
        $exam = Exam::findOrFail($examId);

        $validated = $request->validate([
            'question_text' => 'required|string',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'correct_answer' => 'required|in:A,B,C,D',
            'marks' => 'nullable|numeric|min:0',
            'negative_marks' => 'nullable|numeric|min:0',
            'explanation' => 'nullable|string',
            'difficulty' => 'nullable|in:easy,medium,hard',
        ]);

        $options = [
            ['key' => 'A', 'text' => $validated['option_a']],
            ['key' => 'B', 'text' => $validated['option_b']],
            ['key' => 'C', 'text' => $validated['option_c']],
            ['key' => 'D', 'text' => $validated['option_d']],
        ];

        $orderSeq = Question::where('exam_id', $exam->id)->count() + 1;

        $question = Question::create([
            'exam_id' => $exam->id,
            'branch' => $exam->branch,
            'exam_type' => $exam->exam_type ?? 'iq_mcq',
            'question_text' => $validated['question_text'],
            'options' => $options,
            'correct_answer' => $validated['correct_answer'],
            'marks' => $validated['marks'] ?? 1.0,
            'negative_marks' => $validated['negative_marks'] ?? $exam->negative_marking_per_wrong ?? 0.25,
            'explanation' => $validated['explanation'] ?? null,
            'order_seq' => $orderSeq,
            'difficulty' => $validated['difficulty'] ?? 'medium',
        ]);

        $totalCount = Question::where('exam_id', $exam->id)->count();
        $exam->random_question_count = $totalCount;
        $exam->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Question added to exam paper successfully.',
                'question' => [
                    'id' => $question->id,
                    'order_seq' => $question->order_seq,
                    'question_text' => $question->question_text,
                    'option_a' => $validated['option_a'],
                    'option_b' => $validated['option_b'],
                    'option_c' => $validated['option_c'],
                    'option_d' => $validated['option_d'],
                    'correct_answer' => $question->correct_answer,
                    'marks' => $question->marks,
                    'explanation' => $question->explanation,
                ],
                'total_questions' => $totalCount,
            ]);
        }

        return back()->with('success', 'Question added to exam paper successfully.');
    }

    public function create()
    {
        return redirect()->route('admin.exam_management.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'branch' => 'nullable|in:army,navy,air_force,police',
            'target_track' => 'nullable|in:soldier,prelim,issb,constable,si,asi,general',
            'exam_type' => 'required|in:iq_mcq,word_association,non_verbal_iq,general_aptitude',
            'category' => 'required|string',
            'duration_minutes' => 'required|integer|min:1',
            'total_marks' => 'required|numeric|min:1',
            'pass_marks' => 'required|numeric|min:0',
            'negative_marking_per_wrong' => 'nullable|numeric|min:0',
            'fee' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,scheduled,open,closed,archived',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'schedule_start' => 'nullable|date',
        ]);

        $slug = Str::slug($validated['title']);
        $count = Exam::where('slug', 'like', "{$slug}%")->count();
        if ($count > 0) {
            $slug = "{$slug}-" . ($count + 1);
        }

        $targetTrack = $request->input('target_track');
        if (empty($targetTrack)) {
            $catLower = strtolower($validated['category'] . ' ' . $validated['title']);
            $branch = $validated['branch'] ?? 'army';
            if (in_array($branch, ['army', 'navy', 'air_force'], true)) {
                $targetTrack = str_contains($catLower, 'issb') ? 'issb' : (str_contains($catLower, 'soldier') || str_contains($catLower, 'sainik') || str_contains($catLower, 'sailor') || str_contains($catLower, 'airman') ? 'soldier' : 'prelim');
            } elseif ($branch === 'police') {
                if (str_contains($catLower, 'constable')) {
                    $targetTrack = 'constable';
                } elseif (str_contains($catLower, 'asi') || str_contains($catLower, 'assistant')) {
                    $targetTrack = 'asi';
                } elseif (str_contains($catLower, 'si') || str_contains($catLower, 'sub-inspector')) {
                    $targetTrack = 'si';
                } else {
                    $targetTrack = 'si';
                }
            } else {
                $targetTrack = 'general';
            }
        }

        Exam::create(array_merge($validated, [
            'slug' => $slug,
            'target_track' => $targetTrack,
            'is_paid_for_external' => ($validated['fee'] ?? 0) > 0,
            'is_public_for_external' => $request->has('is_public_for_external'),
            'schedule_start' => $validated['schedule_start'] ? Carbon::parse($validated['schedule_start']) : null,
        ]));

        return redirect()->route('admin.exams.index')->with('success', 'Assessment examination module created.');
    }

    public function edit($id)
    {
        $exam = Exam::with(['questions', 'watWords'])->findOrFail($id);
        return view('backend.exams.edit', compact('exam'));
    }

    public function update(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);

        // Normalize status aliases if provided (e.g. LIVE -> open, ENDED -> closed)
        if ($request->filled('status')) {
            $rawStatus = strtolower(trim($request->input('status')));
            if ($rawStatus === 'live') {
                $rawStatus = 'open';
            } elseif ($rawStatus === 'ended') {
                $rawStatus = 'closed';
            }
            $request->merge(['status' => $rawStatus]);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'branch' => 'nullable|in:army,navy,air_force,police',
            'category' => 'required|string',
            'target_track' => 'nullable|in:soldier,prelim,issb,constable,si,asi,general',
            'exam_type' => 'nullable|in:iq_mcq,word_association,non_verbal_iq,general_aptitude',
            'duration_minutes' => 'required|integer|min:1',
            'total_marks' => 'required|numeric|min:1',
            'pass_marks' => 'required|numeric|min:0',
            'negative_marking_per_wrong' => 'nullable|numeric|min:0',
            'access_type' => 'nullable|in:free,paid,both,0,1',
            'fee' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,scheduled,open,closed,archived,live,ended',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'schedule_start' => 'nullable|date',
            'schedule_end' => 'nullable|date',
        ]);

        $status = match($validated['status']) {
            'live' => 'open',
            'ended' => 'closed',
            default => $validated['status'],
        };

        $rawAccess = $request->input('access_type');
        if ($rawAccess === null) {
            $isPaidInput = $request->input('is_paid_for_external');
            if ($isPaidInput !== null) {
                $rawAccess = ($isPaidInput == 1) ? 'paid' : 'free';
            } else {
                $rawAccess = $exam->access_type ?? 'paid';
            }
        }

        if ($rawAccess === '0') {
            $accessType = 'free';
        } elseif ($rawAccess === '1') {
            $accessType = 'paid';
        } else {
            $accessType = in_array($rawAccess, ['free', 'paid', 'both']) ? $rawAccess : 'paid';
        }

        if ($accessType === 'free') {
            $isPaid = false;
            $fee = 0.00;
        } else {
            $isPaid = true;
            $fee = isset($validated['fee']) ? (float)$validated['fee'] : (float)($exam->fee ?? 0);
        }

        $targetTrack = $request->input('target_track');
        if (empty($targetTrack)) {
            $catLower = strtolower($validated['category'] . ' ' . $validated['title']);
            $branch = $validated['branch'] ?? $exam->branch;
            if (in_array($branch, ['army', 'navy', 'air_force'], true)) {
                $targetTrack = str_contains($catLower, 'issb') ? 'issb' : (str_contains($catLower, 'soldier') || str_contains($catLower, 'sainik') || str_contains($catLower, 'sailor') || str_contains($catLower, 'airman') ? 'soldier' : ($exam->target_track ?: 'prelim'));
            } elseif ($branch === 'police') {
                if (str_contains($catLower, 'constable')) {
                    $targetTrack = 'constable';
                } elseif (str_contains($catLower, 'asi') || str_contains($catLower, 'assistant')) {
                    $targetTrack = 'asi';
                } elseif (str_contains($catLower, 'si') || str_contains($catLower, 'sub-inspector')) {
                    $targetTrack = 'si';
                } else {
                    $targetTrack = $exam->target_track ?: 'si';
                }
            } else {
                $targetTrack = $exam->target_track ?: 'general';
            }
        }

        $updateData = array_merge($validated, [
            'status' => $status,
            'fee' => $fee,
            'access_type' => $accessType,
            'is_paid_for_external' => $isPaid,
            'exam_type' => $validated['exam_type'] ?? $exam->exam_type,
            'is_public_for_external' => $request->has('is_public_for_external') ? (bool) $request->input('is_public_for_external') : $exam->is_public_for_external,
            'schedule_start' => !empty($validated['schedule_start']) ? Carbon::parse($validated['schedule_start']) : null,
            'schedule_end' => !empty($validated['schedule_end']) ? Carbon::parse($validated['schedule_end']) : null,
        ]);

        if (\Illuminate\Support\Facades\Schema::hasColumn('exams', 'target_track')) {
            $updateData['target_track'] = $targetTrack;
        } else {
            try {
                \Illuminate\Support\Facades\Schema::table('exams', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->string('target_track', 50)->nullable()->index()->after('branch');
                });
                $updateData['target_track'] = $targetTrack;
            } catch (\Throwable $e) {}
        }

        $exam->update($updateData);

        // Append newly added questions if provided via PDF, JSON, or text
        if ($request->filled('questions_json')) {
            $newQuestions = json_decode($request->input('questions_json'), true);
            if (is_array($newQuestions) && !empty($newQuestions)) {
                $orderSeq = Question::where('exam_id', $exam->id)->max('order_seq') ?? 0;
                $currentCount = Question::where('exam_id', $exam->id)->count();
                $totalQCount = max(1, $currentCount + count($newQuestions));
                $perQuestionMark = $exam->total_marks > 0 ? ($exam->total_marks / $totalQCount) : 1.00;

                foreach ($newQuestions as $q) {
                    if (empty($q['question_text'])) continue;
                    Question::create([
                        'exam_id' => $exam->id,
                        'branch' => $exam->branch,
                        'exam_type' => $exam->exam_type ?? 'iq_mcq',
                        'question_text' => $q['question_text'],
                        'options' => [
                            ['key' => 'A', 'text' => $q['option_a'] ?? ($q['options'][0]['text'] ?? '')],
                            ['key' => 'B', 'text' => $q['option_b'] ?? ($q['options'][1]['text'] ?? '')],
                            ['key' => 'C', 'text' => $q['option_c'] ?? ($q['options'][2]['text'] ?? '')],
                            ['key' => 'D', 'text' => $q['option_d'] ?? ($q['options'][3]['text'] ?? '')],
                        ],
                        'correct_answer' => strtoupper($q['correct_answer'] ?? 'A'),
                        'explanation' => $q['explanation'] ?? null,
                        'marks' => $perQuestionMark,
                        'negative_marks' => (float)$exam->negative_marking_per_wrong,
                        'difficulty' => 'medium',
                        'order_seq' => ++$orderSeq,
                    ]);
                }

                $exam->random_question_count = Question::where('exam_id', $exam->id)->count();
                $exam->save();
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Assessment module '{$exam->title}' updated successfully.",
                'exam' => $exam->fresh(['questions']),
            ]);
        }

        if ($request->filled('return_to')) {
            return redirect($request->input('return_to'))->with('success', "Assessment module '{$exam->title}' settings updated successfully.");
        }

        if ($exam->branch) {
            return redirect()->route('admin.exams.branch', $exam->branch)->with('success', "Assessment module '{$exam->title}' settings updated successfully.");
        }

        return redirect()->route('admin.exam_management.index')->with('success', "Assessment module '{$exam->title}' updated successfully.");
    }

    public function destroy(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);
        $title = $exam->title;
        $status = $exam->status;
        $branch = $exam->branch;

        \DB::transaction(function () use ($exam) {
            $exam->questions()->delete();
            $exam->watWords()->delete();

            $attemptIds = $exam->attempts()->pluck('id');
            if ($attemptIds->isNotEmpty()) {
                \App\Models\ExamAttemptAnswer::whereIn('attempt_id', $attemptIds)->delete();
                $exam->attempts()->delete();
            }

            $exam->delete();
        });

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Exam '{$title}' deleted permanently.",
                'deleted_id' => (int) $id,
                'status' => $status,
                'branch' => $branch,
            ]);
        }

        return back()->with('success', "Exam '{$title}' deleted successfully.");
    }

    public function questions($id)
    {
        $exam = Exam::with('questions')->findOrFail($id);
        return view('backend.exams.questions', compact('exam'));
    }

    public function storeQuestion(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);

        $validated = $request->validate([
            'question_text' => 'required|string',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'correct_answer' => 'required|in:A,B,C,D',
            'marks' => 'required|numeric|min:0.5',
            'negative_marks' => 'nullable|numeric|min:0',
            'explanation' => 'nullable|string',
            'difficulty' => 'required|in:easy,medium,hard',
        ]);

        $options = [
            ['key' => 'A', 'text' => $validated['option_a']],
            ['key' => 'B', 'text' => $validated['option_b']],
            ['key' => 'C', 'text' => $validated['option_c']],
            ['key' => 'D', 'text' => $validated['option_d']],
        ];

        $orderSeq = $exam->questions()->count() + 1;

        Question::create([
            'exam_id' => $exam->id,
            'branch' => $exam->branch,
            'exam_type' => $exam->exam_type,
            'question_text' => $validated['question_text'],
            'options' => $options,
            'correct_answer' => $validated['correct_answer'],
            'marks' => $validated['marks'],
            'negative_marks' => $validated['negative_marks'] ?? $exam->negative_marking_per_wrong,
            'explanation' => $validated['explanation'] ?? null,
            'order_seq' => $orderSeq,
            'difficulty' => $validated['difficulty'],
        ]);

        return back()->with('success', 'Question added to question bank.');
    }

    public function watWords($id)
    {
        $exam = Exam::with('watWords')->findOrFail($id);
        return view('backend.exams.wat_words', compact('exam'));
    }

    public function storeWatWord(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);

        $validated = $request->validate([
            'word' => 'required|string|max:50',
            'display_seconds' => 'required|integer|min:5|max:30',
        ]);

        $orderSeq = $exam->watWords()->count() + 1;

        WatWord::create([
            'exam_id' => $exam->id,
            'word' => strtoupper(trim($validated['word'])),
            'display_seconds' => $validated['display_seconds'],
            'order_seq' => $orderSeq,
        ]);

        return back()->with('success', 'WAT word added to flash sequence.');
    }

    public function attempts(Request $request)
    {
        $query = ExamAttempt::with(['exam', 'user', 'student']);

        if ($request->filled('exam_id')) {
            $query->where('exam_id', $request->exam_id);
        }

        $attempts = $query->orderBy('id', 'desc')->paginate(20);
        $exams = Exam::all();

        return view('backend.exams.attempts', compact('attempts', 'exams'));
    }

    public function showAttempt($id)
    {
        $attempt = ExamAttempt::with(['exam.questions', 'exam.watWords', 'user', 'student', 'answers.question', 'answers.watWord'])->findOrFail($id);
        return view('backend.exams.attempt_detail', compact('attempt'));
    }
}
