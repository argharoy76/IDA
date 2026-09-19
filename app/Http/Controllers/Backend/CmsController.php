<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use App\Models\CmsSetting;
use App\Models\CmsNotice;
use App\Models\CmsGalleryItem;
use App\Models\ContactInquiry;
use Carbon\Carbon;

class CmsController extends Controller
{
    public function index()
    {
        $settings = CmsSetting::all()->keyBy('key');
        $notices = CmsNotice::orderBy('publish_date', 'desc')->get();
        $gallery = CmsGalleryItem::orderBy('display_order')->get();
        $inquiries = ContactInquiry::orderBy('created_at', 'desc')->get();
        $courses = \App\Models\Course::withCount('batches')->get();

        return view('backend.cms.index', compact('settings', 'notices', 'gallery', 'inquiries', 'courses'));
    }

    public function pageHome()
    {
        $settings = CmsSetting::all()->keyBy('key');
        $courses = \App\Models\Course::orderBy('title')->get();
        return view('backend.cms.home', compact('settings', 'courses'));
    }

    public function pageCourses()
    {
        $settings = CmsSetting::all()->keyBy('key');
        $courses = \App\Models\Course::withCount('batches')->get();
        return view('backend.cms.courses', compact('settings', 'courses'));
    }

    public function pageOnlineTests()
    {
        $settings = CmsSetting::all()->keyBy('key');
        $exams = \App\Models\Exam::withCount('questions')->get();
        return view('backend.cms.online_tests', compact('settings', 'exams'));
    }

    public function pageAbout()
    {
        $settings = CmsSetting::all()->keyBy('key');
        return view('backend.cms.about', compact('settings'));
    }

    public function pageClasses()
    {
        $settings = CmsSetting::all()->keyBy('key');
        $batches = \App\Models\Batch::with('course', 'instructor')->where('status', 'active')->get();
        return view('backend.cms.classes', compact('settings', 'batches'));
    }

    public function pageContact()
    {
        $settings = CmsSetting::all()->keyBy('key');
        return view('backend.cms.contact', compact('settings'));
    }

    public function pageGallery()
    {
        $settings = CmsSetting::all()->keyBy('key');
        $gallery = CmsGalleryItem::orderBy('display_order')->get();
        return view('backend.cms.gallery', compact('settings', 'gallery'));
    }

    public function pageNotices()
    {
        $settings = CmsSetting::all()->keyBy('key');
        $notices = CmsNotice::orderBy('publish_date', 'desc')->get();
        return view('backend.cms.notices', compact('settings', 'notices'));
    }

    public function pageBranding()
    {
        $settings = CmsSetting::all()->keyBy('key');
        return view('backend.cms.branding', compact('settings'));
    }

    public function pageInquiries()
    {
        $inquiries = ContactInquiry::orderBy('created_at', 'desc')->get();
        return view('backend.cms.inquiries', compact('inquiries'));
    }

