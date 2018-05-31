<?php

namespace Bpocallaghan\Generators\Commands;

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
            // optional for the generate:console
            [
                'command',
                null,
                InputOption::VALUE_OPTIONAL,
                'The terminal command that should be assigned.',
                'command:name'
            ],
        ], parent::getOptions());
    }
}