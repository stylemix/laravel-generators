<?php

namespace Bpocallaghan\Generators;

use Bpocallaghan\Generators\Commands\GeneratorCommand;
use Bpocallaghan\Generators\Models\SchemaItemAbstract;
use Illuminate\Container\Container;

class Generator extends Container
{
    /**
     * @var GeneratorCommand
     */
    protected $currentResource;

    /**
     * Register schema item class for given types
     *
     * @param string $class
     * @param string[] ...$types
     */
    public function bindType($class, ...$types)
    {
        foreach ($types as $type) {
            $this->bind('types.' . strtolower($type), $class);
        }
    }

    /**
     * Creates schema item instance from parsed schema
     *
     * @param array $attributes
     *
     * @return SchemaItemAbstract
     */
    public function makeSchemaItem(array $attributes)
    {
        $abstract = 'types.' . strtolower($attributes['type']);

        if ($this->bound($abstract)) {
            return $this->make($abstract, compact('attributes'));
        }

        return $this->make('types.*', compact('attributes'));
    }

    public function setCurrentResource($resource)
    {
        $this->currentResource = $resource;
    }

    public function getCurrentResource()
    {
        return $this->currentResource;
    }

}