    public function updateSettings(Request $request)
    {
        $data = $request->except(['_token', '_method']);

        // Ensure upload destination folder exists
        $uploadPath = public_path('uploads/cms');
        if (!File::isDirectory($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true, true);
        }

        // Handle File Uploads
        $fileFields = ['site_logo', 'site_crest', 'hero_bg_image', 'about_image', 'cta_bg_image'];
        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $filename = $field . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move($uploadPath, $filename);
                $data[$field] = 'uploads/cms/' . $filename;

                // If official site_logo is uploaded, sync to site_crest so all legacy references stay unified
                if ($field === 'site_logo') {
                    $data['site_crest'] = 'uploads/cms/' . $filename;
                }
            } elseif ($request->filled('remove_' . $field)) {
                $oldPath = cms($field);
                if ($oldPath && str_starts_with($oldPath, 'uploads/cms/') && File::exists(public_path($oldPath))) {
                    @unlink(public_path($oldPath));
                }
                $data[$field] = '';
                if ($field === 'site_logo') {
                    $data['site_crest'] = '';
                }
            }
        }

        // Remove the remove_ flags from data
        foreach ($fileFields as $field) {
            unset($data['remove_' . $field]);
        }

        // If submitted from homepage editor and no courses were checked, explicitly save empty array
        if ($request->has('form_section') && $request->input('form_section') === 'home') {
            if (!$request->has('featured_courses')) {
                $data['featured_courses'] = [];
            }
            unset($data['form_section']);
        }

        // Persist all settings (null values converted to empty string for clean removal)
        foreach ($data as $key => $val) {
            CmsSetting::updateOrCreate(
                ['key' => $key],
                ['value' => is_array($val) ? json_encode(array_values($val)) : ($val ?? '')]
            );
        }

        // Clear CMS cache (both driver cache and request memory)
        if (function_exists('cms_clear_cache')) {
            cms_clear_cache();
        } else {
            Cache::forget('cms_settings_map');
        }

        return back()->with('success', 'Academy website CMS settings & appearance updated successfully.');
    }

    public function storeNotice(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string',
            'publish_date' => 'required|date',
        ]);

        $slug = Str::slug($validated['title']);
        $count = CmsNotice::where('slug', 'like', "{$slug}%")->count();
        if ($count > 0) {
            $slug = "{$slug}-" . ($count + 1);
        }

        CmsNotice::create(array_merge($validated, [
            'slug' => $slug,
            'is_urgent' => $request->boolean('is_urgent'),
            'is_pinned' => $request->boolean('is_pinned'),
            'is_published' => true,
        ]));

        return back()->with('success', 'Notice published to public bulletin.');
    }

    public function deleteNotice($id)
    {
        $notice = CmsNotice::findOrFail($id);
        $notice->delete();

        return back()->with('success', 'Notice deleted successfully.');
    }

    public function storeGallery(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'caption' => 'nullable|string',
            'image_path' => 'nullable|string',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $imagePath = $validated['image_path'] ?? 'gallery-default.jpg';

        if ($request->hasFile('image_file')) {
            $uploadPath = public_path('uploads/cms');
            if (!File::isDirectory($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true, true);
            }
            $file = $request->file('image_file');
            $filename = 'gallery_' . time() . '_' . Str::random(5) . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $filename);
            $imagePath = 'uploads/cms/' . $filename;
        }

        $order = CmsGalleryItem::count() + 1;

        CmsGalleryItem::create([
            'title' => $validated['title'],
            'category' => $validated['category'],
            'caption' => $validated['caption'] ?? null,
            'image_path' => $imagePath,
            'display_order' => $order,
            'is_published' => true,
        ]);

        return back()->with('success', 'Gallery item added successfully.');
    }

    public function deleteGallery($id)
    {
        $item = CmsGalleryItem::findOrFail($id);
        $item->delete();

        return back()->with('success', 'Gallery photo deleted successfully.');
    }

    public function updateInquiryStatus(Request $request, $id)
    {
        $inquiry = ContactInquiry::findOrFail($id);
        $inquiry->update(['status' => $request->input('status', 'read')]);

        return back()->with('success', 'Inquiry marked as ' . $inquiry->status . '.');
    }

    public function deleteInquiry($id)
    {
        $inquiry = ContactInquiry::findOrFail($id);
        $inquiry->delete();

        return back()->with('success', 'Inquiry deleted successfully.');
    }

    public function addHeroSlide(Request $request)
    {
        $currentSlides = cms_hero_slides();

        $uploadPath = public_path('uploads/cms');
        if (!File::isDirectory($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true, true);
        }

        $addedCount = 0;

        // 1. Multiple files upload
        if ($request->hasFile('slide_images')) {
            foreach ($request->file('slide_images') as $file) {
                if ($file && $file->isValid()) {
                    $ext = strtolower($file->getClientOriginalExtension());
                    $filename = 'hero_slide_' . time() . '_' . Str::random(6) . '.' . $ext;
                    $file->move($uploadPath, $filename);
                    $currentSlides[] = 'uploads/cms/' . $filename;
                    $addedCount++;
                }
            }
        }

        // 2. Single file upload
        if ($request->hasFile('slide_image')) {
            $file = $request->file('slide_image');
            if ($file && $file->isValid()) {
                $ext = strtolower($file->getClientOriginalExtension());
                $filename = 'hero_slide_' . time() . '_' . Str::random(6) . '.' . $ext;
                $file->move($uploadPath, $filename);
                $currentSlides[] = 'uploads/cms/' . $filename;
                $addedCount++;
            }
        }

        // 3. Image URL input
        if ($request->filled('slide_url')) {
            $url = trim($request->input('slide_url'));
            if (filter_var($url, FILTER_VALIDATE_URL)) {
                $currentSlides[] = $url;
                $addedCount++;
            }
        }

        if ($addedCount > 0) {
            CmsSetting::updateOrCreate(
                ['key' => 'hero_slides'],
                ['value' => json_encode(array_values($currentSlides))]
            );
            cms_clear_cache();

            return back()->with('success', "{$addedCount} background slide(s) added successfully! Total active slides: " . count($currentSlides));
        }

        return back()->with('error', 'Please choose at least one valid image file or enter an image URL.');
    }

    public function setPrimaryHeroSlide($index)
    {
        $currentSlides = cms_hero_slides();
        $index = (int) $index;

        if (isset($currentSlides[$index])) {
            $selected = $currentSlides[$index];
            unset($currentSlides[$index]);
            array_unshift($currentSlides, $selected);
            $currentSlides = array_values($currentSlides);

            CmsSetting::updateOrCreate(
                ['key' => 'hero_slides'],
                ['value' => json_encode($currentSlides)]
            );
            cms_clear_cache();

            return back()->with('success', 'Primary background image updated successfully.');
        }

        return back()->with('error', 'Selected slide not found.');
    }

    public function deleteHeroSlide($index)
    {
        $currentSlides = cms_hero_slides();
        $index = (int) $index;

        if (isset($currentSlides[$index])) {
            $removed = $currentSlides[$index];
            unset($currentSlides[$index]);
            $currentSlides = array_values($currentSlides);

            if (str_starts_with($removed, 'uploads/cms/') && File::exists(public_path($removed))) {
                @unlink(public_path($removed));
            }

            CmsSetting::updateOrCreate(
                ['key' => 'hero_slides'],
                ['value' => json_encode($currentSlides)]
            );
            cms_clear_cache();

            return back()->with('success', 'Slide removed successfully. Remaining active slides: ' . count($currentSlides));
        }

        return back()->with('error', 'Selected slide not found.');
    }

    public function resetHeroSlides()
    {
        $defaultSlides = [
            'https://images.unsplash.com/photo-1541872703-74c5e44368f9?q=80&w=1600&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1508614589041-895b88991e3e?q=80&w=1600&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1519074069444-1ba4ea16e901?q=80&w=1600&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1526778548025-fa2f459cd5c1?q=80&w=1600&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1579952363873-27f3bade9f55?q=80&w=1600&auto=format&fit=crop',
        ];

        CmsSetting::updateOrCreate(
            ['key' => 'hero_slides'],
            ['value' => json_encode($defaultSlides)]
        );
        cms_clear_cache();

        return back()->with('success', 'Hero slides reset to the default 5 military carousel photos.');
    }
}
