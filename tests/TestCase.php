<?php

namespace Delta1186\ConnectionMigrations\Tests;

use Delta1186\ConnectionMigrations\ConnectionMigrationsServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            ConnectionMigrationsServiceProvider::class,
        ];
    }
}
