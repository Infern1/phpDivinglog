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
- Extensions: `pdo`, `mbstring`, `json`, plus one of:
  - `pdo_mysql` (for a MySQL/MariaDB database), or
  - `pdo_sqlite` (for a SQLite file -- no database server required; also used by the
    fixture-backed integration/smoke tests regardless of which driver you deploy with)
- Composer 2+

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

`DB_DSN` quick examples:

- `mysql:host=127.0.0.1;port=3306;dbname=divelog;charset=utf8mb4`
- `mysql:unix_socket=/var/run/mysqld/mysqld.sock;dbname=divelog;charset=utf8mb4`
- `sqlite:/absolute/path/to/divinglog.sqlite`

### Database: MySQL or SQLite

phpDivingLog can run against either engine -- switching is a `.env` change, not a code
change:

- **MySQL** (default): set `DB_DSN` (or `DB_HOST`/`DB_PORT`/`DB_NAME`) plus `DB_USER`/
  `DB_PASSWORD`, and keep `TABLE_PREFIX=DL_` (or your custom prefix).
- **SQLite**: set `DB_DSN=sqlite:/absolute/path/to/your-file.sqlite`. No `DB_USER`/
  `DB_PASSWORD` is needed -- SQLite has no server-side authentication. Requires the
  `pdo_sqlite` PHP extension. Store the file outside `public/` (e.g. under `var/`), since
  it's opened read-only and never needs to be web-accessible.

  Two SQLite file shapes are supported, selected via `TABLE_PREFIX`:
  - A **native Diving Log SQLite export** (the file Diving Log itself can export, with
    unprefixed tables like `Logbook`, `Place`, `Country`): set `TABLE_PREFIX=` (empty).
  - A **MySQL-export-shaped SQLite file** (tables named like `DL_Logbook`): keep
    `TABLE_PREFIX=DL_` as usual.

  Known limitation: photos, maps, and certification scans stored as in-database BLOBs in a
  native Diving Log export are not rendered -- phpDivingLog only reads filesystem-path
  columns (e.g. `PhotoPath`-style fields) for images, matching its MySQL-export behavior.
  If your SQLite file only has BLOB image data, those images simply won't display; nothing
  else is affected.

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

Static media assets are served from `public/images/`, and frontend runtime assets are served from `public/assets/`.

## License

GPL-3.0-or-later
