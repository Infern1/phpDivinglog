---
name: gortex-get-table-prefix
description: "Work in the . · get_table_prefix area — 29 symbols across 1 files (89% cohesion)"
---

# . · get_table_prefix

29 symbols | 1 files | 89% cohesion

## When to Use

Use this skill when working on files in:
- `classes.inc.php`

## Key Files

| File | Symbols |
|------|---------|
| `classes.inc.php` | set_divesite_info, get_table_prefix, TopLevelMenu, set_divelog_info, get_site_nr, ... |

## Entry Points

- `classes.inc.php::TopLevelMenu.get_nav_links`

## Connected Communities

- **. · get_divegallery_info** (2 cross-edges)

## How to Explore

```
get_communities with id: "community-10"
smart_context with task: "understand . · get_table_prefix", format: "gcx"
find_usages with id: "classes.inc.php::TopLevelMenu.get_nav_links", format: "gcx"
```

_`format: "gcx"` returns the [GCX1 compact wire format](../../docs/wire-format.md) — round-trippable, ~27% fewer tokens than JSON. Drop it for JSON output; agents using `@gortex/wire` or the Go `github.com/gortexhq/gcx-go` package decode either._
