<?php

namespace Bpocallaghan\Generators\Traits;

use Bpocallaghan\Generators\Models\SchemaItem;

trait HasRelations
{
    protected function getRelationsData()
    {
        $schema = $this->getSchema();

        return [
            'relations' => $schema->filter(function (SchemaItem $field) {
                return $field->isRelation();
            })
        ];
    }
}