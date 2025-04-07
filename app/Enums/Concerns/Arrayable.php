<?php

namespace App\Enums\Concerns;

trait Arrayable
{
    public static function toArray(): array
    {
        $names = array_column(self::cases(), 'name');
        $values = array_column(self::cases(), 'value');

        return array_combine($names, $values);
    }
}
