<?php

namespace Stylemix\Generators\Commands;

use Stylemix\Generators\Components\ExtraParser;
use Stylemix\Generators\Components\SchemaParser;
use Stylemix\Generators\Generator;
use Stylemix\Generators\Traits\ArgumentsOptions;
use Stylemix\Generators\Traits\NameBuilders;
use Stylemix\Generators\Traits\Settings;
use Illuminate\Console\GeneratorCommand as LaravelGeneratorCommand;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Composer;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

abstract class GeneratorCommand extends LaravelGeneratorCommand
{
    use ArgumentsOptions, Settings, NameBuilders;

    /**
     * @var Composer
     */
    protected $composer;

	/**
	 * @var \Illuminate\View\Factory
	 */
	protected $view;


    function __construct(Filesystem $files, Composer $composer, \Illuminate\Contracts\View\Factory $view)
    {
        parent::__construct($files);

        $this->composer = $composer;
	    $this->view = $view;
    }

	/**
	 * Execute the console command.
	 *
	 * @return void
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 */
    public function handle()
    {
        // setup
	    $this->setSettings();
	    $this->setResourceName(str_replace($this->settings['postfix'], '', $this->getArgumentNameOnly()));

        // check the path where to create and save file
	    $path = $this->getPath('');
	    if ($this->files->exists($path) && $this->optionForce() === false) {
		    $this->warn($path . ' already exists! Use --force to overwrite the file.');

		    return;
	    }

	    // make all the directories
	    $this->makeDirectory($path);

	    // build file and save it at location
	    $this->files->put($path, $this->buildClass());

	    // output to console
	    $this->comment(ucfirst($this->getType()) . ' created successfully.');
	    $this->line('- ' . $path);

	    // if we need to run "composer dump-autoload"
	    if ($this->settings['dump_autoload'] === true) {
			$this->comment('Running composer dump autoload...');
			$this->composer->dumpAutoloads();
	    }
    }

    protected function setResourceName($name)
    {
        $this->resource = $name;

        // Register current resource to make it accessible for other components
        app(Generator::class)->setCurrentResource($this->getResourceName());
    }


    /**
	 * Get current generator type.
	 * Override this method to define custom type
	 *
	 * @return string
	 */
	protected function getType() {
		return strtolower($this->type ?: str_replace('Command', '', class_basename(static::class)));
	}

	/**
	 * @return array|mixed|string
	 */
	protected function getFileBaseName()
	{
		$name = $this->getArgumentNameOnly();

		return $name;
	}

	/**
	 * Get the filename of the file to generate
	 *
	 * @return string
	 */
	protected function getFileName()
	{
		$name = $this->getFileBaseName();

		// override the name
		if ($this->option('name')) {
			return $this->option('name') . $this->settings['file_type'];
		}

		return $this->settings['prefix'] . $name . $this->settings['postfix'] . $this->settings['file_type'];
	}

	/**
	 * Get the destination class path.
	 *
	 * @param  string $name
	 * @return string
	 */
	protected function getPath($name)
	{
		$name = $this->getFileName();

		$withName = boolval($this->option('name'));

		$path = $this->settings['path'];
		if ($this->settingsDirectoryNamespace() === true) {
			$path .= $this->getArgumentPath($withName);
		}

		$path .= $name;

		return $path;
	}

    /**
     * Only return the name of the file
     * Ignore the path / namespace of the file
     *
     * @return array|mixed|string
     */
    protected function getArgumentNameOnly()
    {
        $name = $this->argumentName();

        if (str_contains($name, '/')) {
            $name = str_replace('/', '.', $name);
        }

        if (str_contains($name, '\\')) {
            $name = str_replace('\\', '.', $name);
        }

        if (str_contains($name, '.')) {
            return substr($name, strrpos($name, '.') + 1);
        }

        return $name;
    }

    /**
     * Return the path of the file
     *
     * @param bool $withName
     * @return array|mixed|string
     */
    protected function getArgumentPath($withName = false)
    {
        $name = $this->argumentName();

        if (str_contains($name, '.')) {
            $name = str_replace('.', '/', $name);
        }

        if (str_contains($name, '\\')) {
            $name = str_replace('\\', '/', $name);
        }

        // ucfirst char, for correct namespace
        $name = implode('/', array_map('ucfirst', explode('/', $name)));

        // if we need to keep lowercase
        if ($this->settingsDirectoryFormat() === 'strtolower') {
            $name = implode('/', array_map('strtolower', explode('/', $name)));
        }

        // if we want the path with name
        if ($withName) {
            return $name . '/';
        }

        if (str_contains($name, '/')) {
            return substr($name, 0, strripos($name, '/') + 1);
        }

        return '';
    }

	/**
	 * Build the class with the given name.
	 *
	 * @param $name
	 *
	 * @return string
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 */
	protected function buildClass($name = null)
	{
		$template = $this->getStub();

		if (str_is('*.blade.php', $template)) {
			$engine = $this->view->getEngineFromPath($template);
			view()->addNamespace('stubs', dirname($template));

			return $engine->get($template, ['__env' => view() ] + $this->getData());
		}

		$stub = $this->files->get($this->getStub());
		foreach ($this->getData() as $key => $val) {
			$stub = str_replace('{{' . $key . '}}', $val, $stub);
		}

		return $stub;
	}

