<?php

namespace App\Console\Commands;

use App\Firebase\FirebaseService;
use App\Modules\Auth\Services\AuthService;
use App\Modules\User\Models\User;
use Illuminate\Console\Command;

class RefreshDatabases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes all data from local db and cloud db (by your env storage bucket) and creates new fake data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $firebase = new FirebaseService();

        $firebase->wipeMyData();
        $this->call('migrate:fresh');
        $this->call('cache:clear');
        $this->call('db:seed');

        // Удобства ради возьмём первого юзера и авторизуемся для получения нового токена
        $user = User::first();
        $token = auth()->attempt([
            'email' => $user->email,
            'password' => 'password'
        ]);

        $this->info("First user jwt: {$token}");
    }
}
