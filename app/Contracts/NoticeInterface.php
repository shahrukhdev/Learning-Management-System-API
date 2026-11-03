<?php

namespace App\Contracts;

use App\DTOs\Notice\StoreNoticeDTO;
use App\DTOs\Notice\UpdateNoticeDTO;
use App\Models\Notice;
use Illuminate\Pagination\LengthAwarePaginator;

interface NoticeInterface
{
    public function getAllNotices(array $filters = []): LengthAwarePaginator;

    public function createNotice(StoreNoticeDTO $dto): Notice;

    public function getNotice(int $id): Notice;

    public function updateNotice(int $id, UpdateNoticeDTO $dto): Notice;

    public function deleteNotice(int $id): void;
}