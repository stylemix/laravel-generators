<?php

namespace Stylemix\Generators\Models;

use Illuminate\Support\Collection;

class Schema extends Collection
{

    /**
     * Check if schema contains fields of any given types
     *
     * @param array ...$types
     *
     * @return bool
     */
    public function hasTypes(...$types)
    {
        $existing = $this->pluck('type');

        return $existing->intersect($types)->count() > 0;
    }


    /**
     * Check if schema contains any date fields
     *
     * @return bool
     */
    public function hasDateTypes()
    {
        return $this->hasTypes('date', 'dateTime', 'dateTimeTz', 'timestamp', 'timestampTz');
    }
}