---
name: gortex-smarty-sysplugins-istplobj
description: "Work in the smarty/sysplugins · _isTplObj area — 30 symbols across 11 files (84% cohesion)"
---

# smarty/sysplugins · _isTplObj

30 symbols | 11 files | 84% cohesion

## When to Use

Use this skill when working on files in:
- `includes/smarty/sysplugins/smarty_internal_data.php`
- `includes/smarty/sysplugins/smarty_internal_debug.php`
- `includes/smarty/sysplugins/smarty_internal_method_append.php`
- `includes/smarty/sysplugins/smarty_internal_method_appendbyref.php`
- `includes/smarty/sysplugins/smarty_internal_method_assignbyref.php`
- `includes/smarty/sysplugins/smarty_internal_method_assignglobal.php`
- `includes/smarty/sysplugins/smarty_internal_method_configload.php`
- `includes/smarty/sysplugins/smarty_internal_method_getconfigvariable.php`
- `includes/smarty/sysplugins/smarty_internal_method_gettags.php`
- `includes/smarty/sysplugins/smarty_internal_runtime_updatescope.php`
- `includes/smarty/sysplugins/smarty_internal_template.php`

## Key Files

| File | Symbols |
|------|---------|
| `includes/smarty/sysplugins/smarty_internal_data.php` | assign, _isDataObj, _isTplObj |
| `includes/smarty/sysplugins/smarty_internal_debug.php` | get_debug_vars, display_debug |
| `includes/smarty/sysplugins/smarty_internal_method_append.php` | append, Smarty_Internal_Method_Append |
| `includes/smarty/sysplugins/smarty_internal_method_appendbyref.php` | appendByRef, Smarty_Internal_Method_AppendByRef |
| `includes/smarty/sysplugins/smarty_internal_method_assignbyref.php` | assignByRef, Smarty_Internal_Method_AssignByRef |
| `includes/smarty/sysplugins/smarty_internal_method_assignglobal.php` | assignGlobal, Smarty_Internal_Method_AssignGlobal |
| `includes/smarty/sysplugins/smarty_internal_method_configload.php` | Smarty_Internal_Method_ConfigLoad, _assignConfigVars, _loadConfigVars, _getConfigVariable, _loadConfigFile, ... |
| `includes/smarty/sysplugins/smarty_internal_method_getconfigvariable.php` | Smarty_Internal_Method_GetConfigVariable, getConfigVariable |
| `includes/smarty/sysplugins/smarty_internal_method_gettags.php` | getTags, Smarty_Internal_Method_GetTags |
| `includes/smarty/sysplugins/smarty_internal_runtime_updatescope.php` | _updateVariableInOtherScope, _updateVarStack, Smarty_Internal_Runtime_UpdateScope, _updateScope, _getAffectedScopes |
| `includes/smarty/sysplugins/smarty_internal_template.php` | _assignInScope |

## Connected Communities

- **smarty/sysplugins · _getSmartyObj** (3 cross-edges)
- **smarty/sysplugins · render** (3 cross-edges)
- **smarty/sysplugins · Smarty_CacheResource_Custom** (1 cross-edges)
- **smarty/sysplugins · load · smarty_cacheresource** (1 cross-edges)
- **smarty/sysplugins · process · smarty_internal_cacheresource_file** (1 cross-edges)
- **smarty/sysplugins · Smarty_Internal_Configfileparser** (1 cross-edges)

## How to Explore

```
get_communities with id: "community-98"
smart_context with task: "understand smarty/sysplugins · _isTplObj", format: "gcx"
```

_`format: "gcx"` returns the [GCX1 compact wire format](../../docs/wire-format.md) — round-trippable, ~27% fewer tokens than JSON. Drop it for JSON output; agents using `@gortex/wire` or the Go `github.com/gortexhq/gcx-go` package decode either._
