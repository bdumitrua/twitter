<?php

namespace Database\Factories;

use App\Modules\User\Events\UsersListSubscribtionEvent;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UsersList;
use App\Modules\User\Models\UsersListSubscribtion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Model>
 */
class UsersListSubscribtionFactory extends Factory
{
    protected $model = UsersListSubscribtion::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::all()->random();
        $usersList = UsersList::all()->random();

        return [
            'user_id' => $user->id,
            'users_list_id' => $usersList->id,
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    // public function configure()
    // {
    //     return $this->afterCreating(function (UsersListSubscribtion $usersListSubscribtion) {
    //         event(new UsersListSubscribtionEvent($usersListSubscribtion->users_list_id, true));
    //     });
    // }
}
