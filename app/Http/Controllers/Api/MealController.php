<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MealValidation;
use Carbon\Carbon;

class MealController extends Controller
{
    public function today()
    {
        $today = Carbon::today();
        $user = auth()->user();

        $morningValidation = MealValidation::where('participant_id', $user->id)
            ->whereDate('created_at', $today)
            ->where('type', 'morning_meal')
            ->exists();

        $eveningValidation = MealValidation::where('participant_id', $user->id)
            ->whereDate('created_at', $today)
            ->where('type', 'evening_meal')
            ->exists();

        return response()->json([
            'morning' => $morningValidation,
            'evening' => $eveningValidation
        ]);
    }

    public function history(Request $request)
    {
        $user = auth()->user();
        
        $validations = MealValidation::with('validator')
            ->where('participant_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'data' => $validations->map(function ($validation) {
                return [
                    'id' => $validation->id,
                    'type' => $validation->type === 'morning_meal' ? 'Morning Meal' : 'Evening Meal',
                    'validator_name' => $validation->validator->name,
                    'created_at' => $validation->created_at
                ];
            }),
            'next_page_url' => $validations->nextPageUrl(),
            'prev_page_url' => $validations->previousPageUrl()
        ]);
    }

    public function validate(Request $request)
    {
        $request->validate([
            'participant_id' => 'required|exists:users,id',
            'type' => 'required|in:morning_meal,evening_meal'
        ]);

        $validator = auth()->user();
        $now = Carbon::now();

        // Check if it's the correct time for validation
        $isMorningTime = $now->hour >= 7 && $now->hour < 9;
        $isEveningTime = $now->hour >= 18 && $now->hour < 20;

        if ($request->type === 'morning_meal' && !$isMorningTime) {
            return response()->json([
                'message' => 'Morning meal validation is only available between 7:00 AM and 9:00 AM'
            ], 400);
        }

        if ($request->type === 'evening_meal' && !$isEveningTime) {
            return response()->json([
                'message' => 'Evening meal validation is only available between 6:00 PM and 8:00 PM'
            ], 400);
        }

        // Check if participant already validated this meal today
        $existingValidation = MealValidation::where('participant_id', $request->participant_id)
            ->where('type', $request->type)
            ->whereDate('created_at', $now->toDateString())
            ->exists();

        if ($existingValidation) {
            return response()->json([
                'message' => 'Participant has already validated this meal today'
            ], 400);
        }

        // Create validation
        $validation = MealValidation::create([
            'participant_id' => $request->participant_id,
            'validator_id' => $validator->id,
            'type' => $request->type
        ]);

        return response()->json([
            'message' => 'Meal validation successful',
            'validation' => $validation
        ]);
    }

    public function stats()
    {
        $today = Carbon::today();
        $validator = auth()->user();

        $morningCount = MealValidation::where('validator_id', $validator->id)
            ->whereDate('created_at', $today)
            ->where('type', 'morning_meal')
            ->count();

        $eveningCount = MealValidation::where('validator_id', $validator->id)
            ->whereDate('created_at', $today)
            ->where('type', 'evening_meal')
            ->count();

        $totalToday = $morningCount + $eveningCount;
        $totalAll = MealValidation::where('validator_id', $validator->id)->count();

        return response()->json([
            'today' => [
                'morning' => $morningCount,
                'evening' => $eveningCount,
                'total' => $totalToday
            ],
            'total_validations' => $totalAll
        ]);
    }
}
