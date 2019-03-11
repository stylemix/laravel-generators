<?php

namespace Stylemix\Generators\Commands;

use Symfony\Component\Console\Input\InputArgument;

class MigrationPivotCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate:migration:pivot';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Migration Pivot class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Pivot';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $path = $this->getPath();

        if ($this->files->exists($path) && $this->optionForce() === false) {
            return $this->error($this->type . ' already exists!');
        }

        $this->makeDirectory($path);
        $this->files->put($path, $this->buildClass());

        $this->info($this->type . ' created successfully.');
        $this->info('- ' . $path);
    }


	protected function getData()
	{
		$tables = $this->getSortedTableNames();

		return array_merge(
			[
				'pivotTableName' => $this->getPivotTableName(),
				'class' => $this->getClassName(),
			],
			array_combine(
				['columnOne', 'columnTwo'],
				array_map('str_singular', $tables)
			),
			array_combine(
				['tableOne', 'tableTwo'],
				array_map('str_plural', $tables)
			)
		);
	}

	/**
	 * Parse the name and format.
	 *
	 * @return string
	 */
	protected function getClassName()
	{
		$tables = array_map('str_singular', $this->getSortedTableNames());
		$name = implode('', array_map('ucwords', $tables));
		$pieces = explode('_', $name);
		$name = implode('', array_map('ucwords', $pieces));

		return "Create{$name}PivotTable";
	}

	/**
	 * Get the destination class path.
	 *
	 * @param  string $name
	 * @return string
	 */
	protected function getPath($name = null)
	{
		return './database/migrations/' . date('Y_m_d_His', time() + 1) . '_create_' . $this->getPivotTableName() . '_pivot_table.php';
	}

	/**
	 * Build the class with the given name.
	 *
	 * @param  string $name
	 * @return string
	 */
	/*protected function buildClass($name = null)
	{
		$stub = $this->files->get($this->getStub());

		return $this->replacePivotTableName($stub)
			->replaceSchema($stub)
			->replaceClass($stub, $this->getArgumentNameOnly());
	}*/

	/**
     * Apply the name of the pivot table to the stub.
     *
     * @param  string $stub
     * @return $this
     */
//    protected function replacePivotTableName(&$stub)
//    {
//        $stub = str_replace('{{pivotTableName}}', $this->getPivotTableName(), $stub);
//
//        return $this;
//    }

    /**
     * Apply the correct schema to the stub.
     *
     * @param  string $stub
     * @return $this
     */
//    protected function replaceSchema(&$stub)
//    {
//        $tables = $this->getSortedTableNames();
//
//        $stub = str_replace(['{{columnOne}}', '{{columnTwo}}'],
//            array_merge(array_map('str_singular', $tables), $tables), $stub);
//
//        $stub = str_replace(['{{tableOne}}', '{{tableTwo}}'],
//            array_merge(array_map('str_plural', $tables), $tables), $stub);
//
//        return $this;
//    }

    /**
     * Replace the class name for the given stub.
     *
     * @param  string $stub
     * @param  string $name
     * @return string
     */
//    protected function replaceClass($stub, $name)
//    {
//        $class = str_replace($this->getNamespace($name) . '\\', '', $name);
//
//        return str_replace('{{class}}', $class, $stub);
//    }

    /**
     * Get the name of the pivot table.
     *
     * @return string
     */
    protected function getPivotTableName()
    {
        return implode('_', array_map('str_singular', $this->getSortedTableNames()));
    }

    /**
     * Sort the two tables in alphabetical order.
     *
     * @return array
     */
    protected function getSortedTableNames()
    {
        $tables = [
            strtolower($this->argument('tableOne')),
            strtolower($this->argument('tableTwo'))
        ];

        sort($tables);

        return $tables;
    }


    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return config('generator_stubs.' . strtolower($this->type) . '_stub');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['tableOne', InputArgument::REQUIRED, 'The name of the first table.'],
            ['tableTwo', InputArgument::REQUIRED, 'The name of the second table.']
        ];
    }
}
