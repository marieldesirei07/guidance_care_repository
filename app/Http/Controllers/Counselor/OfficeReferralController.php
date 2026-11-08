<?php

namespace App\Http\Controllers\Counselor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OfficeReferralController extends Controller
{
    public function create($caseId)
    {
        $case = DB::table('cases')
            ->leftJoin('users', 'cases.student_id', '=', 'users.id')
            ->select('cases.*', 'users.name as student_name', 'users.student_number')
            ->where('cases.id', $caseId)
            ->first();

        if (!$case) {
            return redirect()->route('counselor.cases')->with('error', 'Case not found.');
        }

        return view('counselor.office_referrals.create', compact('case'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'case_id' => 'required|exists:cases,id',
            'office' => 'required|in:Clinic,Disciplinary,Other',
            'reason' => 'required|string|min:10',
            'notes_for_office' => 'nullable|string',
        ]);

        DB::table('office_referrals')->insert([
            'case_id' => $request->case_id,
            'referred_by' => Auth::id(),
            'office' => $request->office,
            'reason' => $request->reason,
            'notes_for_office' => $request->notes_for_office,
            'status' => 'Pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Update case status to Referred
        DB::table('cases')->where('id', $request->case_id)->update([
            'status' => 'Referred',
            'updated_at' => now(),
        ]);

        return redirect()->route('counselor.cases.show', $request->case_id)
            ->with('success', 'Case successfully referred to ' . $request->office . '.');
    }
}