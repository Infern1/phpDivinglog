# Tasks Document

- [x] 1. Repurpose the public base URL and add the SEO opt-out flag in src/Support/Config.php
  - File: src/Support/Config.php (extend); .env.example (extend)
  - Update `APP_URL`/`appUrl()` docs/default to describe it as the deployment's public,
    internet-facing base URL (not the GitHub project link); add `APP_SEO_ENABLED` (default
    `'true'`) to `defaults()`/`fromArray()` and a new `seoEnabled(): bool` getter using the
    existing `asBool()` helper.
  - Purpose: Give the feature a config-driven base URL and on/off switch, per the project's
    config-over-code principle.
  - _Leverage: existing `Config::asBool()`, `defaults()`, `fromArray()` pipeline_
  - _Requirements: 5.1, 5.2, 5.3, 6.3_
  - _Prompt: Implement the task for spec seo-metadata, first run spec-workflow-guide to get the workflow guide then implement the task: Role: PHP Backend Developer specializing in configuration systems | Task: Extend src/Support/Config.php per requirements 5 and 6 — update the doc/default for the existing `app_url`/`appUrl()` slot to describe it as the public base URL used for canonical/schema links, and add a new `APP_SEO_ENABLED` boolean setting (default true) with a `seoEnabled(): bool` getter following the existing `asBool()` pattern; update .env.example with both settings and clear comments (base URL must be internet-facing, not local/dev) | Restrictions: Do not break existing `appUrl()` callers; follow the existing defaults()/fromArray() structure exactly; no new parsing logic beyond the existing helpers | Success: `Config::seoEnabled()` returns true by default and reflects `APP_SEO_ENABLED`; `.env.example` documents both settings; PHPStan and PHPCS clean. Set the task to [-] in tasks.md before starting, log the implementation with log-implementation after completion, then mark it [x]._

- [x] 2. Extract the route-to-path-segment map into a reusable constant in adapters/web/Router.php
  - File: adapters/web/Router.php (refactor)
  - Replace the inline `$overviewRoutes`/`$detailRoutes` arrays in `resolve()` with a single
    `public const RESOURCE_SEGMENTS` (resource key → path segment, e.g. `'dives' => 'dives'`) that
    `resolve()` reads from; behavior unchanged.
  - Purpose: Give the new canonical URL builder a single source of truth for route↔path mapping
    instead of duplicating it.
  - _Leverage: existing resolve() route tables_
  - _Requirements: 3.3_
  - _Prompt: Implement the task for spec seo-metadata, first run spec-workflow-guide to get the workflow guide then implement the task: Role: PHP Developer refactoring routing internals | Task: Refactor adapters/web/Router.php per requirement 3 to extract the existing overview/detail resource→segment mapping into a single public const RESOURCE_SEGMENTS, with resolve() reading from it instead of two local arrays | Restrictions: Must not change resolve()'s external behavior for any existing route (query-string and path modes); keep the constant covering exactly the resources currently routed (dives, sites, countries, cities, shops, trips, equipment, gallery, stats, summary, profile as applicable) | Success: All existing routing tests still pass unchanged; RESOURCE_SEGMENTS is usable from outside Router. Set the task to [-] in tasks.md before starting, log the implementation with log-implementation after completion, then mark it [x]._

