<?php

namespace App\Services\Notice;

use App\Contracts\NoticeInterface;
use App\DTOs\Notice\StoreNoticeDTO;
use App\DTOs\Notice\UpdateNoticeDTO;
use App\Models\Notice;
use Illuminate\Pagination\LengthAwarePaginator;

class NoticeService
{
    public function __construct(
        protected NoticeInterface $repository
    ) {}

    public function getAllNotices(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->getAllNotices($filters);
    }

    public function createNotice(StoreNoticeDTO $dto): Notice
    {
        return $this->repository->createNotice($dto);
    }

    public function getNotice(int $id): Notice
    {
        return $this->repository->getNotice($id);
    }

    public function updateNotice(int $id, UpdateNoticeDTO $dto): Notice
    {
        return $this->repository->updateNotice($id, $dto);
    }

    public function deleteNotice(int $id): void
    {
        $this->repository->deleteNotice($id);
    }
}