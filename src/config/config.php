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

    'settings' => [
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
        'resource' => [
            'namespace' => '\Http\Resources',
            'path' => './app/Http/Resources/'
        ],
        'request' => [
            'namespace' => '\Http\Requests',
            'path' => './app/Http/Requests/',
            'postfix' => 'Request',
        ],
        'controller' => [
            'namespace' => '\Http\Controllers',
            'path' => './app/Http/Controllers/',
            'postfix' => 'Controller',
            'directory_namespace' => true,
            'dump_autoload' => true,
            'repository_contract' => false,
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
        'repository' => [
            'directory_namespace' => true,
            'postfix' => 'Repository',
            'namespace' => '\Repositories',
            'path' => './app/Repositories/'
        ],
        'contract' => [
            'directory_namespace' => true,
            'namespace' => '\Contracts',
            'postfix' => 'Repository',
            'path' => './app/Contracts/',
        ],
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

    /*
    |--------------------------------------------------------------------------
    | Requests [stub_key | filename part]
    |--------------------------------------------------------------------------
    */

    'requests' => [
        'create_request' => 'Create',
        'update_request' => 'Update'
    ],

    /*
    |--------------------------------------------------------------------------
    | Where the stubs for the generators are stored
    |--------------------------------------------------------------------------
    */

	'example_stub' => base_path() . '/vendor/stylemix/laravel-generators/resources/stubs/example.stub',
	'model_stub' => base_path() . '/vendor/stylemix/laravel-generators/resources/stubs/model.blade.php',
	'model_plain_stub' => base_path() . '/vendor/stylemix/laravel-generators/resources/stubs/model.plain.stub',
	'resource_stub' => base_path() . '/vendor/stylemix/laravel-generators/resources/stubs/resource.blade.php',
	'resource_plain_stub' => base_path() . '/vendor/stylemix/laravel-generators/resources/stubs/resource.plain.stub',
	'migration_stub' => base_path() . '/vendor/stylemix/laravel-generators/resources/stubs/migration.blade.php',
	'migration_plain_stub' => base_path() . '/vendor/stylemix/laravel-generators/resources/stubs/migration.plain.stub',
	'controller_stub' => base_path() . '/vendor/stylemix/laravel-generators/resources/stubs/controller.blade.php',
	'controller_plain_stub' => base_path() . '/vendor/stylemix/laravel-generators/resources/stubs/controller.plain.stub',
	'controller_admin_stub' => base_path() . '/vendor/stylemix/laravel-generators/resources/stubs/controller_admin.stub',
	'controller_repository_stub' => base_path() . '/vendor/stylemix/laravel-generators/resources/stubs/controller_repository.stub',
	'pivot_stub' => base_path() . '/vendor/stylemix/laravel-generators/resources/stubs/pivot.stub',
	'seed_stub' => base_path() . '/vendor/stylemix/laravel-generators/resources/stubs/seed.stub',
	'seed_plain_stub' => base_path() . '/vendor/stylemix/laravel-generators/resources/stubs/seed.plain.stub',
	'view_stub' => base_path() . '/vendor/stylemix/laravel-generators/resources/stubs/view.stub',
	'view_index_stub' => base_path() . '/vendor/stylemix/laravel-generators/resources/stubs/view.index.stub',
	'view_show_stub' => base_path() . '/vendor/stylemix/laravel-generators/resources/stubs/view.show.stub',
	'asset_index_stub' => base_path() . '/vendor/stylemix/laravel-generators/resources/stubs/asset_index.blade.php',
	'asset_create_stub' => base_path() . '/vendor/stylemix/laravel-generators/resources/stubs/asset_create.blade.php',
	'asset_edit_stub' => base_path() . '/vendor/stylemix/laravel-generators/resources/stubs/asset_edit.blade.php',
	'asset_show_stub' => base_path() . '/vendor/stylemix/laravel-generators/resources/stubs/asset_show.blade.php',
	'create_request_stub' => base_path() . '/vendor/stylemix/laravel-generators/resources/stubs/request_create.blade.php',
	'update_request_stub' => base_path() . '/vendor/stylemix/laravel-generators/resources/stubs/request_update.blade.php',
	'pivot_table_stub' => base_path() . '/vendor/stylemix/laravel-generators/resources/stubs/pivot_table_migration.blade.php',
	//'view_create_stub'            => base_path() . '/vendor/stylemix/laravel-generators/resources/stubs/view.create.stub',
	//'view_edit_stub'              => base_path() . '/vendor/stylemix/laravel-generators/resources/stubs/view.edit.stub',
	'controller_route_stub' => base_path() . '/vendor/stylemix/laravel-generators/resources/stubs/controller_route.stub',
	'view_create_edit_stub' => base_path() . '/vendor/stylemix/laravel-generators/resources/stubs/view.create_edit.stub',
	'schema_create_stub' => base_path() . '/vendor/stylemix/laravel-generators/resources/stubs/schema-create.stub',
	'schema_change_stub' => base_path() . '/vendor/stylemix/laravel-generators/resources/stubs/schema-change.stub',
	'notification_stub' => base_path() . '/vendor/stylemix/laravel-generators/resources/stubs/notification.stub',
	'event_stub' => base_path() . '/vendor/stylemix/laravel-generators/resources/stubs/event.stub',
	'listener_stub' => base_path() . '/vendor/stylemix/laravel-generators/resources/stubs/listener.stub',
	'trait_stub' => base_path() . '/vendor/stylemix/laravel-generators/resources/stubs/trait.stub',
	'job_stub' => base_path() . '/vendor/stylemix/laravel-generators/resources/stubs/job.stub',
	'console_stub' => base_path() . '/vendor/stylemix/laravel-generators/resources/stubs/console.stub',
	'middleware_stub' => base_path() . '/vendor/stylemix/laravel-generators/resources/stubs/middleware.stub',
	'repository_stub' => base_path() . '/vendor/stylemix/laravel-generators/resources/stubs/repository.stub',
	'contract_stub' => base_path() . '/vendor/stylemix/laravel-generators/resources/stubs/contract.stub',
];
