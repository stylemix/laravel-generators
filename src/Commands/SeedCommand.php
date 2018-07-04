<?php

namespace Stylemix\Generators\Commands;

use Symfony\Component\Console\Input\InputOption;


class SeedCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Database Seed class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Seed';


	/*protected function getFileBaseName()
	{
		return $this->getSeedName();
	}*/

	public function buildClass($name = null)
	{
		if (!($resource = $this->option('resource'))) {
			$resource = str_replace('TableSeeder', '', $this->getArgumentNameOnly());
		}

		$this->setResourceName($resource);

		return parent::buildClass();
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array_merge(parent::getOptions(), [
			['resource', null, InputOption::VALUE_OPTIONAL, 'Optional resource name', null],
		]);
	}
}
