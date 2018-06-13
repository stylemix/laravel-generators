<?php

namespace Bpocallaghan\Generators\Commands;

use Artisan;
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
        $this->callRequest();
        $this->callResource();
        $this->callAssets();
        $this->callController();
        $this->callMigration();
        $this->callSeed();
        $this->callMigrate();

        $this->info('All Done!');
    }

    /**
     * Call the generate:model command
     */
    private function callModel()
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

        if ($this->confirm("Create a $name model?", 1)) {
            $this->callCommand('model', $name, [
                '--schema' => $this->option('schema'),
            ]);
        }
    }

    /**
     * Call the generate:resource command
     */
    private function callRequest()
    {
        $stubs = config('generators.requests');

        if ($this->confirm('Create a form validator requests?', 1)) {
            foreach ($stubs as $stub => $type) {
                $this->callCommand('request', $type . $this->getModelName(), [
                    '--resource' => $this->getResourceName(),
                    '--schema' => $this->option('schema'),
                    '--stub' => $stub,
                ]);
            }
        }
    }

    /**
     * Call the generate:resource command
     */
    private function callResource()
    {
        $name = $this->getModelName();

        if ($this->confirm("Create a $name API resource class?", 1)) {
            $this->callCommand('resource', $name, [
                '--schema' => $this->option('schema'),
            ]);
        }
    }

    /**
     * Generate the resource views
     */
    private function callAssets()
    {
        if ($this->confirm("Create crud assets for the $this->resource resource?", 1)) {
            $views = config('generators.resource_assets');
            $resource = $this->argument('resource');
            $resource = str_replace('.', '/', $resource);

            foreach ($views as $key => $name) {
                $this->callCommand('asset', $this->getViewPath($resource), [
                    '--stub' => $key,
                    '--name' => $name,
                    '--schema' => $this->option('schema'),
                ]);
            }
        }
    }

    /**
     * Generate the resource controller
     */
    private function callController()
    {
        $name = $this->getResourceControllerName();

        if ($this->confirm("Create a controller ($name) for the $this->resource resource?", 1)) {
            $name = $this->getCollectionName();

			// if admin - update stub
			if (!str_contains($name, 'admin.')) {
				$this->callCommand('controller', $name, [
                    '--schema' => $this->option('schema'),
                ]);
			} else {
				$this->callCommand('controller', $name, [
					'--stub' => 'controller_admin',
				]);
			}
		}
    }

    /**
     * Call the generate:migration command
     */
    private function callMigration()
    {
        $name = $this->getMigrationName($this->option('migration'));

        if ($this->confirm("Create a migration ($name) for the $this->resource resource?", 1)) {
            $this->callCommand('migration', $name, [
                '--name' => date('Y_m_d_His') . '_' . $name,
                '--schema' => $this->option('schema'),
            ]);
        }
    }

    /**
     * Call the generate:seed command
     */
    private function callSeed()
    {
        $name = $this->getSeedName() . 'TableSeeder';

        if ($this->confirm("Create a seed ($name) for the $this->resource resource?", 1)) {
            $this->callCommand('seed', $name);
        }
    }

    /**
     * Call the migrate command
     */
    protected function callMigrate()
    {
        if ($this->confirm('Migrate the database?', 1)) {
            $this->call('migrate');
        }
    }

    /**
     * @param       $command
     * @param       $name
     * @param array $options
     */
    private function callCommand($command, $name, $options = [])
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
    private function getArgumentResource()
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
    private function getResourceOnly()
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
    private function getResourceControllerName()
    {
        return $this->getControllerName(str_plural($this->resource)) . config('generators.settings.controller.postfix');
    }

    /**
     * Get the name for the migration
     *
     * @param null $name
     *
     * @return string
     */
    private function getMigrationName($name = null)
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
        ]);
    }
}
