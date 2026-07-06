# Requirements Document

## Introduction

The dive detail page presents a "Pictures" gallery. Today, clicking any thumbnail opens a
single image in a modal lightbox overlay, but the viewer is a dead end: there is no way to
move to the next or previous photo without closing the overlay and clicking another
thumbnail. This feature adds in-lightbox navigation (next/previous, keyboard, and position
indicator) so a diver can browse a dive's whole photo set as a sequence.

Because the lightbox is a single shared, dependency-free vanilla-JS component
(`public/assets/js/lightbox.js`) reused by several pages (dive detail, dive-site detail,
dive gallery, and certification/stats galleries), the enhancement is made to that shared
component. Navigation is scoped **per gallery group** so that browsing cycles only through
the images that belong together (e.g. one dive's pictures), never mixing unrelated images
such as map thumbnails or certification scans that happen to be on the same page.

## Alignment with Product Vision

This directly supports Key Feature #4 ("Photo galleries ... with automatic thumbnail
generation and lightbox overlay viewing") and Key Feature #1 (rich dive detail pages) in
`product.md`. It advances the product goal of "Displaying dive profile graphs, galleries,
and statistics in an attractive, navigable form" by making galleries genuinely navigable.
It honors the product principles of read-only presentation (no data changes), host-
friendliness (no new runtime dependency — remains vanilla JS with vendored assets), and
accessibility (Material Design 3 UI with accessible controls).

## Requirements

### Requirement 1 — Navigate to next/previous photo

**User Story:** As a diver viewing a dive's photos, I want to click a "next" or "previous"
control inside the lightbox, so that I can browse through all of the dive's pictures without
closing and reopening the overlay.

#### Acceptance Criteria

1. WHEN a user opens the lightbox from a gallery containing more than one image THEN the
   system SHALL display a visible "previous" control and a visible "next" control within the
   overlay.
2. WHEN the user activates the "next" control THEN the system SHALL replace the displayed
   image with the next image in the same gallery group.
3. WHEN the user activates the "previous" control THEN the system SHALL replace the displayed
   image with the previous image in the same gallery group.
4. WHEN the user activates a navigation control THEN the system SHALL update the image `alt`
   text (and any caption/description) to match the newly displayed image.
5. IF the gallery group contains only a single image THEN the system SHALL hide (or disable)
   the "previous" and "next" controls.

### Requirement 2 — Sequence boundaries and wrap-around

**User Story:** As a diver browsing photos, I want predictable behavior at the first and
last photo, so that I always understand where I am in the set.

#### Acceptance Criteria

1. WHEN the currently displayed image is the last image in the group AND the user activates
   "next" THEN the system SHALL wrap to the first image in the group.
2. WHEN the currently displayed image is the first image in the group AND the user activates
   "previous" THEN the system SHALL wrap to the last image in the group.
3. WHEN the lightbox opens THEN the system SHALL treat the clicked thumbnail as the current
   image and start navigation from that position.

### Requirement 3 — Keyboard navigation

**User Story:** As a keyboard user, I want to move between photos with the arrow keys and
close with Escape, so that I can browse the gallery without a mouse.

#### Acceptance Criteria

1. WHEN the lightbox is open AND the user presses the Right Arrow key THEN the system SHALL
   advance to the next image (using the same wrap-around behavior as Requirement 2).
2. WHEN the lightbox is open AND the user presses the Left Arrow key THEN the system SHALL
   move to the previous image (using the same wrap-around behavior as Requirement 2).
3. WHEN the lightbox is open AND the user presses the Escape key THEN the system SHALL close
   the overlay (preserving the existing native `<dialog>` close behavior).
4. WHEN the lightbox is open THEN keyboard focus SHALL be managed within the overlay so that
   the close and navigation controls are reachable and operable.

### Requirement 4 — Position indicator

**User Story:** As a diver browsing a set of photos, I want to see my position in the set,
so that I know how many photos remain.

#### Acceptance Criteria

1. WHEN the lightbox is open on a group with more than one image THEN the system SHALL show a
   position indicator (e.g. "3 / 8") reflecting the current image index and the group total.
2. WHEN the user navigates to another image THEN the system SHALL update the position
   indicator to match the newly displayed image.
3. IF the gallery group contains only a single image THEN the system MAY omit the position
   indicator.

### Requirement 5 — Correct grouping across shared galleries

**User Story:** As a user on a page that has more than one gallery (e.g. a dive-site page
with a map image and a separate photo gallery), I want navigation to stay within the gallery
I opened, so that unrelated images are not mixed into the sequence.

#### Acceptance Criteria

1. WHEN a page contains multiple distinct galleries THEN the system SHALL confine next/
   previous navigation to the images belonging to the gallery from which the lightbox was
   opened.
2. WHEN a lightbox trigger is not part of a multi-image gallery (e.g. a standalone map image)
   THEN the system SHALL treat it as a single-image group per Requirement 1.5.
3. WHEN new galleries adopt the lightbox trigger convention THEN the grouping behavior SHALL
   apply automatically without page-specific JavaScript.

## Non-Functional Requirements

### Code Architecture and Modularity
- **Single Responsibility Principle**: All navigation logic remains inside the single shared
  lightbox module (`public/assets/js/lightbox.js`); templates only declare markup/data
  attributes.
- **Modular Design**: The gallery-grouping contract SHALL be expressed via data attributes
  in markup so any template can opt in without bespoke JS.
- **Dependency Management**: No new third-party runtime dependency. The implementation SHALL
  remain dependency-free vanilla JS consistent with the existing `public/assets/js/` assets.
- **Clear Interfaces**: The trigger markup contract (attributes read by the lightbox) SHALL
  be documented so all four consuming templates use it consistently.

### Performance
- Navigation SHALL operate on data already present in the DOM (thumbnail anchors); no
  additional network requests are required beyond loading the already-referenced full images.
- The component SHALL continue to use a single delegated event listener rather than binding
  per-thumbnail handlers, preserving current lightweight behavior.

### Security
- Image URLs and `alt`/caption text SHALL continue to be produced by the existing Twig
  templates (auto-escaped); the JS SHALL treat DOM-sourced values as data and SHALL NOT
  inject unescaped HTML.
- No change to the read-only data model; this is a presentation-layer enhancement only.

### Reliability
- WHEN the lightbox markup or attributes are absent/partial THEN the component SHALL degrade
  gracefully (fall back to single-image display) without throwing errors.
- Existing single-image lightbox behavior on all current pages SHALL remain intact.

### Usability
- Controls SHALL be visible, adequately sized touch targets and styled consistently with the
  Material Design 3 (Beer CSS) theme and the existing `.lightbox-dialog` styles in
  `public/assets/css/custom.css`.
- Navigation controls and the position indicator SHALL have accessible labels
  (e.g. `aria-label`) and SHALL respect the current light/dark theme.
