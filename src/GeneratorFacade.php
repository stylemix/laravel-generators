<?php

namespace Stylemix\Generators;


use Illuminate\Support\Facades\Facade;

class GeneratorFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'stylemix.generator';
    }

}