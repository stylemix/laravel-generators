<?php

namespace Bpocallaghan\Generators\Components;


use Bpocallaghan\Generators\Models\SchemaItem;
use Illuminate\Support\Collection;

class ResourceBuilder
{

    /**
     * Create the PHP syntax for the given schema.
     *
     * @param  Collection $schema
     * @param  array $meta
     *
     * @return array|Collection
     */
    public function create($schema, $meta)
    {
        $this->meta = $meta;

        return $schema
            ->each(function (SchemaItem $field) {
                $this->addResourceCode($field);
            });
    }

    protected function addResourceCode(SchemaItem $field)
    {
        if ($field->isRelation()) {
            $field->relationResourceClass = $this->getResourceClass($field);
        }

    }


    protected function getResourceClass(SchemaItem $field)
    {
        return '\\' . $this->meta['resourceClassNamespace'] . '\\' . class_basename($field->relationClass) . $this->meta['resourceClassPostfix'];
    }

}