<?php

namespace Delta1186\ConnectionMigrations;

use Delta1186\ConnectionMigrations\Console\DumpCommand;
use Delta1186\ConnectionMigrations\Console\MigrateCommand;
use Delta1186\ConnectionMigrations\Console\MigrateMakeCommand;
use Delta1186\ConnectionMigrations\Console\ResetCommand;
use Delta1186\ConnectionMigrations\Console\RollbackCommand;
use Delta1186\ConnectionMigrations\Console\StatusCommand;
use Delta1186\ConnectionMigrations\Migrations\MigrationCreator;
use Illuminate\Database\Console\DumpCommand as BaseDumpCommand;
use Illuminate\Database\Console\Migrations\MigrateCommand as BaseMigrateCommand;
use Illuminate\Database\Console\Migrations\MigrateMakeCommand as BaseMigrateMakeCommand;
use Illuminate\Database\Console\Migrations\ResetCommand as BaseResetCommand;
use Illuminate\Database\Console\Migrations\RollbackCommand as BaseRollbackCommand;
use Illuminate\Database\Console\Migrations\StatusCommand as BaseStatusCommand;
use Illuminate\Support\ServiceProvider;

class ConnectionMigrationsServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * Force-loads Laravel's MigrationServiceProvider first so that it cannot
     * fire later as a deferred provider and overwrite our command bindings.
     * Once a provider is loaded, Laravel's loadDeferredProvider() skips it,
     * making our subsequent singleton() calls the permanent winners.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(\Illuminate\Database\MigrationServiceProvider::class);

        $this->app->singleton(BaseMigrateCommand::class, function ($app) {
            return new MigrateCommand($app['migrator'], $app['events']);
        });

        $this->app->singleton(BaseRollbackCommand::class, function ($app) {
            return new RollbackCommand($app['migrator']);
        });

        $this->app->singleton(BaseResetCommand::class, function ($app) {
            return new ResetCommand($app['migrator']);
        });

        $this->app->singleton(BaseStatusCommand::class, function ($app) {
            return new StatusCommand($app['migrator']);
        });

        $this->app->singleton(BaseMigrateMakeCommand::class, function ($app) {
            return new MigrateMakeCommand($app['migration.creator'], $app['composer']);
        });

        $this->app->singleton(BaseDumpCommand::class, function ($app) {
            return new DumpCommand();
        });

        $this->app->singleton('migration.creator', function ($app) {
            return new MigrationCreator($app['files'], $app->basePath('stubs'));
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
