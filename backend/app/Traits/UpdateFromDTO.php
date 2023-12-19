<?php

namespace App\Traits;

use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

trait UpdateFromDTO
{
    /**
     * @param Model $entity
     * @param mixed $dto
     * 
     * @return bool
     */
    public function updateFromDto(Model $entity, $dto): bool
    {
        $dtoProperties = get_object_vars($dto);
        foreach ($dtoProperties as $property => $value) {
            if ($value !== null) {
                $entity->$property = $value;
            }
        }

        return $entity->save();
    }

    /**
     * @param User $user
     * @param mixed $dto
     * 
     * @return bool
     */
    public function updateUserFromDto(User $user, $dto): bool
    {
        $dtoProperties = get_object_vars($dto);
        foreach ($dtoProperties as $property => $value) {
            if (!empty($value)) {
                $user->$property = $property === 'password'
                    ? Hash::make($value)
                    : $value;
            }
        }

        return $user->save();
    }
}
