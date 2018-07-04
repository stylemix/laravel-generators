<?php

namespace Stylemix\Generators\Commands;

use Stylemix\Generators\Components\ResourceBuilder;
use Stylemix\Generators\Models\SchemaItemInterface;
use Stylemix\Generators\Traits\HasRelations;
use Symfony\Component\Console\Input\InputOption;

class ResourceCommand extends GeneratorCommand
{
	use HasRelations;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate:resource';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Eloquent API Resource class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Resource';

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array_merge([
            ['schema', 's', InputOption::VALUE_OPTIONAL, 'Optional schema of fields', null],
        ], parent::getOptions());
    }

    protected function getData()
	{
		return array_merge(parent::getData(), $this->getRelationsData(), [
			// resource namespace
			'values' => $this->getResourceValues(),
		]);
	}

    /**
     * Get API resource field from schema fields.
     *
     * @return array
     */
    protected function getResourceValues()
    {
        $values = [];

        foreach ($this->getSchema() as $field) {
            if (!($value = $field->getApiResourceValue())) {
                continue;
            }

            $values[$field->name] = $value;
        }

        return $values;
    }

}
