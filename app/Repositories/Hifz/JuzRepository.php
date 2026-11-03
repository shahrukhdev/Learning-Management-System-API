<?php

namespace App\Repositories\Hifz;

use App\Contracts\Hifz\JuzInterface;
use App\Exceptions\ApiException;
use App\Models\Juz;
use Illuminate\Database\Eloquent\Collection;

class JuzRepository implements JuzInterface
{
    public function getAllJuz(): Collection
    {
        return Juz::query()
            ->with([
                'surahRanges.surah',
            ])
            ->orderBy('number')
            ->get();
    }

    public function getJuz(int $id): Juz
    {
        $juz = Juz::query()
            ->with([
                'surahRanges.surah',
            ])
            ->find($id);

        if (! $juz) {
            throw new ApiException(
                message: 'Juz not found.',
                status: 404
            );
        }

        return $juz;
    }
}