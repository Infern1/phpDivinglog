# Requirements Document

## Introduction

This feature adds per-page search-engine-optimization (SEO) metadata to every publicly rendered
HTML page in phpDivingLog: a unique `<title>`, a unique `<meta name="description">`, a
`<link rel="canonical">` tag, and a JSON-LD `schema.org` **WebPage** block describing the page.

Today every page shares the same `<title>` fallback (`app_name`) — no controller ever sets the
`title` variable the layout already supports — and there is no meta description, canonical link,
or structured data anywhere in the template set. Search engines therefore cannot distinguish a
dive detail page from the dive-site overview, and shared/indexed links are generic and
low-value. This feature closes that gap so dives, sites, countries, cities, shops, trips,
equipment, stats, and gallery pages are each individually discoverable and describe themselves
accurately when indexed or shared.

Because phpDivingLog has no built-in authentication and is often run as a personal/private
logbook, the feature also adds a config-driven opt-out so a site owner can suppress indexing
entirely rather than have this feature make a private install more discoverable by default.

Originating request (Lloyd Borrett): add support for metadata to improve search-engine
visibility — at minimum a unique title, description, and canonical tag per page, plus optionally
a unique WebPage schema markup per page.

## Alignment with Product Vision

This serves the product goal of "sharing dives, sites, trips, and photos ... with buddies, dive
clubs, or the wider diving community" (`product.md`) by making individual pages discoverable and
well-described outside the app, not just navigable inside it. It honors the product principles
of **config over code** (a single site-identity/opt-out setting in `.env`, no per-template
hand-editing) and **host-friendliness** (works under both existing URL routing modes — pretty
paths and query-string — with no new runtime dependency). It also respects the existing security
posture noted in `tech.md` ("no built-in authentication... privacy relies on web-server-level
controls") by giving private installs an explicit way to opt out of being indexed, rather than
silently changing that posture.

## Requirements

### Requirement 1 — Unique per-page `<title>`

**User Story:** As a visitor or search-engine crawler, I want each page to have a distinct,
descriptive `<title>`, so that search results, browser tabs, and bookmarks reflect the specific
dive, site, trip, etc. being viewed rather than a generic app name.

#### Acceptance Criteria

1. WHEN any content page (dive, dive site, country, city, shop, trip, or equipment
   overview/detail, stats, or either gallery) renders THEN the system SHALL populate the existing
   `title` Twig variable with content specific to that page's subject (e.g. dive number and date,
   site name, country name, equipment name).
2. WHEN an overview/list page is paginated beyond page 1 THEN the title SHALL include the page
   number so search engines and users can distinguish pages.
3. IF a route does not supply a specific title THEN the system SHALL fall back to the current
   `app_name`-based default rather than emitting an empty `<title>`.
4. WHEN the title is rendered THEN it SHALL rely on Twig's existing auto-escaping so no
   unescaped HTML or user-authored data can break the `<title>` tag or inject markup.

### Requirement 2 — Unique per-page meta description

**User Story:** As a visitor arriving from search results, I want a short, accurate description
of what the page is about, so that I can decide whether to click through before I even visit.

#### Acceptance Criteria

1. WHEN any content page renders THEN the system SHALL emit a `<meta name="description">` tag
   whose content is generated from that page's own data (e.g. dive date/site/depth, site
   location/country, trip name/dates, equipment name, or aggregate counts for overview pages).
2. WHEN the generated description would exceed a reasonable length for search-result display
   (~155-160 characters) THEN the system SHALL truncate it without cutting mid-word or leaving
   dangling punctuation.
3. IF a page has no meaningful per-item data (e.g. an empty overview) THEN the system SHALL fall
   back to a generic, still page-type-specific description rather than omitting the tag.
4. WHEN the description is rendered THEN it SHALL contain plain text only (no embedded HTML),
   consistent with Twig auto-escaping.

### Requirement 3 — Canonical URL

**User Story:** As a site owner, I want each page to declare its canonical URL, so that
duplicate-content variations (routing-mode differences, pagination) don't dilute search ranking
or create duplicate listings.

#### Acceptance Criteria

1. WHEN any content page renders THEN the system SHALL emit a
   `<link rel="canonical" href="...">` pointing to the absolute, public URL of that exact page.
