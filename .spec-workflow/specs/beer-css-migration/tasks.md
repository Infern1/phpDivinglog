# Tasks Document

- [x] 1. Vendor Beer CSS assets locally under public/
  - Files: `public/assets/vendor/beercss/beer.min.css`, `public/assets/vendor/beercss/beer.min.js`, `public/assets/vendor/beercss/material-dynamic-colors.min.js`, plus vendored Material Symbols icon font + base typography (css + font files) under `public/assets/vendor/beercss/`
  - Download the Beer CSS dist (pin a specific version) and its icon/typography assets so the app renders fully offline (no CDN at runtime)
  - Extend `tools/build-assets.mjs` only if needed so the vendor folder is included in the copy step
  - Purpose: Provide the Material Design 3 framework locally, satisfying host-friendliness/no-build/no-CDN constraints
  - _Leverage: tools/build-assets.mjs_
  - _Requirements: 1.1, 1.2, 1.3_
  - _Prompt: Implement the task for spec beer-css-migration, first run spec-workflow-guide to get the workflow guide then implement the task: Role: Front-end build engineer familiar with vendored assets and offline-first static hosting | Task: Vendor a pinned Beer CSS release (beer.min.css, beer.min.js, material-dynamic-colors.min.js) plus the Material Symbols icon font and required base typography into public/assets/vendor/beercss/ so pages render fully offline, following requirements 1.1-1.3 | Restrictions: No runtime CDN references, no mandatory build pipeline, do not modify PHP/domain code, keep files under public/ so nginx serves them | Success: All vendored files exist under public/assets/vendor/beercss/, versions are pinned and documented, nothing references an external CDN, and the folder is copied by the asset step if that convention is used. Set the task to in-progress [-] before starting, log-implementation after completion, then mark it [x]._

- [x] 2. Implement the client-side theme controller (no-FOUC + toggle + persistence)
  - Files: `public/assets/js/theme.js` (new); an inline head snippet to be embedded in `templates/layout.html.twig` in task 3
  - Inline head script resolves theme = `localStorage['divelog:theme']` ?? `prefers-color-scheme`, applies `light`/`dark` class to `<body>`/`<html>` synchronously before first paint
  - `theme.js` binds the header toggle: flips the class, persists to `localStorage`, updates the toggle's `aria-pressed`/label, and dispatches a `themechange` event on `window`
  - Purpose: Single client-side contract for theme state with no flash-of-wrong-theme and JS-disabled safety
  - _Leverage: public/assets/js/tables.js (module style/IIFE conventions)_
  - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5_
  - _Prompt: Implement the task for spec beer-css-migration, first run spec-workflow-guide to get the workflow guide then implement the task: Role: Front-end developer specializing in progressive enhancement and accessible UI state | Task: Create theme.js and the synchronous inline head snippet implementing OS-default + persisted light/dark theming with no FOUC and an accessible toggle, per requirements 2.1-2.5 | Restrictions: Theme state is client-only (no server changes), must not throw when localStorage/matchMedia are unavailable, page must remain readable with JS disabled, follow existing JS style | Success: theme.js exposes the toggle behaviour, storage key is divelog:theme, dark preference applies before paint, a themechange event fires on change, and toggle has correct aria state. Set the task to in-progress [-] before starting, log-implementation after completion, then mark it [x]._

- [x] 3. Rework the layout shell (layout.html.twig) onto Beer CSS
  - File: `templates/layout.html.twig`
  - Head: add `<link>` to vendored `beer.min.css` then `custom.css` (created in task 5), embed the inline no-FOUC theme script (task 2), and `<script defer>` for `beer.min.js` and `theme.js`
  - Body: apply Beer CSS main layout with a single `<main>` and the responsive `<nav>` wiring; keep `{% block content %}` and title variable
  - Purpose: Wire framework + theme + responsive layout once for every page
  - _Leverage: templates/layout.html.twig, `templates/partials/{header,nav,footer}.html.twig`_
  - _Requirements: 1.1, 1.4, 2.4, 4.1, 4.2_
  - _Prompt: Implement the task for spec beer-css-migration, first run spec-workflow-guide to get the workflow guide then implement the task: Role: Twig/front-end developer experienced with layout shells and asset wiring | Task: Update layout.html.twig to load the vendored Beer CSS + custom.css, embed the inline theme script, defer beer.min.js + theme.js, and adopt the Beer CSS main layout following requirements 1.1, 1.4, 2.4, 4.1, 4.2 | Restrictions: Presentation-only, keep the `{% block content %}` contract and title variable, reference only local asset paths, single `<main>` per document | Success: Every page inherits Beer CSS + theme with no FOUC, assets load from public/ locally, and the existing content block still renders. Set the task to in-progress [-] before starting, log-implementation after completion, then mark it [x]._

