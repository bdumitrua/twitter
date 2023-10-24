<?php

namespace App\Jobs;

use App\Modules\User\Models\UsersList;
use App\Modules\User\Repositories\UsersListRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RecalculateUsersLists implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private array $listSubscribers;

    public function __construct(array $listSubscribers)
    {
        $this->listSubscribers = $listSubscribers;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(UsersListRepository $usersListRepository)
    {
        foreach ($this->listSubscribers as $listSubscriber) {
            $usersListRepository->recacheUserLists($listSubscriber);
        }
    }
}