- [x] 3. Create src/Support/Seo/CanonicalUrlBuilder.php
  - File: src/Support/Seo/CanonicalUrlBuilder.php (new)
  - Implement `build(string $route, ?int $id, int $page = 1): ?string` per the design: null when
    no base URL configured or resource unmapped; query-string form (`?type=&id=&page=`) vs. path
    form (`/segment/id?page=`), with `dives.overview` page 1 mapping to the bare root `/`; page
    param appended only when `$page > 1`; search/sort query params never reflected.
  - Purpose: Single place that turns a route+id+page into the correct, mode-aware canonical URL.
  - _Leverage: Config (appUrl, queryStringMode), Router::RESOURCE_SEGMENTS_
  - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5_
  - _Prompt: Implement the task for spec seo-metadata, first run spec-workflow-guide to get the workflow guide then implement the task: Role: PHP Backend Developer | Task: Create src/Support/Seo/CanonicalUrlBuilder.php per requirement 3 with a build(route, id, page) method that returns null when Config::appUrl() is empty or the route's resource isn't in Router::RESOURCE_SEGMENTS, otherwise builds the absolute canonical URL matching Config::queryStringMode() — query form `?type=X&id=Y&page=N` or path form `/segment/id?page=N` — special-casing dives.overview page 1 to the bare root path, appending page only when greater than 1, and never reflecting search/sort params | Restrictions: Pure function of its inputs plus Config; no request/header access (avoid Host-header-derived URLs); no I/O | Success: Behaves per the design's worked examples for both modes, both route kinds, and the pagination/root special case; PHPStan and PHPCS clean. Set the task to [-] in tasks.md before starting, log the implementation with log-implementation after completion, then mark it [x]._

