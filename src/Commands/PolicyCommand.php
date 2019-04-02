<?php

namespace Stylemix\Generators\Commands;

class PolicyCommand extends GeneratorCommand
{

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'generate:policy';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new Policy class';

	/**
	 * The type of class being generated.
	 *
	 * @var string
	 */
	protected $type = 'Policy';

	public function handle()
	{
		parent::handle();

		$model = '\\' . $this->getAppNamespace() . $this->getModelName();
		$class = '\\' . $this->getNamespace($this->argumentName()) . '\\' . $this->getClassName();
		$this->info("***\nIn case you don't have policy name guessing, add the following code to your AuthServiceProvider.php into \$policies property:");
		$this->table([], [["{$model}::class => {$class}::class,"]]);
		$this->info("***");
	}
}