2. WHEN the canonical URL is built THEN the system SHALL derive the scheme, host, and any path
   prefix from a single configured public base URL rather than from request headers, to avoid
   Host-header-driven canonical/URL injection.
3. WHEN the deployment is running in query-string routing mode THEN the canonical URL SHALL use
   the `?type=<resource>&id=<id>` form; WHEN the deployment is running in path-based routing mode
   THEN the canonical URL SHALL use the clean path form (e.g. `/dives/5`) — matching whichever
   URL scheme is actually reachable on that deployment.
4. WHEN an overview page is paginated THEN each page's canonical SHALL self-reference that page's
   own URL (including its page parameter), not collapse to page 1.
5. IF the public base URL is not configured THEN the system SHALL omit the canonical tag rather
   than emit a broken or relative one, and this SHALL be documented as a required setup step for
   this feature.

### Requirement 4 — WebPage structured data

**User Story:** As a search engine, I want machine-readable structured data describing each page,
so that phpDivingLog pages can be understood and potentially enhanced in search results.

#### Acceptance Criteria

1. WHEN any content page renders THEN the system SHALL emit a
   `<script type="application/ld+json">` block containing a `schema.org` `WebPage` object with at
   least `@context`, `@type: "WebPage"`, `url` (matching the canonical URL), `name` (matching the
   page title), and `description` (matching the meta description).
2. WHEN the page represents a specific item (dive, site, country, city, shop, trip, equipment)
   THEN the `WebPage` object SHOULD include an `inLanguage` value derived from the app's
   configured language, consistent across pages.
3. WHEN the JSON-LD block is rendered THEN all interpolated values SHALL be produced via safe
   JSON encoding (no raw string concatenation), so that titles or descriptions containing quotes
   or special characters cannot break the script block or inject script content.
4. IF the SEO opt-out (Requirement 6) is active THEN the system SHALL omit the WebPage JSON-LD
   block entirely.

### Requirement 5 — Configured site identity for URL/schema construction

**User Story:** As a site owner, I want to configure my site's public base URL once, so that
canonical links and structured data are correct everywhere without editing every template.

#### Acceptance Criteria

1. WHEN the application is configured THEN the system SHALL expose a single public base URL
   setting, following the existing `.env` / `Config` pattern, used to build canonical URLs and the
   `schema.org` `url` field.
2. WHEN no explicit public base URL is configured THEN the system SHALL NOT guess one from
   request headers, consistent with Requirement 3.2's safety goal.
3. WHEN `.env.example` is updated for this feature THEN it SHALL document the setting with an
   example value and a note that it must be the public, internet-facing URL of the deployment
   (not a local/dev URL).

### Requirement 6 — SEO opt-out for private installs

**User Story:** As a site owner running a private or personal logbook, I want to disable
search-engine metadata entirely, so that I don't inadvertently make my dive history more
discoverable than I intend.

#### Acceptance Criteria

1. WHEN a config flag (default: enabled) is set to disabled THEN the system SHALL emit
   `<meta name="robots" content="noindex,nofollow">` on every page INSTEAD OF the
   title/description/canonical/WebPage enhancements from Requirements 1-4 (the existing generic
   `<title>` fallback SHALL still render so the page remains usable).
2. WHEN the flag is enabled (the default) THEN the system SHALL behave per Requirements 1-4.
3. WHEN the flag's value changes THEN no code change SHALL be required — it SHALL be a single
   `.env` toggle, consistent with the project's config-over-code principle.

### Requirement 7 — Exclusion of non-document routes

**User Story:** As a search engine, I want to be steered away from fragments and
embeddable/API responses that aren't standalone documents, so that only real pages are indexed.

#### Acceptance Criteria

1. WHEN the AJAX dive-detail partial (`dive_detail_partial.html.twig`) is rendered THEN the
   system SHALL NOT emit head metadata — it has no `<head>` and is not a standalone document
   today, so no change is required.
2. WHEN the embeddable summary view (`summary.overview` / `divesummary.html.twig`) renders THEN
   the system SHALL mark it `noindex` — it is designed to be iframed into another site, not
   indexed as its own search result.
3. WHEN the JSON profile-series endpoint (`profile.detail`) or any `adapters/api/` JSON response
   is served THEN no HTML metadata applies — confirmed as a non-goal, no behavior change
   required.
4. WHEN an unmatched route is requested THEN the existing plain-text 404 response MAY remain
   unchanged — a dedicated 404 template/page is out of scope for this feature.

