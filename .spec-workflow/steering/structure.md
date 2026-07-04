# Project Structure

## Directory Organization

```
phpDivinglog/
├── index.php                 # Main entry point (dive list + dive detail view)
├── divesite.php              # Entry point: dive sites overview/detail
├── divecountry.php           # Entry point: countries overview/detail
├── divecity.php              # Entry point: cities overview/detail
├── diveshop.php              # Entry point: dive shops overview/detail
├── divetrip.php              # Entry point: trips overview/detail
├── equipment.php             # Entry point: equipment overview/detail
├── divestats.php             # Entry point: diving statistics
├── divegallery.php           # Entry point: photo gallery
├── divesummary.php           # Embeddable summary widget for external pages
├── drawprofile.php           # Renders the dive profile graph
├── header.php / footer.php   # Shared page chrome (used in embed mode)
├── settings.php              # Non-user-facing internal settings (app version, paths)
├── config.inc.php.example    # Template for user configuration (copy to config.inc.php)
├── test.php                  # Ad-hoc manual smoke/debug file
│
├── classes.inc.php           # Monolithic domain-class file (all model/controller logic)
│
├── tpl/                      # Smarty templates (the view layer, *.tpl)
├── sql/                      # Externalized SQL query files (*.sql)
├── includes/                 # Vendored libraries + shared helpers
│   ├── smarty/               #   Smarty template engine (sysplugins/, plugins/)
│   ├── jqplot/               #   jqPlot charting lib (plugins/)
│   ├── languages/            #   UI translation files (*.inc.php)
│   ├── language/             #   Translator tooling (compare.php)
│   ├── wp-db.php             #   WordPress wpdb database abstraction
│   ├── misc.inc.php          #   Shared helper functions
│   ├── dBug.php              #   Debug variable-dump helper
│   ├── imgd.php / imgp.php   #   Image handling (large, near-duplicate)
│   └── *.css                 #   Stylesheets (divelog.css, tabs.css, ...)
├── js/                       # Client-side JavaScript (jQuery Tools, DataTables assets)
├── images/                   # Static images + user media (pictures, thumbs, maps, flags, equipment)
├── cache/                    # Smarty runtime cache (writable, git-ignored)
├── compile/                  # Smarty compiled templates (writable, git-ignored)
├── doc/                      # Documentation generation (makedoc.sh)
├── CHANGELOG / README.md     # Project docs
├── INSTALL / CONTRIBUTING.md # Install + contribution guides
└── config.inc.php            # User's live configuration (git-ignored, created from example)
```

The layout is **grouped by layer/type**: entry points at the root, all domain logic in one
class file, views in `tpl/`, queries in `sql/`, and third-party/shared code in `includes/`.

## Naming Conventions

### Files
- **Entry-point pages**: lowercase, named after the domain concept — `divesite.php`,
  `divetrip.php`, `equipment.php`.
- **Domain classes**: all defined in the single `classes.inc.php` (not one-class-per-file).
- **Templates**: lowercase with underscores; paired overview/detail suffixes —
  `divesite_overview.tpl`, `divesite_details.tpl`, `dive_details.tpl`.
- **SQL files**: short lowercase descriptive names — `divelist.sql`, `onedive.sql`,
  `countrycities.sql`, `equipservice.sql`.
- **Language files**: `includes/languages/<language>.inc.php` (e.g., `english.inc.php`).
- **Config/include helpers**: `*.inc.php` suffix (`config.inc.php`, `misc.inc.php`,
  `classes.inc.php`).

### Code
- **Classes/Types**: `PascalCase` — `HandleRequest`, `Divelog`, `Divesite`, `DivePictures`,
  `TopLevelMenu`, `AppInfo`.
- **Methods/Functions**: `snake_case` — `set_divelog_info()`, `get_request_type()`,
  `set_main_dive_details()`, `handle_url()`.
- **Config keys**: array entries on `$_config[...]` using lowercase/snake keys —
  `$_config['table_prefix']`, `$_config['show_profile']`, `$_config['picpath_web']`.
- **Language keys**: array entries on `$_lang[...]` (e.g., `$_lang['dive_details_pagetitle']`).
- **Variables**: `snake_case` / lowercase — `$config_file`, `$request`, `$divelog`.

## Import Patterns

### Include Order
1. Load configuration first: every entry point starts with
   `require_once("./config.inc.php")`, which in turn requires `settings.php`.
