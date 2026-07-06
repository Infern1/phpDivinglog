# Changelog

All notable changes to phpDivingLog are documented in this file.

## v4.1.0 — 2026-07-06

### UI framework migration to Beer CSS (Material Design 3)
- Vendored Beer CSS, material-dynamic-colors, Material Symbols, and Inter locally under `public/assets/vendor/beercss/` (no runtime CDN).
- Added a global light/dark theme system with a no-FOUC prepaint bootstrap and an accessible theme toggle (persisted via `localStorage['divelog:theme']`).
- Added switchable color palettes (reef/sunset/kelp/abyss) persisted via `localStorage['divelog:palette']`.
- Added `public/assets/css/custom.css` for project widgets built on theme tokens.
- Made canvas charts theme-aware (redraw on `themechange`).
- Added a favicon link (`/images/favicon.ico`).

### Branding / personalization
- App title/brand derived from the diver's Personal profile (`<First> <Last> Dive Log`), injected as a Twig global.
- Footer now shows copyright (Rob Lensen &lt;rob@bsdfreaks.nl&gt;) and a GitHub link.

### Dive detail improvements
- Fixed next/previous logbook scroll to use pane-local centering (no page jump).
- Made the Logbook pane header sticky and prevented list overlap.
- Hid the "Ascent / descent rates" chart; fixed the depth profile so it renders independently of the (now hidden) rate canvas.
- Equalized hero metric card heights (Depth / Avg depth / Duration in line).
- Added inline tank fallback: when no `DL_Tank` rows exist, tank volume/pressures/O2 are read from the Logbook row itself.

### Dive site detail improvements
- Added previous/next site navigation.
- Added a Google Maps link derived from latitude/longitude.
- Added Max depth and Water type (Salt/Fresh/Brackish), derived from dives.
- Added a site Pictures section using the same media/lightbox as dive detail.
- Enabled lightbox on the site map image.

### Dive country detail
- Fixed empty "Dive sites" list by falling back to the Logbook country mapping when `DL_Place.CountryID` is absent/unpopulated.

### Dive log overview
- Added profile and photo indicator icons per dive.

### Certifications
- Added a Certifications section to the statistics page, reading from the real Diving Log `Brevets` table (`Org`/`Brevet`/`CertDate`/`Number`/`Instructor`/`Scan1Path`/`Scan2Path`), with front/back scans shown via `APP_USER_PATH_WEB` and lightbox.
- Gated by `APP_USER_SHOW_CERTS`; scans gated by `APP_USER_SHOW_PHOTO`.

### Robustness
- Made Personal profile loading schema-agnostic (`SELECT *` + case-insensitive alias resolution) to survive missing optional columns (e.g. `Comment`/`Picture`).
- Extended fixture-backed HTTP smoke tests to cover the above; `composer test`/`stan`/`cs` green.

## v4.0.0 — 2026-07-04

Big-bang modernization release.

- Replaced the legacy monolithic runtime with a layered architecture:
  - Core domain/repository layer in `src/` (`PhpDivingLog` namespace).
  - Standalone Twig web adapter (`adapters/web` + `public/index.php`).
  - Standalone JSON API adapter (`adapters/api` + `public/api.php`).
- Added Composer project metadata, PSR-4 autoloading, and quality scripts.
- Added quality tool configuration: PHPUnit, PHPStan, PHP_CodeSniffer (PSR-12).
- Added an environment-based typed configuration loader and `.env.example`.
- Added a PDO connection factory with table-prefix validation.
- Added an immutable DTO model layer and PDO repositories.
- Added support services: unit conversion; formatting (dates/coords/decimals); translation loader/fallback; RTF conversion + sanitizer; media path resolver.
- Added fixture-backed repository integration tests and HTTP smoke tests.
- Added deployment/testing/API/frontend docs under `docs/`.

### Removed (retired legacy stack after parity verification)
- `classes.inc.php`
- Root legacy controllers (`index.php`, `divesite.php`, `divecountry.php`, etc.)
- `includes/` (Smarty / wp-db.php / jqPlot / img scripts)
- `tpl/`
- `sql/`

### Intentionally retained
- `public/images/` static assets
- `public/assets/` runtime frontend assets

### Potentially impacted legacy-only behaviors
- Direct execution of legacy root PHP entry files (removed).
- Legacy Smarty template override/customization points (removed).
- Legacy jqPlot/Highslide rendering paths (replaced by modern assets).
