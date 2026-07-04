---
name: gortex-smarty-smarty
description: "Work in the smarty · Smarty area — 50 symbols across 2 files (98% cohesion)"
---

# smarty · Smarty

50 symbols | 2 files | 98% cohesion

## When to Use

Use this skill when working on files in:
- `includes/smarty/Smarty.class.php`
- `includes/smarty/SmartyBC.class.php`

## Key Files

| File | Symbols |
|------|---------|
| `includes/smarty/Smarty.class.php` | setPluginsDir, getCacheDir, getConfigDir, getCompileDir, _realpath, ... |
| `includes/smarty/SmartyBC.class.php` | trigger_error, __construct |

## How to Explore

```
get_communities with id: "community-53"
smart_context with task: "understand smarty · Smarty", format: "gcx"
```

_`format: "gcx"` returns the [GCX1 compact wire format](../../docs/wire-format.md) — round-trippable, ~27% fewer tokens than JSON. Drop it for JSON output; agents using `@gortex/wire` or the Go `github.com/gortexhq/gcx-go` package decode either._
