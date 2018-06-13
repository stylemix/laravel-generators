<?php

namespace Bpocallaghan\Generators\Commands;

use Symfony\Component\Console\Input\InputOption;

class RequestCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate:request';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new form validator request for resource creation';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Request';

	public function buildClass($name = null)
	{
		if ($resource = $this->option('resource')) {
			$this->setResourceName($resource);
		}

		return parent::buildClass();
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
            ['resource', null, InputOption::VALUE_OPTIONAL, 'Optional resource name', null],
        ]);
    }

	protected function getData()
	{
		$schema = $this->getSchema();

		return array_merge(parent::getData(), [
			'rules' => $this->getRules($schema),
		]);
	}

	/**
	 * Set rules to schema fields.
	 *
	 * @return array
	 */
	protected function getRules($schema)
	{
		$result = [];

		foreach ($schema as $field) {
			$type = strtolower($field['type']);
			if (in_array(strtolower($field['type']), ['hasone', 'hasmany', 'belongstomany'])) {
				continue;
			}

			$rules = [];
			if (!array_key_exists('nullable', $field['options'])) {
				if (!array_key_exists('default', $field['options'])) {
					$rules[] = 'required';
				}
			}
			else {
				$rules[] = 'nullable';
			}

			if (in_array($type, ['char', 'string', 'text', 'enum'])) {
				$rules[] = 'string';
				if (in_array($type, ['char', 'string']) && isset($field['arguments'][0])) {
					$rules[] = 'max:' . $field['arguments'][0];
				}
			} elseif (in_array($type, ['timestamp', 'date', 'dateTime', 'dateTimeTz'])) {
				$rules[] = 'date';
			} elseif (in_array($type, ['integer', 'bigInteger', 'smallInteger', 'unsignedInteger', 'unsignedSmallInteger', 'unsignedBigInteger'])) {
				$rules[] = 'integer';
			} elseif (in_array($type, ['decimal'])) {
				$rules[] = 'numeric';
			} elseif (in_array($type, ['boolean'])) {
				$rules[] = 'boolean';
			} elseif ($type == 'enum' && count($field['arguments'])) {
				$rules[] = 'in:' . join(',', $field['arguments']);
			} elseif ($type == 'belongsto') {
			    $field['name'] = $field['foreignKey'];
			}

			if (array_key_exists('unique', $field['options'])) {
				$table = $this->getTableName();
				$rules[] = "unique:{$table}";
			}

			if ($extra = array_get($field, 'options.rules')) {
				$rules[] = $rules;
			}

			if (empty($rules)) {
				continue;
			}

			$result[$field['name']] = join('|', $rules);
		}

		return $result;
	}

}
