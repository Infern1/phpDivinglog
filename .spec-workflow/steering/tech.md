# Technology Stack

## Project Type
Server-rendered PHP web application. It is a presentation/reporting layer over a MySQL
database that has been populated by exporting data from the **Diving Log** desktop program.
Deployed as plain PHP files on a standard web server — no application server, build step,
or package installer is required at deploy time.

## Core Technologies

### Primary Language(s)
- **Language**: PHP (minimum PHP 5.5; the codebase runs on modern PHP 7.x/8.x with the
  legacy caveats noted below).
- **Runtime**: PHP running under a web server (Apache or Nginx) via mod_php, PHP-FPM, or CGI.
- **Language-specific tools**: None mandated — no Composer/autoloader. Dependencies are
  vendored directly into the repository and pulled in with `require_once`/`include`.

### Key Dependencies/Libraries
All third-party libraries are vendored into the repo (no package manager):
- **Smarty v3.1.30** (`includes/smarty/`): Template engine for all HTML rendering.
- **wp-db.php** (`includes/wp-db.php`): WordPress database abstraction class (`wpdb`),
  used as the base DB access layer (recently adopted, with mysqlnd support and small
  local modifications).
- **jqPlot** (`includes/jqplot/`): JavaScript charting library used to draw dive profile
  graphs and pie charts.
- **jQuery DataTables** (`js/`, templates): Client-side sortable/paginated tables for
  overview lists.
- **Highslide JS v4.1.4**: Overlays dive/equipment/map images on the current page.
- **RTFClass v155**: Parses Rich Text Format comment fields exported by Diving Log.
- **jQuery Tools** (`js/jquery.tools.min.js`): UI helpers (tabs, overlays).
- **dBug.php** (`includes/dBug.php`): Developer variable-dump/debugging helper.

### Application Architecture
Page-per-view procedural front controllers backed by a monolithic domain class file:
- Each top-level PHP file (`index.php`, `divesite.php`, `divecountry.php`, `divecity.php`,
  `diveshop.php`, `divetrip.php`, `equipment.php`, `divestats.php`, `divegallery.php`,
  `divesummary.php`, `drawprofile.php`) is an **entry point** for one view.
- Each entry point follows the same flow: load `config.inc.php`, construct a
  `HandleRequest` to parse the URL, build a `TopLevelMenu`, instantiate the relevant domain
  class, populate it, then hand data to a **Smarty template** in `tpl/`.
- **`classes.inc.php`** is a single large file (~5,500 lines) containing all domain classes:
  `HandleRequest`, `User`, `TopLevelMenu`, `Divelog`, `Divesite`, `Equipment`, `Diveshop`,
  `Divetrip`, `Divecountry`, `Divecity`, `Divestats`, `DivePictures`, `Tank`, `AppInfo`.
- **SQL is externalized**: query strings live as individual `.sql` files under `sql/`
  and are loaded at runtime rather than embedded in PHP.
- Loosely an MVC split: domain classes = model/controller logic, `tpl/*.tpl` = view,
  `sql/*.sql` = queries, `config.inc.php`/`settings.php` = configuration.

### Data Storage
- **Primary storage**: MySQL database, populated by a MySQL Dump exported from the Diving
  Log desktop application. All measurements are stored in **metric** units.
- **Table prefix**: Configurable (`$_config['table_prefix']`, default `DL_`); the prefix
  mechanism also enables multi-user hosting.
- **Caching / compilation**: Smarty compiled templates in `compile/`; Smarty cache in
  `cache/`. Image thumbnails are generated and cached on disk.
- **Data formats**: HTML (Smarty output), SQL query files, image files (JPG/GIF/PNG),
  and RTF-encoded comment fields.

### External Integrations
- **Diving Log desktop app** (divinglog.de): The upstream data source via MySQL export;
  phpDivingLog tracks its schema (currently referenced Diving Log version ~6.0.22).
- **Google Maps**: User-supplied map links embedded in dive-location comments are turned
  into clickable links (no API integration — plain URL handling).
- **Authentication**: None built in. Access control is expected to be handled at the web
  server level (e.g., HTTP auth) if the logbook should be private.

### Monitoring & Dashboard Technologies
- **Rendering**: Server-side HTML via Smarty templates; no SPA framework.
- **Client-side**: jQuery, DataTables (interactive lists), jqPlot (dive profile/pie charts),
  Highslide (image overlays).
- **Real-time**: None — each page reflects the current database state on request.
- **State management**: The MySQL database is the single source of truth; configuration is
  file-based (`config.inc.php`).

## Development Environment

