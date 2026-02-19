<?php

namespace Delta1186\ConnectionMigrations\Console;

use Illuminate\Database\Console\Migrations\MigrateMakeCommand as BaseMigrateMakeCommand;
use Illuminate\Database\Console\Migrations\TableGuesser;
use Illuminate\Support\Str;

class MigrateMakeCommand extends BaseMigrateMakeCommand
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'make:migration {name : The name of the migration}
        {--create= : The table to be created}
        {--table= : The table to migrate}
        {--path= : The location where the migration file should be created}
        {--realpath : Indicate any provided migration file paths are pre-resolved absolute paths}
        {--fullpath : Output the full path of the migration (Deprecated)}
        {--database= : The database connection to use}';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $name = Str::snake(trim($this->input->getArgument('name')));

        $table = $this->input->getOption('table');

        $create = $this->input->getOption('create') ?: false;

        if (! $table && is_string($create)) {
            $table = $create;
            $create = true;
        }

        if (! $table) {
            [$table, $create] = TableGuesser::guess($name);
        }

        $this->writeMigration($name, $table, $create, $this->getConnectionForMigration());
    }

    /**
     * Write the migration file to disk.
     *
     * @param  string  $name
     * @param  string|null  $table
     * @param  bool  $create
     * @param  string|null  $connection
     * @return void
     */
    protected function writeMigration($name, $table, $create, $connection = null)
    {
        $file = $this->creator->create(
            $name, $this->getMigrationPath(), $table, $create, $connection
        );

        if (windows_os()) {
            $file = str_replace('/', '\\', $file);
        }

        $this->components->info(sprintf('Migration [%s] created successfully.', $file));
    }

    /**
     * Get the database connection name to inject into the migration, if applicable.
     *
     * Returns the connection name only when it differs from the default connection,
     * so the $connection property is only added to migrations that need it.
     *
     * @return string|null
     */
    protected function getConnectionForMigration()
    {
        $database = $this->input->getOption('database');

        if (! $database || ! $this->laravel->bound('config')) {
            return null;
        }

        $default = $this->laravel['config']->get('database.default');

        return $database !== $default ? $database : null;
    }

    /**
     * Get migration path (either specified by '--path' option, the connection's configured
     * path, or the default location).
     *
     * @return string
     */
    protected function getMigrationPath()
    {
        if (! is_null($targetPath = $this->input->getOption('path'))) {
            return ! $this->usingRealPath()
                ? $this->laravel->basePath().'/'.$targetPath
                : $targetPath;
        }

        $database = $this->input->getOption('database');

        if ($database && $this->laravel->bound('config')) {
            $connectionPath = $this->laravel['config']->get(
                "database.connections.{$database}.migrations"
            );

            if (is_string($connectionPath)) {
                return $this->laravel->basePath().'/'.$connectionPath;
            }
        }

        return parent::getMigrationPath();
    }
}
