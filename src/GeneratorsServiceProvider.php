<?php

namespace Stylemix\Generators;

use Stylemix\Generators\Commands\AssetCommand;
use Stylemix\Generators\Commands\JobCommand;
use Stylemix\Generators\Commands\FileCommand;
use Stylemix\Generators\Commands\RequestCommand;
use Stylemix\Generators\Commands\ResourceCommand;
use Stylemix\Generators\Commands\SeedCommand;
use Stylemix\Generators\Commands\ViewCommand;
use Stylemix\Generators\Commands\EventCommand;
use Stylemix\Generators\Commands\ModelCommand;
use Stylemix\Generators\Commands\TraitCommand;
use Stylemix\Generators\Commands\ConsoleCommand;
use Stylemix\Generators\Commands\PublishCommand;
use Stylemix\Generators\Commands\ContractCommand;
use Stylemix\Generators\Commands\ListenerCommand;
use Stylemix\Generators\Commands\CrudCommand;
use Stylemix\Generators\Commands\MigrationCommand;
use Stylemix\Generators\Commands\ControllerCommand;
use Stylemix\Generators\Commands\RepositoryCommand;
use Stylemix\Generators\Commands\MiddlewareCommand;
use Stylemix\Generators\Commands\NotificationCommand;
use Stylemix\Generators\Commands\MigrationPivotCommand;
use Stylemix\Generators\Commands\EventGenerateCommand;
use Stylemix\Generators\Models\GeneralSchemaItem;
use Stylemix\Generators\Models\RelationSchemaItem;
use Illuminate\Support\ServiceProvider;

class GeneratorsServiceProvider extends ServiceProvider
{
    private $commandPath = 'command.bpocallaghan.';

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // merge config
        $configPath = __DIR__ . '/config/config.php';
        $this->mergeConfigFrom($configPath, 'generators');

        // Register container
        $this->app->bind(Generator::class, function () {
            $generator = new Generator();

            $this->registerTypes($generator);

            return $generator;
        }, true);
        
        $this->app->alias(Generator::class, 'stylemix.generator');

        // register all the artisan commands
        $this->registerCommand(PublishCommand::class, 'publish');

        $this->registerCommand(ModelCommand::class, 'model');
        $this->registerCommand(ViewCommand::class, 'view');
        $this->registerCommand(RequestCommand::class, 'request');
        $this->registerCommand(ResourceCommand::class, 'resource');
        $this->registerCommand(ControllerCommand::class, 'controller');
        $this->registerCommand(AssetCommand::class, 'asset');

        $this->registerCommand(MigrationCommand::class, 'migration');
        $this->registerCommand(MigrationPivotCommand::class, 'migrate.pivot');
        $this->registerCommand(SeedCommand::class, 'seed');

        $this->registerCommand(NotificationCommand::class, 'notification');

        $this->registerCommand(EventCommand::class, 'event');
        $this->registerCommand(ListenerCommand::class, 'listener');
        $this->registerCommand(EventGenerateCommand::class, 'event.generate');

        $this->registerCommand(TraitCommand::class, 'trait');
        $this->registerCommand(RepositoryCommand::class, 'repository');
        $this->registerCommand(ContractCommand::class, 'contract');

        $this->registerCommand(JobCommand::class, 'job');
        $this->registerCommand(ConsoleCommand::class, 'console');

        $this->registerCommand(MiddlewareCommand::class, 'middleware');

        $this->registerCommand(CrudCommand::class, 'crud');
        $this->registerCommand(FileCommand::class, 'file');
    }

    /**
     * Register initial schema ite types
     *
     * @param Generator $generator
     */
    protected function registerTypes(Generator $generator)
    {
        $generator->bindType(GeneralSchemaItem::class, '*');
        $generator->bindType(RelationSchemaItem::class, 'belongsTo', 'belongsToMany', 'hasOne', 'hasMany');
    }

    /**
     * Register a singleton command
     *
     * @param $class
     * @param $command
     */
    private function registerCommand($class, $command)
    {
        $this->app->singleton($this->commandPath . $command, function ($app) use ($class) {
            return $app[$class];
        });

        $this->commands($this->commandPath . $command);
    }
}