- [x] 4. Create src/Support/Seo/WebPageSchemaBuilder.php
  - File: src/Support/Seo/WebPageSchemaBuilder.php (new)
  - Implement `build(string $canonicalUrl, string $title, ?string $description, string $language): string`
    assembling the `schema.org` `WebPage` array (`@context`, `@type`, `url`, `name`, optional
    `description`, optional `inLanguage` via a small language-name→BCP-47 map, e.g.
    `'english' => 'en'`, omitted when unmapped) and encoding with
    `JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES`.
  - Purpose: Produce a safe-to-embed JSON-LD string for the WebPage structured data block.
  - _Leverage: PHP's built-in json_encode_
  - _Requirements: 4.1, 4.2, 4.3_
  - _Prompt: Implement the task for spec seo-metadata, first run spec-workflow-guide to get the workflow guide then implement the task: Role: PHP Backend Developer | Task: Create src/Support/Seo/WebPageSchemaBuilder.php per requirement 4 with a build(canonicalUrl, title, description, language) method returning a JSON string for a schema.org WebPage object (@context, @type: WebPage, url, name, description when non-empty, inLanguage when the language maps to a known BCP-47 tag), encoded with JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_UNESCAPED_SLASHES so embedded `</script>`-like substrings or quotes cannot break out of a script tag | Restrictions: Pure function; no HTML escaping (that's the caller's job via json_encode's hex flags only); do not guess an inLanguage value for an unmapped language name | Success: Output round-trips via json_decode to the expected structure; a title/description containing `</script>` or quotes produces no literal `</script>` in the encoded string; PHPStan and PHPCS clean. Set the task to [-] in tasks.md before starting, log the implementation with log-implementation after completion, then mark it [x]._

- [x] 5. Create src/Support/Seo/DescriptionTruncator.php
  - File: src/Support/Seo/DescriptionTruncator.php (new)
  - Implement `truncate(string $text, int $maxLength = 155): string` — returns `$text` unchanged
    when within the limit; otherwise cuts at the last word boundary before the limit and appends
    `…`, with no dangling punctuation.
  - Purpose: Enforce a search-result-friendly meta description length everywhere descriptions are
    built.
  - _Leverage: none (pure string utility)_
  - _Requirements: 2.2_
  - _Prompt: Implement the task for spec seo-metadata, first run spec-workflow-guide to get the workflow guide then implement the task: Role: PHP Developer writing small, well-tested utilities | Task: Create src/Support/Seo/DescriptionTruncator.php per requirement 2.2 with a static or instance truncate(text, maxLength = 155) method that returns text unchanged under the limit, and otherwise cuts at the last whitespace boundary before the limit, trims trailing punctuation, and appends an ellipsis | Restrictions: Must never cut mid-word; must never leave the result longer than maxLength plus the ellipsis; handle empty/short strings gracefully | Success: Behaves correctly for empty, short, exactly-at-limit, and long multi-word inputs; PHPStan and PHPCS clean. Set the task to [-] in tasks.md before starting, log the implementation with log-implementation after completion, then mark it [x]._

- [x] 6. Create src/Support/Seo/PageSeoContextBuilder.php
  - File: src/Support/Seo/PageSeoContextBuilder.php (new)
  - Implement `build(string $route, ?int $id, array $query, ?string $title, ?string $description): array`
    with the three branches from the design: global opt-out (title/meta_description forced to
    null, robots noindex), `summary.overview` (robots noindex only), otherwise (canonical + schema
    built and returned, or `[]` when canonical/title unavailable).
  - Purpose: The single orchestrator that decides which SEO override case applies per request.
  - _Leverage: Config.seoEnabled/appUrl, CanonicalUrlBuilder, WebPageSchemaBuilder_
  - _Requirements: 3, 4, 6, 7.2_
  - _Prompt: Implement the task for spec seo-metadata, first run spec-workflow-guide to get the workflow guide then implement the task: Role: PHP Backend Developer composing small services | Task: Create src/Support/Seo/PageSeoContextBuilder.php per requirements 3, 4, 6, and 7.2 with a build(route, id, query, title, description) method implementing the three cases: (a) Config::seoEnabled() false → return ['title' => null, 'meta_description' => null, 'robots' => 'noindex,nofollow']; (b) route === 'summary.overview' → return ['robots' => 'noindex,nofollow']; (c) otherwise → read page from query (default 1, clamped ≥ 1), call CanonicalUrlBuilder::build(), return [] if the canonical or the title is null, else call WebPageSchemaBuilder::build() and return ['canonical_url' => ..., 'schema_json' => ...] | Restrictions: Depend only on Config and the two other new Seo classes (constructor injection); no Twig/HTTP knowledge; do not mutate inputs | Success: Each of the three branches returns exactly the documented override keys for representative inputs; PHPStan and PHPCS clean. Set the task to [-] in tasks.md before starting, log the implementation with log-implementation after completion, then mark it [x]._

- [x] 7. Add unit tests for CanonicalUrlBuilder and WebPageSchemaBuilder
  - Files: tests/Support/Seo/CanonicalUrlBuilderTest.php (new), tests/Support/Seo/WebPageSchemaBuilderTest.php (new)
  - Cover: both routing modes, overview vs. detail, the `dives.overview` root special case,
    pagination on/off, missing base URL, unmapped resource; JSON-LD required keys, optional
    description/inLanguage, and the `</script>`/quote-safety case.
  - Purpose: Lock in correctness of the two pure URL/schema builders before wiring them in.
  - _Leverage: PHPUnit conventions already used under tests/Support/_
  - _Requirements: 3, 4, 8.2, 8.3_
  - _Prompt: Implement the task for spec seo-metadata, first run spec-workflow-guide to get the workflow guide then implement the task: Role: QA Engineer with PHPUnit expertise | Task: Add tests/Support/Seo/CanonicalUrlBuilderTest.php and tests/Support/Seo/WebPageSchemaBuilderTest.php per requirements 3, 4, and 8, covering CanonicalUrlBuilder's query-string vs path modes, overview/detail forms, the dives.overview root special case, pagination inclusion rules, missing-base-URL and unmapped-resource null cases, and WebPageSchemaBuilder's required JSON-LD keys, optional description/inLanguage inclusion, and safe encoding of a title/description containing `</script>` or quotes (assert no literal `</script>` in the encoded string and that json_decode round-trips correctly) | Restrictions: Pure unit tests, no HTTP/DB fixtures needed; construct Config directly with test values | Success: All new tests pass under `composer test`; failures would catch a regression in URL or schema construction. Set the task to [-] in tasks.md before starting, log the implementation with log-implementation after completion, then mark it [x]._

- [x] 8. Add unit tests for DescriptionTruncator and PageSeoContextBuilder
  - Files: tests/Support/Seo/DescriptionTruncatorTest.php (new), tests/Support/Seo/PageSeoContextBuilderTest.php (new)
  - Cover: truncation boundary cases; the three PageSeoContextBuilder branches (opted-out, summary
    route, normal) each returning exactly the documented keys.
  - Purpose: Lock in correctness of the truncation helper and the orchestrator's branching logic.
  - _Leverage: PHPUnit conventions already used under tests/Support/_
  - _Requirements: 2.2, 6, 7.2, 8.4_
  - _Prompt: Implement the task for spec seo-metadata, first run spec-workflow-guide to get the workflow guide then implement the task: Role: QA Engineer with PHPUnit expertise | Task: Add tests/Support/Seo/DescriptionTruncatorTest.php and tests/Support/Seo/PageSeoContextBuilderTest.php per requirements 2.2, 6, 7.2, and 8, covering truncate() for empty/short/exactly-at-limit/long inputs (no mid-word cuts, no dangling punctuation), and PageSeoContextBuilder::build() for the opted-out case (title/meta_description null, robots noindex), the summary.overview case (robots noindex only, no canonical/schema keys), and the normal case (canonical_url + schema_json present and consistent) | Restrictions: Pure unit tests; construct Config/collaborators directly with test doubles or real instances as convenient | Success: All new tests pass under `composer test` and pin down the exact override keys for each branch. Set the task to [-] in tasks.md before starting, log the implementation with log-implementation after completion, then mark it [x]._

- [x] 9. Wire the new Seo services into adapters/web/bootstrap.php
  - File: adapters/web/bootstrap.php (extend)
  - Construct `CanonicalUrlBuilder`, `WebPageSchemaBuilder`, and `PageSeoContextBuilder` (injecting
    the first two) and add them to the returned container's `services` array.
  - Purpose: Make the orchestrator available to the front controller without it constructing
    dependencies itself.
  - _Leverage: existing services-array wiring pattern in bootstrap.php_
  - _Requirements: 3, 4, 5, 6, 7.2_
  - _Prompt: Implement the task for spec seo-metadata, first run spec-workflow-guide to get the workflow guide then implement the task: Role: PHP Developer wiring application services | Task: Extend adapters/web/bootstrap.php per requirements 3-7 to construct CanonicalUrlBuilder(config), WebPageSchemaBuilder(), and PageSeoContextBuilder(config, canonicalUrlBuilder, webPageSchemaBuilder), adding them to the container's services array following the existing style (e.g. alongside 'formatter', 'mediaResolver') | Restrictions: Do not change any existing service construction or the container's shape beyond additions; keep constructor argument order matching each class's actual signature | Success: The container exposes a working pageSeoContextBuilder service usable by public/index.php; existing services unaffected. Set the task to [-] in tasks.md before starting, log the implementation with log-implementation after completion, then mark it [x]._

- [x] 10. Add the renderPage() helper and apply SEO overrides in public/index.php
  - File: public/index.php (extend)
  - Introduce a local `$renderPage` closure that calls `$seoContextBuilder->build($route, $id,
    $_GET, $payload['title'] ?? null, $payload['meta_description'] ?? null)` and
    `array_merge($payload, $overrides)` before calling `$renderer->render()`; replace each content
    page's `echo $renderer->render($template, $payload); return;` with
    `echo $renderPage($template, $payload, $match['route'], $match['id']); return;`. Leave
    `profile.detail` (JSON) and `not-found` (plain text) untouched.
  - Purpose: Apply the centralized SEO logic uniformly across every content route with a minimal,
    mechanical change to the front controller.
  - _Leverage: PageSeoContextBuilder from the container; existing $match/$_GET already available_
  - _Requirements: 1.1, 3, 4, 6, 7.1, 7.2, 7.3, 7.4_
  - _Prompt: Implement the task for spec seo-metadata, first run spec-workflow-guide to get the workflow guide then implement the task: Role: PHP Developer working on front-controller wiring | Task: Extend public/index.php per requirements 1, 3, 4, 6, and 7 by adding a local renderPage(template, payload, route, id) helper that merges $seoContextBuilder->build($route, $id, $_GET, payload title, payload meta_description) into $payload via array_merge before calling $renderer->render(), then replacing every content-route's render/echo call (dives, sites, countries, cities, shops, trips, equipment, stats, gallery, summary) with a call through this helper, passing $match['route'] and $match['id'] | Restrictions: Do not alter profile.detail (JSON) or the not-found/404 branch; do not change routing/dispatch logic itself, only the render call sites; keep the front controller's existing structure and error handling (404s) intact | Success: Every content page's render call goes through renderPage and receives the correct SEO overrides; JSON and 404 responses are unaffected. Set the task to [-] in tasks.md before starting, log the implementation with log-implementation after completion, then mark it [x]._

- [x] 11. Extend the `<head>` in templates/layout.html.twig
  - File: templates/layout.html.twig (extend)
  - Add conditional blocks for `meta_description`, `robots`, `canonical_url`, and `schema_json`
    (rendered with `|raw` since it is pre-encoded via JSON_HEX_* flags), each only emitted when
    defined and non-empty, alongside the existing `<title>` line.
  - Purpose: Give every template (they all extend this layout) the new metadata for free.
  - _Leverage: existing title|default(...) pattern in the same `<head>`_
  - _Requirements: 1.3, 2.1, 2.4, 3.1, 4.1, 4.3, 6.1, 7.2_
  - _Prompt: Implement the task for spec seo-metadata, first run spec-workflow-guide to get the workflow guide then implement the task: Role: Twig Template Developer | Task: Extend the `<head>` of templates/layout.html.twig per requirements 1-4, 6, and 7 to add, immediately after the existing `<title>` line: a meta description tag guarded by `{% if meta_description is defined and meta_description %}`, a robots meta tag guarded similarly, a canonical link guarded similarly, and a `<script type="application/ld+json">{{ schema_json|raw }}</script>` guarded similarly | Restrictions: Do not use |raw on meta_description, robots, or canonical_url (they must stay auto-escaped); only schema_json uses |raw, and only because it is pre-encoded with JSON_HEX_* flags; do not change the existing `<title>` line's behavior | Success: Pages with the new payload keys render all four tags correctly; pages without them (opted-out, or routes with no overrides) render exactly as before this feature. Set the task to [-] in tasks.md before starting, log the implementation with log-implementation after completion, then mark it [x]._

- [x] 12. Add title/meta_description to adapters/web/Controller/DiveController.php
  - File: adapters/web/Controller/DiveController.php (extend overview() and detail())
  - `overview()`: `title` = "All Dives" (+ " — Page N" when paginated); `meta_description` built
    from the total dive count. `detail()`: `title` = dive number + `location_display` +
    `date_display`; `meta_description` built from `date_display`, `location_display`,
    `depth_display`/`depth_label`, truncated via `DescriptionTruncator`.
  - Purpose: Give the two highest-traffic page types unique, accurate metadata.
  - _Leverage: existing date_display/location_display/depth_display fields already computed; DescriptionTruncator_
  - _Requirements: 1.1, 1.2, 2.1, 2.2, 2.3_
  - _Prompt: Implement the task for spec seo-metadata, first run spec-workflow-guide to get the workflow guide then implement the task: Role: PHP Backend Developer building Twig view-models | Task: Extend DiveController::overview() and detail() per requirements 1 and 2 to add title and meta_description keys — overview: "All Dives" plus a page-number suffix when paginated beyond page 1, description mentioning the total dive count; detail: a title combining the dive number, location_display, and date_display, and a description built from date_display, location_display, and depth_display/depth_label, passed through DescriptionTruncator::truncate() | Restrictions: Reuse fields already computed in these methods; do not add new queries; fall back to a generic but still dive-specific description when location/site data is unavailable | Success: Two different seeded dives produce visibly different title/meta_description; overview description reflects the real count; PHPStan and PHPCS clean. Set the task to [-] in tasks.md before starting, log the implementation with log-implementation after completion, then mark it [x]._

- [x] 13. Add title/meta_description to adapters/web/Controller/DiveSiteController.php
  - File: adapters/web/Controller/DiveSiteController.php (extend overview() and detail())
  - `overview()`: generic title/description with site count. `detail()`: title from site name;
    description from `max_depth_display`, `water_types_display`, and dive count.
  - Purpose: Unique metadata for dive-site pages.
  - _Leverage: existing max_depth_display/water_types_display fields; DescriptionTruncator_
  - _Requirements: 1.1, 2.1, 2.2, 2.3_
  - _Prompt: Implement the task for spec seo-metadata, first run spec-workflow-guide to get the workflow guide then implement the task: Role: PHP Backend Developer building Twig view-models | Task: Extend DiveSiteController::overview() and detail() per requirements 1 and 2 to add title and meta_description — overview: a generic title/description mentioning the total site count; detail: a title built from the site's name and a description built from max_depth_display, water_types_display, and the number of dives at that site, truncated via DescriptionTruncator | Restrictions: Reuse existing computed fields; no new queries; degrade to a generic description when depth/water-type data is absent | Success: Two different seeded sites produce different title/meta_description; PHPStan and PHPCS clean. Set the task to [-] in tasks.md before starting, log the implementation with log-implementation after completion, then mark it [x]._

- [x] 14. Add title/meta_description to CountryController and CityController
  - Files: adapters/web/Controller/CountryController.php (extend), adapters/web/Controller/CityController.php (extend)
  - Each `overview()`/`detail()` gains a title (country/city name for detail; generic label for
    overview) and a meta_description built from that place's dive count / related sites, truncated
    via `DescriptionTruncator`.
  - Purpose: Unique metadata for country and city pages.
  - _Leverage: existing per-controller fields already returned in detail()/overview(); DescriptionTruncator_
  - _Requirements: 1.1, 2.1, 2.2, 2.3_
  - _Prompt: Implement the task for spec seo-metadata, first run spec-workflow-guide to get the workflow guide then implement the task: Role: PHP Backend Developer building Twig view-models | Task: Extend both CountryController and CityController's overview()/detail() methods per requirements 1 and 2 to add title (place name for detail pages, a generic label for overview pages) and meta_description (built from each controller's existing dive-count/related-site fields), truncated via DescriptionTruncator | Restrictions: Reuse existing fields; no new queries; degrade to a generic description when detail data is sparse | Success: Two different seeded countries and two different seeded cities each produce distinct title/meta_description; PHPStan and PHPCS clean. Set the task to [-] in tasks.md before starting, log the implementation with log-implementation after completion, then mark it [x]._

