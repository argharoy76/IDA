<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

// Backend (Admin) Controllers
use App\Http\Controllers\Backend\DashboardController as AdminDashboardController;
use App\Http\Controllers\Backend\StudentController as AdminStudentController;
use App\Http\Controllers\Backend\InstructorController as AdminInstructorController;
use App\Http\Controllers\Backend\CourseController as AdminCourseController;
use App\Http\Controllers\Backend\BatchController as AdminBatchController;
use App\Http\Controllers\Backend\RoutineController as AdminRoutineController;
use App\Http\Controllers\Backend\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Backend\FeeController as AdminFeeController;
use App\Http\Controllers\Backend\PaymentVerificationController as AdminPaymentVerificationController;
use App\Http\Controllers\Backend\FinanceController as AdminFinanceController;
use App\Http\Controllers\Backend\ExamController as AdminExamController;
use App\Http\Controllers\Backend\DossierController as AdminDossierController;
use App\Http\Controllers\Backend\CmsController as AdminCmsController;
use App\Http\Controllers\Backend\AuditLogController as AdminAuditLogController;
use App\Http\Controllers\Backend\AccountController as AdminAccountController;
use App\Http\Controllers\Backend\StudentAccountController as AdminStudentAccountController;
use App\Http\Controllers\Backend\ExamManagementController as AdminExamManagementController;
use App\Http\Controllers\Backend\AdminControlController;

// Instructor Controllers
use App\Http\Controllers\Instructor\DashboardController as InstructorDashboardController;
use App\Http\Controllers\Instructor\StudentController as InstructorStudentController;
use App\Http\Controllers\Instructor\AttendanceController as InstructorAttendanceController;

// Cadet Controllers
use App\Http\Controllers\Cadet\DashboardController as CadetDashboardController;
use App\Http\Controllers\Cadet\RoutineController as CadetRoutineController;
use App\Http\Controllers\Cadet\AttendanceController as CadetAttendanceController;
use App\Http\Controllers\Cadet\FeeController as CadetFeeController;
use App\Http\Controllers\Cadet\DossierController as CadetDossierController;
use App\Http\Controllers\Cadet\ExamController as CadetExamController;

// External Student Controllers
use App\Http\Controllers\External\DashboardController as ExternalDashboardController;
use App\Http\Controllers\External\TestController as ExternalTestController;

