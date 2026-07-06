---
name: gortex-jqplot-plugins-handlemove
description: "Work in the jqplot/plugins · handleMove area — 20 symbols across 6 files (96% cohesion)"
---

# jqplot/plugins · handleMove

20 symbols | 6 files | 96% cohesion

## When to Use

Use this skill when working on files in:
- `includes/jqplot/plugins/jqplot.barRenderer.js`
- `includes/jqplot/plugins/jqplot.canvasOverlay.js`
- `includes/jqplot/plugins/jqplot.cursor.js`
- `includes/jqplot/plugins/jqplot.dragable.js`
- `includes/jqplot/plugins/jqplot.highlighter.js`
- `includes/jqplot/plugins/jqplot.pyramidRenderer.js`

## Key Files

| File | Symbols |
|------|---------|
| `includes/jqplot/plugins/jqplot.barRenderer.js` | highlight |
| `includes/jqplot/plugins/jqplot.canvasOverlay.js` | handleMove, isNearRectangle, isNearLine |
| `includes/jqplot/plugins/jqplot.cursor.js` | positionTooltip, updateTooltip, getIntersectingPoints, handleMouseEnter, handleMouseMove, ... |
| `includes/jqplot/plugins/jqplot.dragable.js` | initDragPoint, handleDown, handleMove |
| `includes/jqplot/plugins/jqplot.highlighter.js` | handleMove, showTooltip, draw |
| `includes/jqplot/plugins/jqplot.pyramidRenderer.js` | highlight |

## Entry Points

- `includes/jqplot/plugins/jqplot.canvasOverlay.js::handleMove`
- `includes/jqplot/plugins/jqplot.highlighter.js::handleMove`

## How to Explore

```
get_communities with id: "community-43"
smart_context with task: "understand jqplot/plugins · handleMove", format: "gcx"
find_usages with id: "includes/jqplot/plugins/jqplot.canvasOverlay.js::handleMove", format: "gcx"
```

_`format: "gcx"` returns the [GCX1 compact wire format](../../docs/wire-format.md) — round-trippable, ~27% fewer tokens than JSON. Drop it for JSON output; agents using `@gortex/wire` or the Go `github.com/gortexhq/gcx-go` package decode either._