- [x] 15. Add title/meta_description to ShopController and TripController
  - Files: adapters/web/Controller/ShopController.php (extend), adapters/web/Controller/TripController.php (extend)
  - Each `overview()`/`detail()` gains a title (shop/trip name for detail; generic label for
    overview) and a meta_description from existing fields (location, dive count/dates), truncated
    via `DescriptionTruncator`.
  - Purpose: Unique metadata for dive-shop and dive-trip pages.
  - _Leverage: existing per-controller fields already returned in detail()/overview(); DescriptionTruncator_
  - _Requirements: 1.1, 2.1, 2.2, 2.3_
  - _Prompt: Implement the task for spec seo-metadata, first run spec-workflow-guide to get the workflow guide then implement the task: Role: PHP Backend Developer building Twig view-models | Task: Extend both ShopController and TripController's overview()/detail() methods per requirements 1 and 2 to add title (shop/trip name for detail pages, a generic label for overview pages) and meta_description built from each controller's existing fields (location, dates, dive count), truncated via DescriptionTruncator | Restrictions: Reuse existing fields; no new queries; degrade to a generic description when detail data is sparse | Success: Two different seeded shops and two different seeded trips each produce distinct title/meta_description; PHPStan and PHPCS clean. Set the task to [-] in tasks.md before starting, log the implementation with log-implementation after completion, then mark it [x]._

