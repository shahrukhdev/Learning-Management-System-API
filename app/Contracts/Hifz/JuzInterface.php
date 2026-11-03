<?php

namespace App\Contracts\Hifz;

use App\Models\Juz;
use Illuminate\Database\Eloquent\Collection;

interface JuzInterface
{
    public function getAllJuz(): Collection;

    public function getJuz(int $id): Juz;
}