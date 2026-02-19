<?php

namespace Delta1186\ConnectionMigrations\Console;

use Delta1186\ConnectionMigrations\Concerns\HasConnectionMigrationPath;
use Illuminate\Database\Console\Migrations\RollbackCommand as BaseRollbackCommand;

class RollbackCommand extends BaseRollbackCommand
{
    use HasConnectionMigrationPath;
}
