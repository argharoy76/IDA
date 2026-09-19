<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        switch ($user->role) {
            case 'super_admin':
            case 'admin':
            case 'finance_manager':
                return redirect()->route('admin.dashboard');

            case 'instructor':
                return redirect()->route('instructor.dashboard');

            case 'academic_student':
                return redirect()->route('cadet.dashboard');

            case 'external_student':
                return redirect()->route('external.dashboard');

            default:
                return redirect()->route('home');
        }
    }
}
