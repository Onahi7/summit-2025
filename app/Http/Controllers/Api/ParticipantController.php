<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ParticipantController extends Controller
{
    public function show(Request $request)
    {
        return response()->json($request->user()->participant);
    }

    public function update(Request $request)
    {
        $request->validate([
            'phone_number' => ['required', 'string'],
            'state' => ['required', 'string'],
            'napps_chapter' => ['required', 'string'],
            'passport_photo' => ['sometimes', 'image', 'max:2048'],
        ]);

        $data = $request->only(['phone_number', 'state', 'napps_chapter']);

        if ($request->hasFile('passport_photo')) {
            $path = $request->file('passport_photo')->store('passports', 'public');
            $data['passport_photo'] = $path;
        }

        $participant = $request->user()->participant()->updateOrCreate(
            ['user_id' => $request->user()->id],
            $data
        );

        return response()->json([
            'message' => 'Profile updated successfully',
            'participant' => $participant
        ]);
    }

    public function getQrCode(Request $request)
    {
        $participant = $request->user()->participant;

        if (!$participant || !$participant->payment_status) {
            return response()->json(['message' => 'Payment required before QR code generation'], 403);
        }

        if (!$participant->qr_code) {
            $participant->qr_code = $this->generateUniqueQrCode();
            $participant->save();
        }

        $qrCode = QrCode::size(300)->generate($participant->qr_code);

        return response()->json([
            'qr_code' => $participant->qr_code,
            'qr_image' => base64_encode($qrCode)
        ]);
    }

    private function generateUniqueQrCode()
    {
        do {
            $code = 'NAPPS-' . strtoupper(uniqid());
        } while (Participant::where('qr_code', $code)->exists());

        return $code;
    }
}
