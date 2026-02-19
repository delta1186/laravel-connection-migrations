# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.0] - 2025-02-19

### Added
- Per-connection migration path resolution for `migrate`, `migrate:rollback`, `migrate:reset`, and `migrate:status` commands via a `migrations` key in `config/database.php`.
- `--database` option for `make:migration` — automatically routes the generated file to the connection's configured migrations directory.
- Auto-injection of `protected $connection = 'name';` into generated migration stubs when `--database` specifies a non-default connection.
- `schema:dump --prune` now prunes the connection's configured migrations directory instead of always pruning `database/migrations`.
