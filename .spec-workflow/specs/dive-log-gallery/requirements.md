# Requirements Document

## Introduction

This feature adds a new **"Dive Log Gallery"** section: a single page that shows thumbnails of
**all** pictures across **all** dives in the logbook, paginated for performance. Clicking a
thumbnail opens the existing lightbox (now with next/previous navigation) and, in addition to
the photo, shows contextual information about the dive the photo belongs to:

```
Dive 202 by <diver>
Location: Japan, Okinawa | Divesite: Nakayukui | When: 22.08.2024 14:00 | view dive (link)
```

Today the app only has a **per-dive** gallery at `/gallery/{id}`. There is no way to browse the
whole logbook's photo collection in one place. This feature provides that aggregate view, reuses
the shared lightbox component, and enriches the lightbox with per-photo dive metadata and a deep
link back to the dive detail page.

The diver name shown in the lightbox is the logbook owner's **Personal profile display name**
(first + last name) — the same source already used for the site title — so no new configuration
is introduced.

## Alignment with Product Vision

This directly serves Key Feature #4 ("Photo galleries ... with automatic thumbnail generation and
lightbox overlay viewing") and the product goal of "Displaying dive profile graphs, galleries, and
statistics in an attractive, navigable form" in `product.md`. It strengthens the sharing story
("Sharing dives, sites, trips, and photos") by giving visitors a browsable, cross-dive photo wall
with links back into each dive. It honors the product principles of read-only presentation
(no data changes), config-over-code (reuses existing profile/units config), and host-friendliness
(server-rendered Twig, vendored assets, pagination to keep pages light).

## Requirements

### Requirement 1 — Aggregate gallery page

**User Story:** As a visitor, I want a single "Dive Log Gallery" page showing thumbnails of every
dive photo, so that I can browse the whole logbook's images without opening each dive.

#### Acceptance Criteria

1. WHEN a user navigates to the gallery route (`/gallery`) THEN the system SHALL render a page
   titled "Dive Log Gallery" containing a grid of photo thumbnails drawn from all dives.
2. WHEN the logbook contains dive pictures THEN the system SHALL display each picture's thumbnail
   using the existing thumbnail generation/resolution mechanism.
3. IF the logbook contains no dive pictures THEN the system SHALL render the page with an
   appropriate empty-state message instead of an empty grid.
4. WHEN the gallery page renders THEN each thumbnail SHALL be a lightbox trigger consistent with
   the existing `data-lightbox` convention and shared lightbox component.

### Requirement 2 — Pagination

**User Story:** As a visitor with a large logbook, I want the gallery to be paginated, so that the
page loads quickly and stays navigable.

#### Acceptance Criteria

1. WHEN the total number of pictures exceeds one page's capacity THEN the system SHALL display only
   one page of thumbnails at a time and provide pagination controls to move between pages.
2. WHEN a user requests a specific page via the page parameter (e.g. `?page=2`) THEN the system
   SHALL render that page's slice of thumbnails.
3. IF the requested page is out of range (below 1 or beyond the last page) THEN the system SHALL
   clamp to the nearest valid page rather than error.
4. WHEN pagination controls are shown THEN the system SHALL indicate the current page and the
   availability of previous/next pages.
5. WHEN the page slice is queried THEN the system SHALL request only that page's rows from the data
   layer (bounded LIMIT/OFFSET), not load all pictures into memory.

### Requirement 3 — Lightbox photo metadata

**User Story:** As a visitor viewing a photo in the lightbox, I want to see which dive it belongs
to and its key context, so that I understand where and when the photo was taken.

#### Acceptance Criteria

1. WHEN a user opens a gallery photo in the lightbox THEN the system SHALL display an information
   block containing: the dive number, the diver name, the location, the dive site, and the
   date/time of the dive.
2. WHEN the information block renders THEN it SHALL follow the format:
   `Dive <number> by <diver>` on the first line and
   `Location: <location> | Divesite: <site> | When: <date time> | view dive` on the second line.
3. WHEN the location, dive site, or date/time is available for the dive THEN the system SHALL show
   the resolved human-readable value using the app's existing formatting (date/time format,
   country/city composition).
