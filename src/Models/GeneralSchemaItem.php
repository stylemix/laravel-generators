<?php

namespace Bpocallaghan\Generators\Models;

class GeneralSchemaItem extends SchemaItemAbstract implements SchemaItemInterface
{

    /**
     * Initialize properties
     */
    protected function init(): void
    {
        $converted = [
            'tinyIncrements',
            'smallIncrements',
            'mediumIncrements',
            'bigIncrements',
            'dateTime',
            'dateTimeTz',
            'timeTz',
            'timestampTz',
            'softDeletes',
            'softDeletesTz',
            'tinyInteger',
            'smallInteger',
            'mediumInteger',
            'bigInteger',
            'unsingedInteger',
            'unsingedSmallInteger',
            'unsignedMediumInteger',
            'unsingedBigInteger',
            'unsignedDecimal',
            'mediumText',
            'longText',
            'ipAddress',
            'macAddress',
            'lineString',
            'geometryCollection',
            'multiPoint',
            'multiLineString',
            'multiPolygon',
            'rememberToken',
        ];

        // Normalize type names to appropriate camel case version
        $this->type = strtr(strtolower($this->type), array_combine(
            array_map('strtolower', $converted),
            $converted
        ));
    }

    /**
     * Used to define data for migration.
     *
     * @return array Array containing 'type', 'arguments', 'options'
     */
    public function getMigrationData()
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'arguments' => $this->prepareArguments(),
            'options' => array_except($this->options, $this->excludedOptionsForMigration()),
        ];
    }

    /**
     * Enum arguments should be passed as single array as 2nd argument
     */
    protected function prepareArguments()
    {
        if ($this->type != 'enum') {
            return $this->arguments;
        }

        $arguments = array_map(function ($arg) {
            return '\'' . $arg . '\'';
        }, $this->arguments);

        return [
            '[' . join(',', $arguments) . ']'
        ];
    }

    protected function excludedOptionsForMigration()
    {
        return ['form', 'rules'];
    }

    /**
     * Define validation rules for the type
     *
     * @return array
     */
    public function getValidationRules()
    {
        $rules = $this->getBasicValidationRules();

        if (in_array($this->type, ['char', 'string', 'text', 'enum'])) {
            $rules[] = 'string';
            if (in_array($this->type, ['char', 'string']) && isset($this->arguments[0])) {
                $rules[] = 'max:' . $this->arguments[0];
            }
        } elseif (in_array($this->type, ['timestamp', 'date', 'dateTime', 'dateTimeTz'])) {
            $rules[] = 'date';
        } elseif (str_contains(strtolower($this->type), 'integer')) {
            $rules[] = 'integer';
        } elseif (str_contains(strtolower($this->type), 'decimal')) {
            $rules[] = 'numeric';
        } elseif (in_array($this->type, ['boolean'])) {
            $rules[] = 'boolean';
        } elseif ($this->type == 'enum' && count($this->arguments)) {
            $rules[] = 'in:' . join(',', $this->arguments);
        }

        return $rules;
    }

    /**
     * Get api resource value for the schema field
     *
     * @return string
     */
    public function getApiResourceValue()
    {
        return "\$this->{$this->name}";
    }
}