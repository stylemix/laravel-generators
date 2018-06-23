<?php

namespace Bpocallaghan\Generators\Models;


interface SchemaItemInterface
{

    /**
     * Used to define data for migration.
     *
     * @return array|false Array containing 'name', 'type', 'arguments', 'options' or false of not supported
     */
    public function getMigrationData();

    /**
     * Define validation rules for the field
     *
     * @return array
     */
    public function getValidationRules();

    /**
     * Get api resource value for the schema field
     *
     * @return string
     */
    public function getApiResourceValue();

}