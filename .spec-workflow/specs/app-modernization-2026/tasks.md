# Tasks Document

> Spec: app-modernization-2026 — big-bang rewrite of phpDivingLog into a decoupled
> PDO/PSR-4 core (`PhpDivingLog\`) + standalone Twig web adapter + JSON API. PHP 8.3+,
> Diving Log MySQL schema treated read-only. Each `_Prompt` is written for an implementing
> agent; start each by running `spec-workflow-guide`, set the task to `[-]` before starting,
> call `log-implementation` on completion, then mark `[x]`.

## Phase 1 — Project scaffolding & tooling

- [x] 1. Initialize Composer project and PSR-4 autoloading
  - File: composer.json, .gitignore (modify)
  - Define `require: php >=8.3`, PSR-4 map `PhpDivingLog\` → `src/`, dev deps (PHPUnit,
    PHPStan or Psalm, squizlabs/php_codesniffer), and Composer scripts `test`, `stan`, `cs`.
  - Add `vendor/`, `.env`, `var/` to .gitignore.
  - Purpose: Establish the modern dependency and autoload foundation (Req 1, Req 10).
  - _Leverage: existing README.md/CHANGELOG for app metadata; current PHP version target from tech.md_
  - _Requirements: 1.1, 1.2, 1.5, 10.2_
  - _Prompt: Implement the task for spec app-modernization-2026, first run spec-workflow-guide to get the workflow guide then implement the task: Role: PHP Platform Engineer specializing in Composer and PSR standards | Task: Create composer.json declaring PHP >=8.3, PSR-4 autoload for namespace PhpDivingLog\\ mapped to src/, dev dependencies for PHPUnit, a static analyzer (PHPStan or Psalm), and PHP_CodeSniffer (PSR-12), plus composer scripts test/stan/cs; update .gitignore to exclude vendor/, .env, and var/ | Restrictions: Do not add WordPress, Smarty, or any web framework; keep the dependency tree minimal; do not commit vendor/ | Success: composer validate passes, composer install works, autoloading resolves a PhpDivingLog\\ stub class, and the three scripts are invocable_

- [x] 2. Create target directory skeleton and quality-tool configs
  - File: phpstan.neon (or psalm.xml), phpcs.xml, plus `.gitkeep` placeholders for src/, adapters/web/, adapters/api/, public/, templates/, resources/lang/, tests/, var/cache/, var/log/, docs/
  - Configure static analysis level and PSR-12 ruleset scoped to src/ and adapters/.
  - Purpose: Lay down the layered structure from design + enforce quality gates (Req 1, Req 10, structure.md).
  - _Leverage: design.md "Directory Layout (target)" section_
  - _Requirements: 1.1, 10.2, 10.4_
  - _Prompt: Implement the task for spec app-modernization-2026, first run spec-workflow-guide to get the workflow guide then implement the task: Role: DevEx Engineer focused on PHP tooling | Task: Create the target directory skeleton exactly as specified in design.md and add phpstan.neon (or psalm.xml) and phpcs.xml configured for PSR-12 over src/ and adapters/ | Restrictions: Do not place source in the legacy root; keep var/ writable and git-ignored except .gitkeep; do not lower analysis below an agreed baseline | Success: Directories exist, phpcs and the analyzer run cleanly on the empty skeleton via composer scripts_

## Phase 2 — Configuration & database

- [x] 3. Implement env-based configuration loader
  - File: src/Support/Config.php, config/config.php, .env.example
  - Load DB DSN/credentials and all behavioral options (table_prefix, unit flags, date/coord
    formats, decimal separator, list sizes, media paths, feature toggles, debug) from env with
    typed accessors and validation; document every option in .env.example.
  - Purpose: Modern, secret-safe configuration mirroring config.inc.php coverage (Req 7).
  - _Leverage: config.inc.php.example and settings.php (option inventory)_
  - _Requirements: 7.1, 7.2, 7.3, 7.4_
  - _Prompt: Implement the task for spec app-modernization-2026, first run spec-workflow-guide to get the workflow guide then implement the task: Role: Backend Developer specializing in configuration and 12-factor apps | Task: Build a typed Config class that reads environment variables (with an optional untracked config file) and exposes validated accessors for every option currently in config.inc.php/settings.php; provide a fully documented .env.example | Restrictions: Never commit secrets; do not read superglobals; fail fast with a typed ConfigException on missing required DB settings without echoing values | Success: Config validates required keys, exposes typed getters, .env.example documents all options, and unit tests cover missing/invalid config_

- [x] 4. Implement PDO Connection factory
  - File: src/Database/Connection.php
  - Build a configured PDO (ERRMODE_EXCEPTION, FETCH_ASSOC, emulate_prepares=false, utf8mb4)
    from Config; provide a table-prefix accessor with allow-list validation (`^[A-Za-z0-9_]*$`).
  - Purpose: Replace wpdb with a single PDO entry point (Req 2).
  - _Leverage: config.inc.php database settings semantics_
  - _Requirements: 2.1, 2.4, 2.6_
  - _Prompt: Implement the task for spec app-modernization-2026, first run spec-workflow-guide to get the workflow guide then implement the task: Role: Database Engineer with PDO expertise | Task: Implement Connection::fromConfig(Config) returning a hardened PDO instance and a validated table-prefix helper used to compose table names safely | Restrictions: Do not use wpdb or wp-db.php; never interpolate unvalidated prefixes; keep credentials out of exceptions/logs | Success: PDO connects with the specified attributes, prefix validation rejects illegal characters, and errors do not leak credentials_

## Phase 3 — Domain models (DTOs)

- [x] 5. Create core domain model DTOs (dive, location, trip group)
  - File: src/Model/Dive.php, DiveSite.php, Country.php, City.php, Shop.php, Trip.php
  - Immutable read-only DTOs with typed properties mapping Diving Log columns to clean names.
  - Purpose: Typed data carriers decoupled from raw column names (Req 1, Req 3).
  - _Leverage: design.md "Data Models"; sql/onedive.sql, onetrip.sql, onecountry.sql_
  - _Requirements: 1.4, 3.1_
  - _Prompt: Implement the task for spec app-modernization-2026, first run spec-workflow-guide to get the workflow guide then implement the task: Role: PHP Developer specializing in domain modeling | Task: Create immutable DTO classes for Dive, DiveSite, Country, City, Shop, and Trip with typed properties as defined in design.md Data Models | Restrictions: DTOs must not access the database, emit HTML, or read superglobals; keep them framework-agnostic | Success: Classes are strict-typed, immutable, and represent the fields listed in the design_

- [x] 6. Create remaining domain model DTOs (equipment, people, media, stats, meta)
  - File: src/Model/Equipment.php, Buddy.php, Picture.php, Tank.php, Stats.php, AppInfo.php, Personal.php, UserDefinedField.php
  - Typed read-only DTOs per design Data Models.
  - Purpose: Complete the model layer (Req 3).
  - _Leverage: sql/oneequipment.sql, buddies.sql, divepics.sql, divestats.sql, personal.sql, userdefined.sql_
  - _Requirements: 3.1, 3.2_
  - _Prompt: Implement the task for spec app-modernization-2026, first run spec-workflow-guide to get the workflow guide then implement the task: Role: PHP Developer specializing in domain modeling | Task: Create immutable DTOs for Equipment, Buddy, Picture, Tank, Stats, AppInfo, Personal, and UserDefinedField matching design.md | Restrictions: No DB access or presentation logic in models; keep types strict | Success: All DTOs compile, are immutable, and cover the fields in the design_

## Phase 4 — Support services (core, reusable by both adapters)

- [x] 7. Implement UnitConverter service
  - File: src/Support/UnitConverter.php, tests/Support/UnitConverterTest.php
  - Convert depth, pressure, weight, temperature, volume metric↔imperial per Config flags; expose unit labels.
  - Purpose: Preserve metric-storage/display-conversion behavior (Req 8).
  - _Leverage: conversion logic in classes.inc.php / includes/misc.inc.php as behavioral reference_
  - _Requirements: 8.1, 8.4, 10.1_
  - _Prompt: Implement the task for spec app-modernization-2026, first run spec-workflow-guide to get the workflow guide then implement the task: Role: PHP Developer with numeric/domain conversion expertise | Task: Implement UnitConverter covering all five measures in both directions driven by Config, plus unit labels, and write PHPUnit tests for edge values | Restrictions: Storage stays metric; conversion happens only at display; no rounding that loses required precision | Success: All conversions match the legacy behavior, labels correct, tests pass with good coverage_

- [x] 8. Implement Formatter service (dates, coordinates, decimals)
  - File: src/Support/Formatter.php, tests/Support/FormatterTest.php
  - Format dates (configurable), coordinates (`d`/`dm`/`dms`), and decimal separators.
  - Purpose: Preserve coordinate/date/decimal formatting (Req 8).
  - _Leverage: coord/date formatting in classes.inc.php; config coord_format/date_format/decsep_
  - _Requirements: 8.2, 8.4, 10.1_
  - _Prompt: Implement the task for spec app-modernization-2026, first run spec-workflow-guide to get the workflow guide then implement the task: Role: PHP Developer specializing in i18n formatting | Task: Implement Formatter for dates, coordinates (d/dm/dms), and decimal separators per Config, with PHPUnit tests | Restrictions: Use DateTimeImmutable; do not hardcode locale; keep formatting pure and side-effect free | Success: All formats match legacy output, tests cover each coord mode and date/decimal option_

- [x] 9. Implement Translator service and normalize language resources
  - File: src/Support/Translator.php, resources/lang/english.php, tests/Support/TranslatorTest.php
  - Load UI strings for the configured language with a key lookup and English fallback; port at least the English language file to the normalized format.
  - Purpose: Preserve multi-language capability (Req 8).
  - _Leverage: includes/languages/english.inc.php (source strings)_
  - _Requirements: 8.3, 8.4_
  - _Prompt: Implement the task for spec app-modernization-2026, first run spec-workflow-guide to get the workflow guide then implement the task: Role: PHP Developer with localization experience | Task: Implement Translator with get(key, params) and English fallback, and convert the existing English language file into the normalized resources/lang format | Restrictions: Do not include() legacy language files at runtime; keep the loader safe against missing keys | Success: Translator returns correct strings, falls back gracefully, and tests cover lookup + fallback + interpolation_

- [x] 10. Implement RtfConverter and HtmlSanitizer
  - File: src/Support/RtfConverter.php, src/Support/HtmlSanitizer.php, tests/Support/RtfConverterTest.php, composer.json (add deps)
  - Convert Diving Log RTF comments to sanitized HTML using a maintained RTF package (or a small tested converter) plus an HTML sanitizer library; degrade to plain text on parse failure.
  - Purpose: Replace legacy RTFClass with a safe modern path (Req 5, Req 9).
  - _Leverage: RTFClass behavior as reference; design Error Handling scenario 5_
  - _Requirements: 5.4, 5.6, 9.2_
  - _Prompt: Implement the task for spec app-modernization-2026, first run spec-workflow-guide to get the workflow guide then implement the task: Role: Security-minded PHP Developer | Task: Implement RtfConverter::toHtml returning sanitized HTML (via HtmlSanitizer wrapping a maintained sanitizer) and add the required Composer dependencies; handle malformed RTF by degrading to escaped plain text | Restrictions: Never output unsanitized HTML; do not reuse the legacy RTFClass; keep the sanitizer allow-list conservative | Success: Formatting preserved for valid RTF, hostile input is neutralized (XSS tests pass), malformed input degrades safely_

- [x] 11. Implement MediaResolver service (safe paths + thumbnails)
  - File: src/Support/MediaResolver.php, tests/Support/MediaResolverTest.php
  - Map DB filenames to safe web/file paths for pictures/thumbs/maps/flags/equipment, reject traversal outside configured roots, generate thumbnails (GD/Imagick), and fall back to the configured missing-image asset.
  - Purpose: Preserve media features with path-traversal safety (Req 5, Req 9).
  - _Leverage: settings.php media paths, thumb-width/height; README picture conventions_
  - _Requirements: 5.3, 9.3, 3.1_
  - _Prompt: Implement the task for spec app-modernization-2026, first run spec-workflow-guide to get the workflow guide then implement the task: Role: PHP Developer with image handling and security expertise | Task: Implement MediaResolver providing pictureUrl/thumbUrl/mapUrl/flagUrl/equipmentUrl, on-demand thumbnail generation with caching under var/cache, path-traversal rejection, and a missing-image fallback | Restrictions: Resolve and confirm every path stays within configured media roots; never trust raw DB filenames; cache thumbnails, do not regenerate each request | Success: Valid media resolves correctly, traversal attempts are rejected (tests), thumbnails cache, and missing files return the placeholder_

## Phase 5 — Repositories (PDO, read-only, prepared statements)

- [x] 12. Implement DiveRepository and StatsRepository
  - File: src/Repository/DiveRepository.php, src/Repository/StatsRepository.php
  - Port onedive/divelist/divelocations and divestats to prepared statements returning DTOs.
  - Purpose: Core dive data + statistics access (Req 2, Req 3).
  - _Leverage: sql/onedive.sql, divelist.sql, divelocations.sql, divestats.sql_
  - _Requirements: 2.2, 2.3, 2.4, 2.5, 3.1_
  - _Prompt: Implement the task for spec app-modernization-2026, first run spec-workflow-guide to get the workflow guide then implement the task: Role: Database Engineer with PDO expertise | Task: Implement DiveRepository (findByNumber, listNumbers with limit/offset, listByPlace) and StatsRepository (aggregate) using prepared statements and returning Dive/Stats DTOs, honoring the validated table prefix | Restrictions: No string-concatenated user values; read-only queries only; the prefix is the only interpolated identifier and must be pre-validated | Success: Methods return correct DTOs, bind all parameters, honor the prefix, and return null/empty appropriately_

- [x] 13. Implement location repositories (DiveSite, Country, City, Shop)
  - File: src/Repository/DiveSiteRepository.php, CountryRepository.php, CityRepository.php, ShopRepository.php
  - Port location/country/city/shop overview + detail queries to prepared statements.
  - Purpose: Location browsing data (Req 3).
  - _Leverage: sql/onecountry.sql, onecity.sql, oneplace.sql, countrycities.sql, countrylist.sql, shoplist.sql, sitelist.sql_
  - _Requirements: 2.2, 2.3, 2.4, 3.1, 3.3_
  - _Prompt: Implement the task for spec app-modernization-2026, first run spec-workflow-guide to get the workflow guide then implement the task: Role: Database Engineer with PDO expertise | Task: Implement DiveSite/Country/City/Shop repositories with overview list + single-item methods returning DTOs, porting the corresponding sql/*.sql logic to prepared statements | Restrictions: Read-only; parameter binding for all values; preserve cross-entity relationships (country↔city↔shop) | Success: Overviews and details return correct DTOs, relationships resolve, and the prefix is honored_

- [x] 14. Implement TripRepository and EquipmentRepository
  - File: src/Repository/TripRepository.php, src/Repository/EquipmentRepository.php
  - Port trip (Trip⨝Country⨝Shop) and equipment (list + one + service-due) queries.
  - Purpose: Trips and equipment data incl. service reminders (Req 3).
  - _Leverage: sql/onetrip.sql, triplist.sql, tripdives.sql, oneequipment.sql, gearlist.sql, equipservice.sql_
  - _Requirements: 2.2, 2.3, 3.1, 3.4_
  - _Prompt: Implement the task for spec app-modernization-2026, first run spec-workflow-guide to get the workflow guide then implement the task: Role: Database Engineer with PDO expertise | Task: Implement TripRepository (findById with Country/Shop joins, list, dives-in-trip) and EquipmentRepository (list, findById, service-due lookup) returning DTOs | Restrictions: Read-only, prepared statements, honor prefix; keep JOIN logic equivalent to the legacy SQL | Success: Trip joins produce the joined DTO fields, equipment service-due logic matches config-driven reminders, tests-ready methods return correct data_

- [x] 15. Implement supporting repositories (Buddy, Picture, Tank, UserDefined, Personal, AppInfo)
  - File: src/Repository/BuddyRepository.php, PictureRepository.php, TankRepository.php, UserDefinedRepository.php, PersonalRepository.php, AppInfoRepository.php
  - Port buddies/divepics/onetank/userdefined/personal/dbinfo queries to prepared statements.
  - Purpose: Complete dive-detail composition and app metadata (Req 3).
  - _Leverage: sql/buddies.sql, divepics.sql, onetank.sql, userdefined.sql, personal.sql, dbinfo.sql_
  - _Requirements: 2.2, 2.3, 3.1, 3.2_
  - _Prompt: Implement the task for spec app-modernization-2026, first run spec-workflow-guide to get the workflow guide then implement the task: Role: Database Engineer with PDO expertise | Task: Implement the supporting repositories returning their DTOs, including BuddyRepository::findByIds using a safely-bound IN-list | Restrictions: Read-only; bind every value including IN-list elements individually; honor prefix | Success: Each repository returns correct DTOs, the buddy IN-list is injection-safe, and app/personal metadata loads_

- [x] 16. Write repository integration tests with seeded fixtures
  - File: tests/Repository/*Test.php, tests/fixtures/schema.sql, tests/fixtures/seed.sql, tests/bootstrap.php
  - Create a fixture schema mirroring the Diving Log tables and seed data; test each repository method (correct rows, prefix honored, joins, not-found → null, IN-list safety).
  - Purpose: Verify data layer without a live Diving Log export (Req 10).
  - _Leverage: all sql/*.sql for column/table shape; design Testing Strategy_
  - _Requirements: 10.1, 10.3, 2.2, 2.4_
  - _Prompt: Implement the task for spec app-modernization-2026, first run spec-workflow-guide to get the workflow guide then implement the task: Role: QA Engineer specializing in database integration testing | Task: Build fixture schema+seed mirroring the Diving Log tables and PHPUnit integration tests covering every repository method including prefix handling, joins, empty results, and IN-list binding | Restrictions: Tests must not depend on a real Diving Log export; keep fixtures minimal but representative; isolate test DB state | Success: All repository methods are covered, tests run from a documented command, and pass deterministically_

## Phase 6 — Standalone web adapter (routing, controllers, Twig)

- [x] 17. Implement web front controller, router, and DI bootstrap
  - File: public/index.php, adapters/web/bootstrap.php, adapters/web/Router.php
  - Front controller wires Config, PDO, repositories, services, Twig; router maps pretty URLs
    with a query-string fallback to controller actions; central error handler.
  - Purpose: Standalone app entry point + routing (Req 1, Req 11).
  - _Leverage: legacy entry-point request-type branching; config query_string fallback_
  - _Requirements: 1.4, 11.1, 11.2, 9.4_
  - _Prompt: Implement the task for spec app-modernization-2026, first run spec-workflow-guide to get the workflow guide then implement the task: Role: Backend Developer specializing in PHP routing and bootstrapping | Task: Implement the web front controller, a small router supporting pretty URLs and a query-string fallback, and a bootstrap that constructs core services and injects them into controllers, with a central error handler that logs details and renders generic errors | Restrictions: Adapters may read superglobals but the core must not; no framework; production errors must not leak internals | Success: Requests route correctly in both URL modes, services are injected, and errors render generic pages while logging server-side_

- [x] 18. Implement TwigRenderer and base layout/partials
  - File: adapters/web/TwigRenderer.php, templates/layout.html.twig, templates/partials/ (header, footer, nav, table, pagination).html.twig
  - Configure Twig with auto-escaping and var/cache; build the shared layout and reusable partials.
  - Purpose: Twig presentation foundation replacing Smarty (Req 4).
  - _Leverage: tpl/header*.tpl, footer*.tpl, links_*.tpl, datatable.tpl as structure reference_
  - _Requirements: 4.1, 4.2, 4.3, 4.4_
  - _Prompt: Implement the task for spec app-modernization-2026, first run spec-workflow-guide to get the workflow guide then implement the task: Role: Frontend/PHP Developer with Twig expertise | Task: Implement a TwigRenderer (auto-escaping on, cache in var/cache) and create layout.html.twig plus header/footer/nav/table/pagination partials mirroring the legacy information architecture | Restrictions: Auto-escaping must stay enabled; no domain logic in templates; do not reintroduce Smarty | Success: Twig renders the layout with working partials, escaping is on by default, and templates compile/cache_

- [x] 19. Implement Dive controllers and templates (overview + detail + profile data)
  - File: adapters/web/Controller/DiveController.php, ProfileController.php, templates/dives_overview.html.twig, dive_detail.html.twig
  - Dive list (paginated/sortable) and full dive detail (main, buddies, conditions, breathing,
    equipment, comments via RtfConverter, user-defined fields, profile); ProfileController emits
    the depth/time series as JSON for the chart.
  - Purpose: Highest-value view parity (Req 3).
  - _Leverage: index.php branching; tpl/dive_details.tpl, dives_overview.tpl; drawprofile.php_
  - _Requirements: 3.1, 3.2, 3.3, 5.2, 8.1_
  - _Prompt: Implement the task for spec app-modernization-2026, first run spec-workflow-guide to get the workflow guide then implement the task: Role: Full-stack PHP Developer | Task: Implement DiveController (overview + detail) and ProfileController (JSON depth/time series), plus their Twig templates, composing Dive/Buddy/Picture/Tank/UserDefined data and applying UnitConverter/Formatter/RtfConverter/MediaResolver | Restrictions: Controllers call core only (no SQL); comments must be sanitized; measurements via UnitConverter | Success: Overview lists and links dives, detail page shows all legacy sections, and the profile endpoint returns valid series data_

- [x] 20. Implement location controllers and templates (sites, countries, cities, shops)
  - File: adapters/web/Controller/ (DiveSite, Country, City, Shop)Controller.php, templates/ divesite/divecountry/divecity/diveshop overview + detail .html.twig
  - Overview + detail pages with cross-navigation, coordinate formatting, and map/flag images.
  - Purpose: Location browsing parity (Req 3).
  - _Leverage: divesite.php, divecountry.php, divecity.php, diveshop.php; matching tpl/*.tpl_
  - _Requirements: 3.1, 3.3, 8.2_
  - _Prompt: Implement the task for spec app-modernization-2026, first run spec-workflow-guide to get the workflow guide then implement the task: Role: Full-stack PHP Developer | Task: Implement site/country/city/shop controllers and Twig templates with overview+detail, cross-links, coordinate formatting via Formatter, and map/flag images via MediaResolver | Restrictions: No SQL in controllers; escape all output; preserve legacy navigation between entities | Success: All four sections render overviews and details with working cross-links and correctly formatted coordinates/images_

- [x] 21. Implement trip, equipment, stats, gallery, and summary controllers and templates
  - File: adapters/web/Controller/ (Trip, Equipment, Stats, Gallery, Summary)Controller.php, templates/ divetrip/equipment/divestats/divegallery/divesummary .html.twig
  - Trip overview/detail, equipment overview/detail with service reminders, statistics page,
    photo gallery with lightbox, and the embeddable dive summary.
  - Purpose: Remaining view parity incl. embeddable summary (Req 3).
  - _Leverage: divetrip.php, equipment.php, divestats.php, divegallery.php, divesummary.php; matching tpl/*.tpl_
  - _Requirements: 3.1, 3.4, 5.3_
  - _Prompt: Implement the task for spec app-modernization-2026, first run spec-workflow-guide to get the workflow guide then implement the task: Role: Full-stack PHP Developer | Task: Implement trip/equipment/stats/gallery/summary controllers and Twig templates, including equipment service reminders and the embeddable summary output | Restrictions: Controllers use core only; gallery images via MediaResolver; keep the summary embeddable/minimal | Success: All five views render with parity, service reminders appear per config, gallery lightbox works, and the summary is embeddable_

## Phase 7 — Front-end assets (no jQuery)

- [x] 22. Implement modern table interactivity and image lightbox (vanilla JS)
  - File: public/assets/js/tables.js, public/assets/js/lightbox.js, public/assets/css/app.css
  - Sortable/paged tables via a maintained lightweight lib or native vanilla JS; image overlay
    via native `<dialog>`/CSS or a tiny maintained lightbox.
  - Purpose: Replace jQuery/DataTables/Highslide (Req 5).
  - _Leverage: datatable.tpl behavior; gallery markup from task 21_
  - _Requirements: 5.1, 5.3, 5.5_
  - _Prompt: Implement the task for spec app-modernization-2026, first run spec-workflow-guide to get the workflow guide then implement the task: Role: Frontend Developer specializing in vanilla JS and accessibility | Task: Implement dependency-free (no jQuery) sortable/paged tables and an accessible image lightbox using native dialog/CSS or a tiny maintained lib, wired to the Twig table/gallery markup | Restrictions: No jQuery/DataTables/Highslide; ensure keyboard accessibility; keep assets small | Success: Tables sort/page and images open in an accessible overlay with no jQuery on the page_

- [x] 23. Implement dive profile chart (modern charting lib)
  - File: public/assets/js/profile-chart.js, template include in dive_detail.html.twig (modify)
  - Render the dive profile from the ProfileController JSON using a maintained chart library
    (e.g. Chart.js or uPlot); honor unit labels; optional dual metric/imperial axes per config.
  - Purpose: Replace jqPlot (Req 5).
  - _Leverage: drawprofile.php scale/axis logic; ProfileController from task 19_
  - _Requirements: 5.2, 5.5, 8.1_
  - _Prompt: Implement the task for spec app-modernization-2026, first run spec-workflow-guide to get the workflow guide then implement the task: Role: Frontend Developer with data-viz experience | Task: Render the dive profile chart from the profile JSON using a maintained charting library, supporting configurable single/dual axis and metric/imperial labels, and wire it into the dive detail template | Restrictions: No jqPlot/jQuery; fetch series from the profile endpoint; keep the bundle lean | Success: The profile renders correctly with proper axes/labels and matches legacy scale behavior_

- [x] 24. Document optional asset build and vendor the runtime assets
  - File: package.json (optional build), public/assets/vendor/* (or documented CDN-less vendoring), docs/frontend-build.md
  - Provide an optional npm build to bundle/minify while ensuring the app runs with pre-built
    assets and no Node at request time.
  - Purpose: Host-friendly asset delivery (Req 5, Req 11).
  - _Leverage: design "Front-end assets" delivery note_
  - _Requirements: 5.5, 11.2, 11.3_
  - _Prompt: Implement the task for spec app-modernization-2026, first run spec-workflow-guide to get the workflow guide then implement the task: Role: Frontend Build Engineer | Task: Add an optional npm build for bundling/minifying assets and vendor the runtime JS/CSS so the app works without a Node runtime; document the workflow in docs/frontend-build.md | Restrictions: Runtime must not require Node; do not rely on external CDNs by default; keep vendored assets versioned | Success: App runs with committed/pre-built assets, and the optional build reproduces them_

## Phase 8 — JSON API adapter

- [x] 25. Implement API front controller, router, and JSON error handling
  - File: public/api.php, adapters/api/bootstrap.php, adapters/api/Router.php, adapters/api/JsonResponse.php
  - Route `/api/*` to JSON controllers reusing the same core; structured JSON errors with 4xx/5xx; read-only, prefix/multi-user aware.
  - Purpose: JSON API foundation (Req 6).
  - _Leverage: web bootstrap from task 17 (shared core wiring)_
  - _Requirements: 6.1, 6.2, 6.3, 6.5_
  - _Prompt: Implement the task for spec app-modernization-2026, first run spec-workflow-guide to get the workflow guide then implement the task: Role: API Engineer specializing in PHP JSON services | Task: Implement the API front controller, router, and a JsonResponse helper with structured error envelopes and correct status codes, reusing the same core services as the web adapter | Restrictions: Reuse core repositories (no duplicated SQL); never leak internals in errors; read-only | Success: /api routes resolve, responses set application/json with correct status codes, and errors return the `{ error: { code, message } }` shape_

- [x] 26. Implement JSON resource controllers and document the API
  - File: adapters/api/Controller/*.php, docs/api.md, docs/wordpress-shim.md
  - Endpoints for dives, sites, countries, cities, shops, trips, equipment, stats (collection +
    item); serialize DTOs to JSON; document the endpoint shape and a future WP shim consumer.
  - Purpose: Complete API + future-adapter guidance (Req 6).
  - _Leverage: repositories from Phase 5; DTOs from Phase 3; design API interfaces_
  - _Requirements: 6.1, 6.2, 6.3, 6.4_
  - _Prompt: Implement the task for spec app-modernization-2026, first run spec-workflow-guide to get the workflow guide then implement the task: Role: API Engineer | Task: Implement JSON controllers for all listed resources (collection + item) serializing DTOs, and write docs/api.md plus a docs/wordpress-shim.md describing how a WP shortcode would consume the API | Restrictions: Reuse core; consistent JSON shapes; 404 for unknown items; do not build the WP plugin itself | Success: Every endpoint returns correct JSON, unknown items 404, and the docs fully describe the endpoint shape and WP consumption pattern_

## Phase 9 — Quality gates, deployment & cutover

- [x] 27. Add HTTP-level smoke tests and wire the quality gate commands
  - File: tests/Http/*Test.php, docs/testing.md, composer.json (finalize scripts)
  - Smoke tests hitting each web view (200 + key content, 404 for unknown ids) and each API
    endpoint (JSON shape); document running PHPUnit + static analysis + PSR-12 together.
  - Purpose: End-to-end verification and CI-equivalent gate (Req 10).
  - _Leverage: fixtures from task 16; controllers/endpoints from Phases 6/8_
  - _Requirements: 10.1, 10.2, 10.4_
  - _Prompt: Implement the task for spec app-modernization-2026, first run spec-workflow-guide to get the workflow guide then implement the task: Role: QA Automation Engineer | Task: Implement HTTP smoke tests for every web view and API endpoint using the seeded fixtures, and document the combined test/stan/cs gate in docs/testing.md and composer scripts | Restrictions: Tests must be deterministic and fixture-backed; assert generic error behavior for unknown ids; do not depend on production data | Success: Smoke tests cover all views/endpoints, the gate runs via documented commands, and all checks pass_

- [x] 28. Write deployment docs and web-server configs (Nginx + Apache, both hosting modes)
  - File: docs/deployment.md, docs/nginx.conf.example, docs/apache-htaccess.example, .env.example (finalize)
  - Document standalone-service and shared-hosting deploys, front-controller rewrite + query-string fallback, required writable dirs, and vendored-dependency/release steps.
  - Purpose: Deployability on service infra and shared hosting (Req 11).
  - _Leverage: current .htaccess; design Deployment section; Config from task 3_
  - _Requirements: 11.1, 11.2, 11.3, 11.4_
  - _Prompt: Implement the task for spec app-modernization-2026, first run spec-workflow-guide to get the workflow guide then implement the task: Role: DevOps Engineer with PHP deployment expertise | Task: Write deployment documentation and example Nginx/Apache configs for both a standalone service and shared hosting, covering the front-controller rewrite with query-string fallback, writable directories, and how vendor/ is shipped or installed | Restrictions: Do not require root/long-running processes for shared hosting; ensure the rewrite fallback is documented; keep secrets in env | Success: A reader can deploy in either mode from the docs, both server configs work, and writable-dir requirements are explicit_

- [x] 29. Retire legacy code and finalize the cutover
  - File: remove/relocate legacy `classes.inc.php`, `tpl/`, `sql/`, `includes/` (smarty, jqplot, wp-db.php, imgd.php, imgp.php), legacy root `*.php`; README.md (rewrite), CHANGELOG (update), CLAUDE.md/AGENTS.md (refresh)
  - Remove the superseded legacy stack once parity is verified; update project docs to the new architecture and run commands.
  - Purpose: Complete the big-bang rewrite and eliminate dead legacy code (Req 1, structure.md, tech.md).
  - _Leverage: parity verified by Phase 9 tests; design Directory Layout_
  - _Requirements: 1.1, 1.2, 1.3, 3.5_
  - _Prompt: Implement the task for spec app-modernization-2026, first run spec-workflow-guide to get the workflow guide then implement the task: Role: Senior PHP Developer leading a migration cutover | Task: After confirming feature parity via the Phase 9 test suite, remove the legacy Smarty/jqPlot/Highslide/wp-db/RTF stack, the monolithic classes.inc.php, tpl/, sql/, and legacy entry points, then rewrite README.md and refresh CHANGELOG/CLAUDE.md/AGENTS.md to the new architecture | Restrictions: Do not delete legacy code until parity tests pass; preserve any still-referenced static assets (images/); list any intentionally dropped features with rationale | Success: The legacy stack is gone, the app runs entirely on the new architecture with tests green, and docs describe the modernized system_
