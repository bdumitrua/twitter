<?php

namespace Database\Factories;

use App\Modules\User\Events\UsersListMembersUpdateEvent;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UsersList;
use App\Modules\User\Models\UsersListMember;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Model>
 */
class UsersListMemberFactory extends Factory
{
    protected $model = UsersListMember::class;

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
}