4. IF a metadata value is unavailable for a given dive (e.g. no dive site) THEN the system SHALL
   omit or show a neutral placeholder for that value without breaking the layout.
5. WHEN the diver name is shown THEN it SHALL be the Personal profile display name (first + last
   name); IF no profile name is available THEN the system SHALL omit the `by <diver>` portion.

### Requirement 4 — Deep link back to the dive

**User Story:** As a visitor interested in a photo, I want a "view dive" link in the lightbox, so
that I can jump directly to that dive's full detail page.

#### Acceptance Criteria

1. WHEN the lightbox information block renders for a gallery photo THEN it SHALL include a
   "view dive" link pointing to that photo's dive detail page (`/dives/<number>`).
2. WHEN the user activates the "view dive" link THEN the system SHALL navigate to the dive detail
   page for the correct dive.
3. WHEN the link is rendered THEN it SHALL be keyboard-focusable and accessibly labeled.

### Requirement 5 — Navigation and discoverability

**User Story:** As a visitor, I want the Dive Log Gallery to be reachable from the primary
navigation, so that I can find it easily.

#### Acceptance Criteria

1. WHEN any page renders the primary navigation THEN the system SHALL include a link to the Dive
   Log Gallery (`/gallery`).
2. WHEN the gallery route is requested in either URL mode (pretty URLs or query-string mode) THEN
   the system SHALL resolve it correctly without colliding with the existing per-dive gallery
   (`/gallery/{id}`).

### Requirement 6 — Lightbox navigation across the gallery

**User Story:** As a visitor browsing the gallery, I want next/previous navigation within the
lightbox, so that I can move through the page's photos as a sequence.

#### Acceptance Criteria

1. WHEN a user opens a photo from the gallery THEN the shared lightbox SHALL treat the page's
   gallery thumbnails as one navigation group (next/previous, wrap-around, keyboard, counter) per
   the existing lightbox navigation behavior.
2. WHEN the user navigates to another photo in the lightbox THEN the system SHALL update the
   displayed image AND its dive metadata information block AND the "view dive" link to match the
   newly displayed photo.

## Non-Functional Requirements

### Code Architecture and Modularity
- **Single Responsibility Principle**: A dedicated controller action builds the gallery view model;
  the repository owns paginated data access; the template owns markup; the shared lightbox owns
  overlay behavior.
- **Modular Design**: Per-photo dive metadata SHALL be conveyed to the lightbox via markup
  `data-*` attributes on the trigger anchors, so the shared lightbox stays template-agnostic and
  other galleries are unaffected.
- **Dependency Management**: No new third-party runtime dependency. Reuse existing repositories,
  `MediaResolver`, `Formatter`, and the shared `lightbox.js`.
- **Clear Interfaces**: The gallery data-attribute contract (which attributes the lightbox reads)
  SHALL be documented alongside the existing `data-lightbox` / `data-lightbox-group` convention.

### Performance
- The data layer SHALL fetch only the current page of pictures via bounded LIMIT/OFFSET and count
  the total via a single count query.
- Per-page dive metadata resolution SHALL be bounded (e.g. resolve the distinct dives referenced by
  the page's pictures) to avoid unbounded N+1 queries per photo.
- Repositories SHALL remain read-only against the Diving Log schema and respect the validated
  `TABLE_PREFIX`.

### Security
- All picture paths, metadata values, and the "view dive" URL SHALL be produced by the existing
  Twig templates and services (auto-escaped); JavaScript SHALL treat DOM/data-attribute values as
  data and SHALL NOT inject unescaped HTML.
- All database access SHALL use prepared PDO statements consistent with existing repositories.

### Reliability
- The gallery and lightbox SHALL degrade gracefully when metadata is partial or absent (missing
  site, city, country, or profile name) without throwing errors or breaking layout.
- Existing per-dive gallery (`/gallery/{id}`) and all other lightbox-consuming pages SHALL remain
  unaffected.

### Usability
- Thumbnails and pagination controls SHALL be styled consistently with the existing gallery grid
  and Material Design 3 (Beer CSS) theme, and SHALL work in both light and dark themes.
- The lightbox information block and "view dive" link SHALL be legible and accessible in both
  themes, with adequate contrast and focusable controls.
