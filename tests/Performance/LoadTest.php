<?php

namespace Tests\Performance;

use Tests\TestCase;
use App\Models\User;
use App\Models\Resource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class LoadTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $participants = [];
    private $validators = [];

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test users
        $this->admin = User::factory()->create(['role' => 'admin']);
        
        // Create 100 participants
        $this->participants = User::factory()->count(100)->create(['role' => 'participant']);
        
        // Create 10 validators
        $this->validators = User::factory()->count(10)->create(['role' => 'validator']);
        
        // Create 50 resources
        Resource::factory()->count(50)->create(['is_published' => true]);
    }

    public function test_concurrent_dashboard_access()
    {
        $startTime = microtime(true);
        $requests = [];

        // Simulate 50 concurrent dashboard requests
        for ($i = 0; $i < 50; $i++) {
            $participant = $this->participants[array_rand($this->participants->toArray())];
            Sanctum::actingAs($participant);
            
            $requests[] = $this->getJson('/api/participant-profile');
        }

        $endTime = microtime(true);
        $totalTime = $endTime - $startTime;

        // Assert that the average response time is under 500ms
        $this->assertLessThan(
            25, // 50 requests * 500ms = 25 seconds total
            $totalTime,
            'Dashboard requests are taking too long'
        );

        // Assert all requests were successful
        foreach ($requests as $response) {
            $response->assertStatus(200);
        }
    }

    public function test_concurrent_resource_access()
    {
        $startTime = microtime(true);
        $requests = [];

        // Simulate 30 concurrent resource access requests
        for ($i = 0; $i < 30; $i++) {
            $participant = $this->participants[array_rand($this->participants->toArray())];
            Sanctum::actingAs($participant);
            
            $requests[] = $this->getJson('/api/resources');
        }

        $endTime = microtime(true);
        $totalTime = $endTime - $startTime;

        // Assert that the average response time is under 300ms
        $this->assertLessThan(
            9, // 30 requests * 300ms = 9 seconds total
            $totalTime,
            'Resource requests are taking too long'
        );

        // Assert all requests were successful
        foreach ($requests as $response) {
            $response->assertStatus(200);
        }
    }

    public function test_concurrent_qr_validations()
    {
        $startTime = microtime(true);
        $requests = [];

        // Simulate 20 concurrent QR validations
        for ($i = 0; $i < 20; $i++) {
            $validator = $this->validators[array_rand($this->validators->toArray())];
            $participant = $this->participants[array_rand($this->participants->toArray())];
            
            Sanctum::actingAs($validator);
            
            $requests[] = $this->postJson('/api/validate-qr', [
                'qr_code' => $participant->id
            ]);
        }

        $endTime = microtime(true);
        $totalTime = $endTime - $startTime;

        // Assert that the average response time is under 400ms
        $this->assertLessThan(
            8, // 20 requests * 400ms = 8 seconds total
            $totalTime,
            'QR validation requests are taking too long'
        );

        // Assert all requests were successful
        foreach ($requests as $response) {
            $response->assertStatus(200);
        }
    }

    public function test_database_query_optimization()
    {
        DB::enableQueryLog();
        
        Sanctum::actingAs($this->admin);
        
        // Test dashboard query performance
        $response = $this->getJson('/api/admin/dashboard');
        $dashboardQueries = count(DB::getQueryLog());
        
        // Assert that dashboard loads with minimal queries (using eager loading)
        $this->assertLessThan(
            10,
            $dashboardQueries,
            'Dashboard is making too many database queries'
        );

        DB::flushQueryLog();

        // Test resource listing query performance
        $response = $this->getJson('/api/resources');
        $resourceQueries = count(DB::getQueryLog());
        
        // Assert that resource listing uses efficient queries
        $this->assertLessThan(
            5,
            $resourceQueries,
            'Resource listing is making too many database queries'
        );
    }

    public function test_caching_effectiveness()
    {
        Sanctum::actingAs($this->participants[0]);

        // First request - should hit the database
        $startTime = microtime(true);
        $this->getJson('/api/resources');
        $firstRequestTime = microtime(true) - $startTime;

        // Second request - should hit the cache
        $startTime = microtime(true);
        $this->getJson('/api/resources');
        $secondRequestTime = microtime(true) - $startTime;

        // Assert that cached request is significantly faster
        $this->assertLessThan(
            $firstRequestTime * 0.5,
            $secondRequestTime,
            'Caching is not providing significant performance improvement'
        );
    }

    public function test_file_upload_performance()
    {
        Sanctum::actingAs($this->admin);

        $file = \Illuminate\Http\UploadedFile::fake()->create('test.pdf', 5000); // 5MB file

        $startTime = microtime(true);
        
        $response = $this->postJson('/api/admin/resources', [
            'title' => 'Performance Test Resource',
            'description' => 'Testing file upload performance',
            'type' => 'document',
            'file' => $file
        ]);

        $uploadTime = microtime(true) - $startTime;

        // Assert that large file upload completes within reasonable time
        $this->assertLessThan(
            5, // 5 seconds
            $uploadTime,
            'File upload is taking too long'
        );

        $response->assertStatus(201);
    }
}
