# phpDivingLog

Modernized phpDivingLog using a decoupled PHP core and separate web/API adapters.

## Overview

This codebase has been rewritten from the legacy monolithic stack to:

- a strict-typed domain/repository core under `src/` (`PhpDivingLog\\*`),
- a standalone Twig web adapter under `adapters/web/` and `public/index.php`,
- a standalone JSON API adapter under `adapters/api/` and `public/api.php`.

The application reads from a Diving Log MySQL export schema (table prefix configurable, default `DL_`).

## Requirements

- PHP 8.3+
- Extensions: `pdo`, `pdo_mysql`, `mbstring`, `json`
- Composer 2+

For local test coverage parity:

- `pdo_sqlite` (for fixture-backed integration/smoke tests)

## Installation

For a concise setup checklist, see `INSTALL.md`.

1. Install dependencies:

   `composer install`

2. Copy environment template and configure DB access:

   `cp .env.example .env`

3. Ensure writable runtime directories:

   - `var/cache`
   - `var/log`

4. Configure web server front controller routing to:

   - `public/index.php` (web)
   - `public/api.php` (API)

See deployment details in `docs/deployment.md`.

If you deploy under a subfolder (for example `https://example.com/divelog`),
see the subfolder section in `INSTALL.md`.

## Runtime configuration

All options are environment-driven via `src/Support/Config.php`.

Important keys:

- `DB_DSN` and `DB_USER` (preferred), or `DB_HOST`/`DB_PORT`/`DB_NAME`/`DB_USER`
- `TABLE_PREFIX` (default `DL_`)
- `APP_QUERY_STRING`
  - `false`: pretty URL mode (rewrite rules)
  - `true`: fallback query-string mode

Complete option list: `.env.example`.

## Entry points

- Web UI: `public/index.php`
- API: `public/api.php`

### Query-string fallback mode

When rewrites are unavailable and `APP_QUERY_STRING=true`:

- `/?type=dives`
- `/?type=dives&id=1`
- `/?type=stats`

## Testing and quality gates

Run the full gate:

1. `composer test`
2. `composer stan`
3. `composer cs`

Or one-liner:

`composer test && composer stan && composer cs`

## Architecture

- Core domain + repositories: `src/`
- Web adapter (Twig): `adapters/web/`, `templates/`
- API adapter (JSON): `adapters/api/`
- Public front controllers/assets: `public/`

## Legacy cutover note

The following legacy stack has been retired in favor of the modernized architecture:

- monolithic `classes.inc.php`
- legacy page controllers in repository root
- `includes/` (Smarty/wp-db.php/jqPlot/img scripts)
- `tpl/`
- `sql/`

Static media assets under `images/` and frontend JS assets under `js/` remain available as project assets.

## License

GPL-3.0-or-later
