<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    private $paystack_secret_key;
    private $ticket_prices = [
        'executive' => 50000,
        'regular' => 30000,
        'observer' => 20000,
    ];

    public function __construct()
    {
        $this->paystack_secret_key = config('services.paystack.secret_key');
    }

    public function initiate(Request $request)
    {
        $request->validate([
            'ticket_type' => ['required', 'string', 'in:executive,regular,observer'],
        ]);

        $amount = $this->ticket_prices[$request->ticket_type];
        $participant = $request->user()->participant;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->paystack_secret_key,
            'Content-Type' => 'application/json',
        ])->post('https://api.paystack.co/transaction/initialize', [
            'email' => $request->user()->email,
            'amount' => $amount * 100, // Convert to kobo
            'callback_url' => route('payment.callback'),
            'metadata' => [
                'participant_id' => $participant->id,
                'ticket_type' => $request->ticket_type,
            ],
        ]);

        if (!$response->successful()) {
            return response()->json(['message' => 'Payment initialization failed'], 500);
        }

        return response()->json($response->json());
    }

    public function verify(Request $request)
    {
        $request->validate([
            'reference' => 'required|string',
        ]);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->paystack_secret_key,
        ])->get("https://api.paystack.co/transaction/verify/{$request->reference}");

        if (!$response->successful()) {
            return response()->json(['message' => 'Payment verification failed'], 500);
        }

        $data = $response->json()['data'];
        
        if ($data['status'] === 'success') {
            $participant = $request->user()->participant;
            $participant->update([
                'payment_status' => true,
                'ticket_type' => $data['metadata']['ticket_type'],
                'amount_paid' => $data['amount'] / 100,
                'payment_reference' => $data['reference'],
            ]);

            return response()->json([
                'message' => 'Payment verified successfully',
                'participant' => $participant,
            ]);
        }

        return response()->json(['message' => 'Payment failed'], 400);
    }

    public function generateReceipt(Request $request)
    {
        $participant = $request->user()->participant;

        if (!$participant->payment_status) {
            return response()->json(['message' => 'No payment found'], 404);
        }

        return response()->json([
            'receipt' => [
                'participant_name' => $request->user()->name,
                'ticket_type' => $participant->ticket_type,
                'amount_paid' => $participant->amount_paid,
                'payment_reference' => $participant->payment_reference,
                'payment_date' => $participant->updated_at->format('Y-m-d H:i:s'),
            ]
        ]);
    }
}