- [x] 16. Add title/meta_description to adapters/web/Controller/EquipmentController.php
  - File: adapters/web/Controller/EquipmentController.php (extend overview() and detail())
  - `overview()`: generic title/description with item count. `detail()`: title from the
    equipment's name; description from its type and service-due fields already computed.
  - Purpose: Unique metadata for equipment pages.
  - _Leverage: existing equipment name/type/service-due fields; DescriptionTruncator_
  - _Requirements: 1.1, 2.1, 2.2, 2.3_
  - _Prompt: Implement the task for spec seo-metadata, first run spec-workflow-guide to get the workflow guide then implement the task: Role: PHP Backend Developer building Twig view-models | Task: Extend EquipmentController::overview() and detail() per requirements 1 and 2 to add title and meta_description — overview: a generic title/description mentioning the total equipment count; detail: a title from the item's name and a description from its type and existing service-due fields, truncated via DescriptionTruncator | Restrictions: Reuse existing fields; no new queries | Success: Two different seeded equipment items produce distinct title/meta_description; PHPStan and PHPCS clean. Set the task to [-] in tasks.md before starting, log the implementation with log-implementation after completion, then mark it [x]._

- [x] 17. Add title/meta_description to adapters/web/Controller/DiveStatisticsController.php
  - File: adapters/web/Controller/DiveStatisticsController.php (extend view())
  - `view()`: title "Dive Statistics"; description built from existing aggregate counts (total
    dives, total time, etc.) already returned by the method.
  - Purpose: Unique, informative metadata for the stats page.
  - _Leverage: existing aggregate fields already returned by view(); DescriptionTruncator_
  - _Requirements: 1.1, 2.1, 2.2, 2.3_
  - _Prompt: Implement the task for spec seo-metadata, first run spec-workflow-guide to get the workflow guide then implement the task: Role: PHP Backend Developer building Twig view-models | Task: Extend DiveStatisticsController::view() per requirements 1 and 2 to add a title ("Dive Statistics") and a meta_description built from the aggregate counts already computed by the method (e.g. total dives, total bottom time), truncated via DescriptionTruncator | Restrictions: Reuse existing fields; no new queries | Success: The stats page has a distinct, accurate title/meta_description reflecting real aggregate data; PHPStan and PHPCS clean. Set the task to [-] in tasks.md before starting, log the implementation with log-implementation after completion, then mark it [x]._

