# Tasks Document

- [x] 1. Add paginated picture access to src/Repository/PictureRepository.php
  - File: src/Repository/PictureRepository.php (extend; keep findByLogId)
  - Add `countAll(): int` (`SELECT COUNT(*) FROM {prefix}Pictures`) and `findPage(int $limit, int $offset): list<Picture>` (`SELECT * FROM {prefix}Pictures ORDER BY LogID DESC, PictureID DESC LIMIT :limit OFFSET :offset`), mapping rows to the existing Picture model shape.
  - Purpose: Provide bounded, cross-dive picture access for the aggregate gallery.
  - _Leverage: existing PictureRepository::queryByColumn mapping style, Picture model, validated table prefix_
  - _Requirements: 1.1, 1.2, 2.5, Non-Functional Performance_
  - _Prompt: Implement the task for spec dive-log-gallery, first run spec-workflow-guide to get the workflow guide then implement the task: Role: PHP Backend Developer specializing in read-only PDO repositories | Task: Extend src/Repository/PictureRepository.php with countAll() and findPage(limit, offset) per requirements 1 and 2, using prepared PDO statements, the validated table prefix, SELECT * with the same defensive column mapping used by findByLogId, and a stable ORDER BY (LogID DESC, PictureID DESC) | Restrictions: Keep repository read-only; do not modify findByLogId behavior; bind limit/offset as integers; return list of Picture models | Success: countAll returns the total picture count and findPage returns the correct ordered slice mapped to Picture; PHPStan and PHPCS clean. Set the task to [-] in tasks.md before starting, log the implementation with log-implementation after completion, then mark it [x]._

- [x] 2. Add bounded dive metadata lookup to src/Repository/DiveRepository.php
  - File: src/Repository/DiveRepository.php (extend)
  - Add `findMetaByLogIds(array $logIds): array` returning a map keyed by LogID with number, date_time, place_id, country_id, place_name, city_name, country_name, using one `SELECT * FROM {prefix}Logbook WHERE LogID IN (...)`; return empty array for empty input.
  - Purpose: Resolve per-page picture metadata in a single query (no N+1).
  - _Leverage: existing mapDateTime() and the inline Place/City/Country extraction used by findByNumber/mapOverviewRow_
  - _Requirements: 3.1, 3.3, 4.1, Non-Functional Performance_
  - _Prompt: Implement the task for spec dive-log-gallery, first run spec-workflow-guide to get the workflow guide then implement the task: Role: PHP Backend Developer specializing in read-only PDO repositories | Task: Add DiveRepository::findMetaByLogIds(array logIds) per requirements 3 and 4 that fetches Logbook rows for the given LogID set in a single prepared IN-query and returns a map keyed by LogID containing number, date_time (via mapDateTime), place_id, country_id, and inline place_name/city_name/country_name, returning an empty array when given no ids | Restrictions: Keep read-only; parameterize the IN clause safely; reuse existing date and name extraction logic; degrade gracefully when optional name columns are absent | Success: The method returns correct metadata keyed by LogID for seeded fixtures and an empty array for empty input; PHPStan and PHPCS clean. Set the task to [-] in tasks.md before starting, log the implementation with log-implementation after completion, then mark it [x]._

