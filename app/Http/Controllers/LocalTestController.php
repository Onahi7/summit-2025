<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LocalTestController extends Controller
{
    public function loginAsAdmin()
    {
        // Find an admin user or create one if it doesn't exist
        $admin = User::where('role', 'admin')->first();
        
        if (!$admin) {
            $admin = User::create([
                'name' => 'Test Admin',
                'email' => 'admin@test.com',
                'password' => bcrypt('password'),
                'role' => 'admin'
            ]);
        }
        
        // Login as admin
        Auth::login($admin);
        
        return redirect('/dashboard/admin');
    }
    
    public function loginAsParticipant()
    {
        // Find a participant user or create one if it doesn't exist
        $participant = User::where('role', 'participant')->first();
        
        if (!$participant) {
            $participant = User::create([
                'name' => 'Test Participant',
                'email' => 'participant@test.com',
                'password' => bcrypt('password'),
                'role' => 'participant'
            ]);
        }
        
        // Login as participant
        Auth::login($participant);
        
        return redirect('/dashboard/participant');
    }
    
    public function loginAsValidator()
    {
        // Find a validator user or create one if it doesn't exist
        $validator = User::where('role', 'validator')->first();
        
        if (!$validator) {
            $validator = User::create([
                'name' => 'Test Validator',
                'email' => 'validator@test.com',
                'password' => bcrypt('password'),
                'role' => 'validator'
            ]);
        }
        
        // Login as validator
        Auth::login($validator);
        
        return redirect('/dashboard/validator');
    }
}
