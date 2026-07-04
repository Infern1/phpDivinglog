---
name: gortex-includes-setheader
description: "Work in the includes · setHeader area — 32 symbols across 2 files (88% cohesion)"
---

# includes · setHeader

32 symbols | 2 files | 88% cohesion

## When to Use

Use this skill when working on files in:
- `includes/imgd.php`
- `includes/imgp.php`

## Key Files

| File | Symbols |
|------|---------|
| `includes/imgd.php` | loadCacheDetails, __construct, parseHeader, setUrl, setCache, ... |
| `includes/imgp.php` | getStatus, setUrl, setHeaderFields, loadCacheDetails, useCache, ... |

## Connected Communities

- **includes · CHttpGet** (4 cross-edges)

## How to Explore

```
get_communities with id: "community-21"
smart_context with task: "understand includes · setHeader", format: "gcx"
```

_`format: "gcx"` returns the [GCX1 compact wire format](../../docs/wire-format.md) — round-trippable, ~27% fewer tokens than JSON. Drop it for JSON output; agents using `@gortex/wire` or the Go `github.com/gortexhq/gcx-go` package decode either._
