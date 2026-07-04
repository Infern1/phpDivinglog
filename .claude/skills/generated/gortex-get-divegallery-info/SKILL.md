---
name: gortex-get-divegallery-info
description: "Work in the . · get_divegallery_info area — 21 symbols across 1 files (82% cohesion)"
---

# . · get_divegallery_info

21 symbols | 1 files | 82% cohesion

## When to Use

Use this skill when working on files in:
- `classes.inc.php`

## Key Files

| File | Symbols |
|------|---------|
| `classes.inc.php` | get_image_link, handle_url, set_divesite_pictures, get_username, __construct, ... |

## Entry Points

- `classes.inc.php::Equipment.set_main_equipment_details`
- `classes.inc.php::Diveshop.set_main_diveshop_details`
- `classes.inc.php::Divetrip.set_main_divetrip_details`

## Connected Communities

- **. · get_table_prefix** (1 cross-edges)

## How to Explore

```
get_communities with id: "community-6"
smart_context with task: "understand . · get_divegallery_info", format: "gcx"
find_usages with id: "classes.inc.php::Equipment.set_main_equipment_details", format: "gcx"
```

_`format: "gcx"` returns the [GCX1 compact wire format](../../docs/wire-format.md) — round-trippable, ~27% fewer tokens than JSON. Drop it for JSON output; agents using `@gortex/wire` or the Go `github.com/gortexhq/gcx-go` package decode either._
