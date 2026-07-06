# Tasks Document

- [x] 1. Add DiveStatistics DTO in src/Model/DiveStatistics.php
  - File: src/Model/DiveStatistics.php
  - Define a `final readonly` DTO holding totals, first/last dive, bottom time, dive-time/depth/water-temp/air-temp groups (value + dive number for extremes), a keyed classifications array, and the five depth-bucket counts
  - Purpose: Provide a typed contract between the statistics repository and controller
  - _Leverage: src/Model/Stats.php (existing readonly model style), src/Model/Dive.php_
  - _Requirements: 6.1, 6.2, 6.3, 7.1_
  - _Prompt: Implement the task for spec dive-entity-linking-and-statistics, first run spec-workflow-guide to get the workflow guide then implement the task: Role: PHP 8.3 domain modeler | Task: Create a final readonly DiveStatistics DTO in src/Model/DiveStatistics.php capturing all statistics fields described in requirements 6.1-6.3 and 7.1, using typed arrays/nullable scalars and DateTimeImmutable, matching the existing readonly-model style in src/Model/Stats.php | Restrictions: No behavior/logic in the DTO, strict_types=1, PhpDivingLog\\Model namespace, phpstan-clean typed arrays with docblocks | Success: DTO compiles, composer stan passes, fields cover every statistic and the depth buckets. After implementing, set the task to [-] in tasks.md before starting and [x] when done, and call log-implementation with artifacts._

- [x] 2. Add DiveStatisticsRepository in src/Repository/DiveStatisticsRepository.php
  - File: src/Repository/DiveStatisticsRepository.php
  - Implement `compute(): DiveStatistics` with: one aggregate query (COUNT/MIN/MAX/AVG/SUM over Logbook), dive-number lookups for extremes, legacy-faithful classification counts, and the five depth buckets; use SQLSTATE 42S22 try/catch to return null for absent columns
  - Purpose: Read-only computation of all statistics with a bounded query count
  - _Leverage: src/Repository/StatsRepository.php, src/Repository/TripRepository.php (SQLSTATE 42S22 fallback pattern), src/Repository/DiveRepository.php_
  - _Requirements: 6.1, 6.2, 6.3, 6.4, 6.5, 7.1, 7.2, 8.1, 8.2, 8.3, 8.4, 8.5_
  - _Prompt: Implement the task for spec dive-entity-linking-and-statistics, first run spec-workflow-guide to get the workflow guide then implement the task: Role: PHP database engineer | Task: Implement DiveStatisticsRepository (read-only PDO) returning a DiveStatistics DTO per requirements 6 and 7 with legacy classification rules from requirement 8: Entry=1 shore / Entry=2 boat; Divetype comma-list codes 3=Night,4=Drift,5=Deep,6=Cave,7=Wreck,8=Photo via the four positional LIKE variants; Water=1/2/3; SupplyType=0/1/2; Deco='True'; Rep='True'; DblTank='True'/'False'; No-deco=total-deco, Non-rep=total-rep; depth buckets with legacy metric boundaries `Depth &lt;= 18`, `18..30`, `30..40`, `40..55`, `55+`. Use bound parameters and %sLogbook with the injected prefix; wrap each classification count so a missing column (SQLSTATE 42S22 or MySQL 'no such column') returns null | Restrictions: SELECT only, no string-concatenated user input, strict_types=1, PhpDivingLog\\Repository namespace, no fatal on missing columns | Success: composer stan passes; against the fixture the aggregates, buckets, and classifications compute correctly and missing columns degrade to null. Set the task [-] then [x] in tasks.md and call log-implementation with artifacts._

