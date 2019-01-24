<?php

namespace Stylemix\Generators\Traits;

use Illuminate\Console\DetectsApplicationNamespace;

trait NameBuilders
{
    use DetectsApplicationNamespace;

	/**
	 * The resource argument
	 *
	 * @var string
	 */
	protected $resource = "";


	/**
	 * Set base resource name
	 *
	 * @param $name
	 */
	protected function setResourceName($name)
	{
		$this->resource = $name;
	}


	/**
	 * Get the resource name
	 *
	 * @param string $name
	 * @return string
	 */
	protected function getResourceName($name = null)
	{
		$name = isset($name) ? $name : $this->resource;

		return lcfirst(str_singular(class_basename($name)));
	}

	/**
	 * Get the name for the model
	 *
	 * @param string $name
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
		return ucwords(camel_case($name ?: $this->getArgumentNameOnly()));
	}

	/**
	 * Get the name for the seed
	 *
	 * @param null $name
	 * @return string
	 */
	protected function getSeedName($name = null)
	{
		return ucwords(camel_case($this->getResourceName($name)));
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

		return $name . config('generators.contract.postfix');
	}

	/**
	 * Get the namespace of where contract was created
	 * @param bool $withApp
	 * @return string
	 */
	protected function getContractNamespace($withApp = true)
	{
		// get path from settings
		$path = config('generators.contract.namespace') . '\\';

		// dont add the default namespace if specified not to in config
		$path .= str_replace('/', '\\', $this->getArgumentPath());

		$pieces = array_map('ucfirst', explode('/', $path));

		$namespace = ($withApp === true ? $this->getAppNamespace() : '') . implode('\\', $pieces);

		$namespace = rtrim(ltrim(str_replace('\\\\', '\\', $namespace), '\\'), '\\');

		return $namespace;
	}

    /**
     * Get full namespace for resource class
     * @return string
     */
    protected function getResourceClassNamespace()
    {
        // get path from settings
        $namespace = $this->getAppNamespace() . config('generators.resource.namespace') . '\\';
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
	protected function getTableName($name = null)
	{
		return str_replace("-", "_", str_plural(snake_case($this->getResourceName($name))));
	}

}
