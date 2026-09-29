<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\CmsNotice;
use App\Models\CmsGalleryItem;
use App\Models\CmsSetting;
use App\Models\ContactInquiry;
use App\Models\Exam;
use App\Models\Student;
use App\Models\Instructor;
use App\Models\Batch;

class HomeController extends Controller
{
    public function index()
    {
        $featuredSetting = cms('featured_courses');
        if ($featuredSetting !== null && $featuredSetting !== '') {
            $courseIds = is_array($featuredSetting) ? $featuredSetting : json_decode($featuredSetting, true);
            if (is_array($courseIds)) {
                $courses = !empty($courseIds) ? Course::whereIn('id', $courseIds)->get() : collect();
            } else {
                $courses = Course::where('is_featured', true)->take(4)->get();
                if ($courses->isEmpty()) {
                    $courses = Course::take(4)->get();
                }
            }
        } else {
            $courses = Course::where('is_featured', true)->take(4)->get();
            if ($courses->isEmpty()) {
                $courses = Course::take(4)->get();
            }
        }

        $notices = CmsNotice::where('is_published', true)->orderBy('publish_date', 'desc')->take(5)->get();
        $gallery = CmsGalleryItem::where('is_published', true)->take(6)->get();

        $totalCadetsOverride = cms('stat_total_cadets');
        $activeBatchesOverride = cms('stat_active_batches');

        $stats = [
            'total_cadets' => filled($totalCadetsOverride) ? (int) $totalCadetsOverride : Student::where('student_type', 'academic')->count(),
            'active_batches' => filled($activeBatchesOverride) ? (int) $activeBatchesOverride : Batch::where('status', 'active')->count(),
            'instructors_count' => Instructor::count(),
            'recommended_cadets' => (int) cms('stat_recommended_cadets', 148), // Commission success count
        ];

        return view('frontend.home.index', compact('courses', 'notices', 'gallery', 'stats'));
    }

    public function about()
    {
        $instructors = Instructor::with('user')->where('status', 'active')->get();
        $teamMembers = \App\Models\TeamMember::where('is_active', true)
            ->orderBy('display_category')
            ->orderBy('display_order')
            ->get();
        return view('frontend.about.index', compact('instructors', 'teamMembers'));
    }

    public function courses(Request $request)
    {
        $query = Course::query();
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        $courses = $query->get();
        $categories = Course::select('category')->distinct()->pluck('category');
        return view('frontend.courses.index', compact('courses', 'categories'));
    }

    public function courseDetail($slug)
    {
        $course = Course::with(['activeBatches.primaryInstructor.user'])->where('slug', $slug)->firstOrFail();
        $relatedCourses = Course::where('id', '!=', $course->id)->take(3)->get();
        return view('frontend.courses.detail', compact('course', 'relatedCourses'));
    }

    public function classes()
    {
        $batches = Batch::with(['course', 'primaryInstructor.user'])->where('status', 'active')->get();
        return view('frontend.classes.index', compact('batches'));
    }

    public function gallery(Request $request)
    {
        $query = CmsGalleryItem::where('is_published', true)->orderBy('display_order');
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        $items = $query->get();
        $categories = CmsGalleryItem::select('category')->distinct()->pluck('category');

        return view('frontend.gallery.index', compact('items', 'categories'));
    }

    public function notices()
    {
        $notices = CmsNotice::where('is_published', true)->orderBy('is_pinned', 'desc')->orderBy('publish_date', 'desc')->paginate(10);
        return view('frontend.notices.index', compact('notices'));
    }

    public function contact()
    {
        return view('frontend.contact.index');
    }

    public function submitInquiry(Request $request)
    {
        // Honeypot check: silently discard bot spam
        if ($request->filled('website_hp')) {
            return back()->with('success', 'Thank you for reaching out to Imperial Defence Academy! Our admissions officer will get in touch with you shortly.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|min:10',
        ]);

        ContactInquiry::create($validated);

        return back()->with('success', 'Thank you for reaching out to Imperial Defence Academy! Our admissions officer will get in touch with you shortly.');
    }

    public function onlineTests(Request $request)
    {
        $query = Exam::where('is_paid_for_external', false)
            ->where('is_public_for_external', true)
            ->where('status', '!=', 'archived');

        $activeBranch = $request->input('branch');
        if ($activeBranch && in_array($activeBranch, ['army', 'navy', 'air_force', 'police'])) {
            $query->where('branch', $activeBranch);
        }

        $exams = $query->orderBy('schedule_start', 'asc')->get();

        $branchCounts = [
            'all' => Exam::where('is_paid_for_external', false)->where('is_public_for_external', true)->where('status', '!=', 'archived')->count(),
            'army' => Exam::where('is_paid_for_external', false)->where('is_public_for_external', true)->where('branch', 'army')->where('status', '!=', 'archived')->count(),
            'navy' => Exam::where('is_paid_for_external', false)->where('is_public_for_external', true)->where('branch', 'navy')->where('status', '!=', 'archived')->count(),
            'air_force' => Exam::where('is_paid_for_external', false)->where('is_public_for_external', true)->where('branch', 'air_force')->where('status', '!=', 'archived')->count(),
            'police' => Exam::where('is_paid_for_external', false)->where('is_public_for_external', true)->where('branch', 'police')->where('status', '!=', 'archived')->count(),
        ];

        return view('frontend.online_tests.index', compact('exams', 'activeBranch', 'branchCounts'));
    }
}
