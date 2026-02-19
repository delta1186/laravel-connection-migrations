<?php

namespace Delta1186\ConnectionMigrations\Console;

use Delta1186\ConnectionMigrations\Concerns\HasConnectionMigrationPath;
use Illuminate\Database\Console\Migrations\MigrateCommand as BaseMigrateCommand;

class MigrateCommand extends BaseMigrateCommand
{
    use HasConnectionMigrationPath;
}
