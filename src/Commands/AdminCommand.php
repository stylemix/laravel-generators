<?php

namespace Stylemix\Generators\Commands;

use Symfony\Component\Console\Input\InputOption;

class AdminCommand extends GeneratorCommand
{
    /**
     * The name of the console command.
     *
     * @var string
     */
    protected $name = 'generate:admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates admin assets';

	/**
	 * The type of class being generated.
	 *
	 * @var string
	 */
	protected $type = 'Admin';

	public function handle()
	{
		$views = config('generators.admin_assets');

		foreach ($views as $key => $name) {
			$this->call('generate:file', [
				'name' => $this->argumentName(),
				'--type' => 'admin',
				'--stub' => $key,
				'--name' => $name,
			]);
		}
	}

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

}