- [x] 4. Restyle header + nav partials (app bar, responsive nav, theme toggle)
  - Files: `templates/partials/header.html.twig`, `templates/partials/nav.html.twig`
  - Header: MD3 app bar with brand + an accessible theme-toggle `<button>` (icon + label, `aria-pressed`) that theme.js binds to
  - Nav: same 7 links (Dives, Sites, Countries, Trips, Equipment, Stats, API) as a Beer CSS responsive nav (persistent on desktop, compact/bottom on mobile) with active-route marking
  - Purpose: Modern, responsive, accessible primary navigation and theme control
  - _Leverage: templates/partials/header.html.twig, templates/partials/nav.html.twig, public/assets/js/theme.js_
  - _Requirements: 4.1, 4.2, 4.3, 4.4_
  - _Prompt: Implement the task for spec beer-css-migration, first run spec-workflow-guide to get the workflow guide then implement the task: Role: Front-end developer with Material Design and accessibility expertise | Task: Restyle header and nav partials into a Beer CSS app bar + responsive nav with an accessible theme toggle, preserving the existing seven links/routes, per requirements 4.1-4.4 | Restrictions: Do not change route URLs, keep links keyboard reachable, toggle must have accessible label + state, no horizontal overflow on mobile | Success: Nav is responsive and keyboard-accessible, links/routes unchanged, active route indicated, and the theme toggle is present and accessible. Set the task to in-progress [-] before starting, log-implementation after completion, then mark it [x]._

- [x] 5. Create custom.css (project widgets on Beer CSS variables) and retire replaced app.css rules
  - Files: `public/assets/css/custom.css` (new); trim `public/assets/css/app.css`
  - Style project-specific widgets using Beer CSS CSS variables so they follow the active theme: dive hero/metric grid, logbook list/items (incl. `.logbook-item.is-active`), tank cards, gallery grid, lightbox dialog, chart legend text, entity detail grids
  - Remove app.css rules that Beer CSS now provides; keep only project overrides
  - Purpose: Theme-aware project styling separated from the framework
  - _Leverage: public/assets/css/app.css_
  - _Requirements: 1.4, 2.2, 4.5_
  - _Prompt: Implement the task for spec beer-css-migration, first run spec-workflow-guide to get the workflow guide then implement the task: Role: CSS developer skilled in design tokens and theming | Task: Create custom.css for project-specific widgets built on Beer CSS variables so they auto-theme, and retire the app.css rules Beer CSS replaces, per requirements 1.4, 2.2, 4.5 | Restrictions: Use Beer CSS variables/surfaces (no hardcoded theme colors where a token exists), preserve widget layout intent and existing class hooks used by JS, ensure sufficient contrast in both themes | Success: Project widgets render correctly and theme automatically in light/dark, app.css no longer duplicates framework styles, and JS-referenced classes still exist. Set the task to in-progress [-] before starting, log-implementation after completion, then mark it [x]._

- [x] 6. Restyle shared row/table/pagination partials
  - Files: `templates/partials/dive_rows.html.twig`, `templates/partials/table.html.twig`, `templates/partials/pagination.html.twig`
  - Convert to Beer CSS table/list conventions; preserve JS hooks (`data-sortable`, `data-dives-table`, `tr[data-href]`) and pagination behaviour
  - Purpose: Restyle shared list markup once so all overview/detail pages benefit
  - _Leverage: `templates/partials/{dive_rows,table,pagination}.html.twig`, public/assets/js/tables.js_
  - _Requirements: 3.1, 3.3_
  - _Prompt: Implement the task for spec beer-css-migration, first run spec-workflow-guide to get the workflow guide then implement the task: Role: Twig/front-end developer focused on reusable components | Task: Restyle the shared dive_rows, table, and pagination partials to Beer CSS while preserving the sortable/clickable-row and pagination JS hooks, per requirements 3.1 and 3.3 | Restrictions: Keep data-sortable, data-dives-table, and tr[data-href] contracts intact; presentation-only; no data changes | Success: Shared partials render with Beer CSS styling, sorting/row-navigation/pagination still work everywhere they are used. Set the task to in-progress [-] before starting, log-implementation after completion, then mark it [x]._

