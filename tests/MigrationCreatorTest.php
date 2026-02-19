<?php

namespace Delta1186\ConnectionMigrations\Tests;

use Delta1186\ConnectionMigrations\Migrations\MigrationCreator;
use Illuminate\Filesystem\Filesystem;

class MigrationCreatorTest extends TestCase
{
    private string $tempDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tempDir = sys_get_temp_dir().'/laravel-connection-migrations-'.uniqid();
        mkdir($this->tempDir, 0755, true);
    }

    protected function tearDown(): void
    {
        (new Filesystem)->deleteDirectory($this->tempDir);
        parent::tearDown();
    }

    public function test_injects_connection_property_when_connection_is_given(): void
    {
        $creator = new MigrationCreator(new Filesystem, $this->tempDir);

        $path = $creator->create('create_orders_table', $this->tempDir, 'orders', true, 'crm');

        $this->assertStringContainsString("protected \$connection = 'crm';", file_get_contents($path));
    }

    public function test_does_not_inject_connection_property_when_connection_is_null(): void
    {
        $creator = new MigrationCreator(new Filesystem, $this->tempDir);

        $path = $creator->create('create_orders_table', $this->tempDir, 'orders', true, null);

        $this->assertStringNotContainsString('$connection', file_get_contents($path));
    }

    public function test_replaces_connection_placeholder_leaving_no_literal_placeholder(): void
    {
        $creator = new MigrationCreator(new Filesystem, $this->tempDir);

        $path = $creator->create('create_orders_table', $this->tempDir, 'orders', true, 'crm');

        $this->assertStringNotContainsString('{{ connection }}', file_get_contents($path));
    }

    public function test_still_replaces_the_table_placeholder(): void
    {
        $creator = new MigrationCreator(new Filesystem, $this->tempDir);

        $path = $creator->create('create_orders_table', $this->tempDir, 'orders', true, 'crm');

        $this->assertStringContainsString("'orders'", file_get_contents($path));
    }

    public function test_uses_the_package_stubs_directory(): void
    {
        $creator = new MigrationCreator(new Filesystem, $this->tempDir);

        $this->assertStringEndsWith('stubs', $creator->stubPath());
        $this->assertDirectoryExists($creator->stubPath());
    }
}
