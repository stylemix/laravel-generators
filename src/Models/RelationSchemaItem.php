<?php

namespace Stylemix\Generators\Models;

class RelationSchemaItem extends SchemaItemAbstract implements SchemaItemInterface
{

    protected function init(): void
    {
        $this->type = strtr(strtolower($this->type), [
            'hasone' => 'hasOne',
            'hasmany' => 'hasMany',
            'belongsto' => 'belongsTo',
            'belongstomany' => 'belongsToMany',
        ]);

        $this->name = $name = str_replace('_id', '', $this->name);

        $arguments = $this->arguments;
        $entity    = array_shift($arguments);
        $entity    = $entity ?: $name;
        $resource  = $this->generator->getCurrentResource();

        $this->relationEntity = str_singular($entity);
        $this->relationClass  = $this->resolveClass($entity);

        switch ($this->type) {
            case 'hasOne':
            case 'hasMany':
                $this->foreignKey = array_get($arguments, 0, $resource . '_id');
                $this->localKey   = array_get($arguments, 1, 'id');
                break;
            case 'belongsTo':
                $this->foreignKey = array_get($arguments, 0, $name . '_id');
                $this->localKey   = array_get($arguments, 1, 'id');
                break;
            case 'belongsToMany':
                $this->pivotTable = array_get($arguments, 0, $this->getPivotTableName($entity, $resource));
                $this->foreignPivotKey = array_get($arguments, 1, $resource . '_id');
                $this->relatedPivotKey = array_get($arguments, 2, str_singular($entity) . '_id');
                break;
        }

        $args = $this->buildRelationArguments($arguments);

        $this->relationCode = "return \$this->{$this->type}({$args});";
    }

    /**
     * Used to define data for migration.
     *
     * @return array|false Array containing 'name', 'type', 'arguments', 'options'
     */
    public function getMigrationData()
    {
        $name = $this->name;
        $type = $this->type;
        $arguments = $this->arguments;
        $options   = $this->options;

        if ($this->isRelation()) {
            switch ($this->type) {
                case 'belongsTo':
                    $name = $this->foreignKey;
                    $type = 'unsignedInteger';
                    $arguments = [];
                    break;
                default:
                    return false;
            }
        }

        return compact('name', 'type', 'arguments', 'options');
    }

    /**
     * Define validation rules for the type
     *
     * @return array
     */
    public function getValidationRules()
    {
        if (in_array($this->type, ['hasOne', 'hasMany', 'belongsToMany'])) {
            return [];
        }

        $rules = $this->getBasicValidationRules();

        if ($this->type == 'belongsTo') {
            $rules[] = 'integer';
        }

        return $rules;
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

    /**
     * @param $arguments
     * @return array|string
     */
    protected function buildRelationArguments($arguments)
    {
        $arguments = array_map(function ($arg) {
            return '\'' . $arg . '\'';
        }, $arguments);

        array_unshift($arguments, $this->relationClass . '::class');
        $arguments = join(', ', $arguments);
        return $arguments;
    }

    /**
     * Get api resource value for the schema field
     *
     * @return string
     */
    public function getApiResourceValue()
    {
        if ($this->isMultipleRelation()) {
            return $this->getResourceClass() . "::collection(\$this->{$this->name})";
        }

        return "\$this->{$this->name}";
    }

    protected function getResourceClass()
    {
        return '\\' . $this->getResourceClassNamespace() . '\\' . class_basename($this->relationClass) . config('generators.resource.postfix');
    }
}