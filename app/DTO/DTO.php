<?php

namespace App\DTO;

class DTO
{
    protected function __construct(array $data)
    {
        foreach($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public function toArray()
    {
        return get_object_vars($this);
    }
}