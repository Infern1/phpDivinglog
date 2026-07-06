# Testing and quality gates

Run the full quality gate from repository root:

1. `composer test`
2. `composer stan`
3. `composer cs`

Or run all checks in a single line:

`composer test && composer stan && composer cs`

## Test coverage

- Unit tests for config, conversion, formatting, translation, media handling, and RTF sanitizing.
- Repository integration tests with fixture-backed SQLite schema/data.
- HTTP smoke tests for web routes and `/api/*` endpoints:
  - successful page/endpoint responses,
  - key content markers in responses,
  - unknown id/resource behavior (`404` + generic message/error envelope).

## Environment notes

- Repository and HTTP smoke tests require `pdo_sqlite`.
- If `pdo_sqlite` is missing, those tests are skipped automatically and PHPUnit reports skips.
- To run all tests without skips, install/enable SQLite PDO in your PHP runtime.
