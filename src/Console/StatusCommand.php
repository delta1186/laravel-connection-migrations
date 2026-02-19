<?php

namespace Delta1186\ConnectionMigrations\Console;

use Delta1186\ConnectionMigrations\Concerns\HasConnectionMigrationPath;
use Illuminate\Database\Console\Migrations\StatusCommand as BaseStatusCommand;

class StatusCommand extends BaseStatusCommand
{
    use HasConnectionMigrationPath;
}
