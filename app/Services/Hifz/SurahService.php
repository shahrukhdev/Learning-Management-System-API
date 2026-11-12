<?php

namespace App\Services\Hifz;

use App\Contracts\Hifz\SurahInterface;
use App\Models\Surah;
use Illuminate\Database\Eloquent\Collection;

class SurahService
{
    public function __construct(
        protected SurahInterface $repository
    ) {}

    public function getAllSurahs(): Collection
    {
        return $this->repository->getAllSurahs();
    }

    public function getSurah(int $id): Surah
    {
        return $this->repository->getSurah($id);
    }
}