<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    public function run()
    {
        $modules = [
            'Users',
            'Roles',
            'Permissions',
            'Dashboard',
            'Notices',
        ];

        foreach ($modules as $module) {
            Module::updateOrCreate(
                ['name' => $module],
                ['name' => $module]
            );
        }
    }
}