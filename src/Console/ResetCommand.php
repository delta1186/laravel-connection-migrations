<?php

namespace Delta1186\ConnectionMigrations\Console;

use Delta1186\ConnectionMigrations\Concerns\HasConnectionMigrationPath;
use Illuminate\Database\Console\Migrations\ResetCommand as BaseResetCommand;

class ResetCommand extends BaseResetCommand
{
    use HasConnectionMigrationPath;
}
