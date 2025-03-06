<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class PaymentService
{
    protected $secretKey;
    protected $baseUrl = 'https://api.paystack.co';

    public function __construct()
    {
        $this->secretKey = config('services.paystack.secret_key');
    }

    public function initiatePayment(User $user, $amount)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/transaction/initialize', [
                'email' => $user->email,
                'amount' => $amount * 100, // Convert to kobo
                'callback_url' => route('payment.verify'),
                'metadata' => [
                    'user_id' => $user->id,
                    'custom_fields' => [
                        [
                            'display_name' => "Participant's Name",
                            'variable_name' => "participant_name",
                            'value' => $user->name
                        ],
                        [
                            'display_name' => "School",
                            'variable_name' => "school",
                            'value' => $user->school_name
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Create payment record
                Payment::create([
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'reference' => $data['data']['reference'],
                    'status' => 'pending'
                ]);

                return $data['data'];
            }

            throw new \Exception('Failed to initialize payment: ' . $response->body());
        } catch (\Exception $e) {
            \Log::error('Payment initialization failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function verifyPayment($reference)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->get($this->baseUrl . '/transaction/verify/' . $reference);

            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['data']['status'] === 'success') {
                    $payment = Payment::where('reference', $reference)->first();
                    if ($payment) {
                        $payment->update([
                            'status' => 'completed',
                            'payment_data' => $data['data']
                        ]);

                        // Update user payment status
                        $payment->user->update([
                            'payment_status' => 'paid'
                        ]);

                        // Create notification
                        app(NotificationService::class)->create([
                            'user_id' => $payment->user_id,
                            'title' => 'Payment Successful',
                            'message' => 'Your payment has been confirmed. You can now access all conference features.',
                            'type' => 'success'
                        ]);

                        return true;
                    }
                }
            }

            return false;
        } catch (\Exception $e) {
            \Log::error('Payment verification failed: ' . $e->getMessage());
            return false;
        }
    }

    public function generateReceipt(Payment $payment)
    {
        // Generate PDF receipt
        $pdf = \PDF::loadView('receipts.payment', [
            'payment' => $payment,
            'user' => $payment->user
        ]);

        return $pdf;
    }
}
