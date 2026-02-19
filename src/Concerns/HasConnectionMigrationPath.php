<?php

namespace Delta1186\ConnectionMigrations\Concerns;

use Illuminate\Support\Collection;

trait HasConnectionMigrationPath
{
    /**
     * Get all of the migration paths.
     *
     * Adds a third resolution step: if the active database connection has a
     * 'migrations' key in config/database.php, that path is used automatically
     * when no --path flag is provided.
     *
     * Priority: --path flag > connection config key > default database/migrations
     *
     * @return string[]
     */
    protected function getMigrationPaths()
    {
        if ($this->input->hasOption('path') && $this->option('path')) {
            return (new Collection($this->option('path')))->map(function ($path) {
                return ! $this->usingRealPath()
                    ? $this->laravel->basePath().'/'.$path
                    : $path;
            })->all();
        }

        if ($connectionPath = $this->connectionMigrationPath()) {
            return [$this->laravel->basePath().'/'.$connectionPath];
        }

        return array_merge(
            $this->migrator->paths(), [$this->getMigrationPath()]
        );
    }

    /**
     * Get the migration path configured for the active database connection, if any.
     *
     * @return string|null
     */
    protected function connectionMigrationPath()
    {
        if (! $this->laravel->bound('config')) {
            return null;
        }

        $connection = $this->migrator->getConnection()
            ?? $this->laravel['config']->get('database.default');

        if (! $connection) {
            return null;
        }

        $path = $this->laravel['config']->get("database.connections.{$connection}.migrations");

        return is_string($path) ? $path : null;
    }
}
