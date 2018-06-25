<?php

namespace Bpocallaghan\Generators\Components;

use Bpocallaghan\Generators\Generator;
use Illuminate\Support\Collection;

class SchemaParser
{
    /**
     * @var array Useful meta information
     */
    protected $meta;
    /**
     * The parsed schema.
     *
     * @var array
     */
    private $schema = [];

    /**
     * @var Generator
     */
    private $generator;

    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * Parse the command line migration schema.
     * Ex: name:string, age:integer:nullable
     *
     * @param  string $schema
     * @param  array $meta
     *
     * @return array|Collection
     */
    public function parse($schema, $meta)
    {
        $this->meta = $meta;

        $fields = $this->splitIntoFields($schema);

        foreach ($fields as $field) {
            $segments = $this->parseSegments($field);

            if ($this->fieldNeedsForeignConstraint($segments)) {
                unset($segments['options']['foreign']);

                // If the user wants a foreign constraint, then
                // we'll first add the regular field.
                $this->addField($segments);

                // And then add another field for the constraint.
                $this->addForeignConstraint($segments);

                continue;
            }

            $this->addField($segments);
        }

        return collect($this->schema)->keyBy('name');
    }

    /**
     * Add a field to the schema array.
     *
     * @param  array $field
     *
     * @return $this
     */
    private function addField($field)
    {
        $field['meta'] = $this->meta;

        $this->schema[] = $this->generator->makeSchemaItem($field);

        return $this;
    }

    /**
     * Get an array of fields from the given schema.
     *
     * @param  string $schema
     *
     * @return array
     */
    private function splitIntoFields($schema)
    {
        return preg_split('/,\s?(?![^()]*\))/', $schema);
    }

    /**
     * Get the segments of the schema field.
     *
     * @param  string $field
     *
     * @return array
     */
    private function parseSegments($field)
    {
        $segments = explode(':', $field);

        $name = array_shift($segments);
        $type = array_shift($segments);
        $arguments = [];
        $options = $this->parseOptions($segments);

        // Do we have arguments being used here?
        // Like: string(100)
        if (preg_match('/(.+?)\(([^)]+)\)/', $type, $matches)) {
            $type = $matches[1];
            $arguments = explode(',', $matches[2]);
        }

        return compact('name', 'type', 'arguments', 'options');
    }

    /**
     * Parse any given options into something usable.
     *
     * @param  array $options
     *
     * @return array
     */
    private function parseOptions($options)
    {
        if (empty($options)) {
            return [];
        }

        foreach ($options as $option) {
            if (str_contains($option, '(')) {
                preg_match('/([a-z]+)\(([^\)]+)\)/i', $option, $matches);

                $results[$matches[1]] = array_map('trim', explode(',', $matches[2]));
            }
            else {
                $results[$option] = true;
            }
        }

        return $results;
    }

    /**
     * Add a foreign constraint field to the schema.
     *
     * @param array $segments
     */
    private function addForeignConstraint($segments)
    {
        $string = sprintf("%s:foreign:references('id'):on('%s')", $segments['name'], $this->getTableNameFromForeignKey($segments['name']));

        $this->addField($this->parseSegments($string));
    }

    /**
     * Try to figure out the name of a table from a foreign key.
     * Ex: user_id => users
     *
     * @param  string $key
     *
     * @return string
     */
    private function getTableNameFromForeignKey($key)
    {
        return str_plural(str_replace('_id', '', $key));
    }

    /**
     * Determine if the user wants a foreign constraint for the field.
     *
     * @param  array $segments
     *
     * @return bool
     */
    private function fieldNeedsForeignConstraint($segments)
    {
        return array_key_exists('foreign', $segments['options']);
    }
}

