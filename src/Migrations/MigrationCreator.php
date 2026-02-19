<?php

namespace Delta1186\ConnectionMigrations\Migrations;

use Illuminate\Database\Migrations\MigrationCreator as BaseMigrationCreator;

class MigrationCreator extends BaseMigrationCreator
{
    /**
     * Create a new migration at the given path.
     *
     * @param  string  $name
     * @param  string  $path
     * @param  string|null  $table
     * @param  bool  $create
     * @param  string|null  $connection
     * @return string
     *
     * @throws \Exception
     */
    public function create($name, $path, $table = null, $create = false, $connection = null)
    {
        $this->ensureMigrationDoesntAlreadyExist($name, $path);

        $stub = $this->getStub($table, $create);

        $path = $this->getPath($name, $path);

        $this->files->ensureDirectoryExists(dirname($path));

        $this->files->put(
            $path, $this->populateStub($stub, $table, $connection)
        );

        $this->firePostCreateHooks($table, $path);

        return $path;
    }

    /**
     * Populate the place-holders in the migration stub.
     *
     * @param  string  $stub
     * @param  string|null  $table
     * @param  string|null  $connection
     * @return string
     */
    protected function populateStub($stub, $table, $connection = null)
    {
        if (! is_null($table)) {
            $stub = str_replace(
                ['DummyTable', '{{ table }}', '{{table}}'],
                $table, $stub
            );
        }

        $connectionLine = $connection
            ? "    protected \$connection = '{$connection}';\n"
            : '';

        $stub = str_replace('{{ connection }}', $connectionLine, $stub);

        return $stub;
    }

    /**
     * Get the path to the stubs.
     *
     * Uses the package's own stubs which include the {{ connection }} placeholder.
     * If the developer has published custom stubs to their application's stubs
     * directory, those will still take precedence via the customStubPath check
     * in the parent's getStub() method.
     *
     * @return string
     */
    public function stubPath()
    {
        return __DIR__.'/../../stubs';
    }
}
