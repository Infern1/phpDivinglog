## Agent Guide

### Current architecture

- Core business/data layer: `src/` (`PhpDivingLog\\*`)
- Web adapter: `adapters/web/` with front controller `public/index.php`
- API adapter: `adapters/api/` with front controller `public/api.php`
- Twig templates: `templates/`

### Non-negotiable migration constraints

- Do not reintroduce legacy runtime dependencies:
  - Smarty
  - wp-db.php
  - jqPlot
  - legacy root page controllers
- Keep repositories read-only against Diving Log schema.

### Configuration and data access

- Use env-based config via `src/Support/Config.php` (`.env.example` is source of truth).
- Use PDO factory in `src/Database/Connection.php`.
- Respect validated `TABLE_PREFIX`.

### Verification commands

- `composer test`
- `composer stan`
- `composer cs`
- `composer test && composer stan && composer cs`

### Notes

- Fixture-backed integration and smoke tests require `pdo_sqlite`.
