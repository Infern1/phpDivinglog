# Product Overview

## Product Purpose
phpDivingLog is a PHP web application that publishes and displays scuba dive logs on a
personal web server. It takes the data recorded in the desktop program **Diving Log**
(divinglog.de) — exported to a MySQL database — and turns it into a browsable, public
(or private) website. It solves the problem that the desktop Diving Log software stores
a rich dive history locally, but offers no easy way to share that history online with
buddies, dive clubs, or the wider diving community.

## Target Users
- **Recreational and technical scuba divers** who log their dives in the Diving Log
  desktop application and want an online presence for their logbook.
- **Dive bloggers and enthusiasts** who want to embed dive summaries or link to detailed
  dive pages from a personal website.
- **Self-hosters** comfortable running a LAMP/LEMP stack who want full control over their
  data rather than relying on a third-party cloud service.

Their key needs and pain points:
- Sharing dives, sites, trips, and photos without exposing the raw desktop database.
- Presenting metric-stored data in either metric or imperial units for a mixed audience.
- Displaying dive profile graphs, galleries, and statistics in an attractive, navigable form.
- A low-maintenance, file-based deploy that works on inexpensive shared hosting.

## Key Features

1. **Dive detail pages**: Full per-dive view including main details, buddies, conditions,
   breathing/gas details, equipment used, comments (RTF), user-defined fields, and a
   rendered dive profile graph.
2. **Location browsing**: Navigable overviews and detail pages for dive sites, countries,
   cities, dive shops, and trips, with cross-links between them.
3. **Equipment tracking**: Equipment list and detail pages, including service reminders
   based on due dates.
4. **Photo galleries**: Support for dive pictures, equipment photos, location/map images,
   and certification scans, with automatic thumbnail generation and overlay viewing.
5. **Statistics**: Aggregated diving statistics across the logbook.
6. **Unit conversion**: Display depth, pressure, weight, temperature, and volume in either
   metric or imperial units (data is always stored metric).
7. **Multi-language UI**: Language files allow the interface to be translated; a compare
   tool assists translators.
8. **Multi-user mode**: Table-prefix based support for hosting multiple divers' logbooks.
9. **Embeddable summary**: `divesummary.php` surfaces a compact dive summary that can be
   pulled into an external HTML page.

## Business Objectives
- Provide a free, open-source (GPL3) companion to the commercial Diving Log desktop app.
- Let divers self-publish their logbooks with minimal technical overhead.
- Remain broadly compatible with the data export format of Diving Log across versions.

## Success Metrics
- **Ease of install**: A diver can go from a fresh MySQL dump to a working site by editing
  a single config file.
- **Data fidelity**: Displayed dives, sites, and statistics accurately reflect the source
  Diving Log export.
- **Compatibility**: The site renders correctly against current Diving Log export schemas
  and on mainstream PHP/MySQL hosting.

## Product Principles
1. **Read-only presentation**: phpDivingLog is a display layer over data authored in the
   Diving Log desktop app. It shows and organizes data; it is not a dive-logging editor.
2. **Config over code**: Behavior (units, paths, display options, single/multi-user) is
   driven by `config.inc.php` so end users rarely need to touch PHP.
3. **Host-friendliness**: Works on modest shared hosting — supports both mod_rewrite and
   plain query-string URLs, and keeps dependencies vendored in the repo.
4. **Faithful to the source data**: Metric storage is preserved; conversions and formatting
   happen only at display time.

## Monitoring & Visibility
- **Dashboard Type**: Web-based, server-rendered pages (Smarty templates).
- **Real-time Updates**: None — pages reflect the current state of the imported MySQL data
  on each request.
- **Key Metrics Displayed**: Dive counts, statistics, recent dives, and per-location/trip
  summaries.
- **Sharing Capabilities**: Public web pages, deep links to individual dives/sites, and an
  embeddable dive summary script.

## Future Vision
Continue tracking Diving Log export changes, modernize the PHP codebase, and improve the
maintainability of the presentation and data-access layers.

### Potential Enhancements
- **Embedding**: Complete the `embed_mode` path so phpDivingLog can be dropped into an
  existing site more cleanly.
- **Modernization**: Reduce the monolithic class file and legacy vendored libraries in
  favour of maintained equivalents.
- **Configurable display**: Finish the partially-implemented user-info and service-reminder
  toggles noted in the configuration file.