2. Domain classes and libraries are pulled in via the config/settings bootstrap
   (Smarty, `classes.inc.php`, `wp-db.php`, language file, helpers).
3. Templates are rendered at the end of each entry point (`$t->display(...)` or
   `$t->fetch(...)`).

### Module/Include Organization
- No autoloader — everything is wired with `require_once`/`include`.
- Paths are resolved from constants defined in config: `ABSPATH_DIVELOG` / `ABSPATH`
  (absolute filesystem paths) and `$_config['app_root']`.
- Web-facing asset paths come from `$_config[...]` keys in `settings.php`
  (`picpath_web`, `mappath_web`, `flagpath_web`, `equippath_web`, ...).

## Code Structure Patterns

### Entry-Point (Front Controller) Organization
Each root PHP page follows the same sequence:
1. `require_once` the config file.
2. Construct `HandleRequest`, set the request URI/depth, call `handle_url()` to parse it.
3. Construct `TopLevelMenu` for navigation.
4. Instantiate the relevant domain class (e.g., `Divesite`) and set its info from the request.
5. Branch on request type (overview list vs. single-item detail) and populate data via
   `set_*` methods.
6. Assign data to Smarty (`$t->assign(...)`) and render the matching template.

### Domain-Class Organization (in `classes.inc.php`)
- One class per domain concept (`Divelog`, `Divesite`, `Equipment`, `Diveshop`, `Divetrip`,
  `Divecountry`, `Divecity`, `Divestats`, `DivePictures`, `Tank`, plus `HandleRequest`,
  `User`, `TopLevelMenu`, `AppInfo`).
- Methods are paired around intent: `set_*` methods gather/prepare data (often executing a
  query from `sql/`), `get_*` methods expose it to the page/template.
- SQL is loaded from external `.sql` files rather than inlined, keeping queries editable.

### Template Organization (in `tpl/`)
- Overview vs. detail split per concept (`*_overview.tpl` / `*_details.tpl`).
- Shared fragments for page chrome: `header*.tpl`, `footer*.tpl`, `link_base.tpl`,
  `links_*.tpl`, `datatable.tpl`.

## Code Organization Principles
1. **Separation by layer**: PHP logic, SQL queries, and HTML templates are kept in distinct
   locations (`classes.inc.php` / `sql/` / `tpl/`).
2. **Config-driven behavior**: Feature flags, units, and paths live in `config.inc.php` /
   `settings.php`, not scattered through code.
3. **Consistency across views**: Every entry point uses the same request→class→template
   flow, so new views should mirror the existing ones.
4. **Faithful data handling**: Metric values stay metric internally; conversion/formatting
   happens at the template/helper boundary.

## Module Boundaries
- **Entry points vs. domain logic**: Root `*.php` pages orchestrate; they should stay thin
  and defer real work to classes in `classes.inc.php`.
- **Domain logic vs. queries**: Classes call out to externalized SQL in `sql/`; SQL text
  should not be embedded in PHP.
- **Logic vs. presentation**: Domain classes must not emit HTML — rendering belongs to
  Smarty templates in `tpl/`.
- **Application code vs. vendored libraries**: Third-party code lives under `includes/`
  (Smarty, jqPlot, wpdb) and should be treated as external — prefer upgrading in place over
  forking, and document any local modifications.
- **Config vs. code**: User settings live only in `config.inc.php` (copied from the example);
  internal, rarely-changed settings live in `settings.php`.

## Code Size Guidelines
- **`classes.inc.php` is an acknowledged outlier** (~5,500 lines). New work should resist
  growing it further; prefer extracting cohesive classes when practical.
- **Entry points**: Keep root pages small (roughly under ~100 lines) — orchestration only.
- **Methods**: Favour focused `set_*`/`get_*` methods over large multi-purpose functions.
- **Templates**: Keep per-view templates focused; factor shared markup into header/footer/
  link fragments.

## Documentation Standards
- Each source file begins with a header docblock: filename, function/purpose, `@author`,
  `@package phpdivinglog`, `@license`, and `@version`.
- Configuration options in `config.inc.php.example` are documented inline with comment blocks
  explaining each setting; `@todo` markers flag options that are not fully implemented.
- User-facing setup is documented in `README.md` and `INSTALL`; contribution rules in
  `CONTRIBUTING.md`; release history in `CHANGELOG`.
- Complex or non-obvious logic should carry inline comments, consistent with the existing
  code style.
