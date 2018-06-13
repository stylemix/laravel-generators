<?php

namespace Bpocallaghan\Generators\Commands;

use Bpocallaghan\Generators\Traits\HasRelations;
use Symfony\Component\Console\Input\InputOption;


class ControllerCommand extends GeneratorCommand
{
	use HasRelations;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate:controller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new resource Controller class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Controller';

	public function handle()
	{
		parent::handle();

		if (!$this->confirm("Add routes to api.php?", 1)) {
			return;
		}

		// get the stub path
		$stub = config('generators.controller_route_stub');

		if (is_null($stub)) {
			$this->error('The stub does not exist in the config file - "controller_route_stub"');
			exit;
		}

		$name = $this->argumentName();
		$stub = $this->files->get($stub);
		$stub = str_replace('{{route}}', str_replace('_', '-', $this->getCollectionName($name)), $stub);

		$postfix    = config('generators.settings.controller.postfix');
		$controller = ucwords(camel_case(str_replace($postfix, '', str_plural($name)))) . $postfix;
		$stub       = str_replace('{{controller}}', $controller, $stub);

		$path = '/routes/api.php';
		if (file_put_contents(base_path() . $path, PHP_EOL . $stub, FILE_APPEND)) {
			$this->info('Route registered successfully.');
			$this->info('- .' . $path);
		}
		else {
			$this->error('Failed to register route.');
		}
	}

	protected function getFileBaseName()
	{
		return $this->getControllerName($this->getArgumentNameOnly());
	}

	protected function getData()
	{
		return array_merge(parent::getData(), $this->getRelationsData(), [
			// resource namespace
			'resourceClassNamespace' => $this->getResourceClassNamespace(),
		]);
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