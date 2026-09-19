<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Course;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::withCount(['batches', 'students'])->get();
        return view('backend.courses.index', compact('courses'));
    }

    public function create()
    {
        return view('backend.courses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'duration' => 'required|string',
            'fee' => 'required|numeric|min:0',
            'eligibility' => 'nullable|string',
            'schedule_info' => 'nullable|string',
            'admission_status' => 'required|in:open,upcoming,closed',
            'description' => 'nullable|string',
            'is_featured' => 'boolean',
        ]);

        $slug = Str::slug($validated['title']);
        $count = Course::where('slug', 'like', "{$slug}%")->count();
        if ($count > 0) {
            $slug = "{$slug}-" . ($count + 1);
        }

        Course::create(array_merge($validated, [
            'slug' => $slug,
            'is_featured' => $request->boolean('is_featured'),
        ]));

        return redirect()->route('admin.courses.index')->with('success', 'Course created successfully.');
    }

    public function edit($id)
    {
        $course = Course::findOrFail($id);
        return view('backend.courses.edit', compact('course'));
    }

    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'duration' => 'required|string',
            'fee' => 'required|numeric|min:0',
            'eligibility' => 'nullable|string',
            'schedule_info' => 'nullable|string',
            'admission_status' => 'required|in:open,upcoming,closed',
            'description' => 'nullable|string',
        ]);

        $course->update(array_merge($validated, [
            'is_featured' => $request->boolean('is_featured'),
        ]));

        return redirect()->route('admin.courses.index')->with('success', 'Course updated successfully.');
    }

    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        if ($course->students()->count() > 0) {
            return back()->with('error', 'Cannot delete course with active enrolled cadets.');
        }
        $course->delete();
        return redirect()->route('admin.courses.index')->with('success', 'Course deleted.');
    }
}
