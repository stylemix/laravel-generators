<?php

namespace Stylemix\Generators\Models;

use Stylemix\Generators\Generator;
use Stylemix\Generators\Traits\NameBuilders;
use Illuminate\Console\DetectsApplicationNamespace;
use Illuminate\Support\Fluent;

/**
 * @property string $name
 * @property string $type
 * @property array $arguments
 * @property array $options
 * @property array $meta
 */
abstract class SchemaItemAbstract extends Fluent
{
    use NameBuilders;

    /**
     * @var Generator
     */
    protected $generator;

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);

        $this->generator = app('stylemix.generator');

        $this->init();
    }

    /**
     * Allow child classes to make some initialization
     */
    protected function init()
    {
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

    protected function getBasicValidationRules()
    {
        $rules = [];
        if (!array_key_exists('nullable', $this->options)) {
            if (!array_key_exists('default', $this->options)) {
                $rules[] = 'required';
            }
        }
        else {
            $rules[] = 'nullable';
        }

        if (array_key_exists('unique', $this->options)) {
            $table = $this->getTableName($this->generator->getCurrentResource());
            $rules[] = "unique:{$table}";
        }

        if ($extra = array_get($this->options, 'rules')) {
            $rules[] = $rules;
        }

        return $rules;
    }
}