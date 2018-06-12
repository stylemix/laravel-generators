<?php

namespace Bpocallaghan\Generators\Traits;

use Bpocallaghan\Generators\Components\RelationsBuilder;

trait HasRelations
{
    protected function getRelationsData()
    {
        $schema = $this->getSchema();
        $meta = ['name' => $this->getArgumentNameOnly()];

        return [
            'relations' => (new RelationsBuilder())->create($schema, $meta)
        ];
    }
}