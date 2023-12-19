<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

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
            if (!empty($value)) {
                $entity->$property = $value;
            }
        }

        return $entity->save();
    }
}
