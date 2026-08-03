# Deployment

This project supports both:

- a standalone web service deployment (Nginx/Apache with document root at `public/`),
- shared hosting deployment (public root may be the project root, with rewrite fallback).

## Runtime requirements

- PHP 8.3+
- Extensions:
  - `pdo`
  - `pdo_mysql` (MySQL/MariaDB deployments) or `pdo_sqlite` (SQLite deployments)
  - `mbstring`
  - `json`
- Writable directories:
  - `var/cache`
  - `var/log`

## Configuration

1. Copy `.env.example` to `.env`.
2. Set database settings:
   - `DB_DSN` and `DB_USER` (preferred), or
   - `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`.

`DB_DSN` format examples:

- TCP:
  - `mysql:host=127.0.0.1;port=3306;dbname=divelog;charset=utf8mb4`
- Unix socket:
  - `mysql:unix_socket=/var/run/mysqld/mysqld.sock;dbname=divelog;charset=utf8mb4`
- SQLite (no database server):
  - `sqlite:/absolute/path/to/divinglog.sqlite`

If `DB_DSN` is empty, the app builds DSN from `DB_HOST`, `DB_PORT`, and `DB_NAME`.
3. Keep secrets out of VCS (`.env` is git-ignored).
4. Set routing mode:
   - `APP_QUERY_STRING=false` for rewritten pretty URLs (recommended),
   - `APP_QUERY_STRING=true` when rewrites are unavailable.

## SQLite deployments (no database server)

For hosts without MySQL access, point `DB_DSN` at a SQLite file instead:

1. Install/enable the `pdo_sqlite` PHP extension (no `pdo_mysql` needed).
2. Copy your SQLite file to a location outside `public/` -- e.g. `var/divinglog.sqlite` --
   so it's never web-accessible. It only needs to be readable by the web server user; the
   app opens it read-only.
3. Set `DB_DSN=sqlite:/absolute/path/to/divinglog.sqlite`. Leave `DB_USER`/`DB_PASSWORD`
   empty -- SQLite has no server-side authentication.
4. Set `TABLE_PREFIX` based on the file's shape:
   - Empty (`TABLE_PREFIX=`) for a native Diving Log SQLite export (unprefixed tables like
     `Logbook`, `Place`, `Country`).
   - `DL_` (the usual default) for a MySQL-export-shaped SQLite file.

Known limitation: a native Diving Log export can store photos/maps/certification scans as
in-database BLOBs; phpDivingLog only renders images from filesystem-path columns (matching
its MySQL-export behavior), so BLOB-only images won't display. Everything else -- dive
listings, statistics, sites, trips, equipment, certifications -- works the same as MySQL.

## Standalone service (recommended)

### Nginx

- Use `docs/nginx.conf.example` as a base.
- Point `root` at the repository `public/` directory.
- Route all non-file requests to front controllers:
  - web to `index.php`
  - API to `api.php`

### Apache

- Set DocumentRoot to `<repo>/public`.
- Enable `mod_rewrite`.
- Use the rewrite example from `docs/apache-htaccess.example` in `public/.htaccess` (or vhost config).

## Shared hosting deployment

When docroot cannot be changed to `public/`:

1. Upload a release package that includes `vendor/` and `public/assets/`.
2. Keep `.env` outside web root if your provider supports it; otherwise protect it with server rules.
3. Add rewrite rules that route app requests to `public/index.php` and API requests to `public/api.php`.
4. If rewrites are not available, set `APP_QUERY_STRING=true` and use query-string mode:
   - `/?type=dives`
   - `/?type=dives&id=1`
   - `/?type=stats`

## Subfolder deployment (`example.com/divelog`)

When your app is served from a URL subfolder:

1. Route that subfolder to the repository `public/` directory if possible.
2. Keep front-controller rewrites enabled inside that subfolder.
3. Ensure API routes in that subfolder resolve to `public/api.php`.

Example URLs:

- `https://example.com/divelog/` (web)
- `https://example.com/divelog/api/dives` (API)

If subfolder rewrites are unavailable, set `APP_QUERY_STRING=true` and use:

- `/divelog/?type=dives`
- `/divelog/?type=dives&id=1`
- `/divelog/?type=stats`

### WordPress at domain root + phpDivingLog in `/divelog`

This is a common setup. The critical rule is precedence:

- Match `/divelog` and `/divelog/api` before generic WordPress front-controller rules.
- Otherwise WordPress rewrites those requests to its own `index.php`.

Use the subfolder examples in:

- `docs/nginx.conf.example`
- `docs/apache-htaccess.example`

## Release process

1. Run quality gates locally:
   - `composer test`
   - `composer stan`
   - `composer cs`
2. Install production dependencies:
   - `composer install --no-dev --optimize-autoloader`
3. Deploy code including:
   - `vendor/`
   - `public/assets/`
   - `templates/`
   - `resources/lang/`
4. Ensure `var/cache` and `var/log` are writable by the web server user.