/*
|--------------------------------------------------------------------------
| Public Academy Website Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/courses', [HomeController::class, 'courses'])->name('courses');
Route::get('/courses/{slug}', [HomeController::class, 'courseDetail'])->name('courses.detail');
Route::get('/classes', [HomeController::class, 'classes'])->name('classes');
Route::get('/gallery', [HomeController::class, 'gallery'])->name('gallery');
Route::get('/notices', [HomeController::class, 'notices'])->name('notices');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'submitInquiry'])->name('contact.submit');
Route::get('/online-tests', [HomeController::class, 'onlineTests'])->name('online_tests');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.post');
Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'adminLogin'])->middleware('throttle:10,1')->name('admin.login.post');
Route::get('/admin/register', [AuthController::class, 'showAdminRegister'])->name('admin.register');
Route::post('/admin/register', [AuthController::class, 'adminRegister'])->middleware('throttle:10,1')->name('admin.register.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1')->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout.get');

// Super Admin Local/Testing Shortcut (Strictly disabled in production)
Route::get('/quick-admin', function () {
    if (!app()->environment('local', 'testing')) {
        abort(403, 'Administrative bypass is disabled in production environments.');
    }
    $admin = \App\Models\User::where('role', 'super_admin')->first();
    if ($admin) {
        \Illuminate\Support\Facades\Auth::login($admin);
        request()->session()->regenerate();
        \App\Models\AuditLog::log('login_quick_admin', 'User', $admin->id, null, [
            'role' => $admin->role,
            'email' => $admin->email,
            'name' => $admin->name,
        ]);
        return redirect()->route('admin.dashboard')->with('success', 'Authenticated as Super Administrator.');
    }
    return redirect()->route('login');
})->name('quick_admin');

Route::get('/admin-login', function () {
    if (!app()->environment('local', 'testing')) {
        return redirect()->route('admin.login');
    }
    return redirect()->route('quick_admin');
});

/*
|--------------------------------------------------------------------------
| Authenticated Dashboard Redirection
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');

/*
|--------------------------------------------------------------------------
| Admin & Finance Management Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth'])->name('admin.')->group(function () {
    // Shared between Super Admin, Admin, and Finance Manager
    Route::middleware('role:super_admin,pro_admin,admin,finance_manager')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Fees & Payments
        Route::get('/fees', [AdminFeeController::class, 'index'])->name('fees.index');
        Route::get('/fees/invoices/create', [AdminFeeController::class, 'createInvoice'])->name('fees.create_invoice');
        Route::post('/fees/invoices', [AdminFeeController::class, 'storeInvoice'])->name('fees.store_invoice');
        Route::post('/fees/store', [AdminFeeController::class, 'storeInvoice'])->name('fees.store');

        Route::get('/payments/verification', [AdminPaymentVerificationController::class, 'index'])->name('payments.verification');
        Route::post('/payments/{id}/approve', [AdminPaymentVerificationController::class, 'approve'])->name('payments.approve');
        Route::post('/payments/{id}/reject', [AdminPaymentVerificationController::class, 'reject'])->name('payments.reject');

        // Finance & Cash Flow
        Route::get('/finance', [AdminFinanceController::class, 'index'])->name('finance.index');
        Route::post('/finance/transaction', [AdminFinanceController::class, 'storeTransaction'])->name('finance.store');
        Route::post('/finance/store-transaction', [AdminFinanceController::class, 'storeTransaction'])->name('finance.store_transaction');
        Route::get('/finance/reports', [AdminFinanceController::class, 'reports'])->name('finance.reports');
    });

    // Restricted strictly to Super Admin, Pro Admin and Admin (Core Executive Clearance)
    Route::middleware('role:super_admin,pro_admin,admin')->group(function () {
        // Students
        Route::resource('students', AdminStudentController::class);
        Route::post('/students/{id}/transfer-batch', [AdminStudentController::class, 'transferBatch'])->name('students.transfer_batch');

        // Instructors
        Route::resource('instructors', AdminInstructorController::class);

        // Courses & Batches
        Route::resource('courses', AdminCourseController::class);
        Route::resource('batches', AdminBatchController::class);

        // Routine & Attendance
        Route::resource('routines', AdminRoutineController::class);
        Route::get('/attendance', [AdminAttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/attendance', [AdminAttendanceController::class, 'store'])->name('attendance.store');

        // Examinations Engine - 4 Branches & Random Paper Generator
        Route::get('/exams/branch/{branch}', [AdminExamController::class, 'branch'])->name('exams.branch');
        Route::post('/exams/branch/{branch}/scan-pdf', [AdminExamController::class, 'scanPdf'])->name('exams.scan_pdf');
        Route::post('/exams/branch/{branch}/create-from-pdf', [AdminExamController::class, 'createFromPdf'])->name('exams.create_from_pdf');
        Route::post('/exams/branch/{branch}/upload-pdf', [AdminExamController::class, 'uploadPdf'])->name('exams.upload_pdf');
        Route::post('/exams/branch/{branch}/confirm-import', [AdminExamController::class, 'confirmImport'])->name('exams.confirm_import');
        Route::post('/exams/branch/{branch}/generate', [AdminExamController::class, 'generateRandomExam'])->name('exams.generate');
        Route::post('/exams/branch/{branch}/clear-pool', [AdminExamController::class, 'clearBranchPool'])->name('exams.clear_pool');
        Route::delete('/exams/pool-questions/{id}', [AdminExamController::class, 'deleteQuestionFromPool'])->name('exams.delete_pool_question');
        Route::post('/exams/{id}/toggle-public', [AdminExamController::class, 'togglePublic'])->name('exams.toggle_public');
        Route::post('/exams/{id}/start-now', [AdminExamController::class, 'startNow'])->name('exams.start_now');
        Route::post('/exams/{id}/end-now', [AdminExamController::class, 'endNow'])->name('exams.end_now');
        Route::post('/exams/{id}/update-schedule', [AdminExamController::class, 'updateSchedule'])->name('exams.update_schedule');
        Route::post('/exams/scan-questions', [AdminExamController::class, 'scanQuestionsGlobal'])->name('exams.scan_questions_global');
        Route::delete('/exams/{examId}/questions/{questionId}', [AdminExamController::class, 'deleteExamQuestion'])->name('exams.delete_exam_question');
        Route::put('/exams/{examId}/questions/{questionId}', [AdminExamController::class, 'updateExamQuestion'])->name('exams.update_exam_question');
        Route::post('/exams/{examId}/questions/{questionId}/update', [AdminExamController::class, 'updateExamQuestion']);
        Route::post('/exams/{examId}/questions/add-single', [AdminExamController::class, 'storeSingleExamQuestion'])->name('exams.add_single_question');

        Route::resource('exams', AdminExamController::class);
        Route::get('/exams/{id}/questions', [AdminExamController::class, 'questions'])->name('exams.questions');
        Route::post('/exams/{id}/questions', [AdminExamController::class, 'storeQuestion'])->name('exams.questions.store');
        Route::post('/exams/{id}/questions-legacy', [AdminExamController::class, 'storeQuestion'])->name('exams.store_question');
        Route::get('/exams/{id}/wat-words', [AdminExamController::class, 'watWords'])->name('exams.wat_words');
        Route::post('/exams/{id}/wat-words', [AdminExamController::class, 'storeWatWord'])->name('exams.wat_words.store');
        Route::post('/exams/{id}/wat-words-legacy', [AdminExamController::class, 'storeWatWord'])->name('exams.store_wat_word');
        Route::get('/exams-attempts', [AdminExamController::class, 'attempts'])->name('exams.attempts');
        Route::get('/exams-attempts/{id}', [AdminExamController::class, 'showAttempt'])->name('exams.attempts.show');
        Route::get('/exams-attempts-detail/{id}', [AdminExamController::class, 'showAttempt'])->name('exams.attempt_detail');

        // Dossier Actions
        Route::post('/dossiers/{studentId}/observations', [AdminDossierController::class, 'storeObservation'])->name('dossiers.store_observation');
        Route::post('/dossiers/{studentId}/strengths-weaknesses', [AdminDossierController::class, 'storeStrengthWeakness'])->name('dossiers.store_strength_weakness');
        Route::post('/dossiers/{studentId}/improvement-plans', [AdminDossierController::class, 'storeImprovementPlan'])->name('dossiers.store_improvement_plan');

        // CMS Management - Separated by Frontend Pages
        Route::get('/cms', [AdminCmsController::class, 'index'])->name('cms.index');
        Route::get('/cms/home', [AdminCmsController::class, 'pageHome'])->name('cms.home');
        Route::get('/cms/courses', [AdminCmsController::class, 'pageCourses'])->name('cms.courses');
        Route::get('/cms/online-tests', [AdminCmsController::class, 'pageOnlineTests'])->name('cms.online_tests');
        Route::get('/cms/about', [AdminCmsController::class, 'pageAbout'])->name('cms.about');
        Route::post('/cms/about/team', [AdminCmsController::class, 'storeTeamMember'])->name('cms.team.store');
        Route::put('/cms/about/team/{id}', [AdminCmsController::class, 'updateTeamMember'])->name('cms.team.update');
        Route::delete('/cms/about/team/{id}', [AdminCmsController::class, 'deleteTeamMember'])->name('cms.team.delete');
        Route::post('/cms/about/team/{id}/reorder', [AdminCmsController::class, 'reorderTeamMember'])->name('cms.team.reorder');
        Route::get('/cms/classes', [AdminCmsController::class, 'pageClasses'])->name('cms.classes');
        Route::get('/cms/contact', [AdminCmsController::class, 'pageContact'])->name('cms.contact');
        Route::get('/cms/gallery', [AdminCmsController::class, 'pageGallery'])->name('cms.gallery');
        Route::get('/cms/notices', [AdminCmsController::class, 'pageNotices'])->name('cms.notices');
        Route::get('/cms/branding', [AdminCmsController::class, 'pageBranding'])->name('cms.branding');
        Route::get('/cms/inquiries', [AdminCmsController::class, 'pageInquiries'])->name('cms.inquiries');

        Route::post('/cms/settings', [AdminCmsController::class, 'updateSettings'])->name('cms.settings');
        Route::post('/cms/settings-update', [AdminCmsController::class, 'updateSettings'])->name('cms.update_settings');
        Route::post('/cms/notices', [AdminCmsController::class, 'storeNotice'])->name('cms.notices.store');
        Route::post('/cms/notices-save', [AdminCmsController::class, 'storeNotice'])->name('cms.store_notice');
        Route::post('/cms/gallery', [AdminCmsController::class, 'storeGallery'])->name('cms.gallery.store');
        Route::post('/cms/gallery-save', [AdminCmsController::class, 'storeGallery'])->name('cms.store_gallery');
        Route::delete('/cms/notices/{id}', [AdminCmsController::class, 'deleteNotice'])->name('cms.notices.delete');
        Route::delete('/cms/gallery/{id}', [AdminCmsController::class, 'deleteGallery'])->name('cms.gallery.delete');
        Route::delete('/cms/inquiries/{id}', [AdminCmsController::class, 'deleteInquiry'])->name('cms.inquiries.delete');
        Route::post('/cms/inquiries/{id}/status', [AdminCmsController::class, 'updateInquiryStatus'])->name('cms.inquiries.status');

        // Hero Slides Management
        Route::post('/cms/hero-slides', [AdminCmsController::class, 'addHeroSlide'])->name('cms.hero_slides.add');
        Route::post('/cms/hero-slides/{index}/primary', [AdminCmsController::class, 'setPrimaryHeroSlide'])->name('cms.hero_slides.primary');
        Route::delete('/cms/hero-slides/{index}', [AdminCmsController::class, 'deleteHeroSlide'])->name('cms.hero_slides.delete');
        Route::post('/cms/hero-slides-reset', [AdminCmsController::class, 'resetHeroSlides'])->name('cms.hero_slides.reset');

        Route::get('/audit-logs', [AdminAuditLogController::class, 'index'])->name('audit.index');

        // Account & Login ID Management
        Route::get('/accounts', [AdminAccountController::class, 'index'])->name('accounts.index');
        Route::post('/accounts', [AdminAccountController::class, 'store'])->name('accounts.store');
        Route::post('/accounts/{id}/update-id', [AdminAccountController::class, 'updateId'])->name('accounts.update_id');
        Route::post('/accounts/{id}/password', [AdminAccountController::class, 'updatePassword'])->name('accounts.password');

        // Student Accounts & Multi-Course Access Control Center
        Route::get('/student-accounts', [AdminStudentAccountController::class, 'index'])->name('student_accounts.index');
        Route::post('/student-accounts/offline', [AdminStudentAccountController::class, 'storeOffline'])->name('student_accounts.store_offline');
        Route::get('/student-accounts/{id}', [AdminStudentAccountController::class, 'show'])->name('student_accounts.show');
        Route::get('/student-accounts/{id}/edit', [AdminStudentAccountController::class, 'edit'])->name('student_accounts.edit');
        Route::put('/student-accounts/{id}', [AdminStudentAccountController::class, 'update'])->name('student_accounts.update');
        Route::post('/student-accounts/{id}/update-id', [AdminStudentAccountController::class, 'updateId'])->name('student_accounts.update_id');
        Route::post('/student-accounts/{id}/assign-courses', [AdminStudentAccountController::class, 'assignCourses'])->name('student_accounts.assign_courses');
        Route::post('/student-accounts/{id}/update-profile', [AdminStudentAccountController::class, 'updateProfile'])->name('student_accounts.update_profile');
        Route::post('/student-accounts/{id}/password', [AdminStudentAccountController::class, 'updatePassword'])->name('student_accounts.password');
        Route::delete('/student-accounts/{id}', [AdminStudentAccountController::class, 'destroy'])->name('student_accounts.destroy');

        // Examination Command & Access Center (Free vs Paid)
        Route::get('/exam-management', [AdminExamManagementController::class, 'index'])->name('exam_management.index');
        Route::get('/exam-management/create', [AdminExamManagementController::class, 'create'])->name('exam_management.create');
        Route::get('/exam-management/{id}/edit', [AdminExamManagementController::class, 'edit'])->name('exam_management.edit');
        Route::put('/exam-management/{id}', [AdminExamController::class, 'update'])->name('exam_management.update');
        Route::post('/exam-management/{id}/toggle-payment', [AdminExamManagementController::class, 'togglePayment'])->name('exam_management.toggle_payment');
        Route::post('/exam-management/{id}/update-fee', [AdminExamManagementController::class, 'updateFee'])->name('exam_management.update_fee');
        Route::post('/exam-management/store', [AdminExamManagementController::class, 'store'])->name('exam_management.store');
        Route::post('/system/sync-database-tracks', [AdminExamManagementController::class, 'syncDatabaseTracks'])->name('system.sync_database_tracks');
        
        // Admin Control Center (Super Admin Only)
        Route::get('/admin-control', [AdminControlController::class, 'index'])->name('admin_control.index');
        Route::get('/admin-control/{id}/edit', [AdminControlController::class, 'edit'])->name('admin_control.edit');
        Route::post('/admin-control', [AdminControlController::class, 'store'])->name('admin_control.store');
        Route::put('/admin-control/{id}', [AdminControlController::class, 'update'])->name('admin_control.update');
        Route::post('/admin-control/{id}/permissions', [AdminControlController::class, 'updatePermissions'])->name('admin_control.update_permissions');
        Route::post('/admin-control/{id}/approve', [AdminControlController::class, 'approve'])->name('admin_control.approve');
        Route::delete('/admin-control/{id}/reject', [AdminControlController::class, 'reject'])->name('admin_control.reject');
        Route::delete('/admin-control/{id}', [AdminControlController::class, 'destroy'])->name('admin_control.destroy');
    });
});

/*
|--------------------------------------------------------------------------
| Instructor Portal Routes
|--------------------------------------------------------------------------
*/
Route::prefix('instructor')->middleware(['auth', 'role:instructor'])->name('instructor.')->group(function () {
    Route::get('/dashboard', [InstructorDashboardController::class, 'index'])->name('dashboard');
    Route::get('/students', [InstructorStudentController::class, 'index'])->name('students.index');
    Route::get('/students/{id}', [InstructorStudentController::class, 'show'])->name('students.show');
    Route::post('/observations', [InstructorStudentController::class, 'storeObservation'])->name('observations.store');
    Route::post('/strengths-weaknesses', [InstructorStudentController::class, 'storeStrengthWeakness'])->name('strengths_weaknesses.store');
    Route::post('/improvement-plans', [InstructorStudentController::class, 'storeImprovementPlan'])->name('improvement_plans.store');
    Route::get('/attendance', [InstructorAttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance', [InstructorAttendanceController::class, 'store'])->name('attendance.store');
});

/*
|--------------------------------------------------------------------------
| Academic Cadet Portal Routes & Shortcuts
|--------------------------------------------------------------------------
*/
Route::get('/cadet', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }
    if (auth()->user()->role === 'external_student') {
        return redirect()->route('external.dashboard');
    }
    return redirect()->route('cadet.dashboard');
})->name('cadet.shortcut');

