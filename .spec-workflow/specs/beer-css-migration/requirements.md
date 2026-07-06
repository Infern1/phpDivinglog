# Requirements Document

## Introduction

This feature migrates the phpDivingLog web adapter's presentation layer from its bespoke,
hand-written stylesheet (`public/assets/css/app.css`) to **Beer CSS**, a lightweight
(~14.4 KB brotli) Material Design 3 CSS framework. The goal is a modern, consistent,
maintainable UI with **first-class light/dark theming** — capabilities the current
single hand-authored theme does not provide.

Beer CSS is chosen because it is actively maintained, requires **no build step**, can be
**vendored locally** (no CDN dependency), and provides turnkey dark mode via a single
`class="dark|light"` setting on `<body>`. These properties fit phpDivingLog's self-hosted,
copy-to-deploy, `public/`-rooted architecture.

The migration is **presentation-only**: it re-skins the Twig templates and replaces the
stylesheet. It must not change any domain logic, repositories, controllers, routing, or
data. All existing behaviours (dive-logbook centering, sortable tables, image lightbox,
dive-profile and statistics charts) must be preserved.

## Alignment with Product Vision

The product steering (`product.md`) lists **"Modernization — modernize the presentation
and data-access layers"** and **"Displaying dive profile graphs, galleries, and statistics
in an attractive, navigable form"** as explicit goals. This feature directly advances the
presentation-layer modernization: it replaces ad-hoc CSS with a maintained design system
and adds a modern, accessible dark mode, while upholding the product principles of
**host-friendliness** (vendored assets, no build tooling) and **read-only presentation**
(no data or logic changes).

## Requirements

### Requirement 1 — Adopt Beer CSS as the UI framework

**User Story:** As a maintainer, I want the app styled with a maintained Material Design 3
framework instead of bespoke CSS, so that future UI changes are easier and consistent.

#### Acceptance Criteria

1. WHEN the application renders any page THEN the system SHALL load Beer CSS from
   locally-vendored assets under `public/` (no external CDN request at runtime).
2. IF the deployment has no Node/build tooling THEN the system SHALL still render fully
   styled pages (Beer CSS requires no build step).
3. WHEN Beer CSS is integrated THEN the system SHALL NOT reintroduce any prohibited legacy
   dependency (Smarty, wp-db.php, jqPlot, legacy root page controllers).
4. WHEN the migration is complete THEN the system SHALL remove or retire the bespoke
   `public/assets/css/app.css` rules that Beer CSS replaces, keeping only project-specific
   overrides in a clearly separated custom stylesheet.

### Requirement 2 — Light and dark theming with persistence

**User Story:** As a visitor, I want to switch between light and dark mode and have my
choice remembered, so that I can read my logbook comfortably in any lighting.

#### Acceptance Criteria

1. WHEN a first-time visitor with no stored preference loads the site THEN the system SHALL
   default the theme to the operating system preference (`prefers-color-scheme`).
2. WHEN a visitor activates the theme toggle THEN the system SHALL switch between light and
   dark by setting `class="light"` / `class="dark"` on the `<body>` element.
3. WHEN a visitor has chosen a theme THEN the system SHALL persist the choice (e.g.
   `localStorage`) and re-apply it on subsequent page loads and navigations.
4. WHEN a page loads with a stored dark preference THEN the system SHALL apply the theme
   before first paint so there is no light-to-dark flash (FOUC).
5. WHEN JavaScript is disabled THEN the system SHALL still render a usable, readable page
   in a default theme.

### Requirement 3 — Preserve all pages and functionality

**User Story:** As a visitor, I want every existing page to look modern but keep all its
current information and interactions, so that nothing I rely on is lost.

#### Acceptance Criteria

1. WHEN each existing view is restyled THEN the system SHALL preserve all currently
   displayed data fields and cross-links, covering: layout/header/nav/footer, dive detail,
   dives overview, dive-site overview and detail, country overview and detail, trip overview
   and detail, equipment overview and detail, statistics, and gallery.
2. WHEN a user selects a dive from the logbook list on the dive detail page THEN the system
   SHALL keep the selected dive centered in the scrollable list (existing `tables.js`
   behaviour preserved).
3. WHEN a user interacts with sortable/clickable overview tables THEN the system SHALL
   preserve the existing sort and row-navigation behaviour.
4. WHEN a user opens a dive image THEN the system SHALL preserve the existing lightbox
   overlay behaviour.
5. WHEN a dive detail or statistics page renders THEN the system SHALL preserve the existing
   canvas-based dive-profile and statistics charts, rendered legibly in both themes.

### Requirement 4 — Responsive and accessible navigation

**User Story:** As a visitor on any device, I want navigation and content to adapt to my
screen and be accessible, so that the logbook is usable on phone, tablet, and desktop.

#### Acceptance Criteria

1. WHEN the viewport is narrow (mobile) THEN the system SHALL present a usable navigation
   pattern (e.g. Beer CSS responsive nav) without horizontal overflow.
2. WHEN the viewport is wide (desktop) THEN the system SHALL present the primary navigation
   in a persistent, readable form.
3. WHEN a keyboard-only user navigates THEN the system SHALL keep all links, the theme
   toggle, and interactive controls reachable and operable via keyboard.
4. WHEN the theme toggle is rendered THEN the system SHALL expose an accessible label and
   current-state indication for assistive technology.
5. WHEN content is rendered in either theme THEN the system SHALL maintain sufficient color
   contrast for text and interactive elements.

### Requirement 5 — No regressions and quality gates stay green

**User Story:** As a maintainer, I want the migration to introduce no functional or test
regressions, so that I can ship it with confidence.

#### Acceptance Criteria

1. WHEN the migration is implemented THEN the system SHALL keep `composer test`,
   `composer stan`, and `composer cs` passing.
2. WHEN HTTP smoke tests assert on page markup THEN the system SHALL keep those assertions
   passing, updating them only where markup contracts intentionally change.
3. WHEN repositories, controllers, models, routing, or configuration are considered THEN the
   system SHALL make no changes to them for the purpose of this migration (presentation-only).
4. WHEN the Diving Log database schema is accessed THEN the system SHALL remain read-only
   (no schema or data-access changes).

## Non-Functional Requirements

### Code Architecture and Modularity
- **Single Responsibility Principle**: Keep Beer CSS vendor assets, project overrides, and
  theme-toggle JavaScript in separate, clearly-named files.
- **Modular Design**: Restyle via shared Twig partials (header, nav, footer, dive-rows) so
  markup conventions are defined once and reused across pages.
- **Dependency Management**: Vendor Beer CSS (and its icon font) locally under `public/`;
  do not add a runtime CDN dependency or a mandatory build pipeline.
- **Clear Interfaces**: The theme mechanism (body class + persisted preference) is the
  single contract between the CSS framework and the app.

### Performance
- Total added CSS/JS payload should remain small (Beer CSS core ~14.4 KB brotli); assets
  are served locally and cacheable.
- Theme application must occur before first paint to avoid a flash of the wrong theme.

### Security
- No new server-side inputs are introduced; theme state is client-side only.
- Vendored assets must be served from the app's own origin (no third-party runtime fetch).

### Reliability
- Pages must remain fully functional with JavaScript disabled (default theme, all content
  and links intact).
- The migration proceeds page-by-page so the site is never left in a broken intermediate
  state; each page remains shippable.

### Usability
- Consistent Material Design 3 look across all pages.
- Honor the user's OS theme by default and remember explicit overrides.
- Preserve the established UX preference: modern styling that is not blue-dominant and does
  not cause layout jumps on interaction.
