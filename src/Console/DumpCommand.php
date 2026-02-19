<?php

namespace Delta1186\ConnectionMigrations\Console;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Database\Console\DumpCommand as BaseDumpCommand;
use Illuminate\Database\Events\MigrationsPruned;
use Illuminate\Filesystem\Filesystem;

class DumpCommand extends BaseDumpCommand
{
    /**
     * Execute the console command.
     *
     * Overrides the base handle() only to fix the --prune path when the active
     * connection has a 'migrations' key configured in config/database.php.
     *
     * @param  \Illuminate\Database\ConnectionResolverInterface  $connections
     * @param  \Illuminate\Contracts\Events\Dispatcher  $dispatcher
     * @return void
     */
    public function handle(ConnectionResolverInterface $connections, Dispatcher $dispatcher)
    {
        if ($this->isProhibited()) {
            return \Illuminate\Console\Command::FAILURE;
        }

        $connection = $connections->connection($database = $this->input->getOption('database'));

        $this->schemaState($connection)->dump(
            $connection, $path = $this->path($connection)
        );

        $dispatcher->dispatch(new \Illuminate\Database\Events\SchemaDumped($connection, $path));

        $info = 'Database schema dumped';

        if ($this->option('prune')) {
            $migrationPath = $connection->getConfig('migrations');

            $pruneDirectory = is_string($migrationPath)
                ? base_path($migrationPath)
                : database_path('migrations');

            (new Filesystem)->deleteDirectory($pruneDirectory, preserve: false);

            $info .= ' and pruned';

            $dispatcher->dispatch(new MigrationsPruned($connection, $pruneDirectory));
        }

        $this->components->info($info.' successfully.');
    }
}
