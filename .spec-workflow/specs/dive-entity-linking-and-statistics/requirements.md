# Requirements Document

## Introduction

This feature enriches the modernized phpDivingLog (PDO/Twig rewrite) so that the
supporting entities — **Dive Sites, Dive Countries, Dive Trips, Dive Equipment**
(and, where already present, Dive Cities and Dive Shops) — become first-class,
richly listed, and **cross-linked** with the dive logbook. From an entity detail
page a visitor can drill into the individual dives associated with that entity,
and from a dive detail page they can navigate back to the related entities.

It also restores the full **Dive Statistics** page that existed in the legacy
application (reference: `classes.inc.php` `Divestats` class, ~line 4404),
including the aggregate figures and the depth-range distribution pie chart, using
a modern PDO-backed repository and a Twig template with a canvas-rendered chart
(consistent with the existing profile chart approach — no jqPlot).

## Alignment with Product Vision

- Preserves the migration constraints in `AGENTS.md`: no Smarty, no `wp-db.php`,
  no jqPlot, no legacy root page controllers; repositories stay **read-only**
  against the Diving Log schema; `TABLE_PREFIX` is respected.
- Continues the established modern architecture: PSR-4 core in `src/`, Twig web
  adapter in `adapters/web/`, front controller `public/index.php`, PDO factory
  in `src/Database/Connection.php`, env-based `Config`.
- Reuses existing conventions already proven in the dive detail work: repository
  column-variant fallbacks for real Diving Log exports, `UnitConverter` /
  `Formatter` for display, canvas charts for visualization, and clickable rows
  for navigation.

## Requirements

### Requirement 1 — Dive Sites overview and links to dives

**User Story:** As a diver browsing my logbook, I want the Dive Sites list to
show each site with a dive count and let me open a site to see every dive logged
there, so that I can review my history at a specific location.

#### Acceptance Criteria

1. WHEN the visitor opens `/sites` THEN the system SHALL list all dive sites with
   name, city/country (when available), and the number of dives at that site.
2. WHEN the visitor selects a dive site row THEN the system SHALL open that site's
   detail page at `/sites/{id}`.
3. WHEN a site detail page renders THEN the system SHALL list every dive at that
   site (dive number, date, max depth, duration), each linking to `/dives/{number}`.
4. WHEN a site has no associated dives THEN the system SHALL render the detail page
   with an empty-state message instead of a dive list.
5. WHEN the site row/list is displayed THEN the whole row SHALL be keyboard- and
   mouse-clickable, consistent with the dive overview behavior.

### Requirement 2 — Dive Countries overview and links to dives

**User Story:** As a diver, I want to see all dive countries and drill into the
dives (and dive sites) within a country, so that I can explore my diving by
geography.

#### Acceptance Criteria

1. WHEN the visitor opens `/countries` THEN the system SHALL list all countries
   with a dive count per country.
2. WHEN the visitor opens `/countries/{id}` THEN the system SHALL show the country
   with its dive sites and/or dives, each linking to the respective detail page.
3. WHEN a dive or site is listed under a country THEN selecting it SHALL navigate
   to `/dives/{number}` or `/sites/{id}` respectively.
4. IF a country has an associated flag image THEN the system SHALL display it using
   the media resolver (absolute web path), otherwise omit it gracefully.

### Requirement 3 — Dive Trips overview and links to dives

**User Story:** As a diver, I want to see all my dive trips/vacations and open a
trip to review the dives that belong to it, so that I can relive a trip.

#### Acceptance Criteria

1. WHEN the visitor opens `/trips` THEN the system SHALL list all trips with name,
   date range (from/to when available), and a dive count.
2. WHEN the visitor opens `/trips/{id}` THEN the system SHALL list the dives that
   belong to that trip, each linking to `/dives/{number}`.
3. WHEN a trip references a country or shop THEN the system SHALL link to that
   related entity where such a detail page exists.
4. IF a trip has no dives THEN the system SHALL show an empty-state message.

### Requirement 4 — Dive Equipment overview and details

**User Story:** As a diver, I want to see all my dive equipment and open an item to
see its details, so that I can track my gear.

#### Acceptance Criteria

1. WHEN the visitor opens `/equipment` THEN the system SHALL list all equipment
   with product name, manufacturer, and (when available) service-due indication.
2. WHEN the visitor opens `/equipment/{id}` THEN the system SHALL show the item's
   details (purchase date, service dates, comment, photo when present).
3. IF equipment-to-dive linkage exists in the source schema THEN the system SHALL
   list the dives that used the item, each linking to `/dives/{number}`; otherwise
   the linkage section SHALL be omitted without error.
4. IF an equipment photo is present THEN the system SHALL render it via the media
   resolver, otherwise show the missing-image placeholder.

### Requirement 5 — Bidirectional links from the dive detail page

**User Story:** As a diver on a dive detail page, I want the related site, country,
trip, and shop to be clickable, so that I can pivot from a dive to its context.

#### Acceptance Criteria

1. WHEN a dive detail page renders AND a related site/country/trip/shop exists THEN
   the system SHALL render that value as a link to the corresponding detail page.
2. IF the related entity id is missing but a denormalized name is present THEN the
   system SHALL render the name as plain text (existing fallback behavior preserved).
3. WHEN no related entity is available THEN the system SHALL render `-`.

