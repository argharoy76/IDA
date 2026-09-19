<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Instructor;
use App\Models\User;

class InstructorController extends Controller
{
    public function index()
    {
        $instructors = Instructor::with(['user', 'batches'])->get();
        return view('backend.instructors.index', compact('instructors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'nullable|string|min:6',
            'phone' => 'required|string|max:20',
            'designation' => 'required|string',
            'specialization' => 'required|string',
            'instructor_code' => 'required|string|unique:instructors,instructor_code',
            'bio' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password'] ?? 'password'),
            'role' => 'instructor',
            'phone' => $validated['phone'],
            'status' => 'active',
        ]);

        Instructor::create([
            'user_id' => $user->id,
            'instructor_code' => $validated['instructor_code'],
            'designation' => $validated['designation'],
            'specialization' => $validated['specialization'],
            'phone' => $validated['phone'],
            'bio' => $validated['bio'] ?? null,
            'status' => 'active',
        ]);

        return redirect()->route('admin.instructors.index')->with('success', 'Instructor profile created successfully.');
    }
}
