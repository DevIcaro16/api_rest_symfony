<?php

namespace App\DTO;

interface DTOInterface
{
    public function rules(?array $onlyFields): array;
    public function validate(
        ?array $onlyFields
    ): array;
    public function isValid(): bool;
}

?>