phpDivingLog Installation
=========================

This project now runs as a modernized PHP application with a Twig web adapter
and a JSON API adapter.

Requirements
------------

- PHP 8.3+
- Extensions: pdo, mbstring, json, plus one of:
  - pdo_mysql (for a MySQL/MariaDB database), or
  - pdo_sqlite (for a SQLite file -- no database server required)
- Composer 2+

Quick Start
-----------

1) Install dependencies

   composer install

2) Create runtime config

   cp .env.example .env

3) Configure database settings in .env

- DB_DSN and DB_USER (preferred), or
- DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASSWORD

`DB_DSN` examples:

- TCP connection:

  `DB_DSN="mysql:host=127.0.0.1;port=3306;dbname=divelog;charset=utf8mb4"`

- Unix socket connection:

  `DB_DSN="mysql:unix_socket=/var/run/mysqld/mysqld.sock;dbname=divelog;charset=utf8mb4"`

- SQLite file (no database server needed):

  `DB_DSN="sqlite:/absolute/path/to/divinglog.sqlite"`

Notes:

- Keep `charset=utf8mb4` in MySQL DSNs.
- If `DB_DSN` is empty, the app builds it from `DB_HOST` + `DB_PORT` + `DB_NAME`.
- `DB_USER` is required for MySQL; not required for a `sqlite:` DSN.

SQLite notes:

- Requires the `pdo_sqlite` extension instead of `pdo_mysql`.
- Store the SQLite file outside `public/` (e.g. under `var/`).
- Set `TABLE_PREFIX=` (empty) to read a native Diving Log SQLite export directly (unprefixed
  tables like `Logbook`, `Place`, `Country`), or keep `TABLE_PREFIX=DL_` for a MySQL-export-
  shaped SQLite file. See the "Database: MySQL or SQLite" section in `README.md` for details,
  including the known limitation around BLOB-embedded photos.

4) Ensure writable runtime directories

- var/cache
- var/log

5) Configure your web server front controller routing

- public/index.php (web UI)
- public/api.php (JSON API)

Subfolder deployment example (`example.com/divelog`)
----------------------------------------------------

If the app is hosted under a URL subfolder, map that URL to the project's
`public/` directory and keep rewrite rules enabled.

Example target:

- `https://example.com/divelog/` -> `<repo>/public/index.php`
- `https://example.com/divelog/api/...` -> `<repo>/public/api.php`

If your hosting panel cannot provide URL rewriting in a subfolder, set:

- `APP_QUERY_STRING=true`

Then use fallback URLs such as:

- `/divelog/?type=dives`
- `/divelog/?type=dives&id=1`
- `/divelog/?type=stats`

WordPress coexistence note
-------------------------

If WordPress is installed at domain root and phpDivingLog is in `/divelog`, make
sure `/divelog` rewrite rules are evaluated before WordPress catch-all rewrites.
Otherwise requests are routed to WordPress `index.php` instead of phpDivingLog.

See concrete examples in:

- `docs/nginx.conf.example`
- `docs/apache-htaccess.example`

For full deployment details and server examples, see:

- docs/deployment.md

Quality Gate
------------

Run all checks before release:

composer test && composer stan && composer cs
