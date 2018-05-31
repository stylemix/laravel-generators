<?php

namespace Bpocallaghan\Generators\Commands;

use Illuminate\Console\DetectsApplicationNamespace;
use Illuminate\Support\Composer;
use Illuminate\Filesystem\Filesystem;
use Bpocallaghan\Generators\Traits\Settings;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Bpocallaghan\Generators\Traits\ArgumentsOptions;
use Illuminate\Console\GeneratorCommand as LaravelGeneratorCommand;

abstract class GeneratorCommand extends LaravelGeneratorCommand
{
    use ArgumentsOptions, Settings, DetectsApplicationNamespace;

    /**
     * @var Composer
     */
    protected $composer;

	/**
	 * @var \Illuminate\View\Factory
	 */
	protected $view;

    /**
     * The resource argument
     *
     * @var string
     */
    protected $resource = "";

    /**
     * The lowercase resource argument
     *
     * @var string
     */
    protected $resourceLowerCase = "";

    /**
     * @var string
     */
    protected $extraOption = '';

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
	    $this->getResourceName($this->getUrl(false));

	    // check the path where to create and save file
	    $path = $this->getPath('');
	    if ($this->files->exists($path) && $this->optionForce() === false) {
		    $this->error($this->type . ' already exists!');

		    return;
	    }

	    // make all the directories
	    $this->makeDirectory($path);

	    // build file and save it at location
	    $this->files->put($path, $this->buildClass($this->getArgumentNameOnly()));

	    // output to console
	    $this->info(ucfirst($this->option('type')) . ' created successfully.');
	    $this->info('- ' . $path);

