<?php

namespace Delta1186\ConnectionMigrations\Tests;

use Illuminate\Filesystem\Filesystem;

class MigrateMakeCommandTest extends TestCase
{
    private string $migrationDir;

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

    protected function setUp(): void
    {
        parent::setUp();
        $this->migrationDir = $this->app->basePath('database/migrations/crm');
    }

    protected function tearDown(): void
    {
        (new Filesystem)->deleteDirectory($this->app->basePath('database'));
        parent::tearDown();
    }

    public function test_writes_migration_to_connection_path_when_database_option_given(): void
    {
        $this->artisan('make:migration', [
            'name'       => 'create_orders_table',
            '--database' => 'crm',
        ])->assertExitCode(0);

        $files = glob($this->migrationDir.'/*_create_orders_table.php');

        $this->assertNotEmpty($files, 'Migration file should be created in database/migrations/crm/');
    }

    public function test_injects_connection_property_for_non_default_connection(): void
    {
        $this->artisan('make:migration', [
            'name'       => 'create_orders_table',
            '--database' => 'crm',
        ])->assertExitCode(0);

        $files = glob($this->migrationDir.'/*_create_orders_table.php');
        $this->assertNotEmpty($files);

        $this->assertStringContainsString(
            "protected \$connection = 'crm';",
            file_get_contents($files[0])
        );
    }

    public function test_does_not_inject_connection_property_for_default_connection(): void
    {
        $defaultDir = $this->app->basePath('database/migrations');

        $this->artisan('make:migration', [
            'name'       => 'create_users_table',
            '--database' => 'mysql',
        ])->assertExitCode(0);

        $files = glob($defaultDir.'/*_create_users_table.php');
        $this->assertNotEmpty($files, 'Migration file should be created in the default migrations directory');

        $this->assertStringNotContainsString('$connection', file_get_contents($files[0]));
    }

    public function test_falls_back_to_default_path_when_connection_has_no_migrations_key(): void
    {
        $this->app['config']->set('database.connections.analytics', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
        ]);

        $defaultDir = $this->app->basePath('database/migrations');

        $this->artisan('make:migration', [
            'name'       => 'create_events_table',
            '--database' => 'analytics',
        ])->assertExitCode(0);

        $files = glob($defaultDir.'/*_create_events_table.php');
        $this->assertNotEmpty($files, 'Should fall back to database/migrations when no migrations key is set');
    }

    public function test_explicit_path_takes_precedence_over_connection_migrations_key(): void
    {
        $customDir = $this->app->basePath('database/migrations/custom');

        $this->artisan('make:migration', [
            'name'       => 'create_products_table',
            '--database' => 'crm',
            '--path'     => 'database/migrations/custom',
        ])->assertExitCode(0);

        $files = glob($customDir.'/*_create_products_table.php');
        $this->assertNotEmpty($files, '--path should override the connection migrations key');

        $crmFiles = glob($this->migrationDir.'/*_create_products_table.php');
        $this->assertEmpty($crmFiles, 'File should NOT be in the crm directory when --path is given');
    }
}
