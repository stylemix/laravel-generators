<?php

namespace Stylemix\Generators\Commands;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CrudCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'generate:crud';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates full CRUD for resource (Model, Assets, Controller, Migration, Seed)';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Resource';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->resource = $this->getResourceOnly();
        $this->settings = config('generators.defaults');

        $this->callModel();
        $this->callFactory();
        $this->callFormResource();
        $this->callRequest();
		$this->callPolicy();
        $this->callResource();
		$this->callController();
		$this->callAdmin();
		$this->callMigration();
        $this->callSeeder();
        $this->callTestCrud();
        $this->callMigrate();

        $this->info('All Done!');
    }

    /**
     * Call the generate:model command
     */
    protected function callModel()
    {
        $name = $this->getModelName();

        $resourceString = $this->getResourceOnly();
        $resourceStringLength = strlen($this->getResourceOnly());

        if ($resourceStringLength > 18) {
            $ans = $this->confirm("Your resource {$resourceString} may have too many characters to use for many to many relationships. The length is {$resourceStringLength}. Continue?");
            if ($ans === false) {
                echo 'generate:resource cancelled!';
                die;
            }
        }

        if ($this->confirmOptional("Create a $name model?", 1)) {
            $this->callCommand('model', $name, [
                '--schema' => $this->option('schema'),
                '--soft-deletes' => $this->option('soft-deletes'),
            ]);
        }
    }

	/**
	 * Call the generate:factory command
	 */
	protected function callFactory()
	{
		if ($this->confirmOptional('Create a model factory?', 1)) {
			$this->callCommand('factory', $this->getResourceName(), [
				'--schema' => $this->option('schema'),
			]);
		}
	}

	protected function callFormResource()
	{
		$name = $this->getModelName();

		if ($this->confirmOptional("Create a $name form resource class?", 1)) {
			$this->callCommand('form', $name . 'Form', [
				'--schema' => $this->option('schema'),
				'--without-request' => 1,
			]);
		}
	}

    /**
     * Call the generate:request command
     */
    protected function callRequest()
    {
		if ($this->confirmOptional('Create a form validator request?', 1)) {
			$this->callCommand('request', $this->getModelName() . 'Request', [
				'--resource' => $this->getResourceName(),
				'--schema' => $this->option('schema'),
				'--stub' => 'form_request',
			]);
        }
    }

	protected function callPolicy()
	{
		if ($this->confirmOptional("Create a policy for $this->resource resource?", 1)) {
			$name = $this->getModelName() . config('generators.settings.policy.postfix');

			$this->callCommand('policy', $name);
		}
	}

    /**
     * Call the generate:resource command
     */
    protected function callResource()
    {
        $name = $this->getModelName();

        if ($this->confirmOptional("Create a $name API resource class?", 1)) {
            $this->callCommand('resource', $name, [
                '--schema' => $this->option('schema'),
            ]);
        }
    }

    /**
     * Generate the resource controller
     */
    protected function callController()
    {
        $name = $this->getResourceControllerName();

        if ($this->confirmOptional("Create a controller ($name) for the $this->resource resource?", 1)) {
            $name = $this->getCollectionName();

			$this->callCommand('controller', $name, [
				'--schema' => $this->option('schema'),
			]);
		}
    }

	/**
	 * Generate resource admin assets
	 */
	protected function callAdmin()
	{
		if ($this->confirmOptional("Create admin assets for the $this->resource resource?", 1)) {
			$this->callCommand('admin', $this->getCollectionName(), [
				'--schema' => $this->option('schema'),
			]);
		}
	}

    /**
     * Call the generate:migration command
     */
    protected function callMigration()
    {
        $name = $this->getMigrationName($this->option('migration'));

        if ($this->confirmOptional("Create a migration ($name) for the $this->resource resource?", 1)) {
            $this->callCommand('migration', $name, [
                '--name' => date('Y_m_d_His') . '_' . $name,
                '--schema' => $this->option('schema'),
				'--soft-deletes' => $this->option('soft-deletes'),
			]);
        }
    }

    /**
     * Call the generate:seed command
     */
    protected function callSeeder()
    {
        $name = $this->getSeedName() . 'TableSeeder';

        if ($this->confirmOptional("Create a seed ($name) for the $this->resource resource?", 1)) {
            $this->callCommand('seed', $name);
        }
    }

    /**
     * Call the generate:test command
     */
    protected function callTestCrud()
    {
        if ($this->confirmOptional("Create a test for the $this->resource resource?", 1)) {
            $this->callCommand('test', $this->getCollectionUpperName(), [
            	'--stub' => 'test_crud',
			]);
        }
    }

    /**
     * Call the migrate command
     */
    protected function callMigrate()
    {
        if ($this->confirm('Migrate the database?', false)) {
            $this->call('migrate');
        }
    }

    /**
     * @param       $command
     * @param       $name
     * @param array $options
     */
    protected function callCommand($command, $name, $options = [])
    {
        $options = array_merge($options, [
            'name' => $name,
            '--plain' => $this->option('plain'),
            '--force' => $this->option('force')
        ]);

        $this->call('generate:' . $command, $options);
    }

    /**
     * The resource argument
     * Lowercase and singular each word
     *
     * @return array|mixed|string
     */
    protected function getArgumentResource()
    {
        $name = $this->argument('resource');
        if (str_contains($name, '/')) {
            $name = str_replace('/', '.', $name);
        }

        if (str_contains($name, '\\')) {
            $name = str_replace('\\', '.', $name);
        }

        // lowecase and singular
        $name = strtolower(str_singular($name));

        return $name;
    }

    /**
     * If there are '.' in the name, get the last occurence
     *
     * @return string
     */
    protected function getResourceOnly()
    {
        $name = $this->getArgumentResource();
        if (!str_contains($name, '.')) {
            return $name;
        }

        return substr($name, strripos($name, '.') + 1);
    }

    /**
     * Get the Controller name for the resource
     *
     * @return string
     */
    protected function getResourceControllerName()
    {
        return $this->getControllerName(str_plural($this->resource)) . config('generators.controller.postfix');
    }

    /**
     * Get the name for the migration
     *
     * @param null $name
     *
     * @return string
     */
    protected function getMigrationName($name = null)
    {
        return 'create_' . str_plural($this->getResourceName($name)) . '_table';
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['resource', InputArgument::REQUIRED, 'The name of the resource being generated.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array_merge(parent::getOptions(), [
            ['migration', null, InputOption::VALUE_OPTIONAL, 'Optional migration name', null],
            ['schema', 's', InputOption::VALUE_OPTIONAL, 'Optional schema to be attached to the migration', null],
			['soft-deletes', null, InputOption::VALUE_NONE, 'Include soft deletion trait'],
		]);
    }
}
