<?php

namespace Stylemix\Generators\Traits;

use Stylemix\Generators\Models\SchemaItemAbstract;

trait HasRelations
{
    protected function getRelationsData()
    {
        $schema = $this->getSchema();

        return [
            'relations' => $schema->filter(function (SchemaItemAbstract $field) {
                return $field->isRelation();
            })
        ];
    }
}