### Build & Development Tools
- **Build System**: None. Deployment is copy-the-files. There is no compilation or bundling.
- **Package Management**: None — dependencies are committed to the repository.
- **Development workflow**: Edit PHP/templates and refresh the browser. When editing
  templates, clearing the `compile/` (and `cache/`) directories forces Smarty to recompile.

### Code Quality Tools
- **Static Analysis**: None configured in-repo. (An editor/LSP such as Intelephense can be
  used locally; the repo does not pin one.)
- **Formatting**: No enforced formatter; follow existing style.
- **Testing Framework**: None. `test.php` is an ad-hoc manual smoke file, not a test suite.
- **Documentation**: `doc/makedoc.sh` supports generating docs; a language-file comparison
  helper (`includes/language/compare.php`) assists translators.

### Version Control & Collaboration
- **VCS**: Git, hosted on GitHub (Infern1/phpDivinglog).
- **Branching Strategy**: GitHub Flow style — feature work via branches and pull requests
  against the main branch.
- **Code Review Process**: Pull requests per `CONTRIBUTING.md`.

## Deployment & Distribution
- **Target Platform(s)**: Any LAMP/LEMP host — Apache or Nginx + MySQL + PHP. Designed to
  work on inexpensive shared hosting.
- **Distribution Method**: Clone or download the repository and copy it to the web root.
- **Installation Requirements**:
  1. Web server (Apache/Nginx), MySQL, PHP ≥ 5.5.
  2. Import the Diving Log MySQL dump into a database.
  3. Copy `config.inc.php.example` to `config.inc.php` and set DB credentials, table prefix,
     units, and file paths.
  4. Ensure `compile/`, `cache/`, and image directories are writable.
- **Update Mechanism**: Manual — pull the latest code and re-copy files; review the
  `CHANGELOG` for schema/config changes.

## Technical Requirements & Constraints

### Performance Requirements
- Suitable for personal-scale traffic (a diver's logbook), not high-concurrency workloads.
- Smarty template compilation caching and on-disk thumbnail generation keep repeat page
  loads cheap.

### Compatibility Requirements
- **Platform Support**: Cross-platform PHP; must run on both Apache and Nginx.
- **URL modes**: Must support both mod_rewrite pretty URLs and plain query-string URLs
  (`$_config['query_string']`) for hosts without rewrite support.
- **Dependency Versions**: MySQL with mysqlnd; PHP ≥ 5.5. Data schema compatibility is
  tied to the Diving Log export format.
- **Units**: Data is metric in storage; imperial display is an opt-in conversion layer.

### Security & Compliance
- **Security Requirements**: No built-in authentication — privacy relies on web-server-level
  controls. Because query strings drive SQL lookups, input handling in the data-access
  layer and externalized SQL is security-relevant.
- **Compliance Standards**: None specifically targeted (personal-use hobby application).
- **Threat Model**: Primary concerns are SQL injection via request parameters and safe
  handling of user-supplied image paths and RTF/comment content.

### Scalability & Reliability
- **Expected Load**: Single-user or small multi-user logbooks; low traffic.
- **Availability**: Best-effort; depends entirely on the host. No HA design.
- **Growth Projections**: Scales with logbook size; large logbooks lean on DataTables
  client-side paging and cached thumbnails.

## Technical Decisions & Rationale

### Decision Log
1. **Smarty templating**: Chosen to separate HTML from PHP logic, keeping views editable
   without touching domain code.
2. **Externalized SQL files (`sql/`)**: Queries live outside PHP so they can be adjusted
   for schema changes without editing class code.
3. **WordPress `wpdb` as the DB layer**: Adopted (with mysqlnd) to get a battle-tested,
   portable database abstraction rather than maintaining bespoke mysql_* calls.
4. **Vendored dependencies, no Composer**: Maximizes compatibility with basic shared
   hosting where CLI tooling may be unavailable; deployment stays copy-only.
5. **Dual URL modes**: Supports both rewrite and query-string routing so the app runs on
   hosts without mod_rewrite.
6. **Metric storage, display-time conversion**: Preserves fidelity to the Diving Log export
   while allowing imperial presentation.

## Known Limitations
- **Monolithic `classes.inc.php`**: All domain logic in one very large file makes navigation
  and maintenance harder; a candidate for gradual decomposition.
- **No automated tests**: Verification is manual; regressions are easy to miss.
- **Legacy/duplicated vendored code**: Some bundled libraries are old, and large near-
  duplicate files exist (e.g., `includes/imgd.php` and `includes/imgp.php`).
- **Incomplete config toggles**: Several options in `config.inc.php` are marked `@todo`
  (embedding mode, some user-info displays, service reminders) and are not fully wired up.
- **No built-in auth**: Privacy must be enforced outside the application.
- **PHP-version drift**: Written against PHP 5.5-era idioms; running on modern PHP may
  surface deprecation warnings that need attention over time.
