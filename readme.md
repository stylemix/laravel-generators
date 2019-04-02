# Laravel 5 File Generators

Custom Laravel 5 File Generators with a config file and publishable stubs.
You can publish the stubs. You can add your own stubs to generate.

## Commands
```bash
php artisan generate:model
php artisan generate:view
php artisan generate:controller
php artisan generate:migration
php artisan generate:migration:pivot
php artisan generate:factory
php artisan generate:seed
php artisan generate:resource
php artisan generate:admin
php artisan generate:crud
php artisan generate:contract
php artisan generate:notification
php artisan generate:event
php artisan generate:listener
php artisan generate:event-listener
php artisan generate:trait
php artisan generate:job
php artisan generate:console
php artisan generate:middleware
php artisan generate:file
```

## Installation

Update your project's `composer.json` file.

```
composer require stylemix/laravel-generators --dev
```

Add the Service Provider (Laravel 5.5 has automatic discovery of packages)
You'll only want to use these generators for local development, add the provider in `app/Providers/AppServiceProvider.php`:

```php
public function register()
{
    if ($this->app->environment() == 'local') {
        $this->app->register(\Stylemix\Generators\GeneratorsServiceProvider::class);
    }
}
```

Run `php artisan` command to see the new commands in the `generate:*` section

## Usage