Route::get('/portal', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }
    return redirect()->route('dashboard');
})->name('portal.shortcut');

Route::prefix('cadet')->middleware(['auth', 'role:academic_student'])->name('cadet.')->group(function () {
    Route::get('/dashboard', [CadetDashboardController::class, 'index'])->name('dashboard');
    Route::get('/routine', [CadetRoutineController::class, 'index'])->name('routine');
    Route::get('/attendance', [CadetAttendanceController::class, 'index'])->name('attendance');
    Route::get('/fees', [CadetFeeController::class, 'index'])->name('fees');
    Route::post('/payments', [CadetFeeController::class, 'submitPayment'])->name('payments.submit');
    Route::get('/dossier', [CadetDossierController::class, 'index'])->name('dossier');

    // Exams
    Route::get('/exams', [CadetExamController::class, 'index'])->name('exams.index');
    Route::get('/exam-history', [CadetExamController::class, 'history'])->name('exams.history');
    Route::get('/exams/{id}/start', [CadetExamController::class, 'start'])->name('exams.start');
    Route::post('/exams/{id}/submit', [CadetExamController::class, 'submit'])->name('exams.submit');
    Route::get('/exams/attempts/{attemptId}/result', [CadetExamController::class, 'result'])->name('exams.result');

    // Profile & Settings
    Route::post('/update-details', [CadetDashboardController::class, 'updateDetails'])->name('update_details');
});

/*
|--------------------------------------------------------------------------
| External Student Portal Routes
|--------------------------------------------------------------------------
*/
Route::prefix('external')->middleware(['auth', 'role:external_student'])->name('external.')->group(function () {
    Route::get('/dashboard', [ExternalDashboardController::class, 'index'])->name('dashboard');
    Route::get('/tests', [ExternalTestController::class, 'index'])->name('tests.index');
    Route::post('/tests/{id}/purchase', [ExternalTestController::class, 'purchase'])->name('tests.purchase');
    Route::get('/tests/{id}/start', [ExternalTestController::class, 'start'])->name('tests.start');
    Route::post('/tests/{id}/submit', [ExternalTestController::class, 'submit'])->name('tests.submit');
    Route::get('/tests/attempts/{attemptId}/result', [ExternalTestController::class, 'result'])->name('tests.result');
});
