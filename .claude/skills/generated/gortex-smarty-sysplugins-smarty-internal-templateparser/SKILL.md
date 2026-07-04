---
name: gortex-smarty-sysplugins-smarty-internal-templateparser
description: "Work in the smarty/sysplugins · Smarty_Internal_Templateparser area — 149 symbols across 8 files (88% cohesion)"
---

# smarty/sysplugins · Smarty_Internal_Templateparser

149 symbols | 8 files | 88% cohesion

## When to Use

Use this skill when working on files in:
- `includes/smarty/sysplugins/smarty_internal_compile_private_function_plugin.php`
- `includes/smarty/sysplugins/smarty_internal_compile_private_object_function.php`
- `includes/smarty/sysplugins/smarty_internal_compile_private_print_expression.php`
- `includes/smarty/sysplugins/smarty_internal_compile_private_registered_function.php`
- `includes/smarty/sysplugins/smarty_internal_templatecompilerbase.php`
- `includes/smarty/sysplugins/smarty_internal_templatelexer.php`
- `includes/smarty/sysplugins/smarty_internal_templateparser.php`
- `includes/smarty/sysplugins/smarty_security.php`

## Key Files

| File | Symbols |
|------|---------|
| `includes/smarty/sysplugins/smarty_internal_compile_private_function_plugin.php` | Smarty_Internal_Compile_Private_Function_Plugin, compile |
| `includes/smarty/sysplugins/smarty_internal_compile_private_object_function.php` | Smarty_Internal_Compile_Private_Object_Function, compile |
| `includes/smarty/sysplugins/smarty_internal_compile_private_print_expression.php` | Smarty_Internal_Compile_Private_Print_Expression, compile_variable_filter, compile |
| `includes/smarty/sysplugins/smarty_internal_compile_private_registered_function.php` | Smarty_Internal_Compile_Private_Registered_Function, compile |
| `includes/smarty/sysplugins/smarty_internal_templatecompilerbase.php` | getRdelLength, compileConfigVariable, getLdelLength, appendPrefixCode, compileVariable, ... |
| `includes/smarty/sysplugins/smarty_internal_templatelexer.php` | isAutoLiteral, yy_r3_44 |
| `includes/smarty/sysplugins/smarty_internal_templateparser.php` | yy_r136, yy_r29, yy_r49, yy_r75, yy_r119, ... |
| `includes/smarty/sysplugins/smarty_security.php` | isTrustedConstant |

## Connected Communities

- **smarty/sysplugins · trigger_template_error** (10 cross-edges)
- **smarty/sysplugins · appendCode** (2 cross-edges)
- **smarty/sysplugins · count** (2 cross-edges)
- **smarty/sysplugins · render** (1 cross-edges)

## How to Explore

```
get_communities with id: "community-103"
smart_context with task: "understand smarty/sysplugins · Smarty_Internal_Templateparser", format: "gcx"
```

_`format: "gcx"` returns the [GCX1 compact wire format](../../docs/wire-format.md) — round-trippable, ~27% fewer tokens than JSON. Drop it for JSON output; agents using `@gortex/wire` or the Go `github.com/gortexhq/gcx-go` package decode either._
