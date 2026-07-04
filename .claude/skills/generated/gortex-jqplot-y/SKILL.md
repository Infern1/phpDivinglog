---
name: gortex-jqplot-y
description: "Work in the jqplot · y area — 28 symbols across 2 files (98% cohesion)"
---

# jqplot · y

28 symbols | 2 files | 98% cohesion

## When to Use

Use this skill when working on files in:
- `includes/jqplot/excanvas.js`
- `includes/jqplot/excanvas.min.js`

## Key Files

| File | Symbols |
|------|---------|
| `includes/jqplot/excanvas.js` | onPropertyChange, getContext |
| `includes/jqplot/excanvas.min.js` | Y, af, G, a, h, ... |

## Entry Points

- `includes/jqplot/excanvas.min.js::y`

## Connected Communities

- **jqplot · jqPlot** (1 cross-edges)

## How to Explore

```
get_communities with id: "community-28"
smart_context with task: "understand jqplot · y", format: "gcx"
find_usages with id: "includes/jqplot/excanvas.min.js::y", format: "gcx"
```

_`format: "gcx"` returns the [GCX1 compact wire format](../../docs/wire-format.md) — round-trippable, ~27% fewer tokens than JSON. Drop it for JSON output; agents using `@gortex/wire` or the Go `github.com/gortexhq/gcx-go` package decode either._