- [x] 18. Add title/meta_description to adapters/web/Controller/GalleryController.php
  - File: adapters/web/Controller/GalleryController.php (extend overview() and forDive())
  - `overview()`: title "Dive Log Gallery" (+ page suffix); description from total photo count.
    `forDive()`: title "Photos — Dive #{number}"; description from the dive's site/date when
    available.
  - Purpose: Unique metadata for both gallery views.
  - _Leverage: existing picture count/dive metadata already available to this controller; DescriptionTruncator_
  - _Requirements: 1.1, 1.2, 2.1, 2.2, 2.3_
  - _Prompt: Implement the task for spec seo-metadata, first run spec-workflow-guide to get the workflow guide then implement the task: Role: PHP Backend Developer building Twig view-models | Task: Extend GalleryController::overview() and forDive() per requirements 1 and 2 to add title and meta_description — overview: "Dive Log Gallery" plus a page-number suffix when paginated, description mentioning the total photo count; forDive: a title referencing the dive number and a description built from that dive's site/date when resolvable, truncated via DescriptionTruncator | Restrictions: Reuse existing fields/dependencies; no new queries beyond what these methods already do | Success: The aggregate gallery and two different per-dive galleries each produce distinct title/meta_description; PHPStan and PHPCS clean. Set the task to [-] in tasks.md before starting, log the implementation with log-implementation after completion, then mark it [x]._

