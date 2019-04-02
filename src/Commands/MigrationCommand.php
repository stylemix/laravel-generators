<?php

namespace Stylemix\Generators\Commands;

use Stylemix\Generators\Components\Migration\NameParser;
use Stylemix\Generators\Components\Migration\SyntaxBuilder;
use Stylemix\Generators\Traits\HasRelations;
use Symfony\Component\Console\Input\InputOption;

class MigrationCommand extends GeneratorCommand
{
	use HasRelations;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate:migration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Migration class, and apply schema at the same time';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Migration';

    /**
     * Meta information for the requested migration.
     *
     * @var array
     */
    protected $meta;


	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 */
    public function handle()
    {
        $this->meta = (new NameParser)->parse($this->argumentName());

        parent::handle();

        // Generate pivot tables
        collect($this->getSchema())
			->filter(function ($field) {
				return strtolower($field['type']) == 'belongstomany';
			})
			->each(function ($field) {
				return $this->generatePivot($field);
			});

        // if model is required
        if ($this->optionModel() === true || $this->optionModel() === 'true') {
            $this->call('generate:model', [
                'name'     => $this->getModelName(),
                '--plain'  => $this->optionPlain(),
                '--force'  => $this->optionForce(),
                '--schema' => $this->optionSchema()
            ]);
        }
    }

    /**
     * Replace the class name in the stub.
     *
     * @return $this
     */
    protected function getClassName()
    {
        return ucwords(camel_case($this->argumentName()));
    }

    /**
     * Replace the table name in the stub.
     *
     * @param  string $url
     * @return $this
     */
    protected function getTableName($url = '')
    {
        return $this->meta['table'];
    }

    /**
     * Get the class name for the Eloquent model generator.
     *
     * @param null $name
     * @return string
     */
    protected function getModelName($name = null)
    {
        $name = str_singular($this->meta['table']);

        $model = '';
        $pieces = explode('_', $name);
        foreach ($pieces as $k => $str) {
            $model = $model . ucwords($str);
        }

        return $model;
    }

    /**
     * Get the path to where we should store the migration.
     *
     * @param  string $name
     * @return string
     */
    protected function getPath($name)
    {
        return './database/migrations/' . date('Y_m_d_His') . '_' . $this->argumentName() . '.php';
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array_merge([
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'Want a model for this table?', false],
            ['schema', 's', InputOption::VALUE_OPTIONAL, 'Optional schema to be attached to the migration', null],
			['soft-deletes', null, InputOption::VALUE_NONE, 'Include soft deletion trait'],
        ], parent::getOptions());
    }

	protected function getData()
	{
		$replace = [];

		if (!$this->optionPlain()) {
			$schema = (new SyntaxBuilder)->create($this->getSchema(), $this->meta);
			$replace = array_combine(['schema_up', 'schema_down'], $schema);
		}

		return array_merge(
			parent::getData(),
			$replace,
			$this->getRelationsData(),
			[
				'schema' => $this->getSchema(),
				'softDeletes' => $this->option('soft-deletes'),
			]
		);
	}


	protected function generatePivot($field)
	{
		$this->call('generate:migration:pivot', [
			'tableOne' => $this->getTableName(),
			'tableTwo' => $field['name'],
		]);
	}
}