- [Models](#models)
- [Migrations](#migrations)
- [Pivot Tables](#pivot-tables)
- [Factories](#factories)
- [Database Seeders](#database-seeders)
- [API Forms](#api-forms)
- [Requests](#requests)
- [API Resources](#api-resources)
- [Controllers](#controllers)
- [Admin assets](#admin-assets)
- [CRUD](#crud)
- [Views](#views)
- [Contract](#contract)
- [Notifications](#notifications)
- [Events and Listeners](#events-and-listeners)
- [Trait](#trait)
- [Job](#job)
- [Console](#console-artisan-command)
- [Middleware](#middleware)
- [File](#file)
- [Configuration](#configuration)


### Models

```
php artisan generate:model bar
php artisan generate:model foo.bar --plain
php artisan generate:model bar --force
php artisan generate:model bar --migration --schema="title:string, body:text"
```


### Migrations

This is very similar as [Jeffrey Way's](https://github.com/laracasts/Laravel-5-Generators-Extended)

```
php artisan generate:migration create_users_table
php artisan generate:migration create_users_table --plain
php artisan generate:migration create_users_table --force
php artisan generate:migration create_posts_table --schema="title:string, body:text, slug:string:unique, published_at:date"
```


### Pivot Tables

This is very similar as [Jeffrey Way's](https://github.com/laracasts/Laravel-5-Generators-Extended)

```
php artisan generate:migration:pivot tags posts
```


### Factories

```
php artisan generate:factory bar
php artisan generate:factory BarFactory
```

- The `Factory` suffix will be added if needed.


### Database Seeders

```
php artisan generate:seed bar
php artisan generate:seed BarTableSeeder
```

- The `TableSeeder` suffix will be added if needed.


### API Forms

```
php artisan generate:form contact
php artisan generate:form ContactForm
php artisan generate:form ContactForm --without-request
```

- The `Form` suffix will be added if needed.
- Request class will be generated alongside with form class with the same name-base: `ContactForm` -> `ContactRequest`.
 Use `--without-request` to prevent this.


### Requests

```
php artisan generate:request contact
php artisan generate:request ContactRequest
```

- The `Request` suffix will be added if needed.


### API Resources

```
php artisan generate:resource bar
php artisan generate:resource BarResource
```

- The `Resource` suffix will be added if needed.


### Controllers

```
php artisan generate:controller foo
php artisan generate:controller foo.bar
php artisan generate:controller fooBar
php artisan generate:controller bar --plain
php artisan generate:controller BarController --plain
```

- The `Controller` postfix will be added if needed.


### Admin assets

```
php artisan generate:admin foo
```

This will generate assets listed in config file `generator.php` in `admin_assets` array

```php
'admin_assets' => [
	// stub name => file name
	'admin_index' => 'Index.vue',
	'admin_create' => 'Create.vue',
	'admin_edit' => 'Edit.vue',
	'admin_routes' => 'routes.js',
]
```


### CRUD

```
php artisan generate:crud bar
php artisan generate:crud foo.bar
php artisan generate:crud foo.bar_baz
php artisan generate:crud bar --schema="title:string, body:text, slug:string:unique, published_at:date"
```

- This will generate a Bar model, BarsController, Bar json resource, admin assets, create_bars_table migration, BarTableSeeder


### Views

```
php artisan generate:view foo
php artisan generate:view foo.bar
php artisan generate:view foo.bar --stub=view_show
php artisan generate:view foo.bar --name=foo_bar
```


### Contract
```
php artisan generate:contract Cache
```
This will generate a Cache Contract file to be used with your repositories.

### Notifications

```
php artisan generate:notification UserRegistered
```

This will generate a UserRegistered notification.
Laravel provides support for sending notifications across a variety of delivery channels, including mail, SMS (via Nexmo), and Slack. Notifications may also be stored in a database so they may be displayed in your web interface.

### Events and Listeners

```
php artisan generate:event InvoiceWasPaid
php artisan generate:listener NotifyUserAboutPayment --event=InvoiceWasPaid
php artisan generate:event-listener
```
This will generate the event and listener.
Laravel's events provides a simple observer implementation, allowing you to subscribe and listen for various events that occur in your application

`php artisan generate:event-listener `
Will generate all the missing events and listeners defined in your EventServiceProvider.

### Trait
```
php artisan generate:trait Http\Controllers\Traits\Bar
```
This will generate a FooBar Trait file. The command will use the name as your namespace.
`generate:trait Foo` will create a file in `app/Foo.php`, `generate:trait Foo\Bar` will create a file in `app/Foo/Bar.php`.

### Job
```
php artisan generate:job SendReminderEmail
```
This will generate a SendReminderEmail Job file.

### Console (Artisan Command)
```
php artisan generate:console SendEmails
php artisan generate:console SendEmails --command=send:emails
```
This will generate a SendEmails Artisan Command file. The --command option is optional.

### Middleware
```
php artisan generate:middleware AuthenticateAdmin
```
This will generate an AuthenticateAdmin Middleware file.

### Configuration

```
php artisan generate:publish-stubs
```

This will copy the config file to `/config/generators.php`.
Here you can change the defaults for the settings of each `type`, like model, view, controller, seed.
You can also change the namespace, path where to create the file, the pre/post fix, and more.
You can also add new stubs.

This will also copy all the stubs to `/resources/stubs/`.
Here you can make changes to the current stubs, add your own boilerplate / comments to the files.
You can also add your own stubs here and specify it in the config to be used.


### File

This is the base command for the view, model, controller, seed commands. 
The migration and migration:pivot uses Jeffrey's classes.
In the config there is a `settings` array, this is the 'types' and their settings. You can add more, for example, if you use repositories, you can add it here.

```
php artisan generate:file foo.bar --type=view
php artisan generate:file foo.bar --type=controller
php artisan generate:file foo.bar --type=model
php artisan generate:file foo.bar --type=model --stub=model_custom
```

### Options for all commands

- `--force` This will override the existing file.
- `--plain` This will use the `*_plain` stub of the command if defined (generate an empty controller)


### Option `--relations`
Usage:
```
--relations="<relation definition 1>,<relation definition 2>,..."
```
Relation definition:
```
<resource>:<relation type>:foreign(<key>):input(<input type>):as(<attribute>)
```

### Customization
This is for all except the `migration` and `migration:pivot` commands

```
php artisan generate:file foo.bar --type=controller
php artisan generate:file foo.bar --type=controller --stub=controller_custom --name=BazzzController --plain --force
```

- `--name` - specify a custom name of the file to be generated

You can override the default stub to be used.
You can create your own stubs with the available placeholders.

You can create new settings' types, for example:
```php
'exception' => [
	'namespace' => '\Exceptions',
	'path' => './app/Exceptions/',
	'postfix' => 'Exception',
],
```