- [x] 3. Add DiveStatisticsFormatter in src/Support/DiveStatisticsFormatter.php
  - File: src/Support/DiveStatisticsFormatter.php
  - Implement `percentageLabel(int $count, int $total): string` (=> "N (P%)" with round(count/total*100), divide-by-zero safe), plus depth/temperature/duration/bottomTime (hh:mm) formatters using UnitConverter/Formatter
  - Purpose: Convert raw statistics into display strings honoring unit config
  - _Leverage: src/Support/UnitConverter.php, src/Support/Formatter.php_
  - _Requirements: 6.4, 6.6, 7.4_
  - _Prompt: Implement the task for spec dive-entity-linking-and-statistics, first run spec-workflow-guide to get the workflow guide then implement the task: Role: PHP developer focused on presentation/formatting | Task: Create DiveStatisticsFormatter in src/Support producing "N (P%)" labels with round(count/total*100) and divide-by-zero safety (requirement 6.4), hh:mm bottom time, and unit-aware depth/temperature/duration strings via UnitConverter/Formatter (requirements 6.6, 7.4) | Restrictions: final readonly, strict_types=1, PhpDivingLog\\Support namespace, no SQL, no globals | Success: Unit-testable pure formatters; composer stan/cs pass. Set the task [-] then [x] in tasks.md and call log-implementation with artifacts._

- [x] 4. Unit tests for DiveStatisticsFormatter in tests/Support/DiveStatisticsFormatterTest.php
  - File: tests/Support/DiveStatisticsFormatterTest.php
  - Test percentage rounding, "N (P%)" output, divide-by-zero (0 (0%)), hh:mm bottom time, and metric/imperial depth/temperature
  - Purpose: Lock the presentation contract
  - _Leverage: tests/Support/MediaResolverTest.php (Config::fromArray test setup), src/Support/UnitConverter.php_
  - _Requirements: 6.4, 6.6, 7.4_
  - _Prompt: Implement the task for spec dive-entity-linking-and-statistics, first run spec-workflow-guide to get the workflow guide then implement the task: Role: PHP QA engineer | Task: Write PHPUnit tests for DiveStatisticsFormatter covering requirements 6.4, 6.6, 7.4 including rounding edge cases and divide-by-zero, constructing Config via Config::fromArray as in existing tests | Restrictions: No DB, deterministic, PhpDivingLog\\Tests\\Support namespace | Success: composer test passes with new tests. Set the task [-] then [x] in tasks.md and call log-implementation with artifacts._

- [x] 5. Extend DiveRepository with entity dive lists in src/Repository/DiveRepository.php
  - File: src/Repository/DiveRepository.php (continue existing)
  - Add `listOverviewByPlace`, `listOverviewByTrip`, `listOverviewByCountry` returning the compact overview row shape (number,date_time,depth,duration,location), filtered/ordered, with SQLSTATE fallback for optional TripID/CountryID columns
  - Purpose: Provide dive lists for site/trip/country detail pages
  - _Leverage: src/Repository/DiveRepository.php listByPlace and listOverview row mapping_
  - _Requirements: 1.3, 2.2, 2.3, 3.2_
  - _Prompt: Implement the task for spec dive-entity-linking-and-statistics, first run spec-workflow-guide to get the workflow guide then implement the task: Role: PHP database engineer | Task: Add listOverviewByPlace/Trip/Country to DiveRepository returning the same compact row shape as listOverview (requirements 1.3, 2.2, 2.3, 3.2), bound parameters, ORDER BY Number DESC, with a graceful SQLSTATE 42S22 fallback (empty list) when the filter column is absent | Restrictions: SELECT only, reuse the existing compact mapping, do not change listOverview signature, strict_types=1 | Success: Methods return correct filtered rows against the fixture; composer stan passes. Set the task [-] then [x] in tasks.md and call log-implementation with artifacts._

