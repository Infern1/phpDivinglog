## phpDivingLog Agent Notes

### Architecture

- Modernized layered app:
  - Core: `src/`
  - Web adapter: `adapters/web/` + `public/index.php`
  - API adapter: `adapters/api/` + `public/api.php`
- Rendering uses Twig templates in `templates/`.
- Data access is read-only via PDO repositories in `src/Repository/`.

### Runtime constraints

- PHP 8.3+
- Config is env-driven (`.env` + `.env.example`) via `src/Support/Config.php`.
- Table prefix is configurable and validated (`TABLE_PREFIX`).

### Removed legacy stack

Legacy runtime artifacts were retired during v4 cutover:

- `classes.inc.php`
- legacy root page controllers
- `includes/`, `tpl/`, `sql/`

Do not reintroduce dependencies on Smarty, wp-db.php, jqPlot, or root page entry scripts.

### Common commands

- Test: `composer test`
- Static analysis: `composer stan`
- Coding standards: `composer cs`
- Full gate: `composer test && composer stan && composer cs`

### Testing notes

- HTTP smoke and repository integration tests use SQLite fixtures (`tests/fixtures/`).
- `pdo_sqlite` should be enabled locally/CI for full parity coverage.
