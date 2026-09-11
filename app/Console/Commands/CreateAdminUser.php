<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    protected $signature = 'matching:create-admin {email} {--name=管理者} {--password=}';
    protected $description = 'Create or update an approved administrator account for BA Matching.';

    public function handle()
    {
        $password = $this->option('password') ?: $this->secret('Admin password');

        if (!$password || strlen($password) < 8) {
            $this->error('Password must be at least 8 characters.');
            return self::FAILURE;
        }

        $user = User::updateOrCreate(
            ['email' => $this->argument('email')],
            [
                'name' => $this->option('name'),
                'password' => Hash::make($password),
                'role' => 'admin',
                'member_type' => null,
                'status' => 'approved',
            ]
        );

        $this->info("Admin user is ready: {$user->email}");
        return self::SUCCESS;
    }
}
