<?php

namespace Tests\Feature\Components;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    private $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create([
            'role' => 'admin'
        ]);
    }

    public function test_admin_can_view_dashboard()
    {
        Sanctum::actingAs($this->admin);

        $response = $this->getJson('/api/admin/dashboard');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'stats' => [
                    'totalParticipants',
                    'participantGrowth',
                    'verifiedQRCodes',
                    'todayVerifications',
                    'totalPayments',
                    'totalResources',
                    'totalDownloads'
                ],
                'participants'
            ]);
    }

    public function test_admin_can_create_validator()
    {
        Sanctum::actingAs($this->admin);

        $validatorData = [
            'name' => 'Test Validator',
            'email' => 'validator@test.com',
            'phone' => '1234567890',
            'state' => 'Lagos',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->postJson('/api/admin/validators', $validatorData);
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'validator' => [
                    'id',
                    'name',
                    'email',
                    'phone',
                    'state',
                    'role'
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'validator@test.com',
            'role' => 'validator'
        ]);
    }

    public function test_admin_can_update_participant()
    {
        Sanctum::actingAs($this->admin);

        $participant = User::factory()->create([
            'role' => 'participant'
        ]);

        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@test.com',
            'phone' => '9876543210',
            'state' => 'Abuja',
            'school' => 'Test School'
        ];

        $response = $this->putJson("/api/admin/participants/{$participant->id}", $updateData);
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'participant'
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $participant->id,
            'name' => 'Updated Name',
            'email' => 'updated@test.com'
        ]);
    }

    public function test_admin_can_export_participants()
    {
        Sanctum::actingAs($this->admin);

        $response = $this->getJson('/api/admin/export-participants');
        
        $response->assertStatus(200)
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }
}
