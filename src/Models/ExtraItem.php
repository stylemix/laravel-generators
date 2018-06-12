<?php

namespace Bpocallaghan\Generators\Models;


use Illuminate\Support\Fluent;


/**
 * @property array options
 */
class ExtraItem extends Fluent
{

    public function option($name)
    {
        return array_get($this->options, $name);
    }

    public function optionArg($name, $arg, $default = null)
    {
        return array_get($this->options, $name . '.' . $arg, $default);
    }

}