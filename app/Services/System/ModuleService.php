<?php

namespace App\Services\System;

use App\Contracts\ModuleInterface;
use Illuminate\Support\Collection;

class ModuleService
{
    public function __construct(protected ModuleInterface $repository)
    {
        //
    }

    public function getAllModules(): Collection
    {
        return $this->repository->getAllModules();
    }
}