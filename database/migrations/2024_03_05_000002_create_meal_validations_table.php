<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meal_validations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participant_id')->constrained()->onDelete('cascade');
            $table->enum('meal_type', ['morning', 'evening']);
            $table->date('date');
            $table->timestamp('validated_at');
            $table->foreignId('validated_by')->constrained('users');
            $table->timestamps();

            $table->unique(['participant_id', 'meal_type', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meal_validations');
    }
};
