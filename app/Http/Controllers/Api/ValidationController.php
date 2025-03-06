<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Participant;
use App\Models\MealValidation;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ValidationController extends Controller
{
    public function validateQrCode(Request $request)
    {
        $request->validate([
            'qr_code' => ['required', 'string'],
        ]);

        $participant = Participant::where('qr_code', $request->qr_code)
            ->where('payment_status', true)
            ->first();

        if (!$participant) {
            return response()->json(['message' => 'Invalid QR code'], 404);
        }

        return response()->json([
            'message' => 'QR code validated successfully',
            'participant' => $participant->load('user'),
        ]);
    }

    public function validateMeal(Request $request)
    {
        $request->validate([
            'qr_code' => ['required', 'string'],
            'meal_type' => ['required', 'in:morning,evening'],
        ]);

        $participant = Participant::where('qr_code', $request->qr_code)
            ->where('payment_status', true)
            ->first();

        if (!$participant) {
            return response()->json(['message' => 'Invalid QR code'], 404);
        }

        $today = Carbon::today();
        $existing = MealValidation::where('participant_id', $participant->id)
            ->where('meal_type', $request->meal_type)
            ->where('date', $today)
            ->first();

        if ($existing) {
            return response()->json(['message' => 'Meal already validated for today'], 400);
        }

        MealValidation::create([
            'participant_id' => $participant->id,
            'meal_type' => $request->meal_type,
            'date' => $today,
            'validated_at' => now(),
            'validated_by' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Meal validation successful',
            'participant' => $participant->load('user'),
        ]);
    }
}
