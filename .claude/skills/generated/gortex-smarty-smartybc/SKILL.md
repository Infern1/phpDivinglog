---
name: gortex-smarty-smartybc
description: "Work in the smarty · SmartyBC area — 35 symbols across 2 files (96% cohesion)"
---

# smarty · SmartyBC

35 symbols | 2 files | 96% cohesion

## When to Use

Use this skill when working on files in:
- `includes/smarty/Smarty.class.php`
- `includes/smarty/SmartyBC.class.php`

## Key Files

| File | Symbols |
|------|---------|
| `includes/smarty/Smarty.class.php` | templateExists |
| `includes/smarty/SmartyBC.class.php` | unregister_outputfilter, get_registered_object, clear_assign, register_object, unregister_block, ... |

## How to Explore

```
get_communities with id: "community-54"
smart_context with task: "understand smarty · SmartyBC", format: "gcx"
```

_`format: "gcx"` returns the [GCX1 compact wire format](../../docs/wire-format.md) — round-trippable, ~27% fewer tokens than JSON. Drop it for JSON output; agents using `@gortex/wire` or the Go `github.com/gortexhq/gcx-go` package decode either._
