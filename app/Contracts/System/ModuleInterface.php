<?php

namespace App\Contracts\System;

use Illuminate\Support\Collection;

interface ModuleInterface
{
    public function getAllModules(): Collection;
}