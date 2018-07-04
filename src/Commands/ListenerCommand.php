<?php

namespace Stylemix\Generators\Commands;

use Illuminate\Console\DetectsApplicationNamespace;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class ListenerCommand extends GeneratorCommand
{
    use DetectsApplicationNamespace;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate:listener';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Event Listener class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Listener';


	/**
	 * Execute the console command.
	 *
	 * @return void
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 */
    public function handle()
    {
        if (!$this->option('event')) {
            $this->error('Missing required option: --event=*NameOfEvent*');
            return;
        }

        parent::handle();
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array_merge([
            ['event', 'e', InputOption::VALUE_REQUIRED, 'The event class being listened for.'],
            [
                'type',
                null,
                InputOption::VALUE_OPTIONAL,
                'Type is listner',
                'listener'
            ]
        ], parent::getOptions());
    }
}