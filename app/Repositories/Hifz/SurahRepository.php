<?php

namespace App\Repositories\Hifz;

use App\Contracts\Hifz\SurahInterface;
use App\Exceptions\ApiException;
use App\Models\Surah;
use Illuminate\Database\Eloquent\Collection;

class SurahRepository implements SurahInterface
{
    public function getAllSurahs(): Collection
    {
        return Surah::query()
            ->orderBy('number')
            ->get();
    }

    public function getSurah(int $id): Surah
    {
        $surah = Surah::find($id);

        if (! $surah) {
            throw new ApiException(
                message: 'Surah not found.',
                status: 404
            );
        }

        return $surah;
    }
}