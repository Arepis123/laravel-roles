<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class HashUserPasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:hash-passwords';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hash plain text passwords for users that were imported without hashing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::whereRaw('LENGTH(password) < 60')->get();

        if ($users->isEmpty()) {
            $this->info('No users with plain text passwords found.');
            return;
        }

        foreach ($users as $user) {
            $user->password = Hash::make($user->password);
            $user->save();
            $this->line("Hashed password for user: {$user->email}");
        }

        $this->info("Finished hashing passwords for {$users->count()} users.");
    }
}
