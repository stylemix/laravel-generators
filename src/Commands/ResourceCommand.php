<?php

namespace Bpocallaghan\Generators\Commands;

use Bpocallaghan\Generators\Components\ResourceBuilder;
use Bpocallaghan\Generators\Traits\HasRelations;
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

    protected function getSchema()
    {
        $schema = parent::getSchema();
        (new ResourceBuilder())->create($schema, [
            'resourceClassNamespace' => $this->getResourceClassNamespace(),
            'resourceClassPostfix' => $this->settings['postfix'],
        ]);

        return $schema;
    }

    protected function getData()
	{
		return array_merge(parent::getData(), $this->getRelationsData(), [
			// resource namespace
			'resourceClassNamespace' => $this->getResourceClassNamespace(),
		]);
	}

}
