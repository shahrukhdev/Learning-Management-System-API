<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

#[Signature('create:admin-user')]
#[Description('Create an admin user for the application')]
class CreateAdminUser extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $role = Role::firstOrCreate(['name' => 'Super Admin']);

        $user = User::updateOrCreate([
            'email' => 'admin@example.com'
        ], [
            'name' => 'Admin',
            'password' => bcrypt('Test1234')
        ]);

        $user->assignRole('Super Admin');

        $this->info('Admin user created successfully.');
    }
}