	/**
	 * Data used in stubs
	 *
	 * @return array
	 */
	protected function getData()
	{
		$name = $this->argumentName();
		$url = $this->getUrl(); // /foo/bar

		return [
			// <?php
			'phpOpenTag' => '<?php',

			// Date field boolean
			'hasDateField' => false,

			// DateTime field boolean
			'hasDateTimeField' => false,

			// App\Foo
			'namespace' => $this->getNamespace($name),

			// App\
			'rootNamespace' => $this->getAppNamespace(),

			// Bar
			'class' => $this->getClassName(),

			// /foo/bar
			'url' => $this->getUrl(),

			// bars
			'collection' => $this->getCollectionName(),

			// Bars
			'collectionUpper' => $this->getCollectionUpperName(),

			// Bar
			'model' => $this->getModelName(),

			// Bar
			'resource' => $this->getResourceName(),

			// bar
			'resourceLowercase' => strtolower($this->getResourceName()),

			// ./resources/views/foo/bar.blade.php
			'path' => $this->getPath(''),

			// foos.bars
			'view' => $this->getViewPath($this->getUrl(false)),

			// foos.bars (remove admin or website if first word)
			'viewPath' => $this->getViewPathFormatted($this->getUrl(false)),

			// bars
			'table' => $this->getTableName($url),

			// contract file name
			'contract' => $this->getContractName(),

			// contract namespace
			'contractNamespace' => $this->getContractNamespace(),

            // fields schema
            'schema' => $this->getSchema(),

			// extra information
			'extra' => $this->getExtra(),
		];
	}

    /**
     * Get name of file/class with the pre and post fix
     *
     * @param $name
     * @return string
     */
    protected function getFileNameComplete($name)
    {
        return $this->settings['prefix'] . $name . $this->settings['postfix'];
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . config('generators.' . $this->getType() . '_namespace');
    }

	/**
	 * Get the full namespace name for a given class.
	 *
	 * @param  string $name
	 * @param bool    $withApp
	 * @return string
	 */
	protected function getNamespace($name, $withApp = true)
	{
		$path = (strlen($this->settings['namespace']) >= 2 ? $this->settings['namespace'] . '\\' : '');

		// dont add the default namespace if specified not to in config
		if ($this->settingsDirectoryNamespace() === true) {
			$path .= str_replace('/', '\\', $this->getArgumentPath());
		}

		$pieces = array_map('ucfirst', explode('/', $path));

		$namespace = ($withApp === true ? $this->getAppNamespace() : '') . implode('\\', $pieces);

		$namespace = rtrim(ltrim(str_replace('\\\\', '\\', $namespace), '\\'), '\\');

		return $namespace;
	}

	/**
	 * Get the url for the given name
	 *
	 * @param bool $lowercase
	 * @return string
	 */
	protected function getUrl($lowercase = true)
	{
		if ($lowercase) {
			$url = '/' . rtrim(implode('/',
					array_map('snake_case', explode('/', $this->getArgumentPath(true)))), '/');
			$url = (implode('/', array_map('str_slug', explode('/', $url))));

			return $url;
		}

		return '/' . rtrim(implode('/', explode('/', $this->getArgumentPath(true))), '/');
	}

	/**
	 * Get the class name
	 * @return mixed
	 */
	protected function getClassName()
	{
		return ucwords(camel_case(str_replace([$this->settings['file_type']], [''], $this->getFileName())));
	}

	/**
	 * Get fields schema
	 *
	 * @return array|Collection
	 */
	protected function getSchema()
	{
        if ($this->hasOption('schema') && $schema = $this->optionSchema()) {
            $schema = app(SchemaParser::class)
                ->parse($schema, ['name' => $this->getArgumentNameOnly()]);
        } else {
            $schema = collect();
        }

        return $schema;
	}

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        $key = $this->getOptionStubKey();

        // get the stub path
        $stub = config('generator_stubs.' . $key);

        if (is_null($stub)) {
            $this->error('The stub does not exist in the config file - "' . $key . '"');
            exit;
        }

        return $stub;
    }

    /**
     * Get the key where the stub is located
     *
     * @return string
     */
    protected function getOptionStubKey()
    {
        $plain = $this->option('plain');
        $stub = $this->option('stub') . ($plain ? '_plain' : '') . '_stub';

        // if no stub, we assume its the same as the type
        if (is_null($this->option('stub'))) {
            $stub = $this->getType() . ($plain ? '_plain' : '') . '_stub';
        }

        return $stub;
    }

	/**
	 * Confirm a question with the user only in interactive mode.

	 * @param string $question
	 * @param bool $default
	 *
	 * @return bool
	 */
	protected function confirmOptional($question, $default = false)
	{
		return !$this->option('interactive') || $this->confirm($question, $default);
	}

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of class being generated.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['plain', null, InputOption::VALUE_NONE, 'Generate an empty class.'],
            ['force', null, InputOption::VALUE_NONE, 'Warning: Override file if it already exist'],
            ['stub', null, InputOption::VALUE_OPTIONAL, 'The name of the view stub you would like to generate.'],
            ['name', null, InputOption::VALUE_OPTIONAL, 'If you want to override the name of the file that will be generated'],
            ['extra', null, InputOption::VALUE_OPTIONAL, 'Extra information to use in stubs'],
			['interactive', 'i', InputOption::VALUE_NONE, 'Ask questions instead of using defaults.'],
		];
    }

	protected function getExtra() {
		if ($schema = $this->option('extra')) {
			return (new ExtraParser())->parse($schema);
		}

		return collect();
	}
}
