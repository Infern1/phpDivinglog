phpDivingLog Installation
=========================

This project now runs as a modernized PHP application with a Twig web adapter
and a JSON API adapter.

Requirements
------------

- PHP 8.3+
- Extensions: pdo, pdo_mysql, mbstring, json
- Composer 2+

Optional for local test parity:

- pdo_sqlite

Quick Start
-----------

1) Install dependencies

   composer install

2) Create runtime config

   cp .env.example .env

3) Configure database settings in .env

- DB_DSN and DB_USER (preferred), or
- DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASSWORD

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
