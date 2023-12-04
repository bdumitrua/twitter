<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

trait CreateDTO
{
    protected function createDTO(Request $request, string $dtoClass)
    {
        $filteredRequestData = array_filter($request->all());
        if (empty($filteredRequestData)) {
            throw new HttpException(Response::HTTP_UNPROCESSABLE_ENTITY, 'At least one field must be filled');
        }

        $dto = new $dtoClass;
        foreach ($filteredRequestData as $key => $value) {
            if (property_exists($dto, $key)) {
                $dto->$key = $value;
            }
        }

        return $dto;
    }
}
