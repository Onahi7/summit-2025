<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Resource;
use App\Models\Notification;
use App\Models\Payment;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ParticipantsExport;

class AdminController extends Controller
{
    public function dashboard()
    {
        $now = Carbon::now();
        $startOfDay = $now->copy()->startOfDay();

        // Get statistics
        $stats = [
            'totalParticipants' => User::where('role', 'participant')->count(),
            'participantGrowth' => $this->calculateParticipantGrowth(),
            'verifiedQRCodes' => DB::table('validations')->count(),
            'todayVerifications' => DB::table('validations')
                ->whereDate('created_at', $now->toDateString())
                ->count(),
            'totalPayments' => Payment::where('status', 'paid')->sum('amount'),
            'totalResources' => Resource::count(),
            'totalDownloads' => Resource::sum('download_count')
        ];

        // Get participants with related data
        $participants = User::where('role', 'participant')
            ->with(['payment', 'validations'])
            ->latest()
            ->get()
            ->map(function ($participant) {
                return [
                    'id' => $participant->id,
                    'name' => $participant->name,
                    'email' => $participant->email,
                    'phone' => $participant->phone,
                    'state' => $participant->state,
                    'school' => $participant->school_name,
                    'paymentStatus' => $participant->payment ? $participant->payment->status : 'pending',
                    'qrVerified' => $participant->validations->isNotEmpty(),
                    'created_at' => $participant->created_at
                ];
            });

        return response()->json([
            'stats' => $stats,
            'participants' => $participants
        ]);
    }

    private function calculateParticipantGrowth()
    {
        $now = Carbon::now();
        $lastMonth = User::where('role', 'participant')
            ->whereMonth('created_at', $now->subMonth()->month)
            ->count();
        
        $thisMonth = User::where('role', 'participant')
            ->whereMonth('created_at', $now->month)
            ->count();

        if ($lastMonth == 0) return 100;
        
        return round((($thisMonth - $lastMonth) / $lastMonth) * 100);
    }

    public function createValidator(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'state' => 'required|string',
            'password' => 'required|string|min:8|confirmed'
        ]);

        $validator = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'state' => $request->state,
            'password' => Hash::make($request->password),
            'role' => 'validator'
        ]);

        return response()->json([
            'message' => 'Validator created successfully',
            'validator' => $validator
        ]);
    }

    public function updateParticipant(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'required|string|max:20',
            'state' => 'required|string',
            'school' => 'required|string'
        ]);

        $participant = User::findOrFail($id);
        $participant->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'state' => $request->state,
            'school_name' => $request->school
        ]);

        return response()->json([
            'message' => 'Participant updated successfully',
            'participant' => $participant
        ]);
    }

    public function exportParticipants()
    {
        return Excel::download(new ParticipantsExport, 'participants.xlsx');
    }
}
