<?php

namespace App\Repositories\Notice;

use App\Contracts\NoticeInterface;
use App\DTOs\Notice\StoreNoticeDTO;
use App\DTOs\Notice\UpdateNoticeDTO;
use App\Exceptions\ApiException;
use App\Models\Notice;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class NoticeRepository implements NoticeInterface
{
    public function getAllNotices(array $filters = []): LengthAwarePaginator
    {
        $search = trim($filters['search'] ?? '');

        return Notice::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('message', 'like', "%{$search}%");
                });
            })
            ->when(
                array_key_exists('is_active', $filters) && $filters['is_active'] !== '',
                function ($query) use ($filters) {
                    $query->where('is_active', $filters['is_active']);
                }
            )
            ->latest()
            ->paginate(
                min(max((int) ($filters['per_page'] ?? 15), 1), 100)
            );
    }

    public function createNotice(StoreNoticeDTO $dto): Notice
    {
        return DB::transaction(function () use ($dto) {
            return Notice::create($dto->toArray());
        });
    }

    public function getNotice(int $id): Notice
    {
        $notice = Notice::query()->find($id);

        if (!$notice) {
            throw new ApiException(message: 'Notice not found.', status: 404);
        }

        return $notice;
    }

    public function updateNotice(int $id, UpdateNoticeDTO $dto): Notice
    {
        return DB::transaction(function () use ($id, $dto) {
            $notice = Notice::query()->find($id);

            if (!$notice) {
                throw new ApiException(message: 'Notice not found.', status: 404);
            }

            $notice->update($dto->toArray());

            return $notice;
        });
    }

    public function deleteNotice(int $id): void
    {
        $notice = Notice::query()->find($id);

        if (!$notice) {
            throw new ApiException(message: 'Notice not found.', status: 404);
        }

        $notice->delete();
    }
}