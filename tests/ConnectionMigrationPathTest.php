<?php

namespace Delta1186\ConnectionMigrations\Tests;

class ConnectionMigrationPathTest extends TestCase
{
    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'mysql');
        $app['config']->set('database.connections.mysql', ['driver' => 'sqlite', 'database' => ':memory:']);
        $app['config']->set('database.connections.crm', [
            'driver'     => 'sqlite',
            'database'   => ':memory:',
            'migrations' => 'database/migrations/crm',
        ]);
    }

    public function test_migrate_command_resolves_connection_path_when_database_option_given(): void
    {
        $this->artisan('migrate', ['--database' => 'crm', '--pretend' => true])
            ->assertExitCode(0);
    }

    public function test_make_migration_command_has_database_option(): void
    {
        $command = $this->app->make(
            \Illuminate\Database\Console\Migrations\MigrateMakeCommand::class
        );

        $this->assertTrue(
            $command->getDefinition()->hasOption('database'),
            'The make:migration command should have a --database option'
        );
    }

    public function test_make_migration_command_is_our_subclass(): void
    {
        $command = $this->app->make(
            \Illuminate\Database\Console\Migrations\MigrateMakeCommand::class
        );

        $this->assertInstanceOf(
            \Delta1186\ConnectionMigrations\Console\MigrateMakeCommand::class,
            $command
        );
    }

    public function test_migrate_command_is_our_subclass(): void
    {
        $command = $this->app->make(
            \Illuminate\Database\Console\Migrations\MigrateCommand::class
        );

        $this->assertInstanceOf(
            \Delta1186\ConnectionMigrations\Console\MigrateCommand::class,
            $command
        );
    }

    public function test_rollback_command_is_our_subclass(): void
    {
        $command = $this->app->make(
            \Illuminate\Database\Console\Migrations\RollbackCommand::class
        );

        $this->assertInstanceOf(
            \Delta1186\ConnectionMigrations\Console\RollbackCommand::class,
            $command
        );
    }

    public function test_reset_command_is_our_subclass(): void
    {
        $command = $this->app->make(
            \Illuminate\Database\Console\Migrations\ResetCommand::class
        );

        $this->assertInstanceOf(
            \Delta1186\ConnectionMigrations\Console\ResetCommand::class,
            $command
        );
    }

    public function test_status_command_is_our_subclass(): void
    {
        $command = $this->app->make(
            \Illuminate\Database\Console\Migrations\StatusCommand::class
        );

        $this->assertInstanceOf(
            \Delta1186\ConnectionMigrations\Console\StatusCommand::class,
            $command
        );
    }

    public function test_migration_creator_is_our_subclass(): void
    {
        $creator = $this->app->make('migration.creator');

        $this->assertInstanceOf(
            \Delta1186\ConnectionMigrations\Migrations\MigrationCreator::class,
            $creator
        );
    }
}
