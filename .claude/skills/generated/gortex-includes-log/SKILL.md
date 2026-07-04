---
name: gortex-includes-log
description: "Work in the includes · log area — 138 symbols across 3 files (99% cohesion)"
---

# includes · log

138 symbols | 3 files | 99% cohesion

## When to Use

Use this skill when working on files in:
- `includes/imgd.php`
- `includes/imgp.php`
- `includes/misc.inc.php`

## Key Files

| File | Symbols |
|------|---------|
| `includes/imgd.php` | ascii, log, rotateExif, setRemoteHostWhitelist, json, ... |
| `includes/imgp.php` | setPngCompression, getPngType, verboseOutput, useOriginalIfPossible, ascii, ... |
| `includes/misc.inc.php` | formatBytes |

## Entry Points

- `includes/imgp.php::CImage.save`
- `includes/imgd.php::CImage.save`
- `includes/imgp.php::CImage.resize`
- `includes/imgd.php::CImage.resize`
- `includes/imgp.php::CImage.initDimensions`

## Connected Communities

- **includes · CFastTrackCache** (5 cross-edges)
- **includes · CAsciiArt** (2 cross-edges)
- **includes · setHeader** (2 cross-edges)
- **includes · set** (2 cross-edges)

## How to Explore

```
get_communities with id: "community-20"
smart_context with task: "understand includes · log", format: "gcx"
find_usages with id: "includes/imgp.php::CImage.save", format: "gcx"
```

_`format: "gcx"` returns the [GCX1 compact wire format](../../docs/wire-format.md) — round-trippable, ~27% fewer tokens than JSON. Drop it for JSON output; agents using `@gortex/wire` or the Go `github.com/gortexhq/gcx-go` package decode either._
