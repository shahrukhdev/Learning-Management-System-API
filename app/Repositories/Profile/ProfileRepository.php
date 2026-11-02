<?php

namespace App\Repositories\Profile;

use App\Contracts\ProfileInterface;
use App\Models\User;
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