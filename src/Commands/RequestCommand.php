<?php

namespace Stylemix\Generators\Commands;

use Symfony\Component\Console\Input\InputOption;

class RequestCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate:request';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new form validator request for resource creation';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Request';

    protected function setResourceName($name)
    {
        if ($resource = $this->option('resource')) {
            $name = $resource;
        }

        parent::setResourceName($name);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array_merge(parent::getOptions(), [
            ['schema', 's', InputOption::VALUE_OPTIONAL, 'Optional schema of fields', null],
            ['resource', null, InputOption::VALUE_OPTIONAL, 'Optional resource name', null],
        ]);
    }

    protected function getData()
    {
        $schema = $this->getSchema();

        return array_merge(parent::getData(), [
            'rules' => $this->getRules($schema),
        ]);
    }

    /**
     * Get rules from schema fields.
     *
     * @return array
     */
    protected function getRules($schema)
    {
        $result = [];

        foreach ($schema as $field) {
            if (!($rules = $field->getValidationRules())) {
                continue;
            }

            $result = array_merge($result, $rules);
        }

        return $result;
    }

}
