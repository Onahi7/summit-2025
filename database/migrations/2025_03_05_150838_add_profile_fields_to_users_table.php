<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('school_name')->nullable();
            $table->string('school_address')->nullable();
            $table->string('position')->nullable();
            $table->string('photo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'gender',
                'school_name',
                'school_address',
                'position',
                'photo'
            ]);
        });
    }
};
