<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Validation;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ValidatorController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function validate(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'type' => 'required|in:accreditation,morning_meal,evening_meal'
        ]);

        try {
            DB::beginTransaction();

            $user = User::where('qr_code', $request->code)
                ->where('payment_status', 'paid')
                ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid QR code or unpaid registration'
                ], 400);
            }

            // Check if already validated today for meals
            if (in_array($request->type, ['morning_meal', 'evening_meal'])) {
                $existingValidation = Validation::where('user_id', $user->id)
                    ->where('type', $request->type)
                    ->whereDate('created_at', Carbon::today())
                    ->first();

                if ($existingValidation) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Meal already validated today'
                    ], 400);
                }
            }

            // Check if already accredited
            if ($request->type === 'accreditation' && $user->is_accredited) {
                return response()->json([
                    'success' => false,
                    'message' => 'User already accredited'
                ], 400);
            }

            // Create validation record
            $validation = Validation::create([
                'user_id' => $user->id,
                'validator_id' => auth()->id(),
                'type' => $request->type,
                'status' => 'success'
            ]);

            // Update user status for accreditation
            if ($request->type === 'accreditation') {
                $user->update(['is_accredited' => true]);
            }

            // Send notification to user
            $this->notificationService->create([
                'user_id' => $user->id,
                'title' => $this->getNotificationTitle($request->type),
                'message' => $this->getNotificationMessage($request->type),
                'type' => 'success'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Validation successful',
                'data' => $validation
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function recentValidations()
    {
        $validations = Validation::with('user')
            ->latest()
            ->take(50)
            ->get()
            ->map(function ($validation) {
                return [
                    'id' => $validation->id,
                    'participant_name' => $validation->user->name,
                    'participant_school' => $validation->user->school_name,
                    'type' => $validation->type,
                    'status' => $validation->status,
                    'created_at' => $validation->created_at
                ];
            });

        return response()->json($validations);
    }

    protected function getNotificationTitle($type)
    {
        return match($type) {
            'accreditation' => 'Accreditation Successful',
            'morning_meal' => 'Morning Meal Validated',
            'evening_meal' => 'Evening Meal Validated',
            default => 'Validation Successful'
        };
    }

    protected function getNotificationMessage($type)
    {
        return match($type) {
            'accreditation' => 'You have been successfully accredited for the conference.',
            'morning_meal' => 'Your morning meal has been validated.',
            'evening_meal' => 'Your evening meal has been validated.',
            default => 'Your request has been validated successfully.'
        };
    }
}
