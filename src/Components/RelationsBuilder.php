<?php

namespace Bpocallaghan\Generators\Components;


use Bpocallaghan\Generators\Models\RelationItem;
use Bpocallaghan\Generators\Models\SchemaItem;
use Bpocallaghan\Generators\Traits\NameBuilders;
use Illuminate\Console\DetectsApplicationNamespace;
use Illuminate\Support\Collection;


class RelationsBuilder
{
    use DetectsApplicationNamespace, NameBuilders;

    protected $meta;

    /**
     * Create the PHP syntax for the given schema.
     *
     * @param  Collection $schema
     * @param  array $meta
     */
    public function create($schema, $meta)
    {
        $this->meta = $meta;

        $schema
            ->filter(function (SchemaItem $field) {
                return $field->isRelation();
            })
            ->each(function (SchemaItem $field) {
                $this->buildRelation($field);
            });
    }

    protected function buildRelation(SchemaItem $field)
    {
        $field['name'] = $name = str_replace('_id', '', $field['name']);

        $arguments = $field['arguments'];
        $class     = array_shift($arguments);
        $class     = $class ?: $name;

        switch ($field['type']) {
            case 'hasOne':
            case 'hasMany':
                $field['foreignKey'] = array_get($arguments, 0, $this->meta['name'] . '_id');
                $field['localKey']   = array_get($arguments, 1, 'id');
                break;
            case 'belongsTo':
                $field['foreignKey'] = array_get($arguments, 0, $class . '_id');
                $field['localKey']   = array_get($arguments, 1, 'id');
                break;
            case 'belongsToMany':
                $field['pivotTable'] = array_get($arguments, 0, $this->getPivotTableName($class, $this->meta['name']));
                $field['foreignPivotKey'] = array_get($arguments, 1, $this->meta['name'] . '_id');
                $field['relatedPivotKey'] = array_get($arguments, 2, str_singular($class) . '_id');
                break;
        }

        $arguments = array_map(function ($arg) {
            return '\'' . $arg . '\'';
        }, $arguments);

        $field['relationClass'] = $class = $this->resolveClass($class);

        array_unshift($arguments, $class . '::class');
        $arguments = join(', ', $arguments);

        $field['relationCode'] = "return \$this->{$field['type']}({$arguments});";
    }

    protected function resolveClass($class)
    {
        return '\\' . $this->getAppNamespace() . $this->getModelName($class);
    }

    /**
     * Get the name of the pivot table.
     *
     * @return string
     */
    protected function getPivotTableName($one, $two)
    {
        $tables = [
            strtolower($one),
            strtolower($two)
        ];

        sort($tables);

        return implode('_', array_map('str_singular', $tables));
    }

}