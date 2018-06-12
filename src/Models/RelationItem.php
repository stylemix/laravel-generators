<?php

namespace Bpocallaghan\Generators\Models;


use Illuminate\Support\Fluent;


/**
 * @property string type
 */
class RelationItem extends Fluent
{

    public function typeOneOf(...$types)
    {
        return in_array($this->type, $types);
    }

}