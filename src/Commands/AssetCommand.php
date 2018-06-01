<?php

namespace Bpocallaghan\Generators\Commands;


use Bpocallaghan\Generators\Traits\HasRelations;
use Symfony\Component\Console\Input\InputOption;


class AssetCommand extends GeneratorCommand
{
	use HasRelations;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate:asset';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new frontend Javascript assets for resources';

	/**
	 * The type of class being generated.
	 *
	 * @var string
	 */
	protected $type = 'Asset';

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array_merge([
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