- [x] 3. Add repository unit tests for the new picture/metadata methods
  - Files: tests/Repository/PictureRepositoryTest.php (new), tests/Repository/DiveRepositoryTest.php (extend)
  - Test countAll, findPage (limit/offset/order), and findMetaByLogIds (correct fields + empty input) against the sqlite fixtures.
  - Purpose: Lock in data-layer correctness for pagination and metadata.
  - _Leverage: existing tests/Repository/*Test.php fixture bootstrapping (pdo_sqlite), tests/fixtures/*_
  - _Requirements: 2.5, 3.1, Non-Functional Reliability_
  - _Prompt: Implement the task for spec dive-log-gallery, first run spec-workflow-guide to get the workflow guide then implement the task: Role: QA Engineer with PHPUnit + fixture-backed repository testing expertise | Task: Add tests/Repository/PictureRepositoryTest.php and extend tests/Repository/DiveRepositoryTest.php to cover countAll, findPage (limit/offset/order), and findMetaByLogIds (field correctness and empty-input behavior) per requirements 2 and 3, reusing the existing sqlite fixture setup | Restrictions: Use existing fixtures/bootstrapping; require pdo_sqlite (skip if unavailable, matching existing tests); keep tests isolated | Success: New tests pass under `composer test` and assert ordering, slicing, and metadata mapping. Set the task to [-] in tasks.md before starting, log the implementation with log-implementation after completion, then mark it [x]._

- [x] 4. Add the overview() action to adapters/web/Controller/GalleryController.php
  - File: adapters/web/Controller/GalleryController.php (extend; keep forDive)
  - Add constructor dependencies (DiveRepository, PersonalRepository, Formatter) and `overview(int $page = 1, int $perPage = 24): array` that counts, clamps the page, fetches the page slice, resolves metadata via findMetaByLogIds, and builds per-picture view-model items (thumb, url, description, diveNumber, diver, location, site, when, diveUrl) plus currentPage/pages/total.
  - Purpose: Produce the paginated gallery view-model with per-photo dive metadata.
  - _Leverage: MediaResolver (thumbUrl/pictureUrl), Formatter.formatDate + H:i, PersonalRepository.getProfile, DiveController metadata/location composition pattern_
  - _Requirements: 1, 2, 3, 4_
  - _Prompt: Implement the task for spec dive-log-gallery, first run spec-workflow-guide to get the workflow guide then implement the task: Role: PHP Backend Developer building Twig view-models | Task: Extend GalleryController with an overview(page, perPage) action implementing requirements 1-4 — count and clamp pages, fetch the page slice, resolve dive metadata via DiveRepository.findMetaByLogIds, and assemble per-picture items (thumb, url, description, diveNumber, diver from the Personal profile display name, location as country/city, site as place name, when as formatted date plus H:i, diveUrl as /dives/number) with currentPage/pages/total | Restrictions: Do not change forDive; keep read-only; leave metadata parts empty when unavailable and omit the diver when no profile name; do not introduce N+1 (resolve metadata once per page) | Success: overview returns a correct, bounded view-model for seeded data; PHPStan and PHPCS clean. Set the task to [-] in tasks.md before starting, log the implementation with log-implementation after completion, then mark it [x]._

- [x] 5. Wire the /gallery route and dispatch
  - Files: adapters/web/Router.php, public/index.php
  - Add `gallery` to the overview route map so `/gallery` resolves to `gallery.overview` (and `?type=gallery` in query-string mode) without colliding with `/gallery/{id}`; construct GalleryController with the new dependencies; add a dispatch branch that reads `?page`, calls overview, and renders the new template.
  - Purpose: Make the aggregate gallery reachable in both URL modes.
  - _Leverage: existing Router overview/detail maps, existing dives.overview page-parameter handling and controller construction in public/index.php_
  - _Requirements: 1.1, 2.2, 5.2_
  - _Prompt: Implement the task for spec dive-log-gallery, first run spec-workflow-guide to get the workflow guide then implement the task: Role: PHP Developer working on routing/front-controller wiring | Task: Add the gallery.overview route per requirements 1, 2, and 5 by including gallery in the Router overview map and adding a dispatch branch in public/index.php that reads the page query parameter, calls GalleryController.overview, and renders dive_log_gallery.html.twig, updating the GalleryController construction to pass the new dependencies (dives, personal, formatter) | Restrictions: Do not break the existing /gallery/{id} detail route in either URL mode; mirror the existing page-parameter parsing from dives.overview; keep dispatch style consistent | Success: /gallery renders the overview and /gallery/{id} still renders the per-dive gallery; both URL modes work. Set the task to [-] in tasks.md before starting, log the implementation with log-implementation after completion, then mark it [x]._

- [x] 6. Generalize the pagination partial with a basePath parameter
  - File: templates/partials/pagination.html.twig
  - Add an optional `basePath` (default `/`) so links target `{{ basePath }}?page=N...`; keep existing dives-overview callers working unchanged.
  - Purpose: Reuse one pagination control for the gallery and the dives overview.
  - _Leverage: existing partials/pagination.html.twig markup_
  - _Requirements: 2.1, 2.4_
  - _Prompt: Implement the task for spec dive-log-gallery, first run spec-workflow-guide to get the workflow guide then implement the task: Role: Twig Template Developer | Task: Generalize templates/partials/pagination.html.twig per requirement 2 by adding an optional basePath variable (default '/') used to build page links, preserving current dives-overview behavior | Restrictions: Do not break existing callers (default must equal current behavior); keep query params (q/sort) handling intact; keep output auto-escaped | Success: The partial renders correct links for both / and /gallery base paths; existing dives-overview pagination is unchanged. Set the task to [-] in tasks.md before starting, log the implementation with log-implementation after completion, then mark it [x]._

- [x] 7. Create the Dive Log Gallery template templates/dive_log_gallery.html.twig
  - File: templates/dive_log_gallery.html.twig (new)
  - Render a title "Dive Log Gallery", a `.gallery-grid` list with `data-lightbox-group="dive-log-gallery"` whose anchors carry data-lightbox plus data-dive-number/data-diver/data-location/data-site/data-when/data-dive-url and a thumbnail img; include the pagination partial with basePath '/gallery'; render an empty-state when there are no pictures; include /assets/js/lightbox.js.
  - Purpose: Present the paginated thumbnail grid and expose per-photo metadata to the lightbox.
  - _Leverage: .gallery-grid styles, shared lightbox.js/data-lightbox convention, generalized pagination partial, layout.html.twig_
  - _Requirements: 1.1, 1.3, 1.4, 2.1, 3.1, 4.1, 6.1_
  - _Prompt: Implement the task for spec dive-log-gallery, first run spec-workflow-guide to get the workflow guide then implement the task: Role: Twig Template Developer | Task: Create templates/dive_log_gallery.html.twig per requirements 1, 2, 3, 4, and 6 rendering a titled gallery grid (ul.gallery-grid with data-lightbox-group="dive-log-gallery") whose anchors carry data-lightbox and the per-photo data attributes (data-dive-number, data-diver, data-location, data-site, data-when, data-dive-url) with thumbnail images, an empty-state message when pictures is empty, the pagination partial with basePath '/gallery', and the lightbox script | Restrictions: Keep all output auto-escaped; reuse existing gallery-grid markup and the pagination partial; do not add inline JS | Success: The page renders the grid, pagination, and empty state, and each anchor exposes the metadata attributes the lightbox reads. Set the task to [-] in tasks.md before starting, log the implementation with log-implementation after completion, then mark it [x]._

- [x] 8. Add a Gallery link to the primary navigation
  - File: templates/partials/nav.html.twig
  - Add a nav link to `/gallery` with a suitable Material Symbol icon (e.g. photo_library) and label "Gallery".
  - Purpose: Make the Dive Log Gallery discoverable.
  - _Leverage: existing nav link markup and Material Symbols usage_
  - _Requirements: 5.1_
  - _Prompt: Implement the task for spec dive-log-gallery, first run spec-workflow-guide to get the workflow guide then implement the task: Role: Twig Template Developer | Task: Add a primary-navigation link to /gallery per requirement 5 in templates/partials/nav.html.twig, matching the existing link markup and using an appropriate Material Symbol icon and the label Gallery | Restrictions: Match existing nav item structure and data-nav-link usage; do not reorder unrelated links disruptively | Success: Every page's primary nav shows a working Gallery link to /gallery. Set the task to [-] in tasks.md before starting, log the implementation with log-implementation after completion, then mark it [x]._

- [x] 9. Render the dive-info panel in the shared lightbox public/assets/js/lightbox.js
  - File: public/assets/js/lightbox.js (extend)
  - Add an info panel to the dialog and, in renderCurrentImage, read data-dive-number/data-diver/data-location/data-site/data-when/data-dive-url from the current anchor to populate two lines (Dive number by diver; Location/Divesite/When joined by " | ") plus a "view dive" link; hide the panel when data-dive-number is absent; update on next/prev.
  - Purpose: Show per-photo dive context and a deep link inside the lightbox.
  - _Leverage: the grouped lightbox render pipeline from the dive-photo-lightbox-navigation spec (renderCurrentImage, currentGroup/currentIndex)_
  - _Requirements: 3.1, 3.2, 3.4, 4.1, 4.2, 4.3, 6.2_
  - _Prompt: Implement the task for spec dive-log-gallery, first run spec-workflow-guide to get the workflow guide then implement the task: Role: Front-end Developer specializing in dependency-free vanilla JavaScript | Task: Extend public/assets/js/lightbox.js per requirements 3, 4, and 6 by adding an info panel to the dialog and populating it in renderCurrentImage from the current anchor data attributes — line 1 "Dive number" plus " by diver" when present, line 2 the Location/Divesite/When parts (omitting empties) joined by " | " followed by a view-dive link whose href is data-dive-url — hiding the panel when data-dive-number is absent and updating it on every navigation | Restrictions: No third-party libraries; set values via textContent and setAttribute only (no unescaped HTML injection); keep single-image and existing galleries working (panel hidden when no metadata); guard all DOM access | Success: Gallery photos show the correct two-line info block and a working view-dive link; the block updates on next/prev; non-gallery lightboxes are unaffected; no console errors. Set the task to [-] in tasks.md before starting, log the implementation with log-implementation after completion, then mark it [x]._

- [x] 10. Style the lightbox info panel in public/assets/css/custom.css
  - File: public/assets/css/custom.css (extend the lightbox styles)
  - Add rules for `.lightbox-info`, `.lightbox-info-title`, `.lightbox-info-meta`, and the view-dive link, using existing theme custom properties; hidden state via the hidden attribute.
  - Purpose: Present the info block and link legibly in both themes.
  - _Leverage: existing lightbox CSS block and theme custom properties in public/assets/css/custom.css_
  - _Requirements: 3.1, 4.3, Non-Functional Usability_
  - _Prompt: Implement the task for spec dive-log-gallery, first run spec-workflow-guide to get the workflow guide then implement the task: Role: UI Developer with CSS and Material Design 3 theming expertise | Task: Add styles for the lightbox info panel (.lightbox-info, .lightbox-info-title, .lightbox-info-meta, and the view-dive link) per requirements 3 and 4, reusing existing theme custom properties so the block is legible in light and dark themes, including the hidden state | Restrictions: Reuse theme variables (no hard-coded colors); do not regress existing lightbox styles; keep the view-dive link an accessible, focusable target | Success: The info block and link are legible and on-theme in both themes; hidden state works; no regressions. Set the task to [-] in tasks.md before starting, log the implementation with log-implementation after completion, then mark it [x]._

- [x] 11. Add an HTTP smoke test for the gallery overview
  - File: tests/Http/WebSmokeTest.php (extend)
  - Add `testGalleryOverviewRenders`: GET /gallery returns 200 and contains "Dive Log Gallery", a gallery-grid with data-lightbox-group="dive-log-gallery", at least one data-dive-number and data-dive-url="/dives/..." attribute, and the /assets/js/lightbox.js script; assert the primary nav includes a /gallery link.
  - Purpose: Guard the route, template contract, and nav wiring.
  - _Leverage: existing WebSmokeTest request() helper and sqlite fixtures (which seed Pictures)_
  - _Requirements: 1.1, 3.1, 4.1, 5.1_
  - _Prompt: Implement the task for spec dive-log-gallery, first run spec-workflow-guide to get the workflow guide then implement the task: Role: QA Engineer with PHPUnit HTTP smoke testing expertise | Task: Add testGalleryOverviewRenders to tests/Http/WebSmokeTest.php per requirements 1, 3, 4, and 5 asserting GET /gallery returns 200 and the body contains the gallery title, the gallery-grid with data-lightbox-group="dive-log-gallery", at least one data-dive-number and a data-dive-url pointing at /dives/, and the lightbox script tag, plus a primary-nav /gallery link | Restrictions: Reuse the existing request() helper and fixtures; do not add new tooling; keep the test isolated | Success: The new smoke test passes under `composer test` and fails if the route, gallery contract, or nav link regresses. Set the task to [-] in tasks.md before starting, log the implementation with log-implementation after completion, then mark it [x]._

- [x] 12. Verify the full gate and manually validate acceptance criteria
  - Files: none (verification task)
  - Run `composer test && composer stan && composer cs`. Manually verify: /gallery grid + pagination; opening a photo shows the two-line info block (Dive number by diver; Location | Divesite | When | view dive) with a working link; next/prev updates image + info + link; empty/partial metadata degrades cleanly; per-dive /gallery/{id} still works; light/dark themes legible.
  - Purpose: Confirm the feature meets all requirements with no regressions.
  - _Leverage: composer gate; templates dive_log_gallery, dive_detail, divesite_detail_
  - _Requirements: 1, 2, 3, 4, 5, 6_
  - _Prompt: Implement the task for spec dive-log-gallery, first run spec-workflow-guide to get the workflow guide then implement the task: Role: Release/QA Engineer | Task: Run `composer test && composer stan && composer cs` and perform manual browser E2E validation of requirements 1-6 for the Dive Log Gallery — grid, pagination, lightbox info block and view-dive link, metadata updates on next/prev, graceful degradation, unaffected per-dive gallery, and correct theming | Restrictions: Only modify production code to fix defects surfaced by the checks, staying within the approved design | Success: The gate passes clean and every acceptance criterion is confirmed; no console errors. Set the task to [-] in tasks.md before starting, log the implementation with log-implementation after completion, then mark it [x]._
