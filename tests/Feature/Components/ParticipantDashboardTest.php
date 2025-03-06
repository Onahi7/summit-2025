<?php

namespace Tests\Feature\Components;

use Tests\TestCase;
use App\Models\User;
use App\Models\Payment;
use App\Models\Resource;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ParticipantDashboardTest extends TestCase
{
    use RefreshDatabase;

    private $participant;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->participant = User::factory()->create([
            'role' => 'participant'
        ]);
    }

    public function test_participant_can_view_profile()
    {
        Sanctum::actingAs($this->participant);

        $response = $this->getJson('/api/participant-profile');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name',
                'email',
                'phone',
                'state',
                'school_name',
                'payment_status',
                'is_accredited'
            ]);
    }

    public function test_participant_can_update_profile()
    {
        Sanctum::actingAs($this->participant);

        Storage::fake('public');
        $file = UploadedFile::fake()->image('avatar.jpg');

        $updateData = [
            'name' => 'Updated Name',
            'phone' => '9876543210',
            'state' => 'Abuja',
            'school_name' => 'Updated School',
            'photo' => $file
        ];

        $response = $this->postJson('/api/participant-profile', $updateData);
        
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Profile updated successfully'
            ]);

        Storage::disk('public')->assertExists('photos/' . $file->hashName());
    }

    public function test_participant_can_view_qr_code()
    {
        Sanctum::actingAs($this->participant);

        $response = $this->getJson('/api/qr-code');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'qr_code'
            ]);
    }

    public function test_participant_can_view_meal_status()
    {
        Sanctum::actingAs($this->participant);

        $response = $this->getJson('/api/meals/today');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'morning',
                'evening'
            ]);
    }

    public function test_participant_can_view_meal_history()
    {
        Sanctum::actingAs($this->participant);

        $response = $this->getJson('/api/meals/history');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'next_page_url',
                'prev_page_url'
            ]);
    }

    public function test_participant_can_initiate_payment()
    {
        Sanctum::actingAs($this->participant);

        $response = $this->postJson('/api/initiate-payment');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'authorization_url',
                'reference'
            ]);
    }

    public function test_participant_can_view_notifications()
    {
        Sanctum::actingAs($this->participant);

        // Create some test notifications
        Notification::factory()->count(3)->create([
            'user_id' => $this->participant->id
        ]);

        $response = $this->getJson('/api/notifications');
        
        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_participant_can_mark_notification_as_read()
    {
        Sanctum::actingAs($this->participant);

        $notification = Notification::factory()->create([
            'user_id' => $this->participant->id,
            'read_at' => null
        ]);

        $response = $this->postJson('/api/notifications/mark-as-read', [
            'notification_id' => $notification->id
        ]);
        
        $response->assertStatus(200);
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_participant_can_access_resources()
    {
        Sanctum::actingAs($this->participant);

        // Create some test resources
        Resource::factory()->count(3)->create();

        $response = $this->getJson('/api/resources');
        
        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_participant_can_download_resource()
    {
        Sanctum::actingAs($this->participant);

        Storage::fake('public');
        $file = UploadedFile::fake()->create('document.pdf', 100);
        
        $resource = Resource::factory()->create([
            'file_path' => $file->store('resources', 'public')
        ]);

        $response = $this->getJson("/api/resources/{$resource->id}");
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'title',
                'description',
                'file_path',
                'download_url'
            ]);
    }
}
