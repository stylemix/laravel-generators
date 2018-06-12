<?php

namespace Bpocallaghan\Generators\Components;


use Bpocallaghan\Generators\Models\RelationItem;
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
     * @param  array $schema
     * @param  array $meta
     *
     * @return array|Collection
     */
    public function create($schema, $meta)
    {
        $this->meta = $meta;

        return collect($schema)
            ->filter(function ($field) {
                return in_array(strtolower($field['type']), ['hasone', 'hasmany', 'belongsto', 'belongstomany']);
            })
            ->map(function ($field) {
                return $this->buildRelation($field);
            })
            ->keyBy('name');
    }


    protected function buildRelation($relation)
    {
        $relation['type'] = strtr(strtolower($relation['type']), [
            'hasone' => 'hasOne',
            'hasmany' => 'hasMany',
            'belongsto' => 'belongsTo',
            'belongstomany' => 'belongsToMany',
        ]);

        $relation['name'] = $name = str_replace('_id', '', $relation['name']);

        $arguments = $relation['arguments'];
        $class     = array_shift($arguments);
        $class     = $class ?: $name;

        switch ($relation['type']) {
            case 'hasOne':
            case 'hasMany':
                $relation['foreignKey'] = array_get($arguments, 0, $this->meta['name'] . '_id');
                $relation['localKey']   = array_get($arguments, 1, 'id');
                break;
            case 'belongsTo':
                $relation['foreignKey'] = array_get($arguments, 0, $class . '_id');
                $relation['localKey']   = array_get($arguments, 1, 'id');
                break;
            case 'belongsToMany':
                $relation['pivotTable'] = array_get($arguments, 0, $this->getPivotTableName($class, $this->meta['name']));
                $relation['foreignPivotKey'] = array_get($arguments, 1, $this->meta['name'] . '_id');
                $relation['relatedPivotKey'] = array_get($arguments, 2, str_singular($class) . '_id');
                break;
        }

        $arguments = array_map(function ($arg) {
            return '\'' . $arg . '\'';
        }, $arguments);

        $relation['class'] = $class = $this->resolveClass($class);

        array_unshift($arguments, $class . '::class');
        $arguments = join(', ', $arguments);

        $relation['code'] = "return \$this->{$relation['type']}({$arguments});";

        return new RelationItem($relation);
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