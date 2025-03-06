<?php

namespace Tests\Feature\Components;

use Tests\TestCase;
use App\Models\User;
use App\Models\MealValidation;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class ValidatorDashboardTest extends TestCase
{
    use RefreshDatabase;

    private $validator;
    private $participant;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->validator = User::factory()->create([
            'role' => 'validator'
        ]);

        $this->participant = User::factory()->create([
            'role' => 'participant'
        ]);
    }

    public function test_validator_can_validate_qr_code()
    {
        Sanctum::actingAs($this->validator);

        $response = $this->postJson('/api/validate-qr', [
            'qr_code' => $this->participant->id
        ]);
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'participant' => [
                    'id',
                    'name',
                    'email',
                    'payment_status',
                    'is_accredited'
                ]
            ]);
    }

    public function test_validator_can_validate_morning_meal()
    {
        Sanctum::actingAs($this->validator);

        // Set time to 8:00 AM
        Carbon::setTestNow(Carbon::today()->setHour(8));

        $response = $this->postJson('/api/validator/meals/validate', [
            'participant_id' => $this->participant->id,
            'type' => 'morning_meal'
        ]);
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'validation'
            ]);

        $this->assertDatabaseHas('meal_validations', [
            'participant_id' => $this->participant->id,
            'validator_id' => $this->validator->id,
            'type' => 'morning_meal'
        ]);
    }

    public function test_validator_cannot_validate_morning_meal_outside_hours()
    {
        Sanctum::actingAs($this->validator);

        // Set time to 10:00 AM
        Carbon::setTestNow(Carbon::today()->setHour(10));

        $response = $this->postJson('/api/validator/meals/validate', [
            'participant_id' => $this->participant->id,
            'type' => 'morning_meal'
        ]);
        
        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Morning meal validation is only available between 7:00 AM and 9:00 AM'
            ]);
    }

    public function test_validator_can_validate_evening_meal()
    {
        Sanctum::actingAs($this->validator);

        // Set time to 7:00 PM
        Carbon::setTestNow(Carbon::today()->setHour(19));

        $response = $this->postJson('/api/validator/meals/validate', [
            'participant_id' => $this->participant->id,
            'type' => 'evening_meal'
        ]);
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'validation'
            ]);

        $this->assertDatabaseHas('meal_validations', [
            'participant_id' => $this->participant->id,
            'validator_id' => $this->validator->id,
            'type' => 'evening_meal'
        ]);
    }

    public function test_validator_cannot_validate_evening_meal_outside_hours()
    {
        Sanctum::actingAs($this->validator);

        // Set time to 5:00 PM
        Carbon::setTestNow(Carbon::today()->setHour(17));

        $response = $this->postJson('/api/validator/meals/validate', [
            'participant_id' => $this->participant->id,
            'type' => 'evening_meal'
        ]);
        
        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Evening meal validation is only available between 6:00 PM and 8:00 PM'
            ]);
    }

    public function test_validator_cannot_validate_same_meal_twice()
    {
        Sanctum::actingAs($this->validator);

        // Set time to 8:00 AM
        Carbon::setTestNow(Carbon::today()->setHour(8));

        // First validation
        $this->postJson('/api/validator/meals/validate', [
            'participant_id' => $this->participant->id,
            'type' => 'morning_meal'
        ]);

        // Second validation attempt
        $response = $this->postJson('/api/validator/meals/validate', [
            'participant_id' => $this->participant->id,
            'type' => 'morning_meal'
        ]);
        
        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Participant has already validated this meal today'
            ]);
    }

    public function test_validator_can_view_validation_stats()
    {
        Sanctum::actingAs($this->validator);

        $response = $this->getJson('/api/validator/meals/stats');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'today' => [
                    'morning',
                    'evening',
                    'total'
                ],
                'total_validations'
            ]);
    }

    public function test_validator_can_view_recent_validations()
    {
        Sanctum::actingAs($this->validator);

        // Create some test validations
        MealValidation::factory()->count(5)->create([
            'validator_id' => $this->validator->id
        ]);

        $response = $this->getJson('/api/validator/recent-validations');
        
        $response->assertStatus(200)
            ->assertJsonCount(5);
    }
}
