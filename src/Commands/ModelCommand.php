<?php

namespace Bpocallaghan\Generators\Commands;

use Bpocallaghan\Generators\Traits\HasRelations;
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
			['relation', 'r', InputOption::VALUE_OPTIONAL, 'Define models relation.', null],
			['scout', null, InputOption::VALUE_OPTIONAL, 'Define whether use scout or not.', null],
        ], parent::getOptions());
    }

	protected function getData()
	{
		return array_merge(parent::getData(), $this->getRelationsData(), [
			// fields schema
			'schema' => $this->getSchema(),
			// check if there is a scout option
			'scoutIncluded' => $this->option('scout') ? true : false,
		]);
	}

}