- [x] 19. Add HTTP smoke tests for the SEO metadata feature (Requirement 8)
  - File: tests/Http/WebSmokeTest.php (extend)
  - Add tests: two distinct dives (and two distinct sites) render different title/description/
    canonical; a dive detail page's JSON-LD parses and contains the required WebPage keys matching
    title/canonical; canonical form differs correctly between query-string and path-based fixture
    configurations; `APP_SEO_ENABLED=false` yields `noindex` and no canonical/JSON-LD; `/summary`
    always carries `noindex` regardless of the flag.
  - Purpose: Automated proof of per-page uniqueness, structural correctness, and the opt-out —
    the concrete, testable half of "does this feature work" per Requirement 8.
  - _Leverage: existing WebSmokeTest request() helper and sqlite fixtures_
  - _Requirements: 8.1, 8.2, 8.3, 8.4_
  - _Prompt: Implement the task for spec seo-metadata, first run spec-workflow-guide to get the workflow guide then implement the task: Role: QA Engineer with PHPUnit HTTP smoke testing expertise | Task: Extend tests/Http/WebSmokeTest.php per requirement 8 with tests asserting: (1) two distinct seeded dives (and two distinct seeded sites) produce different title, meta description, and canonical URL values; (2) a dive detail page's application/ld+json block parses as JSON and contains @type=WebPage with url/name matching the page's canonical/title; (3) canonical URL form differs correctly between a query-string-mode fixture and a path-mode fixture; (4) with APP_SEO_ENABLED=false the noindex meta appears and no canonical/JSON-LD block appears; (5) GET /summary always includes noindex regardless of the flag | Restrictions: Reuse the existing request() helper and fixtures; do not add new tooling or a JS test runner; keep each test isolated and deterministic | Success: All new tests pass under `composer test` and would fail if per-page uniqueness, JSON-LD validity, canonical mode-awareness, or the opt-out regressed. Set the task to [-] in tasks.md before starting, log the implementation with log-implementation after completion, then mark it [x]._