### Requirement 6 — Dive Statistics page (aggregates)

**User Story:** As a diver, I want a statistics page summarizing my diving, so that
I can see totals and records at a glance, matching the legacy statistics screen.

#### Acceptance Criteria

1. WHEN the visitor opens `/stats` THEN the system SHALL display:
   total number of dives; first dive (date + dive number link); last dive (date +
   dive number link); total bottom time (hh:mm); longest, shortest, and average
   dive time (with dive-number links for longest/shortest); deepest, shallowest,
   and average depth (with dive-number links for deepest/shallowest).
2. WHEN temperature data exists THEN the system SHALL display coldest, warmest, and
   average water temperature and air temperature (with dive-number links for the
   extremes), each honoring the configured unit conversion.
3. WHEN classification data exists THEN the system SHALL display counts and
   percentages for: Deco vs No-deco dives; Repetitive vs Non-repetitive dives;
   Saltwater / Freshwater / Brackish dives; Shore vs Boat dives; Night, Drift,
   Deep, Cave, Wreck, Photo dives; Single vs Twin cylinder dives; OC / SCR / CCR
   dives.
4. WHEN a percentage is shown THEN it SHALL be computed as
   `round(count / totalDives * 100)` and rendered as `N (P%)`, matching legacy output.
5. IF a source column required for a classification is absent in the connected
   database THEN the system SHALL omit or zero that statistic without raising an
   error (real-export resilience).
6. WHEN all values are formatted THEN depth, temperature, and time SHALL use the
   existing `UnitConverter`/`Formatter` so metric/imperial settings are honored.

### Requirement 7 — Depth-range distribution pie chart

**User Story:** As a diver, I want a pie chart of how my dives distribute across
depth ranges, so that I can visualize my depth profile at a glance.

#### Acceptance Criteria

1. WHEN the statistics page renders THEN the system SHALL show a pie chart with five
   buckets by max depth in metres: 0–18, 19–30, 31–40, 41–55, and >55 msw.
2. WHEN bucket counts are computed THEN the boundaries SHALL be inclusive-upper as
   in legacy: `Depth <= 18`, `Depth > 18 AND <= 30`, `Depth > 30 AND <= 40`,
   `Depth > 40 AND <= 55`, `Depth > 55`.
3. WHEN the chart renders THEN each slice SHALL be labeled with its percentage and a
   legend SHALL identify each bucket, rendered on a `<canvas>` (no third-party chart
   library), consistent with the existing profile chart implementation.
4. IF the imperial length setting is active THEN bucket labels SHALL present the
   equivalent feet ranges while bucketing SHALL remain based on the stored metric
   depth values.

### Requirement 8 — Classification rule fidelity to legacy

**User Story:** As a maintainer, I want the statistics classification to match the
legacy rules, so that numbers are consistent with the old application.

#### Acceptance Criteria

1. WHEN classifying dive type multi-value fields THEN the system SHALL treat
   `Divetype` as a comma-separated list and match a type code when it appears alone,
   at the start, middle, or end (codes: 3=Night, 4=Drift, 5=Deep, 6=Cave, 7=Wreck,
   8=Photo).
2. WHEN classifying entry THEN `Entry = 1` SHALL count as Shore and `Entry = 2` as
   Boat.
3. WHEN classifying water THEN `Water = 1/2/3` SHALL map to Saltwater/Freshwater/
   Brackish.
4. WHEN classifying supply type THEN `SupplyType = 0/1/2` SHALL map to OC/SCR/CCR.
5. WHEN classifying deco/rep/tank THEN `Deco = 'True'`, `Rep = 'True'`,
   `DblTank = 'True'`/`'False'` SHALL be honored, and No-deco / Non-rep SHALL be
   derived as `total - matched`.

## Non-Functional Requirements

### Code Architecture and Modularity
- **Single Responsibility Principle**: Each repository method answers one query
  concern; each controller assembles one view payload; statistics computation lives
  in a dedicated service/repository, not in templates.
- **Modular Design**: New repository query methods (e.g. dives-by-site,
  dives-by-trip, dives-by-country) and a statistics repository/service SHALL be
  reusable and independently testable.
- **Dependency Management**: Controllers depend on repositories and support services
  via constructor injection through `adapters/web/bootstrap.php`; no new global
  state.
- **Clear Interfaces**: Return typed view arrays; keep template contracts explicit.

### Performance
- Overview and statistics pages SHALL render with a bounded number of queries
  (no per-row N+1 for dive counts; use aggregate/grouped queries where possible).
- Statistics SHALL be answerable within a small, fixed number of aggregate queries.

### Security
- All repositories remain **read-only** (SELECT only) against the Diving Log schema.
- All dynamic identifiers SHALL use bound PDO parameters; no string-concatenated SQL
  with user input.
- Template output SHALL be auto-escaped by Twig; media paths SHALL pass through the
  existing safe media resolver.

### Reliability
- All new behavior SHALL tolerate real-export column and value variants (missing
  optional columns, alternate column names) without fatal errors, consistent with
  existing repository fallbacks.
- `composer test`, `composer stan`, and `composer cs` SHALL all pass.

### Usability
- Rows SHALL be fully clickable (mouse + keyboard) and links SHALL be clear.
- The statistics layout SHALL be readable on wide and narrow screens and SHALL avoid
  a blue-heavy palette, consistent with existing styling.
