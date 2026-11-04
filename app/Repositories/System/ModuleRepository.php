<?php

namespace App\Repositories\System;

use App\Contracts\System\ModuleInterface;
use App\Models\Module;
use Illuminate\Support\Collection;

class ModuleRepository implements ModuleInterface
{
    public function getAllModules(): Collection
    {
        return Module::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
    }
}