- [x] 6. Add dive-count listings to DiveSiteRepository and CountryRepository
  - Files: src/Repository/DiveSiteRepository.php, src/Repository/CountryRepository.php
  - Add `listWithDiveCounts(int $limit = 500): array` using LEFT JOIN Logbook GROUP BY the entity id, returning entity model + DiveCount; SQLSTATE fallback to a countless list when the join column is missing
  - Purpose: Overviews show per-entity dive counts without N+1
  - _Leverage: existing mapSite/mapCountry mapping, TripRepository SQLSTATE fallback pattern_
  - _Requirements: 1.1, 2.1_
  - _Prompt: Implement the task for spec dive-entity-linking-and-statistics, first run spec-workflow-guide to get the workflow guide then implement the task: Role: PHP database engineer | Task: Add listWithDiveCounts to DiveSiteRepository (join on PlaceID) and CountryRepository (join via Logbook.CountryID or Place.CountryID) returning each entity plus a DiveCount (requirements 1.1, 2.1), grouped in one query, with a SQLSTATE 42S22 fallback to the existing countless list | Restrictions: SELECT only, bound/validated prefix, do not break existing list()/findById(), strict_types=1 | Success: Overviews can render counts; composer stan passes. Set the task [-] then [x] in tasks.md and call log-implementation with artifacts._

- [x] 7. Repository tests for entity dive lists and counts
  - File: tests/Repository/EntityDiveListingTest.php
  - Test listOverviewByPlace/Trip/Country filtering + ordering and listWithDiveCounts counts against the SQLite fixture; extend tests/fixtures seed as needed
  - Purpose: Guarantee linking queries are correct
  - _Leverage: existing tests/fixtures/schema.sql and seed.sql, existing repository test setup_
  - _Requirements: 1.1, 1.3, 2.1, 2.2, 3.2_
  - _Prompt: Implement the task for spec dive-entity-linking-and-statistics, first run spec-workflow-guide to get the workflow guide then implement the task: Role: PHP QA engineer | Task: Add PHPUnit tests validating listOverviewByPlace/Trip/Country and listWithDiveCounts against the SQLite fixture (requirements 1.1,1.3,2.1,2.2,3.2), extending tests/fixtures/seed.sql with dives tied to a place/trip/country | Restrictions: Use pdo_sqlite fixture pattern already in the suite, keep repositories read-only, deterministic assertions | Success: composer test passes. Set the task [-] then [x] in tasks.md and call log-implementation with artifacts._

- [x] 8. Add DiveStatisticsController and wire /stats in bootstrap + index
  - Files: adapters/web/Controller/DiveStatisticsController.php, adapters/web/bootstrap.php, public/index.php
  - Implement `view(): array` assembling formatted aggregates, classification labels, dive-number links, and depth_distribution ([{label,count,percent}]) plus a JSON payload; register the repository/formatter in bootstrap; route stats.overview to the new controller (keep StatsRepository/SummaryController untouched)
  - Purpose: Serve the rich statistics page
  - _Leverage: adapters/web/Controller/StatsController.php, adapters/web/bootstrap.php services/repositories wiring, public/index.php route dispatch_
  - _Requirements: 6.1, 6.2, 6.3, 7.1, 7.2, 7.3_
  - _Prompt: Implement the task for spec dive-entity-linking-and-statistics, first run spec-workflow-guide to get the workflow guide then implement the task: Role: PHP web adapter developer | Task: Create DiveStatisticsController.view() building the /stats payload (requirements 6.1-6.3, 7.1-7.3) from DiveStatisticsRepository + DiveStatisticsFormatter, including a depth_distribution list and a JSON-serializable bucket payload; register DiveStatisticsRepository and DiveStatisticsFormatter in bootstrap.php and dispatch the stats.overview route to this controller in public/index.php | Restrictions: Do not modify StatsRepository or SummaryController, keep constructor injection style, strict_types=1 | Success: /stats renders via the new controller; composer stan passes. Set the task [-] then [x] in tasks.md and call log-implementation with artifacts._

