<?php

namespace Database\Factories;

use App\Modules\User\Events\UserGroupMembersUpdateEvent;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UserGroup;
use App\Modules\User\Models\UserGroupMember;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\UserGroup\Models\UserGroupModel>
 */
class UserGroupMemberFactory extends Factory
{
    protected $model = UserGroupMember::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::all()->random();
        $userGroup = UserGroup::all()->random();

        return [
            'user_id' => $user->id,
            'user_group_id' => $userGroup->id
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    // public function configure()
    // {
    //     return $this->afterCreating(function (UserGroupMember $userGroupMember) {
    //         event(new UserGroupMembersUpdateEvent($userGroupMember->user_group_id));
    //     });
    // }
}
