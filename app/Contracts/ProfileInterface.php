<?php

namespace App\Contracts;

use App\Models\User;

interface ProfileInterface
{
    public function getProfile(User $user): User;

    public function updateProfile(User $user, array $data): User;
}