- [x] 7. Restyle the dive detail page
  - File: `templates/dive_detail.html.twig`
  - Convert hero, metric grid, logbook list, panels, details grid, tanks, gallery to Beer CSS cards/lists; preserve logbook JS contracts (`data-logbook-pane`, `data-logbook-list`, `data-logbook-link`, `.logbook-item.is-active`, `data-dive-number`) and keep both tables.js and profile-chart.js loaded
  - Purpose: Restyle the most complex page while preserving logbook centering and profile chart
  - _Leverage: templates/dive_detail.html.twig, templates/partials/dive_rows.html.twig, public/assets/js/tables.js, public/assets/js/profile-chart.js_
  - _Requirements: 3.1, 3.2, 3.5_
  - _Prompt: Implement the task for spec beer-css-migration, first run spec-workflow-guide to get the workflow guide then implement the task: Role: Twig/front-end developer | Task: Restyle dive_detail.html.twig to Beer CSS preserving all data fields, the logbook JS contracts, and the profile chart, per requirements 3.1, 3.2, 3.5 | Restrictions: Keep every data-logbook-* hook and .logbook-item.is-active/data-dive-number; keep tables.js and profile-chart.js loaded; presentation-only | Success: Dive detail renders in Beer CSS in both themes, selected dive still centers in the logbook list, and the profile chart still renders. Set the task to in-progress [-] before starting, log-implementation after completion, then mark it [x]._

- [x] 8. Make canvas charts theme-aware
  - Files: `public/assets/js/profile-chart.js`, `public/assets/js/stats-chart.js`; optional shared helper `public/assets/js/chart-theme.js`
  - Read colors from CSS variables / body class at draw time and redraw on the `themechange` event so charts stay legible in both themes
  - Purpose: Keep dive-profile and statistics charts readable in light and dark
  - _Leverage: public/assets/js/profile-chart.js, public/assets/js/stats-chart.js, public/assets/js/theme.js_
  - _Requirements: 3.5, 4.5_
  - _Prompt: Implement the task for spec beer-css-migration, first run spec-workflow-guide to get the workflow guide then implement the task: Role: Front-end developer experienced with Canvas rendering | Task: Make profile-chart.js and stats-chart.js theme-aware by sourcing colors from theme tokens and redrawing on themechange, per requirements 3.5 and 4.5, optionally via a shared chart-theme helper | Restrictions: Preserve existing chart data and rendering logic, do not introduce a charting library, degrade gracefully if theme tokens are missing | Success: Both charts are legible in light and dark and update live on theme toggle without reload. Set the task to in-progress [-] before starting, log-implementation after completion, then mark it [x]._

- [x] 9. Restyle the dives overview page
  - File: `templates/dives_overview.html.twig`
  - Convert filters/controls and the dives table to Beer CSS; preserve sort control, clickable rows, and pagination
  - Purpose: Modern, consistent overview list
  - _Leverage: templates/dives_overview.html.twig, `templates/partials/{dive_rows,table,pagination}.html.twig`, public/assets/js/tables.js_
  - _Requirements: 3.1, 3.3_
  - _Prompt: Implement the task for spec beer-css-migration, first run spec-workflow-guide to get the workflow guide then implement the task: Role: Twig/front-end developer | Task: Restyle dives_overview.html.twig to Beer CSS preserving the sort control, clickable rows, and pagination, per requirements 3.1 and 3.3 | Restrictions: Keep JS hooks and data intact; presentation-only | Success: Dives overview renders in Beer CSS in both themes with sorting/row-navigation/pagination intact. Set the task to in-progress [-] before starting, log-implementation after completion, then mark it [x]._

- [x] 10. Restyle dive-site overview and detail pages
  - Files: `templates/divesite_overview.html.twig`, `templates/divesite_detail.html.twig`
  - Convert cards/tables/cross-links to Beer CSS; preserve dive-count columns, cross-links, and dive_rows drill-through
  - Purpose: Consistent site browsing
  - _Leverage: templates/divesite_*.html.twig, templates/partials/dive_rows.html.twig, public/assets/js/tables.js_
  - _Requirements: 3.1, 3.3_
  - _Prompt: Implement the task for spec beer-css-migration, first run spec-workflow-guide to get the workflow guide then implement the task: Role: Twig/front-end developer | Task: Restyle divesite_overview and divesite_detail to Beer CSS preserving dive counts, cross-links, and the dive_rows drill-through, per requirements 3.1 and 3.3 | Restrictions: Preserve all data/links and JS hooks; presentation-only | Success: Both site pages render in Beer CSS in both themes with data, links, sorting, and pagination intact. Set the task to in-progress [-] before starting, log-implementation after completion, then mark it [x]._

