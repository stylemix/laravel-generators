<?php

namespace Stylemix\Generators\Commands;

use Symfony\Component\Console\Input\InputOption;
use Illuminate\Console\DetectsApplicationNamespace;

class FileCommand extends GeneratorCommand
{
    use DetectsApplicationNamespace;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate:file';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a file from a stub in the config';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'File';


	protected function getType()
	{
		return $this->option('type');
	}


    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array_merge([
            [
                'type',
                null,
                InputOption::VALUE_OPTIONAL,
                'The type of file: model, view, controller, migration, seed',
                'view'
            ],
        ], parent::getOptions());
    }
}