- [x] 9. Rewrite templates/divestats.html.twig with stats layout + pie canvas
  - File: templates/divestats.html.twig
  - Render all aggregates and classification labels (N (P%)), dive-number links for extremes, and a `<canvas>` with depth-bucket JSON in a data attribute/script tag; readable, non-blue-heavy, responsive
  - Purpose: Present the statistics matching the legacy screen
  - _Leverage: templates/dive_detail.html.twig (panels/canvas usage), public/assets/css/app.css_
  - _Requirements: 6.1, 6.2, 6.3, 7.1, 7.3_
  - _Prompt: Implement the task for spec dive-entity-linking-and-statistics, first run spec-workflow-guide to get the workflow guide then implement the task: Role: Twig/HTML front-end developer | Task: Rewrite templates/divestats.html.twig to present the statistics payload (requirements 6.1-6.3, 7.1, 7.3) with dive-number links and a canvas placeholder carrying depth-bucket JSON, reusing dive-panel styles | Restrictions: Twig auto-escaping (no raw for user data), no jqPlot, keep layout responsive and not blue-heavy | Success: Page renders with all sections and a canvas hook. Set the task [-] then [x] in tasks.md and call log-implementation with artifacts._

- [x] 10. Add depth-distribution pie chart JS in public/assets/js/stats-chart.js
  - File: public/assets/js/stats-chart.js
  - Read bucket JSON from the page and draw a labeled pie with a legend on the canvas (DPR-aware), no external libraries; handle empty/zero data
  - Purpose: Visualize the depth distribution like the legacy chart
  - _Leverage: public/assets/js/profile-chart.js (canvas DPR/setup/legend patterns)_
  - _Requirements: 7.1, 7.3, 7.4_
  - _Prompt: Implement the task for spec dive-entity-linking-and-statistics, first run spec-workflow-guide to get the workflow guide then implement the task: Role: Front-end canvas developer | Task: Implement stats-chart.js to draw a depth-range pie chart with legend from the page's bucket JSON (requirements 7.1, 7.3, 7.4), reusing DPR/setup patterns from profile-chart.js, with imperial labels when configured and a safe empty state | Restrictions: Vanilla JS, no chart library, no blocking errors when data is empty | Success: Pie renders with slice percentages and legend. Set the task [-] then [x] in tasks.md and call log-implementation with artifacts._

- [x] 11. Extend site/country/trip detail controllers + templates with dive lists
  - Files: adapters/web/Controller/DiveSiteController.php, adapters/web/Controller/CountryController.php, adapters/web/Controller/TripController.php, templates/divesite_detail.html.twig, templates/divecountry_detail.html.twig, templates/divetrip_detail.html.twig, templates/partials/dive_rows.html.twig
  - Add dives (compact rows) to each detail payload via the new DiveRepository methods; create a shared dive-rows partial with clickable data-href rows and an empty state
  - Purpose: Enable drill-through from an entity to its dives
  - _Leverage: src/Repository/DiveRepository.php (new methods), adapters/web/bootstrap.php wiring, templates/dives_overview.html.twig clickable-row markup, public/assets/js/tables.js_
  - _Requirements: 1.3, 1.4, 1.5, 2.2, 2.3, 3.2, 3.4_
  - _Prompt: Implement the task for spec dive-entity-linking-and-statistics, first run spec-workflow-guide to get the workflow guide then implement the task: Role: PHP web + Twig developer | Task: Inject DiveRepository into the site/country/trip controllers and add a 'dives' compact list to each detail payload (requirements 1.3,2.2,2.3,3.2), then render a shared templates/partials/dive_rows.html.twig with data-href rows linking to /dives/{number} and empty-state messages (requirements 1.4,1.5,3.4) | Restrictions: Constructor injection via bootstrap.php, Twig auto-escaping, reuse existing clickable-row JS, strict_types=1 | Success: Site/trip/country detail pages list their dives and link through; composer stan passes. Set the task [-] then [x] in tasks.md and call log-implementation with artifacts._