- [x] 11. Restyle country overview and detail pages
  - Files: `templates/divecountry_overview.html.twig`, `templates/divecountry_detail.html.twig`
  - Convert to Beer CSS; preserve counts, cross-links, and dive_rows listing
  - Purpose: Consistent country browsing
  - _Leverage: templates/divecountry_*.html.twig, templates/partials/dive_rows.html.twig, public/assets/js/tables.js_
  - _Requirements: 3.1, 3.3_
  - _Prompt: Implement the task for spec beer-css-migration, first run spec-workflow-guide to get the workflow guide then implement the task: Role: Twig/front-end developer | Task: Restyle divecountry_overview and divecountry_detail to Beer CSS preserving counts, cross-links, and dive listings, per requirements 3.1 and 3.3 | Restrictions: Preserve all data/links and JS hooks; presentation-only | Success: Both country pages render in Beer CSS in both themes with data/links/sorting/pagination intact. Set the task to in-progress [-] before starting, log-implementation after completion, then mark it [x]._

- [x] 12. Restyle city overview and detail pages
  - Files: `templates/divecity_overview.html.twig`, `templates/divecity_detail.html.twig`
  - Convert to Beer CSS; preserve counts, cross-links, and listings
  - Purpose: Consistent city browsing
  - _Leverage: templates/divecity_*.html.twig, templates/partials/dive_rows.html.twig_
  - _Requirements: 3.1, 3.3_
  - _Prompt: Implement the task for spec beer-css-migration, first run spec-workflow-guide to get the workflow guide then implement the task: Role: Twig/front-end developer | Task: Restyle divecity_overview and divecity_detail to Beer CSS preserving counts, cross-links, and listings, per requirements 3.1 and 3.3 | Restrictions: Preserve all data/links and JS hooks; presentation-only | Success: Both city pages render in Beer CSS in both themes with data/links intact. Set the task to in-progress [-] before starting, log-implementation after completion, then mark it [x]._

- [x] 13. Restyle shop overview and detail pages
  - Files: `templates/diveshop_overview.html.twig`, `templates/diveshop_detail.html.twig`
  - Convert to Beer CSS; preserve counts, cross-links, and listings
  - Purpose: Consistent shop browsing
  - _Leverage: templates/diveshop_*.html.twig, templates/partials/dive_rows.html.twig_
  - _Requirements: 3.1, 3.3_
  - _Prompt: Implement the task for spec beer-css-migration, first run spec-workflow-guide to get the workflow guide then implement the task: Role: Twig/front-end developer | Task: Restyle diveshop_overview and diveshop_detail to Beer CSS preserving counts, cross-links, and listings, per requirements 3.1 and 3.3 | Restrictions: Preserve all data/links and JS hooks; presentation-only | Success: Both shop pages render in Beer CSS in both themes with data/links intact. Set the task to in-progress [-] before starting, log-implementation after completion, then mark it [x]._

- [x] 14. Restyle trip overview and detail pages
  - Files: `templates/divetrip_overview.html.twig`, `templates/divetrip_detail.html.twig`
  - Convert to Beer CSS; preserve counts, cross-links, and dive_rows listing
  - Purpose: Consistent trip browsing
  - _Leverage: templates/divetrip_*.html.twig, templates/partials/dive_rows.html.twig, public/assets/js/tables.js_
  - _Requirements: 3.1, 3.3_
  - _Prompt: Implement the task for spec beer-css-migration, first run spec-workflow-guide to get the workflow guide then implement the task: Role: Twig/front-end developer | Task: Restyle divetrip_overview and divetrip_detail to Beer CSS preserving counts, cross-links, and dive listings, per requirements 3.1 and 3.3 | Restrictions: Preserve all data/links and JS hooks; presentation-only | Success: Both trip pages render in Beer CSS in both themes with data/links/sorting/pagination intact. Set the task to in-progress [-] before starting, log-implementation after completion, then mark it [x]._

- [x] 15. Restyle equipment overview and detail pages
  - Files: `templates/equipment_overview.html.twig`, `templates/equipment_detail.html.twig`
  - Convert to Beer CSS; preserve service-reminder info, dive counts, and listings
  - Purpose: Consistent equipment browsing
  - _Leverage: templates/equipment_*.html.twig, templates/partials/dive_rows.html.twig, public/assets/js/tables.js_
  - _Requirements: 3.1, 3.3_
  - _Prompt: Implement the task for spec beer-css-migration, first run spec-workflow-guide to get the workflow guide then implement the task: Role: Twig/front-end developer | Task: Restyle equipment_overview and equipment_detail to Beer CSS preserving service reminders, counts, and listings, per requirements 3.1 and 3.3 | Restrictions: Preserve all data/links and JS hooks; presentation-only | Success: Both equipment pages render in Beer CSS in both themes with all data intact. Set the task to in-progress [-] before starting, log-implementation after completion, then mark it [x]._

