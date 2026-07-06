---
name: gortex-smarty-sysplugins-render
description: "Work in the smarty/sysplugins · render area — 64 symbols across 16 files (80% cohesion)"
---

# smarty/sysplugins · render

64 symbols | 16 files | 80% cohesion

## When to Use

Use this skill when working on files in:
- `includes/smarty/sysplugins/smarty_internal_data.php`
- `includes/smarty/sysplugins/smarty_internal_debug.php`
- `includes/smarty/sysplugins/smarty_internal_method_gettemplatevars.php`
- `includes/smarty/sysplugins/smarty_internal_nocache_insert.php`
- `includes/smarty/sysplugins/smarty_internal_resource_php.php`
- `includes/smarty/sysplugins/smarty_internal_runtime_cachemodify.php`
- `includes/smarty/sysplugins/smarty_internal_runtime_inheritance.php`
- `includes/smarty/sysplugins/smarty_internal_runtime_tplfunction.php`
- `includes/smarty/sysplugins/smarty_internal_runtime_updatecache.php`
- `includes/smarty/sysplugins/smarty_internal_template.php`
- `includes/smarty/sysplugins/smarty_internal_templatebase.php`
- `includes/smarty/sysplugins/smarty_resource_recompiled.php`
- `includes/smarty/sysplugins/smarty_security.php`
- `includes/smarty/sysplugins/smarty_template_cached.php`
- `includes/smarty/sysplugins/smarty_template_compiled.php`
- `includes/smarty/sysplugins/smarty_template_resource_base.php`

## Key Files

| File | Symbols |
|------|---------|
| `includes/smarty/sysplugins/smarty_internal_data.php` | _mergeVars, getVariable |
| `includes/smarty/sysplugins/smarty_internal_debug.php` | start_template, get_key, debugUrl, start_compile, end_cache, ... |
| `includes/smarty/sysplugins/smarty_internal_method_gettemplatevars.php` | _getVariable, getTemplateVars, Smarty_Internal_Method_GetTemplateVars |
| `includes/smarty/sysplugins/smarty_internal_nocache_insert.php` | compile, Smarty_Internal_Nocache_Insert |
| `includes/smarty/sysplugins/smarty_internal_resource_php.php` | getContent, populateCompiledFilepath, renderUncompiled, __construct, Smarty_Internal_Resource_Php |
| `includes/smarty/sysplugins/smarty_internal_runtime_cachemodify.php` | Smarty_Internal_Runtime_CacheModify, cacheModifiedCheck |
| `includes/smarty/sysplugins/smarty_internal_runtime_inheritance.php` | endChild |
| `includes/smarty/sysplugins/smarty_internal_runtime_tplfunction.php` | registerTplFunctions |
| `includes/smarty/sysplugins/smarty_internal_runtime_updatecache.php` | cacheModifiedCheck, writeCachedContent, removeNoCacheHash, updateCache, Smarty_Internal_Runtime_UpdateCache, ... |
| `includes/smarty/sysplugins/smarty_internal_template.php` | _loadInheritance, Smarty_Internal_Template, __get, loadCompiler, _cleanUp, ... |
| `includes/smarty/sysplugins/smarty_internal_templatebase.php` | display, _execute, isCached, fetch |
| `includes/smarty/sysplugins/smarty_resource_recompiled.php` | process, populateCompiledFilepath, checkTimestamps, Smarty_Resource_Recompiled |
| `includes/smarty/sysplugins/smarty_security.php` | endTemplate |
| `includes/smarty/sysplugins/smarty_template_cached.php` | render |
| `includes/smarty/sysplugins/smarty_template_compiled.php` | render |
| `includes/smarty/sysplugins/smarty_template_resource_base.php` | getRenderedTemplateCode, process, Smarty_Template_Resource_Base |

## Connected Communities

- **smarty/sysplugins · _isTplObj** (6 cross-edges)
- **smarty/sysplugins · compileTemplateSource** (4 cross-edges)
- **smarty/sysplugins · load · smarty_cacheresource** (3 cross-edges)
- **smarty/sysplugins · Smarty_CacheResource** (3 cross-edges)
- **smarty/sysplugins · _getSmartyObj** (2 cross-edges)
- **smarty/sysplugins · Smarty_CacheResource_Custom** (1 cross-edges)
- **smarty/sysplugins · process · smarty_internal_cacheresource_file** (1 cross-edges)
- **smarty/sysplugins · trigger_template_error** (1 cross-edges)
- **smarty/sysplugins · Smarty_Internal_Configfileparser** (1 cross-edges)

## How to Explore

```
get_communities with id: "community-73"
smart_context with task: "understand smarty/sysplugins · render", format: "gcx"
```

_`format: "gcx"` returns the [GCX1 compact wire format](../../docs/wire-format.md) — round-trippable, ~27% fewer tokens than JSON. Drop it for JSON output; agents using `@gortex/wire` or the Go `github.com/gortexhq/gcx-go` package decode either._
