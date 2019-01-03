<?php

namespace Stylemix\Generators\Commands;

use Symfony\Component\Console\Input\InputOption;

class FactoryCommand extends GeneratorCommand
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate:factory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Eloquent Factory file';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Factory';

	protected function getFileBaseName()
	{
		$fileBaseName = parent::getFileBaseName();

		return preg_replace('/Factory$/', '', ucfirst($fileBaseName)) . 'Factory';
	}

	/**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array_merge([
            ['schema', 's', InputOption::VALUE_OPTIONAL, 'Optional schema to be attached to the migration', null],
        ], parent::getOptions());
    }

}
