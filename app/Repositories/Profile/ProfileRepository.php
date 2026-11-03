<?php

namespace App\Repositories\Profile;

use App\Contracts\ProfileInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileRepository implements ProfileInterface
{
    public function getProfile(User $user): User
    {
        return $user->loadMissing('roles');
    }

    public function updateProfile(User $user, array $data): User
    {
        $oldImage = $user->image;

        unset($data['confirm_password']);

        if (!empty($data['new_password'])) {
            $data['password'] = Hash::make($data['new_password']);
        }

        unset($data['new_password']);

        if (array_key_exists('image', $data)) {
            if ($data['image']) {
                $data['image'] = $data['image']->store('users', 'public');
            } else {
                $data['image'] = null;
            }
        }

        $user->update($data);

        if ($oldImage && array_key_exists('image', $data)) {
            Storage::disk('public')->delete($oldImage);
        }

        return $user->load('roles');
    }
}