- [x] 12. Add dive counts and clickable rows to entity overviews + country link on dive detail
  - Files: adapters/web/Controller/DiveSiteController.php, adapters/web/Controller/CountryController.php, adapters/web/Controller/TripController.php, adapters/web/Controller/EquipmentController.php, templates/divesite_overview.html.twig, templates/divecountry_overview.html.twig, templates/divetrip_overview.html.twig, templates/equipment_overview.html.twig, templates/dive_detail.html.twig
  - Overview methods use listWithDiveCounts (sites/countries) and expose diveCount; overview templates show counts + full-row data-href; ensure the dive detail country value links when an id exists
  - Purpose: Complete the overview context and bidirectional links (Req 5)
  - _Leverage: templates/dives_overview.html.twig clickable rows, public/assets/js/tables.js, existing overview templates_
  - _Requirements: 1.1, 1.2, 2.1, 3.1, 4.1, 5.1, 5.2, 5.3_
  - _Prompt: Implement the task for spec dive-entity-linking-and-statistics, first run spec-workflow-guide to get the workflow guide then implement the task: Role: PHP web + Twig developer | Task: Update site/country overviews to use listWithDiveCounts and expose diveCount, add trip and equipment overview counts where available, render clickable full-row navigation on all four overviews (requirements 1.1,1.2,2.1,3.1,4.1), and make the dive detail country value a link when an id exists (requirements 5.1-5.3) | Restrictions: Reuse existing clickable-row markup/JS, keep denormalized-name fallback to plain text and '-' when absent, Twig auto-escaping | Success: Overviews show counts and are clickable; dive detail links country; composer stan passes. Set the task [-] then [x] in tasks.md and call log-implementation with artifacts._

- [x] 13. Add web smoke tests for stats and entity linking
  - File: tests/Http/WebSmokeTest.php (extend)
  - Assert /stats renders aggregates, classification labels, the canvas hook and bucket JSON; assert /sites/{id}, /trips/{id}, /countries/{id} render dive rows linking to /dives/{number} and empty states; assert overviews show counts and data-href rows
  - Purpose: Guard the end-to-end wiring
  - _Leverage: tests/Http/WebSmokeTest.php existing setup and fixtures_
  - _Requirements: 1.1, 1.3, 2.1, 2.2, 3.1, 3.2, 6.1, 7.1_
  - _Prompt: Implement the task for spec dive-entity-linking-and-statistics, first run spec-workflow-guide to get the workflow guide then implement the task: Role: PHP QA engineer | Task: Extend WebSmokeTest to cover the statistics page and entity drill-through (requirements 1.1,1.3,2.1,2.2,3.1,3.2,6.1,7.1), asserting DOM hooks (canvas, bucket JSON, /dives/{number} links, dive counts, data-href rows) against the fixture data | Restrictions: Use the existing smoke-test harness and fixtures, deterministic assertions | Success: composer test passes with the new assertions. Set the task [-] then [x] in tasks.md and call log-implementation with artifacts._

- [x] 14. Final verification and cleanup
  - Files: (project-wide) run composer test && composer stan && composer cs; fix any issues
  - Ensure all new files follow coding standards, no unused code, and all statistics/linking behavior degrades gracefully on missing columns
  - Purpose: Ship a green, consistent feature
  - _Leverage: composer scripts (test, stan, cs)_
  - _Requirements: All_
  - _Prompt: Implement the task for spec dive-entity-linking-and-statistics, first run spec-workflow-guide to get the workflow guide then implement the task: Role: Senior PHP engineer | Task: Run composer test && composer stan && composer cs, resolve any failures across the new statistics and entity-linking code, and verify graceful degradation on missing columns (all requirements) | Restrictions: Do not weaken read-only/security guarantees, keep changes minimal and standards-compliant | Success: All three checks pass cleanly. Set the task [-] then [x] in tasks.md and call log-implementation with artifacts._
