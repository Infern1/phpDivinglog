---
name: gortex-smarty-sysplugins-getsmartyobj
description: "Work in the smarty/sysplugins · _getSmartyObj area — 50 symbols across 25 files (85% cohesion)"
---

# smarty/sysplugins · _getSmartyObj

50 symbols | 25 files | 85% cohesion

## When to Use

Use this skill when working on files in:
- `includes/smarty/sysplugins/smarty_internal_data.php`
- `includes/smarty/sysplugins/smarty_internal_debug.php`
- `includes/smarty/sysplugins/smarty_internal_method_adddefaultmodifiers.php`
- `includes/smarty/sysplugins/smarty_internal_method_createdata.php`
- `includes/smarty/sysplugins/smarty_internal_method_getdebugtemplate.php`
- `includes/smarty/sysplugins/smarty_internal_method_getdefaultmodifiers.php`
- `includes/smarty/sysplugins/smarty_internal_method_getregisteredobject.php`
- `includes/smarty/sysplugins/smarty_internal_method_literals.php`
- `includes/smarty/sysplugins/smarty_internal_method_loadfilter.php`
- `includes/smarty/sysplugins/smarty_internal_method_registercacheresource.php`
- `includes/smarty/sysplugins/smarty_internal_method_registerclass.php`
- `includes/smarty/sysplugins/smarty_internal_method_registerdefaultconfighandler.php`
- `includes/smarty/sysplugins/smarty_internal_method_registerdefaultpluginhandler.php`
- `includes/smarty/sysplugins/smarty_internal_method_registerdefaulttemplatehandler.php`
- `includes/smarty/sysplugins/smarty_internal_method_registerobject.php`
- `includes/smarty/sysplugins/smarty_internal_method_registerplugin.php`
- `includes/smarty/sysplugins/smarty_internal_method_setautoloadfilters.php`
- `includes/smarty/sysplugins/smarty_internal_method_setdebugtemplate.php`
- `includes/smarty/sysplugins/smarty_internal_method_setdefaultmodifiers.php`
- `includes/smarty/sysplugins/smarty_internal_method_unregistercacheresource.php`
- `includes/smarty/sysplugins/smarty_internal_method_unregisterobject.php`
- `includes/smarty/sysplugins/smarty_internal_method_unregisterplugin.php`
- `includes/smarty/sysplugins/smarty_internal_method_unregisterresource.php`
- `includes/smarty/sysplugins/smarty_internal_templatecompilerbase.php`
- `includes/smarty/sysplugins/smarty_internal_templatelexer.php`

## Key Files

| File | Symbols |
|------|---------|
| `includes/smarty/sysplugins/smarty_internal_data.php` | _getSmartyObj |
| `includes/smarty/sysplugins/smarty_internal_debug.php` | register_data |
| `includes/smarty/sysplugins/smarty_internal_method_adddefaultmodifiers.php` | Smarty_Internal_Method_AddDefaultModifiers, addDefaultModifiers |
| `includes/smarty/sysplugins/smarty_internal_method_createdata.php` | Smarty_Internal_Method_CreateData, createData |
| `includes/smarty/sysplugins/smarty_internal_method_getdebugtemplate.php` | getDebugTemplate, Smarty_Internal_Method_GetDebugTemplate |
| `includes/smarty/sysplugins/smarty_internal_method_getdefaultmodifiers.php` | getDefaultModifiers, Smarty_Internal_Method_GetDefaultModifiers |
| `includes/smarty/sysplugins/smarty_internal_method_getregisteredobject.php` | getRegisteredObject, Smarty_Internal_Method_GetRegisteredObject |
| `includes/smarty/sysplugins/smarty_internal_method_literals.php` | addLiterals, set, setLiterals, getLiterals, Smarty_Internal_Method_Literals |
| `includes/smarty/sysplugins/smarty_internal_method_loadfilter.php` | _checkFilterType, loadFilter, Smarty_Internal_Method_LoadFilter |
| `includes/smarty/sysplugins/smarty_internal_method_registercacheresource.php` | registerCacheResource, Smarty_Internal_Method_RegisterCacheResource |
| `includes/smarty/sysplugins/smarty_internal_method_registerclass.php` | registerClass, Smarty_Internal_Method_RegisterClass |
| `includes/smarty/sysplugins/smarty_internal_method_registerdefaultconfighandler.php` | Smarty_Internal_Method_RegisterDefaultConfigHandler, registerDefaultConfigHandler |
| `includes/smarty/sysplugins/smarty_internal_method_registerdefaultpluginhandler.php` | Smarty_Internal_Method_RegisterDefaultPluginHandler, registerDefaultPluginHandler |
| `includes/smarty/sysplugins/smarty_internal_method_registerdefaulttemplatehandler.php` | registerDefaultTemplateHandler |
| `includes/smarty/sysplugins/smarty_internal_method_registerobject.php` | Smarty_Internal_Method_RegisterObject, registerObject |
| `includes/smarty/sysplugins/smarty_internal_method_registerplugin.php` | registerPlugin, Smarty_Internal_Method_RegisterPlugin |
| `includes/smarty/sysplugins/smarty_internal_method_setautoloadfilters.php` | setAutoloadFilters, _checkFilterType, Smarty_Internal_Method_SetAutoloadFilters |
| `includes/smarty/sysplugins/smarty_internal_method_setdebugtemplate.php` | setDebugTemplate, Smarty_Internal_Method_SetDebugTemplate |
| `includes/smarty/sysplugins/smarty_internal_method_setdefaultmodifiers.php` | setDefaultModifiers, Smarty_Internal_Method_SetDefaultModifiers |
| `includes/smarty/sysplugins/smarty_internal_method_unregistercacheresource.php` | unregisterCacheResource, Smarty_Internal_Method_UnregisterCacheResource |
| `includes/smarty/sysplugins/smarty_internal_method_unregisterobject.php` | Smarty_Internal_Method_UnregisterObject, unregisterObject |
| `includes/smarty/sysplugins/smarty_internal_method_unregisterplugin.php` | unregisterPlugin, Smarty_Internal_Method_UnregisterPlugin |
| `includes/smarty/sysplugins/smarty_internal_method_unregisterresource.php` | Smarty_Internal_Method_UnregisterResource, unregisterResource |
| `includes/smarty/sysplugins/smarty_internal_templatecompilerbase.php` | initDelimiterPreg |
| `includes/smarty/sysplugins/smarty_internal_templatelexer.php` | __construct |

## Connected Communities

- **smarty/sysplugins · trigger_template_error** (1 cross-edges)

## How to Explore

```
get_communities with id: "community-83"
smart_context with task: "understand smarty/sysplugins · _getSmartyObj", format: "gcx"
```

_`format: "gcx"` returns the [GCX1 compact wire format](../../docs/wire-format.md) — round-trippable, ~27% fewer tokens than JSON. Drop it for JSON output; agents using `@gortex/wire` or the Go `github.com/gortexhq/gcx-go` package decode either._
