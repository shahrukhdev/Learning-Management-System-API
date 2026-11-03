<?php

namespace App\Services\Hifz;

use App\Contracts\Hifz\JuzInterface;
use App\Models\Juz;
use Illuminate\Database\Eloquent\Collection;

class JuzService
{
    public function __construct(
        protected JuzInterface $repository
    ) {}

    public function getAllJuz(): Collection
    {
        return $this->repository->getAllJuz();
    }

    public function getJuz(int $id): Juz
    {
        return $this->repository->getJuz($id);
    }
}