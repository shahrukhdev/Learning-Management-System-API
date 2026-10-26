<?php

namespace App\Services\Profile;

use App\Contracts\ProfileInterface;
use App\Models\User;

class ProfileService
{
    public function __construct(protected ProfileInterface $repository)
    {
        //
    }

    public function getProfile(User $user): User
    {
        return $this->repository->getProfile($user);
    }

    public function updateProfile(User $user, array $data): User
    {
        return $this->repository->updateProfile($user, $data);
    }
}