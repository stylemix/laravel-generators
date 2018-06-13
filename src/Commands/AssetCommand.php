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
		], parent::getOptions());
	}

    protected function getData()
    {
        return array_merge(parent::getData(), $this->getRelationsData());
    }

}
