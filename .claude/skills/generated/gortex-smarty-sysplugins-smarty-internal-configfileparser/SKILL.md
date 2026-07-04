---
name: gortex-smarty-sysplugins-smarty-internal-configfileparser
description: "Work in the smarty/sysplugins · Smarty_Internal_Configfileparser area — 48 symbols across 5 files (88% cohesion)"
---

# smarty/sysplugins · Smarty_Internal_Configfileparser

48 symbols | 5 files | 88% cohesion

## When to Use

Use this skill when working on files in:
- `includes/smarty/sysplugins/smarty_internal_config_file_compiler.php`
- `includes/smarty/sysplugins/smarty_internal_configfilelexer.php`
- `includes/smarty/sysplugins/smarty_internal_configfileparser.php`
- `includes/smarty/sysplugins/smarty_internal_smartytemplatecompiler.php`
- `includes/smarty/sysplugins/smarty_internal_templateparser.php`

## Key Files

| File | Symbols |
|------|---------|
| `includes/smarty/sysplugins/smarty_internal_config_file_compiler.php` | __construct, trigger_config_file_error, Smarty_Internal_Config_File_Compiler, compileTemplate |
| `includes/smarty/sysplugins/smarty_internal_configfilelexer.php` | PrintTrace, yy_r6_2 |
| `includes/smarty/sysplugins/smarty_internal_configfileparser.php` | yy_shift, yy_r7, yy_r12, yy_destructor, yy_r0, ... |
| `includes/smarty/sysplugins/smarty_internal_smartytemplatecompiler.php` | doCompile |
| `includes/smarty/sysplugins/smarty_internal_templateparser.php` | insertPhpCode |

## Connected Communities

- **smarty/sysplugins · count** (3 cross-edges)
- **smarty/sysplugins · compileTemplateSource** (2 cross-edges)
- **smarty/sysplugins · replace · smarty_internal_templatecompilerbase** (2 cross-edges)
- **smarty/sysplugins · append_subtree** (1 cross-edges)
- **smarty/sysplugins · render** (1 cross-edges)
- **smarty/sysplugins · trigger_template_error** (1 cross-edges)
- **smarty/sysplugins · process · smarty_internal_cacheresource_file** (1 cross-edges)
- **smarty/sysplugins · Smarty_Internal_Resource_String** (1 cross-edges)

## How to Explore

```
get_communities with id: "community-71"
smart_context with task: "understand smarty/sysplugins · Smarty_Internal_Configfileparser", format: "gcx"
```

_`format: "gcx"` returns the [GCX1 compact wire format](../../docs/wire-format.md) — round-trippable, ~27% fewer tokens than JSON. Drop it for JSON output; agents using `@gortex/wire` or the Go `github.com/gortexhq/gcx-go` package decode either._
