<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('users:populate-initials')]
#[Description('Populate initials for all existing users')]
class PopulateUserInitials extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        User::query()
            ->select(['id', 'name'])
            ->chunkById(500, function ($users) use (&$updatedCount) {
                foreach ($users as $user) {
                    $user->update([
                        'initials' => getInitials($user->name),
                    ]);

                    $updatedCount++;
                }
            });

        $this->info("Successfully populated initials for {$updatedCount} users.");

        return self::SUCCESS;
    }
}
