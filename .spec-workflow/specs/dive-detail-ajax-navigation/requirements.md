# Requirements Document

## Introduction

The dive detail page (`/dives/{number}`) shows a sticky Logbook sidebar plus prev/next
sequence arrows. Today each of those is a full-page navigation: clicking a dive reloads the
whole document, which resets the main window scroll position and produces a visible jump and
flicker. This feature replaces that with in-place (AJAX) content swapping so switching between
dives feels instant and preserves the reader's scroll position — matching the seamless
browsing behavior seen on sites like divelogs.org — while keeping every URL a real, shareable,
refresh-safe address.

## Alignment with Product Vision

phpDivingLog is a server-rendered reporting layer over a diver's logbook, using dependency-free
vanilla JS for interactivity (theme controller, charts, sortable tables, lightbox). This feature
stays within that philosophy: a small, framework-free progressive-enhancement layer on top of the
existing Twig-rendered detail page. It improves usability (fluid browsing of a large logbook)
without introducing a SPA framework, without changing the read-only data model, and without
breaking direct links, refresh, or the no-JavaScript fallback.

## Requirements

### Requirement 1 — In-place dive switching from the Logbook sidebar

**User Story:** As a diver browsing my logbook, I want clicking a dive in the sidebar to swap the
detail content in place, so that the page does not jump to the top and I keep my reading position.

#### Acceptance Criteria

1. WHEN the user clicks a Logbook sidebar link AND JavaScript is enabled THEN the system SHALL
   fetch the target dive's detail content and replace the current detail content without a full
   page reload.
2. WHEN the detail content is swapped THEN the system SHALL preserve the window scroll position
   and the Logbook sidebar's internal scroll position (no jump to top).
3. WHEN the detail content is swapped THEN the system SHALL update the active/highlighted item in
   the Logbook list to the newly selected dive.
4. IF JavaScript is disabled or unavailable THEN the sidebar links SHALL continue to work as
   normal full-page navigations (progressive enhancement, no regression).

### Requirement 2 — In-place switching from the prev/next sequence arrows

**User Story:** As a diver, I want the previous/next arrows to swap dives in place too, so that
stepping through consecutive dives is fluid and consistent with the sidebar.

#### Acceptance Criteria

1. WHEN the user clicks the previous or next sequence arrow AND JavaScript is enabled THEN the
   system SHALL swap the detail content in place using the same mechanism as the sidebar links.
2. WHEN a swap completes THEN the system SHALL update the prev/next arrows' targets and
   enabled/disabled state to reflect the newly shown dive.
3. IF a boundary is reached (no previous or no next dive) THEN the corresponding arrow SHALL be
   shown as disabled and perform no navigation.

### Requirement 3 — Correct address bar, history, and refresh behavior

**User Story:** As a user, I want the URL to always reflect the dive I am viewing, so that I can
bookmark, refresh, share, and use the browser back/forward buttons reliably.

#### Acceptance Criteria

1. WHEN a dive is swapped in place THEN the system SHALL update the browser address bar to the
   canonical dive URL (`/dives/{number}`) using history state, without reloading.
2. WHEN the user presses the browser Back or Forward button THEN the system SHALL restore the
   corresponding dive's detail content in place and keep the address bar consistent.
3. WHEN the user refreshes or opens a dive URL directly THEN the system SHALL render the full
   detail page server-side exactly as it does today.
4. WHEN the address bar is updated during a swap THEN the page title SHALL be updated to reflect
   the shown dive.

### Requirement 4 — Server-side partial rendering endpoint

**User Story:** As the application, I need a way to return just the dive detail content fragment,
so that the client can swap it in without re-rendering the entire page shell.

#### Acceptance Criteria

1. WHEN the detail route receives a request marked as a partial (for example via a request header
   or an explicit query flag) THEN the system SHALL return only the dive detail content fragment,
   not the full page shell (no header, nav, or footer).
2. WHEN the same route is requested normally THEN the system SHALL return the complete page as it
   does today.
3. WHEN a partial is requested for a non-existent dive THEN the system SHALL return a not-found
   response consistent with the existing full-page not-found handling.
4. WHEN the partial fragment is produced THEN it SHALL contain everything the client needs to
   refresh the view: the hero/metadata, the detail panels, the pictures, and the data needed to
   update the sidebar active state and the prev/next arrow targets.

### Requirement 5 — Re-initialization of dynamic content after a swap

**User Story:** As a diver, I want the dive profile chart, the pictures lightbox, and other
interactive elements to work after switching dives, so that in-place navigation behaves exactly
like a fresh page load.

#### Acceptance Criteria

1. WHEN the detail content is swapped THEN the system SHALL (re)initialize the dive profile chart
   for the newly shown dive.
2. WHEN the detail content is swapped THEN the pictures lightbox SHALL operate on the newly shown
   dive's pictures group.
3. WHEN the detail content is swapped THEN any theme-dependent rendering (for example charts)
   SHALL respect the current light/dark theme and palette.
4. IF a swap fails (network or server error) THEN the system SHALL fall back to a normal full-page
   navigation to the target dive so the user is never left on a broken view.

## Non-Functional Requirements

### Code Architecture and Modularity

- **Single Responsibility Principle**: The partial-rendering concern lives in the controller/front
  controller and a focused template include; the client swap logic lives in one small vanilla-JS
  module.
- **Modular Design**: The detail content markup SHALL be factored so the same fragment is used by
  both the full page and the partial response (no duplicated markup).
- **Progressive Enhancement**: The JS layer SHALL enhance existing anchors; with JS off, behavior
  is unchanged.
- **Dependency Management**: No new runtime dependencies, no SPA framework, no build step; the
  client code stays dependency-free vanilla JS consistent with the existing assets.

### Performance

- A dive swap SHALL transfer only the detail fragment (not the full shell), keeping payloads small.
- Re-initialization SHALL avoid duplicate event bindings or leaked observers across repeated swaps.

### Security

- The partial endpoint SHALL reuse the existing read-only repository/controller path and output
  escaping; no new SQL, no unescaped HTML, and no change to the read-only data model.

### Reliability

- Any swap error SHALL degrade gracefully to a full-page navigation (Requirement 5.4).
- Direct load, refresh, and browser back/forward SHALL remain fully functional (Requirement 3).

### Usability

- Switching dives SHALL preserve scroll position and avoid visible jumps/flicker.
- The address bar, page title, sidebar active state, and prev/next arrows SHALL always reflect the
  dive currently shown.
