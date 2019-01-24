<?php

namespace Stylemix\Generators\Traits;

trait Settings
{
    /**
     * Settings of the file type to be generated
     *
     * @var array
     */
    protected $settings = [];

    /**
     * Find the type's settings and set local var
     */
    public function setSettings()
    {
        $type = $this->getType();
        $options = config('generators', []);

        if (!array_key_exists($type, $options)) {
            $this->error('Oops!, no settings key by the type name provided');
            exit;
        }

        $settings = $options[$type];

        // set the default keys and values if they do not exist
		$settings += config('generators.defaults');

        $this->settings = $settings;
    }

    /**
     * Return false or the value for given key from the settings
     *
     * @param $key
     *
     * @return bool
     */
    public function settingsKey($key)
    {
        if (is_array($this->settings) == false || isset($this->settings[$key]) == false) {
            return false;
        }

        return $this->settings[$key];
    }

    /**
     * Get the directory format setting's value
     */
    protected function settingsDirectoryFormat()
    {
        return $this->settingsKey('directory_format') ? $this->settings['directory_format'] : false;
    }

    /**
     * Get the directory format setting's value
     */
    protected function settingsDirectoryNamespace()
    {
        return $this->settingsKey('directory_namespace') ? $this->settings['directory_namespace'] : false;
    }
}
