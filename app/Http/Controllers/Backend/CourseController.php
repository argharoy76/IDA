<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Course;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $selectedBranch = $request->query('branch');
        if ($selectedBranch === 'all') {
            $selectedBranch = null;
        }
        $selectedTrack = $request->query('track');
        $search = $request->query('search');

        // Standardized 4-Wing Lineup Stats
        $stats = [
            'total' => Course::count(),
            'navy' => Course::where('branch', 'navy')->count(),
            'police' => Course::where('branch', 'police')->count(),
            'army' => Course::where('branch', 'army')->count(),
            'air_force' => Course::where('branch', 'air_force')->count(),
        ];

        // Track stats (contextual to selected branch or global)
        $trackQuery = Course::query();
        if ($selectedBranch && in_array($selectedBranch, ['navy', 'police', 'army', 'air_force'])) {
            $trackQuery->where('branch', $selectedBranch);
        }

        $stats['prelim'] = (clone $trackQuery)->where('target_track', 'preliminary')->count();
        $stats['issb'] = (clone $trackQuery)->where('target_track', 'issb')->count();
        $stats['constable'] = (clone $trackQuery)->where('target_track', 'constable')->count();
        $stats['si'] = (clone $trackQuery)->where('target_track', 'si')->count();
        $stats['asi'] = (clone $trackQuery)->where('target_track', 'asi')->count();
        $stats['soldier'] = (clone $trackQuery)->where('target_track', 'soldier')->count();

        // Filter courses query
        $query = Course::withCount(['batches', 'students']);

        if ($selectedBranch && in_array($selectedBranch, ['navy', 'police', 'army', 'air_force'])) {
            $query->where('branch', $selectedBranch);
        }

        if ($selectedTrack && in_array($selectedTrack, ['preliminary', 'issb', 'constable', 'si', 'asi', 'soldier'])) {
            $query->where('target_track', $selectedTrack);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('course_code', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $courses = $query->orderBy('id', 'desc')->paginate(25)->withQueryString();

        return view('backend.courses.index', compact(
            'courses',
            'selectedBranch',
            'selectedTrack',
            'search',
            'stats'
        ));
    }

    public function create()
    {
        return view('backend.courses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_code' => 'nullable|string|max:50',
            'title' => 'required|string|max:255',
            'branch' => 'required|in:navy,police,army,air_force',
            'target_track' => 'required|in:preliminary,issb,constable,si,asi,soldier',
            'category' => 'nullable|string|max:100',
            'duration' => 'required|string|max:100',
            'fee' => 'required|numeric|min:0',
            'eligibility' => 'nullable|string|max:500',
            'schedule_info' => 'nullable|string|max:500',
            'admission_status' => 'required|in:open,upcoming,closed',
            'description' => 'nullable|string',
            'is_featured' => 'nullable|boolean',
        ]);

        // Default category matching branch if not explicitly given
        $category = $validated['category'] ?? null;
        if (empty($category)) {
            $category = match($validated['branch']) {
                'navy' => 'Navy (BNA)',
                'police' => 'Police Service',
                'army' => 'Army (BMA)',
                'air_force' => 'Air Force (BAFA)',
                default => 'General Course',
            };
        }

        $slug = Str::slug($validated['title']);
        $count = Course::where('slug', 'like', "{$slug}%")->count();
        if ($count > 0) {
            $slug = "{$slug}-" . ($count + 1);
        }

        $course = Course::create([
            'course_code' => $validated['course_code'] ?? null,
            'title' => $validated['title'],
            'slug' => $slug,
            'branch' => $validated['branch'],
            'target_track' => $validated['target_track'],
            'category' => $category,
            'duration' => $validated['duration'],
            'fee' => $validated['fee'],
            'eligibility' => $validated['eligibility'] ?? null,
            'schedule_info' => $validated['schedule_info'] ?? null,
            'admission_status' => $validated['admission_status'],
            'description' => $validated['description'] ?? null,
            'is_featured' => $request->boolean('is_featured'),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Course '{$course->title}' created successfully.",
                'course' => $course,
            ]);
        }

        return redirect()->route('admin.courses.index', ['branch' => $course->branch])
            ->with('success', "Course '{$course->title}' created successfully.");
    }

    public function edit($id)
    {
        $course = Course::findOrFail($id);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'course' => $course,
            ]);
        }

        return view('backend.courses.edit', compact('course'));
    }

    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $validated = $request->validate([
            'course_code' => 'nullable|string|max:50',
            'title' => 'required|string|max:255',
            'branch' => 'required|in:navy,police,army,air_force',
            'target_track' => 'required|in:preliminary,issb,constable,si,asi,soldier',
            'category' => 'nullable|string|max:100',
            'duration' => 'required|string|max:100',
            'fee' => 'required|numeric|min:0',
            'eligibility' => 'nullable|string|max:500',
            'schedule_info' => 'nullable|string|max:500',
            'admission_status' => 'required|in:open,upcoming,closed',
            'description' => 'nullable|string',
            'is_featured' => 'nullable|boolean',
        ]);

        $category = $validated['category'] ?? $course->category;
        if (empty($category)) {
            $category = match($validated['branch']) {
                'navy' => 'Navy (BNA)',
                'police' => 'Police Service',
                'army' => 'Army (BMA)',
                'air_force' => 'Air Force (BAFA)',
                default => 'General Course',
            };
        }

        $course->update([
            'course_code' => $validated['course_code'] ?? null,
            'title' => $validated['title'],
            'branch' => $validated['branch'],
            'target_track' => $validated['target_track'],
            'category' => $category,
            'duration' => $validated['duration'],
            'fee' => $validated['fee'],
            'eligibility' => $validated['eligibility'] ?? null,
            'schedule_info' => $validated['schedule_info'] ?? null,
            'admission_status' => $validated['admission_status'],
            'description' => $validated['description'] ?? null,
            'is_featured' => $request->boolean('is_featured'),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Course '{$course->title}' updated successfully.",
                'course' => $course,
            ]);
        }

        return redirect()->route('admin.courses.index', ['branch' => $course->branch])
            ->with('success', "Course '{$course->title}' updated successfully.");
    }

    public function destroy(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        if ($course->students()->count() > 0) {
            $msg = 'Cannot delete course with active enrolled cadets.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        $courseTitle = $course->title;
        $course->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Course '{$courseTitle}' deleted successfully.",
            ]);
        }

        return redirect()->route('admin.courses.index')->with('success', "Course '{$courseTitle}' deleted.");
    }
}
