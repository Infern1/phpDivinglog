# Deployment

This project supports both:

- a standalone web service deployment (Nginx/Apache with document root at `public/`),
- shared hosting deployment (public root may be the project root, with rewrite fallback).

## Runtime requirements

- PHP 8.3+
- Extensions:
  - `pdo`
  - `pdo_mysql`
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
3. Keep secrets out of VCS (`.env` is git-ignored).
4. Set routing mode:
   - `APP_QUERY_STRING=false` for rewritten pretty URLs (recommended),
   - `APP_QUERY_STRING=true` when rewrites are unavailable.

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
