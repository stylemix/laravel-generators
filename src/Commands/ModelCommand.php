<?php

namespace Stylemix\Generators\Commands;

use Stylemix\Generators\Traits\HasRelations;
use Symfony\Component\Console\Input\InputOption;

class ModelCommand extends GeneratorCommand
{

	use HasRelations;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate:model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Eloquent Model class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Model';


	/**
	 * Execute the console command.
	 *
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 */
    public function handle()
    {
        parent::handle();

        if ($this->option('migration')) {
            $name = $this->getMigrationName();

            $this->call('generate:migration', [
                'name'     => $name,
                '--model'  => false,
                '--schema' => $this->option('schema')
            ]);
        }
    }


	protected function getFileBaseName()
	{
		return $this->getModelName();
	}

	/**
     * Get the name for the migration
     *
     * @return string
     */
    protected function getMigrationName()
    {
        return 'create_' . str_plural(strtolower($this->getArgumentNameOnly())) . '_table';
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array_merge([
            ['migration', 'm', InputOption::VALUE_NONE, 'Create a new migration file as well.'],
            ['schema', 's', InputOption::VALUE_OPTIONAL, 'Optional schema to be attached to the migration', null],
			['soft-deletes', null, InputOption::VALUE_NONE, 'Include soft deletion trait'],
		], parent::getOptions());
    }

	protected function getData()
	{
		return array_merge(
			parent::getData(),
			$this->getRelationsData(),
			[
				'softDeletes' => $this->option('soft-deletes'),
			]
		);
	}

}
