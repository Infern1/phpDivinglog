---
name: gortex-includes-wpdb
description: "Work in the includes · wpdb area — 72 symbols across 2 files (100% cohesion)"
---

# includes · wpdb

72 symbols | 2 files | 100% cohesion

## When to Use

Use this skill when working on files in:
- `includes/misc.inc.php`
- `includes/wp-db.php`

## Key Files

| File | Symbols |
|------|---------|
| `includes/misc.inc.php` | sql_file, parse_mysql_query |
| `includes/wp-db.php` | strip_invalid_text, delete, check_safe_collation, db_connect, strip_invalid_text_from_query, ... |

## How to Explore

```
get_communities with id: "community-124"
smart_context with task: "understand includes · wpdb", format: "gcx"
```

_`format: "gcx"` returns the [GCX1 compact wire format](../../docs/wire-format.md) — round-trippable, ~27% fewer tokens than JSON. Drop it for JSON output; agents using `@gortex/wire` or the Go `github.com/gortexhq/gcx-go` package decode either._
