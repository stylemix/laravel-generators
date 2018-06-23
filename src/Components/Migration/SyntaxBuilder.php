<?php

namespace Bpocallaghan\Generators\Components\Migration;

use Bpocallaghan\Generators\Exceptions\GeneratorException;
use Bpocallaghan\Generators\Models\SchemaItemInterface;
use Bpocallaghan\Generators\Models\SchemaItemAbstract;
use Illuminate\Support\Collection;

class SyntaxBuilder
{
    /**
     * A template to be inserted.
     *
     * @var string
     */
    private $template;

    /**
     * Create the PHP syntax for the given schema.
     *
     * @param  Collection $schema
     * @param  array $meta
     *
     * @return array
     */
    public function create($schema, $meta)
    {
        $up = $this->createSchemaForUpMethod($schema, $meta);
        $down = $this->createSchemaForDownMethod($schema, $meta);

        return compact('up', 'down');
    }

    /**
     * Create the schema for the "up" method.
     *
     * @param  Collection $schema
     * @param  array  $meta
     *
     * @return string
     * @throws GeneratorException
     */
    private function createSchemaForUpMethod($schema, $meta)
    {
        $fields = $this->constructSchema($schema);

        if ($meta['action'] == 'create') {
            return $this->insert($fields)->into($this->getCreateSchemaWrapper());
        }

        if ($meta['action'] == 'add') {
            return $this->insert($fields)->into($this->getChangeSchemaWrapper());
        }

        if ($meta['action'] == 'remove') {
            $fields = $this->constructSchema($schema, 'Drop');

            return $this->insert($fields)->into($this->getChangeSchemaWrapper());
        }

        // Otherwise, we have no idea how to proceed.
        throw new GeneratorException;
    }

    /**
     * Construct the syntax for a down field.
     *
     * @param  Collection $schema
     * @param  array $meta
     *
     * @return string
     * @throws GeneratorException
     */
    private function createSchemaForDownMethod($schema, $meta)
    {
        // If the user created a table, then for the down
        // method, we should drop it.
        if ($meta['action'] == 'create') {
            return sprintf("Schema::dropIfExists('%s');", $meta['table']);
        }

        // If the user added columns to a table, then for
        // the down method, we should remove them.
        if ($meta['action'] == 'add') {
            $fields = $this->constructSchema($schema, 'Drop');

            return $this->insert($fields)->into($this->getChangeSchemaWrapper());
        }

        // If the user removed columns from a table, then for
        // the down method, we should add them back on.
        if ($meta['action'] == 'remove') {
            $fields = $this->constructSchema($schema);

            return $this->insert($fields)->into($this->getChangeSchemaWrapper());
        }

        // Otherwise, we have no idea how to proceed.
        throw new GeneratorException;
    }

    /**
     * Store the given template, to be inserted somewhere.
     *
     * @param  string $template
     *
     * @return $this
     */
    private function insert($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get the stored template, and insert into the given wrapper.
     *
     * @param  string $wrapper
     * @param  string $placeholder
     *
     * @return mixed
     */
    private function into($wrapper, $placeholder = 'schema_up')
    {
        return str_replace('{{' . $placeholder . '}}', $this->template, $wrapper);
    }

    /**
     * Get the wrapper template for a "create" action.
     *
     * @return string
     */
    private function getCreateSchemaWrapper()
    {
        return file_get_contents(config('generators.schema_create_stub'));
    }

    /**
     * Get the wrapper template for an "add" action.
     *
     * @return string
     */
    private function getChangeSchemaWrapper()
    {
        return file_get_contents(config('generators.schema_change_stub'));
    }

    /**
     * Construct the schema fields.
     *
     * @param  Collection  $schema
     * @param  string $direction
     *
     * @return string
     */
    private function constructSchema($schema, $direction = 'Add')
    {
        if (!$schema) {
            return '';
        }

        $fields = $schema
            ->map(function (SchemaItemInterface $field) use ($direction) {
                if (!($migration = $field->getMigrationData())) {
                    return null;
                }

                $method = "{$direction}Column";

                return $this->$method($migration);
            });

        return implode("\n" . str_repeat(' ', 12), $fields->filter()->all());
    }

    /**
     * Construct the syntax to add a column.
     *
     * @param array $migration
     *
     * @return string
     */
    private function addColumn($migration)
    {
        $syntax = sprintf("\$table->%s('%s')", $migration['type'], $migration['name']);

        // If there are arguments for the schema type, like decimal('amount', 5, 2)
        // then we have to remember to work those in.
        if ($migration['arguments']) {
            $syntax = substr($syntax, 0, -1) . ', ';
            $syntax .= implode(', ', $migration['arguments']) . ')';
        }

        foreach ($migration['options'] as $method => $value) {
            $syntax .= sprintf("->%s(%s)", $method, $value === true ? '' : join(', ', $value));
        }

        return $syntax . ';';
    }

    /**
     * Construct the syntax to drop a column.
     *
     * @param  array $migration
     *
     * @return string
     */
    private function dropColumn($migration)
    {
        return sprintf("\$table->dropColumn('%s');", $migration['name']);
    }
}