### Requirement 8 — Verifying the feature (automated correctness + manual SEO validation)

**User Story:** As the team shipping this feature, I want a concrete way to know it worked, so
that "improves SEO" isn't a vague, unverifiable claim.

This codebase's automated tests **cannot** measure actual search-ranking improvement — that
depends on external crawlers, indexing latency, and competing sites, none of which are under this
project's control or observable in CI. What the test suite *can* verify is that the metadata is
technically correct, unique per page, and well-formed, which is the precondition for any SEO
benefit. Real-world impact is checked separately, manually, with standard external tools.

#### Acceptance Criteria

1. WHEN the automated test suite runs THEN HTTP smoke tests SHALL assert that two distinct
   pages of the same type (e.g. two different dives, two different dive sites) render two
   different `<title>` values, two different meta descriptions, and two different canonical URLs
   — proving genuine per-page uniqueness rather than a shared static string.
2. WHEN a page's JSON-LD block is emitted THEN a test SHALL parse it as JSON and assert it
   contains the required `schema.org` `WebPage` keys (`@context`, `@type`, `url`, `name`,
   `description`) with values matching that page's title/description/canonical.
3. WHEN the canonical URL is emitted THEN tests SHALL cover both routing modes (query-string and
   path-based) and assert the expected URL form for each, per Requirement 3.3.
4. WHEN the SEO opt-out flag (Requirement 6) is toggled THEN a test SHALL assert the `noindex`
   meta tag appears when disabled and the full metadata set appears when enabled.
5. WHEN this feature ships THEN the team SHALL additionally perform a manual, one-time
   verification pass — running a representative sample of page types through an external
   structured-data validator (e.g. Google's Rich Results Test / schema.org validator) to confirm
   the JSON-LD is not just well-formed but actually recognized as valid `WebPage` data, and
   noting the result in the PR/changelog. This manual step is documentation, not an automated
   gate, since it depends on third-party tooling outside the test suite.
6. Ongoing ranking/indexing impact (e.g. via Google Search Console, if the site owner has it
   configured) is explicitly OUT OF SCOPE for this feature's completion criteria — it is a
   longer-term observation the site owner can make after deployment, not a condition of "done."

## Non-Functional Requirements

### Code Architecture and Modularity
- **Single Responsibility Principle**: A dedicated SEO/meta view-model builder SHALL compute
  title/description/canonical/schema for a given route and its data, rather than scattering
  string concatenation across every controller — controllers and templates stay thin.
- **Modular Design**: The layout's `<head>` SHALL gain well-defined Twig variables/blocks (e.g.
  `meta_description`, `canonical_url`, `schema_json`, `robots`) that any current or future
  template can populate, mirroring the existing `title` variable pattern.
- **Dependency Management**: No new third-party runtime dependency — JSON-LD encoding uses PHP's
  built-in `json_encode`; URL building uses existing `Config`/`Router` primitives.
- **Clear Interfaces**: The per-route mapping of "what goes in the title/description" SHALL be
  documented so it is obvious which controller is responsible for which page's metadata.

### Performance
- Metadata generation SHALL reuse data the controller has already fetched for the page — no
  additional queries against the read-only Diving Log schema.

### Security
- All interpolated values (title, description, JSON-LD) SHALL go through Twig auto-escaping
  (HTML) or `json_encode` (JSON-LD) — never raw string concatenation — so dive comments, site
  names, or other user-authored data cannot inject markup or script content.
- Canonical and schema URLs SHALL be built from configured server-side values, never from
  unvalidated request input (e.g. the `Host` header), to avoid host-header injection into
  indexable links.

### Reliability
- Missing or partial data (no dive site, no description-worthy fields) SHALL degrade to a
  sensible generic description rather than error or emit an empty tag.
- The opt-out toggle (Requirement 6) SHALL be covered by an HTTP smoke test so a regression
  cannot silently start indexing a private install, or silently stop indexing a public one.
- See Requirement 8 for the full testing strategy: automated tests verify technical correctness
  and per-page uniqueness; actual search-ranking impact is validated manually, post-deploy, with
  external tools, and is not part of this feature's "done" criteria.

### Usability
- This feature has no visible UI for visitors; success is measured by valid, distinct
  `<title>`/description/canonical/JSON-LD per page, verifiable via view-source and structured
  data testing tools.