- [x] 16. Restyle the statistics page
  - File: `templates/divestats.html.twig`
  - Convert layout and stat cards to Beer CSS; keep the stats chart payload and stats-chart.js wiring; ensure charts are legible in both themes (relies on task 8)
  - Purpose: Modern, legible statistics presentation
  - _Leverage: templates/divestats.html.twig, public/assets/js/stats-chart.js_
  - _Requirements: 3.1, 3.5_
  - _Prompt: Implement the task for spec beer-css-migration, first run spec-workflow-guide to get the workflow guide then implement the task: Role: Twig/front-end developer | Task: Restyle divestats.html.twig to Beer CSS preserving the stats data and chart wiring, per requirements 3.1 and 3.5 | Restrictions: Keep the chart canvas/payload and stats-chart.js hook intact; presentation-only | Success: Stats page renders in Beer CSS in both themes and charts remain legible/interactive. Set the task to in-progress [-] before starting, log-implementation after completion, then mark it [x]._

- [x] 17. Restyle the gallery page
  - File: `templates/divegallery.html.twig`
  - Convert the gallery grid to Beer CSS; preserve thumbnails and the lightbox overlay behaviour
  - Purpose: Consistent, modern photo gallery
  - _Leverage: templates/divegallery.html.twig, public/assets/js/lightbox.js, public/assets/css/custom.css_
  - _Requirements: 3.1, 3.4_
  - _Prompt: Implement the task for spec beer-css-migration, first run spec-workflow-guide to get the workflow guide then implement the task: Role: Twig/front-end developer | Task: Restyle divegallery.html.twig to Beer CSS preserving thumbnails and the lightbox overlay, per requirements 3.1 and 3.4 | Restrictions: Keep the lightbox trigger markup/hooks intact; presentation-only | Success: Gallery renders in Beer CSS in both themes and the lightbox still opens images. Set the task to in-progress [-] before starting, log-implementation after completion, then mark it [x]._

- [x] 18. Special-case the embeddable summary
  - File: `templates/divesummary.html.twig`
  - Keep the summary lightweight and self-contained; do NOT force it into the full Beer CSS shell/theme (or scope it) so it does not override a host page's styles
  - Purpose: Preserve safe external embedding
  - _Leverage: templates/divesummary.html.twig_
  - _Requirements: 3.1, 5.3_
  - _Prompt: Implement the task for spec beer-css-migration, first run spec-workflow-guide to get the workflow guide then implement the task: Role: Twig/front-end developer | Task: Ensure divesummary.html.twig stays lightweight and does not impose the global Beer CSS shell/theme on an embedding host page, per requirements 3.1 and 5.3 | Restrictions: Do not clobber host-page styles, keep the summary content intact, presentation-only | Success: The embeddable summary renders its content without pulling in or overriding a host page's global styles. Set the task to in-progress [-] before starting, log-implementation after completion, then mark it [x]._

- [x] 19. Update smoke tests and run the full quality gate
  - Files: `tests/Http/WebSmokeTest.php`; run `composer test && composer stan && composer cs`
  - Keep asserted markup hooks passing; add an assertion that Beer CSS + theme assets are referenced in the layout; update assertions only where a markup contract intentionally changed
  - Purpose: Guarantee no regressions and green gates before shipping
  - _Leverage: tests/Http/WebSmokeTest.php_
  - _Requirements: 5.1, 5.2, 5.3, 5.4_
  - _Prompt: Implement the task for spec beer-css-migration, first run spec-workflow-guide to get the workflow guide then implement the task: Role: PHP developer/QA engineer | Task: Update WebSmokeTest to reflect intentional markup changes and assert the Beer CSS/theme assets are wired, then confirm composer test/stan/cs all pass, per requirements 5.1-5.4 | Restrictions: Do not weaken meaningful assertions, keep repositories/controllers/routing unchanged, no schema/data changes | Success: composer test, composer stan, and composer cs all pass, smoke tests assert the theme/framework wiring, and no domain code changed. Set the task to in-progress [-] before starting, log-implementation after completion, then mark it [x]._
