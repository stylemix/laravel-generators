<?php

namespace Stylemix\Generators\Commands;


use Symfony\Component\Console\Input\InputOption;

class FormCommand extends GeneratorCommand
{

	/**
	 * The name of the console command.
	 *
	 * @var string
	 */
	protected $name = 'generate:form';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Generates form API resource (optionally with request validator class)';

	protected $type = 'form_resource';

	/**
	 * Execute the console command.
	 */
	public function handle()
	{
		parent::handle();

		if ($this->option('without-request')) {
			return;
		}

		$this->call('generate:request', [
			'name' => ucfirst($this->argumentName()) . 'Request',
			'--stub' => 'form_request',
		]);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array_merge(parent::getOptions(), [
			['schema', 's', InputOption::VALUE_OPTIONAL, 'Optional schema of fields', null],
			['without-request', null, InputOption::VALUE_NONE, 'Don\'t generate request class'],
		]);
	}
}
