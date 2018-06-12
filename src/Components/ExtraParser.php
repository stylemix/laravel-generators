<?php

namespace Bpocallaghan\Generators\Components;


use Bpocallaghan\Generators\Models\ExtraItem;
use Illuminate\Support\Collection;

class ExtraParser
{
    /**
     * The parsed data.
     *
     * @var array
     */
    private $extra = [];

    /**
     * Parse the command line extra option.
     *
     * @param  string $schema
     *
     * @return array|Collection
     */
    public function parse($schema)
    {
        $fields = $this->splitIntoFields($schema);

        foreach ($fields as $field) {
            $segments = $this->parseSegments($field);
            $this->addSegment($segments);
        }

        return collect($this->extra)->keyBy('name');
    }

    /**
     * Add a field to the schema array.
     *
     * @param  array $segment
     *
     * @return $this
     */
    private function addSegment($segment)
    {
        $this->extra[] = new ExtraItem($segment);

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
        $options = $this->parseOptions($segments);

        return compact('name', 'options');
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

}