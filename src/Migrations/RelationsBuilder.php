<?php

namespace Bpocallaghan\Generators\Migrations;


use Bpocallaghan\Generators\Traits\NameBuilders;
use Illuminate\Console\DetectsApplicationNamespace;


class RelationsBuilder
{
	use DetectsApplicationNamespace, NameBuilders;

	/**
	 * Create the PHP syntax for the given schema.
	 *
	 * @param  array $schema
	 * @param  array $meta
	 *
	 * @return array
	 */
	public function create($schema, $meta)
	{
		return collect($schema)
			->filter(function($field) {
				return in_array(strtolower($field['type']), ['hasone', 'hasmany', 'belongsto', 'belongstomany']);
			})
			->map(function ($field) {
				return $this->buildRelation($field);
			})
			->all();
	}


	protected function buildRelation($field)
	{
		$field['type'] = strtr(strtolower($field['type']), [
			'hasone' => 'hasOne',
			'hasmany' => 'hasMany',
			'belongsto' => 'belongsTo',
			'belongstomany' => 'belongsToMany',
		]);

		$arguments = $field['arguments'];
		$class     = array_shift($arguments);
		$arguments = array_map(function ($arg) {
			return '\'' . $arg . '\'';
		}, $arguments);

		$field['name'] = $relation = $class ?: str_replace('_id', '', $field['name']);
		array_unshift($arguments, $this->resolveClass($relation));
		$arguments = join(', ', $arguments);

		$field['code'] = "return \$this->{$field['type']}({$arguments});";

		return $field;
	}


	protected function resolveClass($class)
	{
		return '\\' . $this->getAppNamespace() . $this->getModelName($class) . '::class';
	}
}