	    // if we need to run "composer dump-autoload"
	    if ($this->settings['dump_autoload'] === true) {
		    $this->composer->dumpAutoloads();
	    }
    }

	/**
	 * Get the filename of the file to generate
	 *
	 * @return string
	 */
	protected function getFileName()
	{
		$name = $this->getArgumentNameOnly();

		switch ($this->option('type')) {
			case 'view':

				break;
			case 'model':
			case 'resource':
				$name = $this->getModelName();
				break;
			case 'controller':
				$name = $this->getControllerName($name);
				break;
			case 'seed':
				$name = $this->getSeedName($name);
				break;
		}

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
	protected function buildClass($name)
	{
		$template = $this->getStub();

		if (str_is('*.blade.php', $template)) {
			$engine = $this->view->getEngineFromPath($template);
			view()->addNamespace('stubs', dirname($template));

			return $engine->get($template, ['__env' => view() ] + $this->getData() + ['schema' => $this->getSchema()]);
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
		$relation = $this->option('relation') ? explode(':', $this->option('relation')) : false;

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
			'resource' => $this->resource,

			// bar
			'resourceLowercase' => $this->resourceLowerCase,

			// ./resources/views/foo/bar.blade.php
			'path' => $this->getPath(''),

			// foos.bars
			'view' => $this->getViewPath($this->getUrl(false)),

			// foos.bars (remove admin or website if first word)
			'viewPath' => $this->getViewPathFormatted($this->getUrl(false)),

			// bars
			'table' => $this->getTableName($url),

			// console command name
			'command' => $this->option('command'),

			// contract file name
			'contract' => $this->getContractName(),

			// contract namespace
			'contractNamespace' => $this->getContractNamespace(),

			// resource namespace
			'resourceClassNamespace' => $this->getResourceClassNamespace(),

			// check if there is a scout option
			'scoutIncluded' => $this->option('scout') ? true : false,

			// check if there is a relation option
			'hasRelation' => $this->option('relation') ? true : false,

			// hasMany, belongsTo, hasOne ...
			'relationType' => $relation ? $relation[0] : '',

			// books, author, posts etc
			'relatedTo' => $relation ? $relation[1] : '',

			// authour_id, post_id etc
			'foreignKey' => $relation ? $relation[2] : '',

			// current crud's local key
			'localKey' => count($relation) > 3 ? $relation[3] : '',

			// "Many to many" relationship's joing table name
			'pivotTable' => count($relation) > 4 ? $relation[4] : '',

			// final model's name for "hasManyThrough" relation
			'finalModel' => $relation ? $relation[1] : '',

			// intermediate model's name for "hasManyThrough" relation
			'intermediateModel' => $relation ? $relation[2] : '',

			// the name of the foreign key on the intermediate model for "hasManyThrough" relation
			'intermediateKey' => count($relation) > 3 ? $relation[3] : '',

			// the name of the foreign key on the final model for "hasManyThrough" relation
			'finalKey' => count($relation) > 4 ? $relation[4] : '',
		];
	}

    /**
     * Get the resource name
     *
     * @param      $name
     * @param bool $format
     * @return string
     */
    protected function getResourceName($name, $format = true)
    {
        // we assume its already formatted to resource name
        if ($name && $format === false) {
            return $name;
        }

        $name = isset($name) ? $name : $this->resource;

        $this->resource = lcfirst(str_singular(class_basename($name)));
        $this->resourceLowerCase = strtolower($name);

        return $this->resource;
    }

    /**
     * Get the name for the model
     *
     * @param null $name
     * @return string
     */
    protected function getModelName($name = null)
    {
        $name = isset($name) ? $name : $this->resource;

        //return ucwords(camel_case($this->getResourceName($name)));

        return str_singular(ucwords(camel_case(class_basename($name))));
    }

    /**
     * Get the name for the controller
     *
     * @param null $name
     * @return string
     */
    protected function getControllerName($name = null)
    {
        return ucwords(camel_case(str_replace($this->settings['postfix'], '', ($name))));
    }

    /**
     * Get the name for the seed
     *
     * @param null $name
     * @return string
     */
    protected function getSeedName($name = null)
    {
        return ucwords(camel_case(str_replace($this->settings['postfix'], '',
            $this->getResourceName($name))));
    }

    /**
     * Get the name of the collection
     *
     * @param null $name
     * @return string
     */
    protected function getCollectionName($name = null)
    {
        return str_plural($this->getResourceName($name));
    }

    /**
     * Get the plural uppercase name of the resouce
     * @param null $name
     * @return null|string
     */
    protected function getCollectionUpperName($name = null)
    {
        $name = str_plural($this->getResourceName($name));

        $pieces = explode('_', $name);
        $name = "";
        foreach ($pieces as $k => $str) {
            $name .= ucfirst($str);
        }

        return $name;
    }

    /**
     * Get the name of the contract
     * @param null $name
     * @return string
     */
    protected function getContractName($name = null)
    {
        $name = isset($name) ? $name : $this->resource;

        $name = str_singular(ucwords(camel_case(class_basename($name))));

        return $name . config('generators.settings.contract.postfix');
    }

    /**
     * Get the namespace of where contract was created
     * @param bool $withApp
     * @return string
     */
    protected function getContractNamespace($withApp = true)
    {
        // get path from settings
        $path = config('generators.settings.contract.namespace') . '\\';

        // dont add the default namespace if specified not to in config
        $path .= str_replace('/', '\\', $this->getArgumentPath());

        $pieces = array_map('ucfirst', explode('/', $path));

        $namespace = ($withApp === true ? $this->getAppNamespace() : '') . implode('\\', $pieces);

        $namespace = rtrim(ltrim(str_replace('\\\\', '\\', $namespace), '\\'), '\\');

        return $namespace;
    }

    /**
     * Get the path to the view file
     *
     * @param $name
     * @return string
     */
    protected function getViewPath($name)
    {
        $pieces = explode('/', $name);

        // dont plural if reserve word
        foreach ($pieces as $k => $value) {
            if (!in_array($value, config('generators.reserve_words'))) {
                $pieces[$k] = str_plural(snake_case($pieces[$k]));
            }
        }

        $name = implode('.', $pieces);

        //$name = implode('.', array_map('str_plural', explode('/', $name)));

        return strtolower(rtrim(ltrim($name, '.'), '.'));
    }

    /**
     * Remove 'admin' and 'webiste' if first in path
     * The Base Controller has it as a 'prefix path'
     *
     * @param $name
     * @return string
     */
    protected function getViewPathFormatted($name)
    {
        $path = $this->getViewPath($name);

        if (strpos($path, 'admin.') === 0) {
            $path = substr($path, 6);
        }

        if (strpos($path, 'admins.') === 0) {
            $path = substr($path, 7);
        }

        if (strpos($path, 'website.') === 0) {
            $path = substr($path, 8);
        }

        if (strpos($path, 'websites.') === 0) {
            $path = substr($path, 9);
        }

        return $path;
    }

    /**
     * Get the table name
     *
     * @param $name
     * @return string
     */
    protected function getTableName($name)
    {
        return str_replace("-", "_", str_plural(snake_case(class_basename($name))));
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
        return $rootNamespace . config('generators.' . strtolower($this->type) . '_namespace');
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
	 * Get full namespace for resource class
	 * @return string
	 */
	protected function getResourceClassNamespace()
	{
		// get path from settings
		$namespace = $this->getAppNamespace() . config('generators.settings.resource.namespace') . '\\';
		$namespace = rtrim(ltrim(str_replace('\\\\', '\\', $namespace), '\\'), '\\');

		return $namespace;
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
        $stub = config('generators.' . $key);

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
            $stub = $this->option('type') . ($plain ? '_plain' : '') . '_stub';
        }

        return $stub;
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
            [
                'stub',
                null,
                InputOption::VALUE_OPTIONAL,
                'The name of the view stub you would like to generate.'
            ],
            [
                'name',
                null,
                InputOption::VALUE_OPTIONAL,
                'If you want to override the name of the file that will be generated'
            ],
        ];
    }
}
