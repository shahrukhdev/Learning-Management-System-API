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
            'Admin' => 'System administration, user management, and operational oversight.',
            'Teacher' => 'Classroom management, grading, lesson planning, and direct student interaction.',
            'Parent' => 'Read-only access to specific student records, attendance, academic progress, and fee details.',
            'Student' => 'Access to personal academic records, class schedules, assignments, grades, and learning resources.',
        ];

        foreach ($roles as $name => $description) {
            Role::updateOrCreate(
                [
                    'name' => $name,
                    'guard_name' => 'web',
                ],
                [
                    'description' => $description,
                ]
            );
        }

        $this->info('Roles created and descriptions updated successfully.');

        return self::SUCCESS;
    }
}
