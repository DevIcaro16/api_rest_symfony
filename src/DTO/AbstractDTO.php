<?php

namespace App\DTO;

abstract class AbstractDTO
{
    public function all()
    {
        return get_object_vars($this);
    }

    public function toArray(): array
    {
        return $this->all();
    }
}

?>