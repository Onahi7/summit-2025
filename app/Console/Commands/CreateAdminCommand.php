<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminCommand extends Command
{
    protected $signature = 'admin:create {--email=} {--name=} {--password=}';
    protected $description = 'Create a new admin user';

    public function handle()
    {
        $email = $this->option('email') ?? $this->ask('What is the admin email?');
        $name = $this->option('name') ?? $this->ask('What is the admin name?');
        $password = $this->option('password') ?? $this->secret('What is the admin password?');

        $validator = Validator::make([
            'email' => $email,
            'name' => $name,
            'password' => $password,
        ], [
            'email' => ['required', 'email', 'unique:users,email'],
            'name' => ['required', 'string', 'min:3'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return 1;
        }

        $admin = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $this->info("Admin user created successfully!");
        $this->table(
            ['Name', 'Email', 'Role'],
            [[$admin->name, $admin->email, $admin->role]]
        );

        return 0;
    }
}
