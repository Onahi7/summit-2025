<?php

namespace Tests\Feature\API;

use Tests\TestCase;
use App\Models\User;
use App\Models\Resource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ResourceControllerTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $participant;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->participant = User::factory()->create(['role' => 'participant']);
    }

    public function test_admin_can_upload_resource()
    {
        Sanctum::actingAs($this->admin);
        Storage::fake('public');

        $file = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->postJson('/api/admin/resources', [
            'title' => 'Test Resource',
            'description' => 'Test Description',
            'type' => 'document',
            'file' => $file,
            'is_published' => true
        ]);
        
        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'title',
                'description',
                'type',
                'file_path',
                'is_published'
            ]);

        Storage::disk('public')->assertExists('resources/' . $file->hashName());
    }

    public function test_admin_can_update_resource()
    {
        Sanctum::actingAs($this->admin);
        Storage::fake('public');

        $resource = Resource::factory()->create();
        $file = UploadedFile::fake()->create('updated.pdf', 100);

        $response = $this->putJson("/api/admin/resources/{$resource->id}", [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'type' => 'document',
            'file' => $file
        ]);
        
        $response->assertStatus(200)
            ->assertJson([
                'title' => 'Updated Title',
                'description' => 'Updated Description'
            ]);

        Storage::disk('public')->assertExists('resources/' . $file->hashName());
    }

    public function test_admin_can_delete_resource()
    {
        Sanctum::actingAs($this->admin);
        Storage::fake('public');

        $file = UploadedFile::fake()->create('document.pdf', 100);
        $resource = Resource::factory()->create([
            'file_path' => $file->store('resources', 'public')
        ]);

        $response = $this->deleteJson("/api/admin/resources/{$resource->id}");
        
        $response->assertStatus(204);
        Storage::disk('public')->assertMissing($resource->file_path);
        $this->assertDatabaseMissing('resources', ['id' => $resource->id]);
    }

    public function test_admin_can_reorder_resources()
    {
        Sanctum::actingAs($this->admin);

        $resources = Resource::factory()->count(3)->create();

        $reorderData = $resources->map(function ($resource, $index) {
            return [
                'id' => $resource->id,
                'order' => $index + 1
            ];
        })->toArray();

        $response = $this->postJson('/api/admin/resources/reorder', [
            'resources' => $reorderData
        ]);
        
        $response->assertStatus(200);

        foreach ($reorderData as $data) {
            $this->assertDatabaseHas('resources', [
                'id' => $data['id'],
                'order' => $data['order']
            ]);
        }
    }

    public function test_participant_can_view_published_resources()
    {
        Sanctum::actingAs($this->participant);

        Resource::factory()->count(3)->create(['is_published' => true]);
        Resource::factory()->create(['is_published' => false]);

        $response = $this->getJson('/api/resources');
        
        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_participant_cannot_view_unpublished_resources()
    {
        Sanctum::actingAs($this->participant);

        $resource = Resource::factory()->create(['is_published' => false]);

        $response = $this->getJson("/api/resources/{$resource->id}");
        
        $response->assertStatus(404);
    }

    public function test_validates_file_size_and_type()
    {
        Sanctum::actingAs($this->admin);
        Storage::fake('public');

        // Test file too large
        $largefile = UploadedFile::fake()->create('large.pdf', 11000);

        $response = $this->postJson('/api/admin/resources', [
            'title' => 'Test Resource',
            'description' => 'Test Description',
            'type' => 'document',
            'file' => $largefile
        ]);
        
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['file']);

        // Test invalid file type
        $invalidFile = UploadedFile::fake()->create('invalid.exe', 100);

        $response = $this->postJson('/api/admin/resources', [
            'title' => 'Test Resource',
            'description' => 'Test Description',
            'type' => 'document',
            'file' => $invalidFile
        ]);
        
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['file']);
    }
}
