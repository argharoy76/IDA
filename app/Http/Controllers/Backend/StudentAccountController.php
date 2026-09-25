<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Student;
use App\Models\User;
use App\Models\Course;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\AuditLog;
use Carbon\Carbon;

class StudentAccountController extends Controller
{
    /**
     * Display student accounts directory with stats and course enrollment control.
     */
    public function index(Request $request)
    {
        $query = Student::with(['user', 'courses', 'currentCourse']);

        // Handle Segment / Wing Filter
        $selectedWing = $request->query('wing');
        if ($request->filled('wing') && $request->wing !== 'all') {
            $wingVal = trim($request->wing);
            if ($wingVal === 'Air Force' || $wingVal === 'Airforce') {
                $query->where(function ($q) {
                    $q->where('target_wing', 'like', '%Air%')
                      ->orWhere('target_wing', 'like', '%BAFA%');
                });
            } else {
                $query->where('target_wing', 'like', "%{$wingVal}%");
            }
        } elseif ($request->filled('wing_filter') && $request->wing_filter !== 'all') {
            $wingVal = trim($request->wing_filter);
            if ($wingVal === 'Air Force' || $wingVal === 'Airforce') {
                $query->where(function ($q) {
                    $q->where('target_wing', 'like', '%Air%')
                      ->orWhere('target_wing', 'like', '%BAFA%');
                });
            } else {
                $query->where('target_wing', 'like', "%{$wingVal}%");
            }
        }

        // Handle Sub-Program / Track Filter (Preliminary, ISSB, Constable, SI, ASI)
        $selectedTrack = $request->query('track') ?? $request->query('program');
        if ($request->filled('track') && $request->track !== 'all') {
            $trackVal = strtolower(trim($request->track));
            $query->where(function ($q) use ($trackVal) {
                if ($trackVal === 'preliminary' || $trackVal === 'prelim' || $trackVal === 'p') {
                    $q->where(function ($sq) {
                        $sq->where('target_wing', 'like', '%prelim%')
                          ->orWhere('target_wing', 'like', '%written%')
                          ->orWhereHas('courses', function ($cq) {
                              $cq->where('title', 'like', '%prelim%')
                                 ->orWhere('title', 'like', '%regular%')
                                 ->orWhere('title', 'like', '%bma%')
                                 ->orWhere('title', 'like', '%bna%')
                                 ->orWhere('title', 'like', '%bafa%');
                          })
                          ->orWhereHas('currentCourse', function ($cq) {
                              $cq->where('title', 'like', '%prelim%')
                                 ->orWhere('title', 'like', '%regular%')
                                 ->orWhere('title', 'like', '%bma%')
                                 ->orWhere('title', 'like', '%bna%')
                                 ->orWhere('title', 'like', '%bafa%');
                          });
                    });
                } elseif ($trackVal === 'issb' || $trackVal === 'i' || $trackVal === 'iss') {
                    $q->where(function ($sq) {
                        $sq->where('target_wing', 'like', '%issb%')
                          ->orWhereHas('courses', function ($cq) {
                              $cq->where('title', 'like', '%issb%')
                                 ->orWhere('category', 'like', '%issb%');
                          })
                          ->orWhereHas('currentCourse', function ($cq) {
                              $cq->where('title', 'like', '%issb%')
                                 ->orWhere('category', 'like', '%issb%');
                          });
                    });
                } elseif ($trackVal === 'constable' || $trackVal === 'con') {
                    $q->where(function ($sq) {
                        $sq->where('target_wing', 'like', '%constable%')
                          ->orWhere('target_wing', 'like', '%con%')
                          ->orWhereHas('courses', function ($cq) {
                              $cq->where('title', 'like', '%constable%')
                                 ->orWhere('category', 'like', '%constable%');
                          })
                          ->orWhereHas('currentCourse', function ($cq) {
                              $cq->where('title', 'like', '%constable%')
                                 ->orWhere('category', 'like', '%constable%');
                          });
                    });
                } elseif ($trackVal === 'si' || $trackVal === 'sub-inspector') {
                    $q->where(function ($sq) {
                        $sq->where('target_wing', 'like', '%sub-inspector%')
                          ->orWhere('target_wing', 'like', '%si%')
                          ->orWhereHas('courses', function ($cq) {
                              $cq->where('title', 'like', '%sub-inspector%')
                                 ->orWhere('title', 'like', '% si %')
                                 ->orWhere('title', 'like', '%si &%');
                          })
                          ->orWhereHas('currentCourse', function ($cq) {
                              $cq->where('title', 'like', '%sub-inspector%')
                                 ->orWhere('title', 'like', '% si %')
                                 ->orWhere('title', 'like', '%si &%');
                          });
                    });
                } elseif ($trackVal === 'asi' || $trackVal === 'assistant sub-inspector') {
                    $q->where(function ($sq) {
                        $sq->where('target_wing', 'like', '%asi%')
                          ->orWhere('target_wing', 'like', '%assistant sub-inspector%')
                          ->orWhereHas('courses', function ($cq) {
                              $cq->where('title', 'like', '%asi%')
                                 ->orWhere('title', 'like', '%assistant sub-inspector%');
                          })
                          ->orWhereHas('currentCourse', function ($cq) {
                              $cq->where('title', 'like', '%asi%')
                                 ->orWhere('title', 'like', '%assistant sub-inspector%');
                          });
                    });
                }
            });
        }

        // Filter by student type (offline vs online / external)
        if ($request->filled('type')) {
            if ($request->type === 'offline') {
                $query->where(function ($q) {
                    $q->where('student_type', 'offline')
                      ->orWhere('student_type', 'academic');
                });
            } elseif ($request->type === 'online') {
                $query->where(function ($q) {
                    $q->where('student_type', 'online')
                      ->orWhere('student_type', 'external');
                });
            } elseif (in_array($request->type, ['academic', 'external'])) {
                $query->where('student_type', $request->type);
            }
        }

        // Filter by course enrollment status
        if ($request->filled('course_status')) {
            if (in_array($request->course_status, ['has_courses', 'has_course'])) {
                $query->where(function ($q) {
                    $q->has('courses')
                      ->orWhereNotNull('current_course_id');
                });
            } elseif (in_array($request->course_status, ['no_courses', 'no_course'])) {
                $query->doesntHave('courses')->whereNull('current_course_id');
            }
        }

        // Search by ID, name, phone, email, address
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('student_id_code', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%")
                         ->orWhere('account_id', 'like', "%{$search}%");
                  });
            });
        }

        $students = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();
        $courses = Course::orderBy('title')->get();

        // System Statistics including Wing counts
        $stats = [
            'total' => Student::count(),
            'navy' => Student::where('target_wing', 'like', '%Navy%')->count(),
            'police' => Student::where('target_wing', 'like', '%Police%')->count(),
            'army' => Student::where('target_wing', 'like', '%Army%')->count(),
            'airforce' => Student::where(function ($q) {
                $q->where('target_wing', 'like', '%Air%')
                  ->orWhere('target_wing', 'like', '%BAFA%');
            })->count(),
            'offline' => Student::whereIn('student_type', ['offline', 'academic'])->count(),
            'online' => Student::whereIn('student_type', ['online', 'external'])->count(),
            'with_courses' => Student::where(function ($q) {
                $q->has('courses')->orWhereNotNull('current_course_id');
            })->count(),
            'unassigned' => Student::doesntHave('courses')->whereNull('current_course_id')->count(),
        ];

        // Detailed Sub-Program / Track breakdown for each wing (Handwritten Diagram implementation)
        $wingTrackStats = [
            'Army' => [
                'all' => $stats['army'],
                'preliminary' => Student::where('target_wing', 'like', '%Army%')
                    ->where(function ($q) {
                        $q->where('target_wing', 'like', '%prelim%')
                          ->orWhere('target_wing', 'like', '%written%')
                          ->orWhereHas('courses', fn($cq) => $cq->where('title', 'like', '%prelim%')->orWhere('title', 'like', '%bma%')->orWhere('title', 'like', '%regular%'))
                          ->orWhereHas('currentCourse', fn($cq) => $cq->where('title', 'like', '%prelim%')->orWhere('title', 'like', '%bma%')->orWhere('title', 'like', '%regular%'))
                          ->orWhere(fn($sq) => $sq->doesntHave('courses')->where('target_wing', 'Army'));
                    })->count(),
                'issb' => Student::where('target_wing', 'like', '%Army%')
                    ->where(function ($q) {
                        $q->where('target_wing', 'like', '%issb%')
                          ->orWhereHas('courses', fn($cq) => $cq->where('title', 'like', '%issb%'))
                          ->orWhereHas('currentCourse', fn($cq) => $cq->where('title', 'like', '%issb%'));
                    })->count(),
            ],
            'Navy' => [
                'all' => $stats['navy'],
                'preliminary' => Student::where('target_wing', 'like', '%Navy%')
                    ->where(function ($q) {
                        $q->where('target_wing', 'like', '%prelim%')
                          ->orWhereHas('courses', fn($cq) => $cq->where('title', 'like', '%prelim%')->orWhere('title', 'like', '%bna%')->orWhere('title', 'like', '%cadet prep%'))
                          ->orWhereHas('currentCourse', fn($cq) => $cq->where('title', 'like', '%prelim%')->orWhere('title', 'like', '%bna%')->orWhere('title', 'like', '%cadet prep%'))
                          ->orWhere(fn($sq) => $sq->doesntHave('courses'));
                    })->count(),
                'issb' => Student::where('target_wing', 'like', '%Navy%')
                    ->where(function ($q) {
                        $q->where('target_wing', 'like', '%issb%')
                          ->orWhereHas('courses', fn($cq) => $cq->where('title', 'like', '%issb%'))
                          ->orWhereHas('currentCourse', fn($cq) => $cq->where('title', 'like', '%issb%'));
                    })->count(),
            ],
            'Air Force' => [
                'all' => $stats['airforce'],
                'preliminary' => Student::where(fn($q) => $q->where('target_wing', 'like', '%Air%')->orWhere('target_wing', 'like', '%BAFA%'))
                    ->where(function ($q) {
                        $q->where('target_wing', 'like', '%prelim%')
                          ->orWhereHas('courses', fn($cq) => $cq->where('title', 'like', '%prelim%')->orWhere('title', 'like', '%bafa%')->orWhere('title', 'like', '%flight cadet%'))
                          ->orWhereHas('currentCourse', fn($cq) => $cq->where('title', 'like', '%prelim%')->orWhere('title', 'like', '%bafa%')->orWhere('title', 'like', '%flight cadet%'))
                          ->orWhere(fn($sq) => $sq->doesntHave('courses'));
                    })->count(),
                'issb' => Student::where(fn($q) => $q->where('target_wing', 'like', '%Air%')->orWhere('target_wing', 'like', '%BAFA%'))
                    ->where(function ($q) {
                        $q->where('target_wing', 'like', '%issb%')
                          ->orWhereHas('courses', fn($cq) => $cq->where('title', 'like', '%issb%'))
                          ->orWhereHas('currentCourse', fn($cq) => $cq->where('title', 'like', '%issb%'));
                    })->count(),
            ],
            'Police' => [
                'all' => $stats['police'],
                'constable' => Student::where('target_wing', 'like', '%Police%')
                    ->where(function ($q) {
                        $q->where('target_wing', 'like', '%constable%')->orWhere('target_wing', 'like', '%con%')
                          ->orWhereHas('courses', fn($cq) => $cq->where('title', 'like', '%constable%'))
                          ->orWhereHas('currentCourse', fn($cq) => $cq->where('title', 'like', '%constable%'));
                    })->count(),
                'si' => Student::where('target_wing', 'like', '%Police%')
                    ->where(function ($q) {
                        $q->where('target_wing', 'like', '%sub-inspector%')->orWhere('target_wing', 'like', '%si%')
                          ->orWhereHas('courses', fn($cq) => $cq->where('title', 'like', '%sub-inspector%')->orWhere('title', 'like', '% si %')->orWhere('title', 'like', '%police si%'))
                          ->orWhereHas('currentCourse', fn($cq) => $cq->where('title', 'like', '%sub-inspector%')->orWhere('title', 'like', '% si %')->orWhere('title', 'like', '%police si%'))
                          ->orWhere(fn($sq) => $sq->doesntHave('courses'));
                    })->count(),
                'asi' => Student::where('target_wing', 'like', '%Police%')
                    ->where(function ($q) {
                        $q->where('target_wing', 'like', '%asi%')->orWhere('target_wing', 'like', '%assistant sub-inspector%')
                          ->orWhereHas('courses', fn($cq) => $cq->where('title', 'like', '%asi%')->orWhere('title', 'like', '%assistant sub-inspector%'))
                          ->orWhereHas('currentCourse', fn($cq) => $cq->where('title', 'like', '%asi%')->orWhere('title', 'like', '%assistant sub-inspector%'));
                    })->count(),
            ],
        ];

        return view('backend.student_accounts.index', compact('students', 'courses', 'stats', 'selectedWing', 'selectedTrack', 'wingTrackStats'));
    }

    /**
     * View Details full page for a student account.
     */
    public function show($id)
    {
        $student = Student::with([
            'user',
            'courses',
            'currentCourse',
            'examAttempts.exam',
            'invoices.feeType',
            'invoices.payments',
            'payments.invoice',
            'payments.verifier',
        ])->findOrFail($id);

        $payments = $student->payments()->with(['invoice', 'verifier'])->latest()->get();
        $invoices = $student->invoices()->with(['feeType', 'payments'])->latest()->get();

        $totalPaid = $student->payments()->whereIn('verification_status', ['approved', 'verified'])->sum('amount');
        if ($totalPaid == 0) {
            $totalPaid = (float) $student->invoices()->sum('paid_amount');
        }
        $totalDue = (float) $student->invoices()->sum('due_amount');
        $totalBilled = (float) $student->invoices()->sum('net_amount');

        return view('backend.student_accounts.show', compact('student', 'payments', 'invoices', 'totalPaid', 'totalDue', 'totalBilled'));
    }

    /**
     * Update Details full page for a student account.
     */
    public function edit($id)
    {
        $student = Student::with(['user', 'courses', 'currentCourse', 'invoices.feeType', 'payments'])->findOrFail($id);
        $courses = Course::orderBy('title')->get();
        $invoices = $student->invoices()->with(['feeType', 'payments'])->latest()->get();
        $payments = $student->payments()->with(['invoice', 'verifier'])->latest()->get();
        $totalPaid = (float) $student->payments()->where('verification_status', 'approved')->sum('amount');
        $totalDue = (float) $student->invoices()->sum('due_amount');
        $totalBilled = (float) $student->invoices()->sum('net_amount');

        // Determine current wing and current tracks
        $tw = strtolower($student->target_wing ?? '');
        if (str_contains($tw, 'navy')) {
            $currentWing = 'Navy';
        } elseif (str_contains($tw, 'air')) {
            $currentWing = 'Air Force';
        } elseif (str_contains($tw, 'police')) {
            $currentWing = 'Police';
        } elseif (str_contains($tw, 'general')) {
            $currentWing = 'General';
        } elseif (str_contains($tw, 'army')) {
            $currentWing = 'Army';
        } else {
            $branches = $student->getEnrolledBranches();
            if (in_array('navy', $branches)) $currentWing = 'Navy';
            elseif (in_array('air_force', $branches)) $currentWing = 'Air Force';
            elseif (in_array('police', $branches)) $currentWing = 'Police';
            else $currentWing = 'Army';
        }

        $currentTracks = $student->getTargetTracks();

        return view('backend.student_accounts.edit', compact(
            'student', 'courses', 'invoices', 'payments', 'totalPaid', 'totalDue', 'totalBilled', 'currentWing', 'currentTracks'
        ));
    }

    /**
     * Update all details (Profile, Login ID, Courses, Payment Details, Password) from the edit page.
     */
    public function update(Request $request, $id)
    {
        $student = Student::with(['user', 'courses'])->findOrFail($id);
        $user = $student->user;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'custom_id' => 'required|string|max:60',
            'age' => 'nullable|integer|min:10|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'gender' => 'required|in:male,female,other',
            'address' => 'required|string|max:500',
            'student_type' => 'required|in:offline,online,academic,external',
            'target_wing' => 'nullable|string|max:100',
            'branch_wing' => 'nullable|string|max:100',
            'category_tracks' => 'nullable|array',
            'category_tracks.*' => 'string|max:50',
            'institution' => 'nullable|string|max:255',
            'hsc_year' => 'nullable|string|max:20',
            'district' => 'nullable|string|max:100',
            'course_ids' => 'nullable|array',
            'course_ids.*' => 'exists:courses,id',
            'course_payment_status' => 'nullable|array',
            'course_payment_status.*' => 'in:paid,partial,unpaid,exempt',
            'password' => 'nullable|string|min:6|confirmed',

            // Payment / Invoice adjustments
            'invoice_status' => 'nullable|array',
            'invoice_status.*' => 'in:paid,partial,pending,overdue',
            'invoice_paid_amount' => 'nullable|array',
            'invoice_paid_amount.*' => 'nullable|numeric|min:0',
            'invoice_due_amount' => 'nullable|array',
            'invoice_due_amount.*' => 'nullable|numeric|min:0',

            // Optional new payment transaction voucher
            'record_new_payment' => 'nullable|boolean',
            'new_payment_amount' => 'nullable|numeric|min:1',
            'new_payment_method' => 'nullable|string|max:50',
            'new_payment_trx' => 'nullable|string|max:100',
            'new_payment_status' => 'nullable|in:approved,pending,rejected',
            'new_payment_notes' => 'nullable|string|max:500',
            'new_payment_invoice_id' => 'nullable|exists:invoices,id',
        ]);

        $newId = strtoupper(trim($validated['custom_id']));

        // Check ID uniqueness
        $userIdCollision = User::where('account_id', $newId)->where('id', '!=', $user->id)->exists();
        $studentIdCollision = Student::where('student_id_code', $newId)->where('id', '!=', $student->id)->exists();
        if ($userIdCollision || $studentIdCollision) {
            return back()->withInput()->with('error', "The Login ID '{$newId}' is already assigned to another account. Please choose a unique ID.");
        }

        // Update User
        $userUpdates = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'account_id' => $newId,
        ];
        if (!empty($validated['password'])) {
            $userUpdates['password'] = Hash::make($validated['password']);
        }
        $user->update($userUpdates);

        // Derive base wing and composed target_wing with category tracks
        $branchWing = $request->input('branch_wing') ?: $request->input('target_wing') ?: ($student->target_wing ?: 'Army');
        $bwLower = strtolower($branchWing);
        if (str_contains($bwLower, 'navy')) {
            $baseWing = 'Navy';
        } elseif (str_contains($bwLower, 'air')) {
            $baseWing = 'Air Force';
        } elseif (str_contains($bwLower, 'police')) {
            $baseWing = 'Police';
        } elseif (str_contains($bwLower, 'general')) {
            $baseWing = 'General';
        } else {
            $baseWing = 'Army';
        }

        $rawTracks = $request->input('category_tracks', []);
        if (!is_array($rawTracks)) {
            $rawTracks = !empty($rawTracks) ? explode(',', $rawTracks) : [];
        }
        $tracks = array_values(array_unique(array_filter(array_map('strtolower', array_map('trim', $rawTracks)))));

        if ($baseWing === 'Police') {
            $validPolice = array_intersect(['constable', 'si', 'asi'], $tracks);
            if (count($validPolice) === 3) {
                $targetWing = 'Police - Constable, SI & ASI';
            } elseif (in_array('si', $validPolice) && in_array('asi', $validPolice)) {
                $targetWing = 'Police - SI & ASI';
            } elseif (in_array('constable', $validPolice) && in_array('si', $validPolice)) {
                $targetWing = 'Police - Constable & SI';
            } elseif (in_array('constable', $validPolice) && in_array('asi', $validPolice)) {
                $targetWing = 'Police - Constable & ASI';
            } elseif (in_array('si', $validPolice)) {
                $targetWing = 'Police - Sub-Inspector (SI)';
            } elseif (in_array('asi', $validPolice)) {
                $targetWing = 'Police - Assistant Sub-Inspector (ASI)';
            } elseif (in_array('constable', $validPolice)) {
                $targetWing = 'Police - Constable';
            } else {
                $targetWing = 'Police';
            }
        } elseif (in_array($baseWing, ['Army', 'Navy', 'Air Force', 'General'], true)) {
            $hasPrelim = in_array('prelim', $tracks, true);
            $hasIssb = in_array('issb', $tracks, true);

            if ($hasPrelim && $hasIssb) {
                $targetWing = "{$baseWing} - Prelim & ISSB";
            } elseif ($hasPrelim) {
                $targetWing = "{$baseWing} - Preliminary";
            } elseif ($hasIssb) {
                $targetWing = "{$baseWing} - ISSB";
            } else {
                $targetWing = $baseWing;
            }
        } else {
            $targetWing = $baseWing;
        }

        // Update Student
        $studentData = [
            'student_id_code' => $newId,
            'age' => $validated['age'] ?? null,
            'gender' => $validated['gender'],
            'address' => $validated['address'],
            'student_type' => $validated['student_type'],
            'target_wing' => $targetWing,
            'institution' => $validated['institution'] ?? $student->institution,
            'hsc_year' => $validated['hsc_year'] ?? $student->hsc_year,
            'district' => $validated['district'] ?? $student->district,
        ];

        if (\Illuminate\Support\Facades\Schema::hasColumn('students', 'target_tracks')) {
            $studentData['target_tracks'] = $tracks;
        } else {
            try {
                \Illuminate\Support\Facades\Schema::table('students', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->string('target_tracks', 150)->nullable()->after('target_wing');
                });
                $studentData['target_tracks'] = $tracks;
            } catch (\Throwable $e) {}
        }

        $student->update($studentData);

        // Sync Courses & Payment Status
        $courseIds = $validated['course_ids'] ?? [];
        $paymentStatuses = $validated['course_payment_status'] ?? [];
        $syncData = [];
        foreach ($courseIds as $cid) {
            $pStatus = $paymentStatuses[$cid] ?? 'paid';
            $syncData[$cid] = [
                'enrolled_at' => now(),
                'status' => 'active',
                'assigned_by' => auth()->id(),
                'payment_status' => $pStatus,
                'notes' => 'Updated via Student Management Edit page',
            ];
        }
        $student->courses()->sync($syncData);
        $student->update(['current_course_id' => count($courseIds) > 0 ? (int)$courseIds[0] : null]);

        // Update Invoices (if submitted)
        if (!empty($validated['invoice_status'])) {
            foreach ($validated['invoice_status'] as $invId => $invStat) {
                $inv = Invoice::where('id', $invId)->where('student_id', $student->id)->first();
                if ($inv) {
                    $invUpdates = ['status' => $invStat];
                    if (isset($validated['invoice_paid_amount'][$invId]) && is_numeric($validated['invoice_paid_amount'][$invId])) {
                        $invUpdates['paid_amount'] = (float)$validated['invoice_paid_amount'][$invId];
                    }
                    if (isset($validated['invoice_due_amount'][$invId]) && is_numeric($validated['invoice_due_amount'][$invId])) {
                        $invUpdates['due_amount'] = (float)$validated['invoice_due_amount'][$invId];
                    }
                    $inv->update($invUpdates);
                }
            }
        }

        // Record New Payment Voucher (if filled)
        if (!empty($validated['new_payment_amount']) && $validated['new_payment_amount'] > 0) {
            $paymentCount = Payment::count() + 1;
            $payNum = sprintf('PAY-%s-%04d', date('Y'), $paymentCount);
            while (Payment::where('payment_number', $payNum)->exists()) {
                $paymentCount++;
                $payNum = sprintf('PAY-%s-%04d', date('Y'), $paymentCount);
            }

            $pStatus = $validated['new_payment_status'] ?? 'approved';
            Payment::create([
                'payment_number' => $payNum,
                'invoice_id' => $validated['new_payment_invoice_id'] ?? null,
                'student_id' => $student->id,
                'user_id' => $user->id,
                'amount' => (float)$validated['new_payment_amount'],
                'payment_method' => $validated['new_payment_method'] ?: 'Cash / Office Receipt',
                'transaction_reference' => $validated['new_payment_trx'] ?: ('REC-' . strtoupper(substr(uniqid(), -6))),
                'payment_date' => now(),
                'verification_status' => $pStatus,
                'verified_by' => $pStatus === 'approved' ? auth()->id() : null,
                'verified_at' => $pStatus === 'approved' ? now() : null,
                'admin_notes' => $validated['new_payment_notes'] ?: 'Recorded via Student Management Edit Details page',
            ]);

            // If linked to an invoice and approved, update invoice balance
            if ($pStatus === 'approved' && !empty($validated['new_payment_invoice_id'])) {
                $targetInv = Invoice::where('id', $validated['new_payment_invoice_id'])->where('student_id', $student->id)->first();
                if ($targetInv) {
                    $newPaid = $targetInv->paid_amount + (float)$validated['new_payment_amount'];
                    $newDue = max(0, $targetInv->net_amount - $newPaid);
                    $newStatus = $newDue <= 0 ? 'paid' : ($newPaid > 0 ? 'partially_paid' : 'pending');
                    $targetInv->update([
                        'paid_amount' => $newPaid,
                        'due_amount' => $newDue,
                        'status' => $newStatus,
                    ]);
                }
            }
        }

        AuditLog::log('update_student_details_page', 'Student', $student->id, null, [
            'name' => $user->name,
            'login_id' => $newId,
            'courses_count' => count($courseIds),
            'updated_by' => auth()->user()->name,
        ]);

        return redirect()->route('admin.student_accounts.show', $student->id)->with('success', "Student details and payment records for '{$user->name}' ({$newId}) updated successfully!");
    }

    /**
     * Create an offline student account filled by an employee/worker.
     */
    public function storeOffline(Request $request)
    {
        // Normalize single course_id to course_ids array
        if ($request->filled('course_id') && !$request->has('course_ids')) {
            $request->merge(['course_ids' => [(int)$request->input('course_id')]]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'custom_id' => 'nullable|string|max:60|unique:users,account_id|unique:students,student_id_code',
            'age' => 'nullable|integer|min:10|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email',
            'gender' => 'required|in:male,female,other',
            'address' => 'required|string|max:500',
            'password' => 'nullable|string|min:6',
            'course_id' => 'nullable|exists:courses,id',
            'course_ids' => 'nullable|array',
            'course_ids.*' => 'exists:courses,id',
            'target_wing' => 'nullable|string|max:100',
            'target_program' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Determine Login ID: custom provided or auto-generated
        $currentYear = date('Y');
        if (!empty($validated['custom_id'])) {
            $studentIdCode = strtoupper(trim($validated['custom_id']));
        } else {
            $baseId = (Student::max('id') ?? 0) + 1;
            $studentIdCode = sprintf('OFF-%s-%03d', $currentYear, $baseId);
            while (User::where('account_id', $studentIdCode)->orWhereHas('student', fn($q) => $q->where('student_id_code', $studentIdCode))->exists()) {
                $baseId++;
                $studentIdCode = sprintf('OFF-%s-%03d', $currentYear, $baseId);
            }
        }

        $plainPassword = $validated['password'] ?: 'password123';

        // 1. Create User Account
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'account_id' => $studentIdCode,
            'password' => Hash::make($plainPassword),
            'role' => 'academic_student',
            'phone' => $validated['phone'],
            'status' => 'active',
        ]);

        // 2. Create Cadet Profile
        $assignedCourseIds = $validated['course_ids'] ?? [];
        $firstCourseId = count($assignedCourseIds) > 0 ? $assignedCourseIds[0] : null;

        // Combine target_wing and program track if provided
        $targetWing = $validated['target_wing'] ?? 'Army';
        if (!empty($request->target_program)) {
            $targetWing = trim($targetWing) . ' - ' . trim($request->target_program);
        }

        $student = Student::create([
            'user_id' => $user->id,
            'student_id_code' => $studentIdCode,
            'roll_number' => sprintf('%02d', (Student::max('id') ?? 0) + 1),
            'student_type' => 'offline',
            'gender' => $validated['gender'],
            'age' => $validated['age'] ?? null,
            'address' => $validated['address'],
            'target_wing' => $targetWing,
            'current_course_id' => $firstCourseId,
            'admission_date' => Carbon::today(),
            'status' => 'active',
        ]);

        // 3. Assign Courses if cadet paid for any initially
        if (!empty($assignedCourseIds)) {
            $syncData = [];
            foreach ($assignedCourseIds as $cid) {
                $syncData[$cid] = [
                    'enrolled_at' => now(),
                    'status' => 'active',
                    'assigned_by' => auth()->id(),
                    'payment_status' => 'paid',
                    'notes' => $validated['notes'] ?? 'Assigned at offline desk enrollment',
                ];
            }
            $student->courses()->sync($syncData);
        }

        AuditLog::log('create_offline_student', 'Student', $student->id, null, [
            'name' => $student->user->name,
            'account_id' => $studentIdCode,
            'assigned_courses_count' => count($assignedCourseIds),
            'created_by_worker' => auth()->user()->name,
        ]);

        $courseMsg = count($assignedCourseIds) > 0 
            ? count($assignedCourseIds) . ' course(s) granted.' 
            : 'No initial course assigned (free/unassigned profile).';

        return redirect()->route('admin.student_accounts.index')->with('success', 
            "Offline Cadet Account created! Assigned Login ID: {$studentIdCode}. Password: {$plainPassword}. {$courseMsg}");
    }

    /**
     * Assign or update custom ID number for student login.
     */
    public function updateId(Request $request, $id)
    {
        $student = Student::with('user')->findOrFail($id);
        $user = $student->user;

        $validated = $request->validate([
            'custom_id' => 'required|string|max:60',
        ]);

        $newId = strtoupper(trim($validated['custom_id']));

        // Check uniqueness excluding current records
        $userIdCollision = User::where('account_id', $newId)->where('id', '!=', $user->id)->exists();
        $studentIdCollision = Student::where('student_id_code', $newId)->where('id', '!=', $student->id)->exists();

        if ($userIdCollision || $studentIdCollision) {
            return back()->with('error', "ID '{$newId}' is already in use by another account. Please specify a unique ID.");
        }

        $oldId = $student->student_id_code;
        $student->update(['student_id_code' => $newId]);
        $user->update(['account_id' => $newId]);

        AuditLog::log('update_student_id', 'Student', $student->id, [
            'old_id' => $oldId,
        ], [
            'new_id' => $newId,
            'updated_by' => auth()->user()->name,
        ]);

        return back()->with('success', "Student Login ID successfully updated to '{$newId}'. The student can now use this ID to sign in.");
    }

    /**
     * Assign, add, or revoke courses for the student (Multi-Course Access Engine).
     * If he pays for 1 course -> employee assigns 1 course.
     * If he pays for 2 courses -> employee assigns 2 courses.
     */
    public function assignCourses(Request $request, $id)
    {
        $student = Student::with('courses')->findOrFail($id);

        $courseIds = $request->input('course_ids', []);
        if (!is_array($courseIds)) {
            $courseIds = [];
        }

        $syncData = [];
        foreach ($courseIds as $cid) {
            $syncData[$cid] = [
                'enrolled_at' => now(),
                'status' => 'active',
                'assigned_by' => auth()->id(),
                'payment_status' => 'paid',
                'notes' => $request->input('notes', 'Assigned by staff/admin'),
            ];
        }

        $student->courses()->sync($syncData);

        // Keep current_course_id aligned with first active course
        $firstId = count($courseIds) > 0 ? (int)$courseIds[0] : null;
        $student->update(['current_course_id' => $firstId]);

        AuditLog::log('assign_student_courses', 'Student', $student->id, null, [
            'course_count' => count($courseIds),
            'course_ids' => $courseIds,
            'assigned_by' => auth()->user()->name,
        ]);

        $count = count($courseIds);
        $message = $count > 0 
            ? "Course access updated for {$student->user->name}! Granted access to {$count} course(s)."
            : "All course access cleared for {$student->user->name}. Account is now an unassigned free profile.";

        return back()->with('success', $message);
    }

    /**
     * Update student demographic profile details.
     */
    public function updateProfile(Request $request, $id)
    {
        $student = Student::with('user')->findOrFail($id);
        $user = $student->user;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'nullable|integer|min:10|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'gender' => 'required|in:male,female,other',
            'address' => 'required|string|max:500',
            'student_type' => 'required|in:offline,online,academic,external',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
        ]);

        $student->update([
            'age' => $validated['age'] ?? null,
            'gender' => $validated['gender'],
            'address' => $validated['address'],
            'student_type' => $validated['student_type'],
        ]);

        return back()->with('success', "Student profile updated successfully for {$user->name}.");
    }

    /**
     * Quick password reset by employee/admin.
     */
    public function updatePassword(Request $request, $id)
    {
        $student = Student::with('user')->findOrFail($id);

        $validated = $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $student->user->update([
            'password' => Hash::make($validated['password']),
        ]);

        AuditLog::log('reset_student_password', 'User', $student->user->id, null, [
            'reset_by' => auth()->user()->name,
        ]);

        return back()->with('success', "Login password reset successfully for student {$student->user->name}.");
    }

    /**
     * Delete a student account, their course assignments, and user record.
     * Guarded by strict confirmation verification to prevent accidental deletion.
     */
    public function destroy(Request $request, $id)
    {
        $student = Student::with('user')->findOrFail($id);
        $name = $student->user->name;
        $studentIdCode = $student->student_id_code ?: ($student->user->account_id ?? '');
        $userId = $student->user_id;

        // Security Confirmation Guard
        $confirm = trim($request->input('confirm_delete', ''));
        $expectedId = strtoupper(trim($studentIdCode));
        $typedConfirm = strtoupper($confirm);

        if ($typedConfirm !== $expectedId && $typedConfirm !== 'DELETE') {
            return back()->with('error', "Security Warning: Deletion aborted! To permanently delete account '{$name}', you must type '{$studentIdCode}' or 'DELETE' in the confirmation box.");
        }

        // Remove course assignments
        $student->courses()->detach();

        AuditLog::log('delete_student_permanent', 'Student', $student->id, [
            'name' => $name,
            'student_id_code' => $studentIdCode,
            'email' => $student->user->email,
        ], [
            'deleted_by' => auth()->user()->name,
            'ip' => $request->ip(),
        ]);

        // Delete student record
        $student->delete();

        // Delete user account
        User::where('id', $userId)->delete();

        return back()->with('success', "Student account '{$name}' ({$studentIdCode}) and all associated records have been permanently removed.");
    }
}