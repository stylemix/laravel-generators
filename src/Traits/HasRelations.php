<?php

namespace Bpocallaghan\Generators\Traits;

use Bpocallaghan\Generators\Models\SchemaItemAbstract;

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