- [x] 20. Verify the full gate and perform the manual structured-data validation pass
  - Files: none (verification task)
  - Run `composer test && composer stan && composer cs`. Manually: view-source a representative
    sample (dives overview, a dive detail, a dive-site detail, the stats page, the gallery) and
    confirm unique title/description/canonical per page; run the dive detail page's JSON-LD
    through an external schema.org/Rich Results validator and note the result; confirm
    `APP_SEO_ENABLED=false` suppresses everything except the generic title and a noindex tag; note
    the validation result per Requirement 8.5/8.6 (manual, not a CI gate).
  - Purpose: Confirm the feature meets all requirements with no regressions, and close the loop on
    the reviewer's "how do we test this" question with a documented manual check.
  - _Leverage: composer gate; the running app; an external structured-data validator_
  - _Requirements: 1, 2, 3, 4, 5, 6, 7, 8_
  - _Prompt: Implement the task for spec seo-metadata, first run spec-workflow-guide to get the workflow guide then implement the task: Role: Release/QA Engineer | Task: Run `composer test && composer stan && composer cs` and perform manual verification of requirements 1-8 — unique title/description/canonical across several page types, valid JSON-LD confirmed via an external validator, the SEO opt-out behaving correctly, and the embeddable summary page always noindexed — recording the validator result in the PR/changelog per requirement 8.5 | Restrictions: Only modify production code to fix defects surfaced by the checks, staying within the approved design; do not treat ranking/indexing outcomes as part of this task's success criteria (out of scope per requirement 8.6) | Success: The gate passes clean, every acceptance criterion is manually confirmed, and the structured-data validation result is documented. Set the task to [-] in tasks.md before starting, log the implementation with log-implementation after completion, then mark it [x]._
