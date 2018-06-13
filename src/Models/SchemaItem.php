<?php

namespace Bpocallaghan\Generators\Models;

use Illuminate\Support\Fluent;

/**
 * @property string $name
 * @property string $type
 * @property array $arguments
 * @property array $options
 */
class SchemaItem extends Fluent
{
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);

        $this->type = strtr(strtolower($this->type), [
            'datetime' => 'dateTime',
            'datetimetz' => 'dateTimeTz',
            'smallinteger' => 'smallInteger',
            'biginteger' => 'bigInteger',
            'unsingedinteger' => 'unsingedInteger',
            'unsingedsmallinteger' => 'unsingedSmallInteger',
            'unsingedbiginteger' => 'unsingedBigInteger',
            'hasone' => 'hasOne',
            'hasmany' => 'hasMany',
            'belongsto' => 'belongsTo',
            'belongstomany' => 'belongsToMany',
        ]);
    }

    public function typeOneOf(...$types)
    {
        return in_array($this->type, $types);
    }

    public function argument($key, $default = null)
    {
        return array_get($this->arguments, $key, $default);
    }

    public function option($name, $key = null, $default = null)
    {
        return array_get($this->options, $name . (!is_null($key) ? '.' . $key : ''), $default);
    }

    public function isRelation()
    {
        return $this->typeOneOf('hasOne', 'hasMany', 'belongsTo', 'belongsToMany');
    }

    public function isSingleRelation()
    {
        return $this->typeOneOf('hasOne', 'belongsTo');
    }

    public function isMultipleRelation()
    {
        return $this->typeOneOf('hasMany', 'belongsToMany');
    }
}