<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Teacher\ReferralController;
use App\Http\Controllers\Student\CounselingRequestController;
use App\Http\Controllers\Student\TeacherReferralController;
use App\Http\Controllers\Counselor\CaseController;
use App\Http\Controllers\Counselor\AppointmentController;
use App\Http\Controllers\Counselor\OfficeReferralController;

Route::get('/', function () {
    return view('welcome');
});

require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {

    // Dashboard redirect based on role
    Route::get('/dashboard', function () {
        $role = auth()->user()->role ?? null;

        return match ($role) {
            'student'      => redirect()->route('student.dashboard'),
            'teacher'      => redirect()->route('teacher.dashboard'),
            'counselor'    => redirect()->route('counselor.dashboard'),
            'clinic'       => redirect()->route('clinic.dashboard'),
            'disciplinary' => redirect()->route('disciplinary.dashboard'),
            'admin'        => redirect()->route('admin.dashboard'),
            default        => redirect('/'),
        };
    })->name('dashboard');

    // Change password (all roles). Saves through password.update (Auth\PasswordController).
    Route::get('/password/change', function () {
        return view('auth.change-password');
    })->name('password.change');

    // ==================== STUDENT ====================
    Route::middleware('role:student')->group(function () {

    Route::get('/student/dashboard', function () {
        return view('student.dashboard');
    })->name('student.dashboard');

    // Counseling request form
    Route::get('/student/request', [App\Http\Controllers\Student\CounselingRequestController::class, 'create'])
        ->name('student.request.create');
    Route::post('/student/request', [App\Http\Controllers\Student\CounselingRequestController::class, 'store'])
        ->name('student.request.store');

    // My Counseling Requests (View All)
    Route::get('/student/requests', function () {
        $requests = \Illuminate\Support\Facades\DB::table('counseling_requests')
            ->where('student_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('student.requests.index', compact('requests'));
    })->name('student.requests.index');

    Route::get('/student/requests/{id}', [App\Http\Controllers\Student\CounselingRequestController::class, 'show'])
        ->name('student.requests.show');

    Route::delete('/student/requests/{id}', function ($id) {
        $deleted = \Illuminate\Support\Facades\DB::table('counseling_requests')
            ->where('id', $id)
            ->where('student_id', auth()->id())
            ->delete();

        if (!$deleted) {
            return back()->with('error', 'Request not found or not allowed.');
        }

        return back()->with('success', 'Request deleted.');
    })->name('student.requests.destroy');

    // My Appointments (View All)
    Route::get('/student/appointments', function () {
        $appointments = \Illuminate\Support\Facades\DB::table('appointments')
            ->leftJoin('cases', 'appointments.case_id', '=', 'cases.id')
            ->select('appointments.*', 'cases.case_number', 'cases.concern_type')
            ->where('appointments.student_id', auth()->id())
            ->orderBy('appointments.appointment_datetime', 'desc')
            ->get();

        $appointmentRequests = \Illuminate\Support\Facades\DB::table('appointment_requests')
            ->leftJoin('cases', 'appointment_requests.case_id', '=', 'cases.id')
            ->select('appointment_requests.*', 'cases.case_number')
            ->where('appointment_requests.student_id', auth()->id())
            ->orderByDesc('appointment_requests.created_at')
            ->get();

        return view('student.appointments.index', compact('appointments', 'appointmentRequests'));
    })->name('student.appointments.index');

    Route::get('/student/appointments/request', [App\Http\Controllers\Student\AppointmentRequestController::class, 'create'])
        ->name('student.appointments.request');

    Route::post('/student/appointments/request', [App\Http\Controllers\Student\AppointmentRequestController::class, 'store'])
        ->name('student.appointments.request.store');

    Route::get('/student/appointments/{id}', function ($id) {
        $appointment = \Illuminate\Support\Facades\DB::table('appointments')
            ->leftJoin('cases', 'appointments.case_id', '=', 'cases.id')
            ->select(
                'appointments.*',
                'cases.case_number',
                'cases.concern_type',
                'cases.status as case_status'
            )
            ->where('appointments.id', $id)
            ->where('appointments.student_id', auth()->id())
            ->first();

        if (!$appointment) {
            return redirect()->route('student.appointments.index')
                ->with('error', 'Appointment not found.');
        }

        return view('student.appointments.show', compact('appointment'));
    })->name('student.appointments.show');

    Route::delete('/student/appointments/{id}', function ($id) {
        $deleted = \Illuminate\Support\Facades\DB::table('appointments')
            ->where('id', $id)
            ->where('student_id', auth()->id())
            ->delete();

        if (!$deleted) {
            return back()->with('error', 'Appointment not found or not allowed.');
        }

        return back()->with('success', 'Appointment deleted.');
    })->name('student.appointments.destroy');

    // Teacher Referrals
    Route::get('/student/teacher-referrals', [App\Http\Controllers\Student\TeacherReferralController::class, 'index'])
        ->name('student.teacher_referrals');

    Route::get('/student/teacher-referrals/{id}', [App\Http\Controllers\Student\TeacherReferralController::class, 'show'])
        ->name('student.teacher_referrals.show');

    Route::post('/student/teacher-referrals/{id}/respond', [App\Http\Controllers\Student\TeacherReferralController::class, 'respond'])
        ->name('student.teacher_referrals.respond');

    Route::delete('/student/teacher-referrals/{id}', function ($id) {
        $deleted = \Illuminate\Support\Facades\DB::table('referrals')
            ->where('id', $id)
            ->where('student_id', auth()->id())
            ->delete();

        if (!$deleted) {
            return back()->with('error', 'Referral not found or not allowed.');
        }

        return back()->with('success', 'Referral deleted.');
    })->name('student.teacher_referrals.destroy');

    }); // end role group

    // ==================== TEACHER ====================
    Route::middleware('role:teacher')->group(function () {

    Route::get('/teacher/dashboard', function () {
        return view('teacher.dashboard');
    })->name('teacher.dashboard');

    Route::get('/teacher/referrals', [ReferralController::class, 'index'])
        ->name('teacher.referrals.index');

    Route::get('/teacher/referrals/create', [ReferralController::class, 'create'])
        ->name('teacher.referrals.create');

    Route::post('/teacher/referrals', [ReferralController::class, 'store'])
        ->name('teacher.referrals.store');

    Route::get('/teacher/referrals/{id}', [ReferralController::class, 'show'])
        ->name('teacher.referrals.show');

    Route::delete('/teacher/referrals/{id}', function ($id) {
        $deleted = \Illuminate\Support\Facades\DB::table('referrals')
            ->where('id', $id)
            ->where('teacher_id', auth()->id())
            ->delete();

        if (!$deleted) {
            return back()->with('error', 'Referral not found or not allowed.');
        }

        return back()->with('success', 'Referral deleted.');
    })->name('teacher.referrals.destroy');
    
    }); // end role group

    // ==================== COUNSELOR ====================
    Route::middleware('role:counselor')->group(function () {

    Route::get('/counselor/dashboard', function () {
        return view('counselor.dashboard');
    })->name('counselor.dashboard');

    // Pending
    Route::get('/counselor/pending', [CaseController::class, 'pending'])
        ->name('counselor.pending');
    Route::get('/counselor/pending/referrals', [CaseController::class, 'pendingReferralsAll'])
        ->name('counselor.pending.referrals');
    Route::get('/counselor/pending/requests', [CaseController::class, 'pendingRequestsAll'])
        ->name('counselor.pending.requests');
    Route::get('/counselor/pending/referral/{id}', [CaseController::class, 'showPendingReferral'])
        ->name('counselor.pending.referral');
    // Appointment requests from students (before a case or schedule exists)
    Route::get('/counselor/pending/appointment-request/{id}', [\App\Http\Controllers\Counselor\AppointmentRequestController::class, 'show'])
        ->name('counselor.appointment_requests.show');
    Route::post('/counselor/appointment-requests/{id}/accept', [\App\Http\Controllers\Counselor\AppointmentRequestController::class, 'accept'])
        ->name('counselor.appointment_requests.accept');
    Route::post('/counselor/appointment-requests/{id}/decline', [\App\Http\Controllers\Counselor\AppointmentRequestController::class, 'decline'])
        ->name('counselor.appointment_requests.decline');

    Route::get('/counselor/pending/request/{id}', [CaseController::class, 'showPendingRequest'])
        ->name('counselor.pending.request');
    Route::delete('/counselor/pending/referrals/{id}', [CaseController::class, 'destroyPendingReferral'])
        ->name('counselor.pending.referrals.destroy');
    Route::delete('/counselor/pending/requests/{id}', [CaseController::class, 'destroyPendingRequest'])
        ->name('counselor.pending.requests.destroy');

    // Decline pending (no case created)
    Route::post('/counselor/pending/referral/{id}/decline', [CaseController::class, 'declineReferral'])
        ->whereNumber('id')
        ->name('counselor.pending.referral.decline');
    Route::post('/counselor/pending/request/{id}/decline', [CaseController::class, 'declineRequest'])
        ->whereNumber('id')
        ->name('counselor.pending.request.decline');

    // Cases — create/store BEFORE {id}, and {id} only accepts numbers
    Route::get('/counselor/cases/create', [CaseController::class, 'create'])
        ->name('counselor.cases.create');

    Route::post('/counselor/cases', [CaseController::class, 'store'])
        ->name('counselor.cases.store');

    Route::get('/counselor/cases', [CaseController::class, 'index'])
        ->name('counselor.cases');

    Route::get('/counselor/cases/{id}', [CaseController::class, 'show'])
        ->whereNumber('id')
        ->name('counselor.cases.show');

    Route::put('/counselor/cases/{id}/status', [CaseController::class, 'updateStatus'])
        ->whereNumber('id')
        ->name('counselor.cases.status');

    Route::delete('/counselor/cases/{id}', [CaseController::class, 'destroy'])
        ->whereNumber('id')
        ->name('counselor.cases.destroy');

    Route::post('/counselor/cases/from-referral/{id}', [CaseController::class, 'createFromReferral'])
        ->whereNumber('id')
        ->name('counselor.cases.fromReferral');

    Route::post('/counselor/cases/from-request/{id}', [CaseController::class, 'createFromRequest'])
        ->whereNumber('id')
        ->name('counselor.cases.fromRequest');

    Route::post('/counselor/cases/{case}/notes', [CaseController::class, 'storeNote'])
        ->whereNumber('case')
        ->name('counselor.cases.notes.store');

    // Appointments
    Route::get('/counselor/appointments', [AppointmentController::class, 'index'])
        ->name('counselor.appointments');
    Route::get('/counselor/appointments/create/{caseId}', [AppointmentController::class, 'create'])
        ->name('counselor.appointments.create');
    Route::post('/counselor/appointments', [AppointmentController::class, 'store'])
        ->name('counselor.appointments.store');
    Route::put('/counselor/appointments/{id}/status', [AppointmentController::class, 'updateStatus'])
        ->name('counselor.appointments.status');

    // Office referrals
    Route::get('/counselor/office-referrals', [CaseController::class, 'officeReferrals'])
        ->name('counselor.office_referrals');
    Route::get('/counselor/office-referrals/{id}', [CaseController::class, 'showOfficeReferral'])
        ->name('counselor.office_referrals.show');
    Route::delete('/counselor/office-referrals/{id}', [CaseController::class, 'destroyOfficeReferral'])
        ->name('counselor.office_referrals.destroy');
    Route::get('/counselor/office-referral/create/{caseId}', [OfficeReferralController::class, 'create'])
        ->name('counselor.office_referral.create');
    Route::post('/counselor/office-referral', [OfficeReferralController::class, 'store'])
        ->name('counselor.office_referral.store');

    Route::get('/counselor/referrals', [CaseController::class, 'allReferrals'])->name('counselor.referrals');
    Route::get('/counselor/requests', [CaseController::class, 'allRequests'])->name('counselor.requests');

    Route::delete('/counselor/referrals/{id}', function ($id) {
        \Illuminate\Support\Facades\DB::table('referrals')->where('id', $id)->delete();
        return back()->with('success', 'Referral deleted.');
    })->name('counselor.referrals.destroy');

    Route::delete('/counselor/requests/{id}', function ($id) {
        \Illuminate\Support\Facades\DB::table('counseling_requests')->where('id', $id)->delete();
        return back()->with('success', 'Request deleted.');
    })->name('counselor.requests.destroy');

    }); // end role group

    // ==================== CLINIC ====================
    Route::middleware('role:clinic')->group(function () {

    Route::get('/clinic/dashboard', function () {
        return view('clinic.dashboard');
    })->name('clinic.dashboard');

    // Mark completed (POST + CSRF form; a GET link could be triggered by accident)
    Route::post('/clinic/referrals/{id}/complete', function ($id) {
        \Illuminate\Support\Facades\DB::table('office_referrals')
            ->where('id', $id)
            ->where('office', 'Clinic')
            ->update([
                'status' => 'Completed',
                'updated_at' => now(),
            ]);

        return redirect()->route('clinic.dashboard')
            ->with('success', 'Referral marked as completed.');
    })->name('clinic.referrals.complete');

    // Optional list / show pages (only if you built those views/methods)
    Route::get('/clinic/referrals', [CaseController::class, 'clinicReferralsIndex'])
        ->name('clinic.referrals.index');

    Route::get('/clinic/referrals/{id}', [CaseController::class, 'showClinicReferral'])
        ->name('clinic.referrals.show');


    }); // end role group

    // ==================== DISCIPLINARY ====================
    Route::middleware('role:disciplinary')->group(function () {

    Route::get('/disciplinary/dashboard', function () {
        return view('disciplinary.dashboard');
    })->name('disciplinary.dashboard');

    // Complete MUST be before /referrals/{id}
    Route::post('/disciplinary/referrals/{id}/complete', function ($id) {
        \Illuminate\Support\Facades\DB::table('office_referrals')
            ->where('id', $id)
            ->where('office', 'Disciplinary')
            ->update([
                'status' => 'Completed',
                'updated_at' => now(),
            ]);

        return redirect()->route('disciplinary.dashboard')
            ->with('success', 'Referral marked as resolved.');
    })->name('disciplinary.referrals.complete');


    // View All + details (same pattern as Clinic)
    Route::get('/disciplinary/referrals', [CaseController::class, 'disciplinaryReferralsIndex'])
        ->name('disciplinary.referrals.index');

    Route::get('/disciplinary/referrals/{id}', [CaseController::class, 'showDisciplinaryReferral'])
        ->whereNumber('id')
        ->name('disciplinary.referrals.show');

    }); // end role group

    // ==================== ADMIN ====================
    Route::middleware('role:admin')->group(function () {

    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::get('/admin/users', function () {
        $role = request('role');
        $q = \Illuminate\Support\Facades\DB::table('users')
            ->select('id', 'name', 'email', 'role', 'student_number', 'employee_id', 'created_at')
            ->orderBy('name');

        if ($role) {
            $q->where('role', $role);
        }

        $users = $q->get();
        return view('admin.users.index', compact('users', 'role'));
    })->name('admin.users.index');

    Route::get('/admin/users/create', function () {
        return view('admin.users.create');
    })->name('admin.users.create');

    Route::post('/admin/users', function (\Illuminate\Http\Request $request) {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => ['required', 'email', 'unique:users,email', new \App\Rules\AllowedEmailDomain],
            'password'       => ['required', 'string', \Illuminate\Validation\Rules\Password::defaults()],
            'role'           => 'required|in:student,teacher,counselor,clinic,disciplinary,admin',
            'student_number' => 'nullable|string|max:50',
            'employee_id'    => 'nullable|string|max:50',
        ]);

        \App\Models\User::create([
            'name'           => $data['name'],
            'email'          => $data['email'],
            'password'       => $data['password'], // hashed by User model cast if set
            'role'           => $data['role'],
            'student_number' => $data['student_number'] ?? null,
            'employee_id'    => $data['employee_id'] ?? null,
            'must_change_password' => true, // temporary password: user must change it on first login
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Account created. Give the user the temporary password; they must change it on first login.');
    })->name('admin.users.store');

    Route::get('/admin/users/{id}/edit', function ($id) {
        $user = \Illuminate\Support\Facades\DB::table('users')->where('id', $id)->first();

        if (!$user) {
            return redirect()->route('admin.users.index')->with('error', 'User not found.');
        }

        return view('admin.users.edit', compact('user'));
    })->name('admin.users.edit');

    Route::put('/admin/users/{id}', function (\Illuminate\Http\Request $request, $id) {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => ['required', 'email', 'unique:users,email,' . $id, new \App\Rules\AllowedEmailDomain],
            'role'           => 'required|in:student,teacher,counselor,clinic,disciplinary,admin',
            'student_number' => 'nullable|string|max:50',
            'employee_id'    => 'nullable|string|max:50',
            'password'       => ['nullable', 'string', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        $user = \App\Models\User::findOrFail($id);

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->role = $data['role'];
        $user->student_number = $data['student_number'] ?? null;
        $user->employee_id = $data['employee_id'] ?? null;

        if (!empty($data['password'])) {
            $user->password = $data['password']; // hashed by model cast
            $user->must_change_password = true;   // a reset password is temporary
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    })->name('admin.users.update');

    Route::delete('/admin/users/{id}', function ($id) {
        $user = \App\Models\User::findOrFail($id);

        if ((int) $user->id === (int) auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    })->name('admin.users.destroy');

    }); // end role group

});