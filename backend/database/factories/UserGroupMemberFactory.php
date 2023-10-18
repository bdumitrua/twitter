<?php

namespace Database\Factories;

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
        return [
            //
        ];
    }
}
