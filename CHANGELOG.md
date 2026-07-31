# Changelog

All notable changes to phpDivingLog are documented in this file.

## v4.2.0 — 2026-07-31

### SEO metadata (unique title, description, canonical, WebPage schema)
- Every content page (dives, sites, countries, cities, shops, trips, equipment, stats, both
  galleries) now renders a unique `<title>`, `<meta name="description">`,
  `<link rel="canonical">`, and a `schema.org` `WebPage` JSON-LD block, built from data each
  controller already fetches — no page shares a generic title anymore.
- Canonical URLs correctly match whichever routing mode is active (`?type=&id=` query-string vs.
  clean `/resource/id` paths), and self-reference on paginated overview pages.
- Added `APP_SEO_ENABLED` (`.env`, default `true`) to disable all of the above for private/personal
  installs — falls back to the previous generic title plus a `noindex,nofollow` signal.
- The embeddable summary widget (`/summary`) and the AJAX dive-detail fragment are excluded from
  indexing/enhancement, since neither is a standalone document.

### Implementation
- Repurposed `Config::appUrl()` (previously the GitHub project link, unused elsewhere) as the
  deployment's public base URL used to build canonical/schema URLs; defaults to empty so nothing
  is emitted until configured.
- Added `Support\Seo\CanonicalUrlBuilder`, `WebPageSchemaBuilder`, `DescriptionTruncator`, and
  `PageSeoContextBuilder` (core, framework-agnostic) plus a `renderPage()` helper in
  `public/index.php` that merges the computed overrides into every content route's view-model
  before rendering — centralizing the cross-cutting mechanics in one place instead of duplicating
  them across all nine controllers.
- Extracted `Router::RESOURCE_SEGMENTS` as a shared, reusable route↔path-segment table.
- JSON-LD is encoded with `JSON_HEX_*` flags so user-authored dive/site/comment text can't break
  out of the `<script>` block.
- The `noindex` signal is sent both as a `<meta name="robots">` tag and an `X-Robots-Tag` HTTP
  header, since some routes (the embeddable summary fragment) have no `<head>` at all.

### Tests
- Added unit tests for all four new `Support\Seo\` classes (URL construction in both routing
  modes, JSON-LD structure and injection-safety, description truncation, and the opt-out/summary
  branching logic).
- Extended `WebSmokeTest` with 7 HTTP-level tests proving genuine per-page uniqueness (two
  different dives/sites render different title/description/canonical), JSON-LD validity matching
  canonical/title, canonical mode-awareness, and opt-out behavior.
- Manual structured-data validation: no publicly-reachable deployment exists to run through
  Google's Rich Results Test / the schema.org validator against, so an equivalent rigorous
  structural check was run locally instead — 27 real rendered pages across all 9 controllers (plus
  the opt-out and query-string-mode cases) were confirmed to produce valid, parseable JSON-LD with
  the required `@context`/`@type`/`url`/`name` properties, `url` matching the page's canonical
  link, and `name` matching its `<title>`. Full gate (`composer test && composer stan && composer
  cs`) green.

## v4.1.2 — 2026-07-06

### Seamless dive detail navigation (in-place AJAX)
- Switching dives (Logbook sidebar and prev/next arrows) now swaps the detail content in place
  instead of a full page reload — no jump to top, window and sidebar scroll positions preserved.
- The address bar, page title, sidebar active item, and prev/next arrows always reflect the shown
  dive; browser Back/Forward restores the correct dive in place.
- Direct load, refresh, and deep links still render the full server-side page (progressive
  enhancement); with JavaScript disabled, navigation falls back to normal full-page loads.
- Any fetch/server error during a swap falls back gracefully to a full-page navigation.

### Implementation
- Factored the dive detail body into a shared partial (`templates/partials/dive_detail_content.html.twig`)
  used by both the full page and a new bare fragment template (`dive_detail_partial.html.twig`).
- Added a partial-vs-full dispatch in the front controller (`X-Requested-With: XMLHttpRequest`,
  with a `?partial=1` fallback); 404 handling shared with the full page.
- Added `public/assets/js/dive-detail-nav.js` (dependency-free) to intercept navigation, fetch the
  fragment, swap the hero and content column, update chrome/history, and re-initialize dynamic content.
- Refactored `profile-chart.js` into an idempotent `window.DivelogProfileChart.init()` so the dive
  profile chart re-initializes after each swap (single guarded `themechange` listener); the pictures
  lightbox already works via event delegation.
- Removed the earlier reload-based scroll-restore workaround now that swapping is seamless.

### Tests
- Extended the HTTP smoke-test helper to send request headers.
- Added smoke tests for the partial fragment (shell absent), the full page (includes the nav script),
  and partial not-found; full gate green (`composer test && composer stan && composer cs`).

## v4.1.1 — 2026-07-06

### Dive log gallery (new aggregate photo view)
- Added a new paginated Dive Log Gallery at `/gallery` (while preserving per-dive gallery at `/gallery/{id}`).
- Added `templates/dive_log_gallery.html.twig` with lightbox-enabled thumbnails and pagination.
- Added per-thumbnail contextual labels (site + location), e.g. `Nakayukui · Japan, Okinawa`.
- Added a primary navigation link for Gallery.

### Lightbox enhancements
- Added grouped previous/next navigation and keyboard arrow support across gallery images.
- Added an in-dialog dive info block for aggregate gallery photos:
  - line 1: `Dive <number> by <diver>`
  - line 2: `Location | Divesite | When`
  - deep link: `view dive`
- Added responsive dialog/image sizing so large images use available viewport space more effectively.

### Data/repository compatibility hardening
- Extended `PictureRepository` pagination with schema fallbacks for legacy columns:
  - supports `PictureID` and fallback `ID`
  - supports `LogID` and fallback `Number`
- Extended `DiveRepository::findMetaByLogIds()` with legacy-safe lookup fallback order:
  - `LogID` → `ID` → `Number`
- Prevented SQL fatal errors on mixed/older Diving Log schemas where canonical columns are absent.

### Reuse and wiring
- Extended `GalleryController` with aggregate overview view-model composition (count/page slice + batched metadata).
- Added `PictureRepository::countAll()` and `findPage()`.
- Added `DiveRepository::findMetaByLogIds()` for batched metadata resolution (no N+1 lookups).
- Generalized shared pagination partial with optional `basePath`.

### Tests and verification
- Added repository tests for picture pagination/counting and metadata fallback behavior across schema variants.
- Added HTTP smoke test coverage for `/gallery` rendering contract and nav discoverability.
- Quality gates green: `composer test && composer stan && composer cs` (with existing PHPUnit deprecations unchanged).

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
