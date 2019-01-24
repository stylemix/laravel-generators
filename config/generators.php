<?php

return [
    /*
    |--------------------------------------------------------------------------
    | The singular resource words that will not be pluralized
    | For Example: $ php artisan generate:resource admin.bar
    | The url will be /admin/bars and not /admins/bars
    |--------------------------------------------------------------------------
    */

    'reserve_words' => ['app', 'website', 'admin'],

    /*
    |--------------------------------------------------------------------------
    | The default keys and values for the settings of each type to be generated
    |--------------------------------------------------------------------------
    */

    'defaults' => [
        'namespace' => '',
        'path' => './app/',
        'prefix' => '',
        'postfix' => '',
        'file_type' => '.php',
        'dump_autoload' => false,
        'directory_format' => '',
        'directory_namespace' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Types of files that can be generated
    |--------------------------------------------------------------------------
    */
	'view' => [
		'path' => './resources/views/',
		'file_type' => '.blade.php',
		'directory_format' => 'strtolower',
		'directory_namespace' => true
	],
	'asset' => [
		'path' => './resources/assets/js/',
		'file_type' => '',
		'directory_format' => 'strtolower',
		'directory_namespace' => true
	],
	'model' => [
		'namespace' => '',
		'path' => './app/'
	],
	'factory' => [
		'namespace' => '',
		'path' => './database/factories/'
	],
	'resource' => [
		'namespace' => '\Http\Resources',
		'path' => './app/Http/Resources/'
	],
	'request' => [
		'namespace' => '\Http\Requests',
		'path' => './app/Http/Requests/',
		'postfix' => 'Request',
	],
	'policy' => [
		'namespace' => '\Policies',
		'path' => './app/Policies/',
		'postfix' => 'Policy',
	],
	'controller' => [
		'namespace' => '\Http\Controllers',
		'path' => './app/Http/Controllers/',
		'postfix' => 'Controller',
		'directory_namespace' => true,
	],
	'seed' => [
		'path' => './database/seeds/',
		'dump_autoload' => true,
	],
	'migration' => [
		'path' => './database/migrations/'
	],
	'notification' => [
		'directory_namespace' => true,
		'namespace' => '\Notifications',
		'path' => './app/Notifications/'
	],
	'event' => [
		'directory_namespace' => true,
		'namespace' => '\Events',
		'path' => './app/Events/'
	],
	'listener' => [
		'directory_namespace' => true,
		'namespace' => '\Listeners',
		'path' => './app/Listeners/'
	],
	'trait' => [
		'directory_namespace' => true,
	],
	'job' => [
		'directory_namespace' => true,
		'namespace' => '\Jobs',
		'path' => './app/Jobs/'
	],
	'console' => [
		'directory_namespace' => true,
		'namespace' => '\Console\Commands',
		'path' => './app/Console/Commands/'
	],
	'middleware' => [
		'directory_namespace' => true,
		'namespace' => '\Http\Middleware',
		'path' => './app/Http/Middleware/'
	],
	'contract' => [
		'directory_namespace' => true,
		'namespace' => '\Contracts',
		'path' => './app/Contracts/',
	],

    /*
    |--------------------------------------------------------------------------
    | Resource Views [stub_key | name of the file]
    |--------------------------------------------------------------------------
    */

    'resource_views' => [
        'view_index' => 'index',
        //'view_create'      => 'create',
        //'view_edit'        => 'edit',
        'view_show' => 'show',
        'view_create_edit' => 'create_edit',
    ],

    /*
    |--------------------------------------------------------------------------
    | Resource Assets [stub_key | name of the file]
    |--------------------------------------------------------------------------
    */

    'resource_assets' => [
        'asset_index' => 'Index.vue',
        'asset_create' => 'Create.vue',
        'asset_edit' => 'Edit.vue',
        'asset_show' => 'Show.vue',
    ],

];
