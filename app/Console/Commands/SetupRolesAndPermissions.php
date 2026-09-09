<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

#[Signature('setup:roles-permissions')]
#[Description('Create application roles and permissions')]
class SetupRolesAndPermissions extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $roles = [
            'Admin',
            'Teacher',
            'Parent',
            'Student',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate([
                'name' => $role,
                'guard_name' => 'web',
            ]);
        }

        $this->info('Roles created successfully.');

        return self::SUCCESS;
    }
}
