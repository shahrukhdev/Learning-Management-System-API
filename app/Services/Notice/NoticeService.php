<?php

namespace App\Services\Notice;

use App\Contracts\NoticeInterface;
use App\DTOs\Notice\StoreNoticeDTO;
use App\DTOs\Notice\UpdateNoticeDTO;
use App\Exceptions\ApiException;
use App\Models\Notice;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class NoticeService
{
    /** Roles that only see active notices (inactive ones are drafts / withdrawn). */
    private const ACTIVE_ONLY_ROLES = ['Student'];

    public function __construct(
        protected NoticeInterface $repository
    ) {}

    public function getAllNotices(array $filters = [], ?User $user = null): LengthAwarePaginator
    {
        if ($this->seesActiveOnly($user)) {
            $filters['is_active'] = true;
        }

        return $this->repository->getAllNotices($filters);
    }

    public function createNotice(StoreNoticeDTO $dto): Notice
    {
        return $this->repository->createNotice($dto);
    }

    public function getNotice(int $id, ?User $user = null): Notice
    {
        $notice = $this->repository->getNotice($id);

        if (!$notice->is_active && $this->seesActiveOnly($user)) {
            throw new ApiException(message: 'Notice not found.', status: 404);
        }

        return $notice;
    }

    private function seesActiveOnly(?User $user): bool
    {
        return $user !== null && $user->hasAnyRole(self::ACTIVE_ONLY_ROLES);
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