---
name: gortex-smarty-sysplugins-trigger-template-error
description: "Work in the smarty/sysplugins · trigger_template_error area — 142 symbols across 39 files (87% cohesion)"
---

# smarty/sysplugins · trigger_template_error

142 symbols | 39 files | 87% cohesion

## When to Use

Use this skill when working on files in:
- `includes/smarty/sysplugins/smarty_cacheresource.php`
- `includes/smarty/sysplugins/smarty_internal_compile_append.php`
- `includes/smarty/sysplugins/smarty_internal_compile_assign.php`
- `includes/smarty/sysplugins/smarty_internal_compile_block.php`
- `includes/smarty/sysplugins/smarty_internal_compile_break.php`
- `includes/smarty/sysplugins/smarty_internal_compile_call.php`
- `includes/smarty/sysplugins/smarty_internal_compile_capture.php`
- `includes/smarty/sysplugins/smarty_internal_compile_child.php`
- `includes/smarty/sysplugins/smarty_internal_compile_config_load.php`
- `includes/smarty/sysplugins/smarty_internal_compile_debug.php`
- `includes/smarty/sysplugins/smarty_internal_compile_eval.php`
- `includes/smarty/sysplugins/smarty_internal_compile_extends.php`
- `includes/smarty/sysplugins/smarty_internal_compile_for.php`
- `includes/smarty/sysplugins/smarty_internal_compile_foreach.php`
- `includes/smarty/sysplugins/smarty_internal_compile_function.php`
- `includes/smarty/sysplugins/smarty_internal_compile_if.php`
- `includes/smarty/sysplugins/smarty_internal_compile_include.php`
- `includes/smarty/sysplugins/smarty_internal_compile_include_php.php`
- `includes/smarty/sysplugins/smarty_internal_compile_insert.php`
- `includes/smarty/sysplugins/smarty_internal_compile_ldelim.php`
- `includes/smarty/sysplugins/smarty_internal_compile_make_nocache.php`
- `includes/smarty/sysplugins/smarty_internal_compile_nocache.php`
- `includes/smarty/sysplugins/smarty_internal_compile_private_block_plugin.php`
- `includes/smarty/sysplugins/smarty_internal_compile_private_foreachsection.php`
- `includes/smarty/sysplugins/smarty_internal_compile_private_modifier.php`
- `includes/smarty/sysplugins/smarty_internal_compile_private_php.php`
- `includes/smarty/sysplugins/smarty_internal_compile_private_special_variable.php`
- `includes/smarty/sysplugins/smarty_internal_compile_rdelim.php`
- `includes/smarty/sysplugins/smarty_internal_compile_section.php`
- `includes/smarty/sysplugins/smarty_internal_compile_shared_inheritance.php`
- `includes/smarty/sysplugins/smarty_internal_compile_while.php`
- `includes/smarty/sysplugins/smarty_internal_compilebase.php`
- `includes/smarty/sysplugins/smarty_internal_method_loadplugin.php`
- `includes/smarty/sysplugins/smarty_internal_smartytemplatecompiler.php`
- `includes/smarty/sysplugins/smarty_internal_template.php`
- `includes/smarty/sysplugins/smarty_internal_templatecompilerbase.php`
- `includes/smarty/sysplugins/smarty_internal_templatelexer.php`
- `includes/smarty/sysplugins/smarty_internal_templateparser.php`
- `includes/smarty/sysplugins/smarty_security.php`

## Key Files

| File | Symbols |
|------|---------|
| `includes/smarty/sysplugins/smarty_cacheresource.php` | load |
| `includes/smarty/sysplugins/smarty_internal_compile_append.php` | compile, Smarty_Internal_Compile_Append |
| `includes/smarty/sysplugins/smarty_internal_compile_assign.php` | Smarty_Internal_Compile_Assign, compile |
| `includes/smarty/sysplugins/smarty_internal_compile_block.php` | Smarty_Internal_Compile_Block, compile |
| `includes/smarty/sysplugins/smarty_internal_compile_break.php` | compile, Smarty_Internal_Compile_Break, checkLevels |
| `includes/smarty/sysplugins/smarty_internal_compile_call.php` | Smarty_Internal_Compile_Call, compile |
| `includes/smarty/sysplugins/smarty_internal_compile_capture.php` | compileSpecialVariable, compile, Smarty_Internal_Compile_Capture, Smarty_Internal_Compile_CaptureClose, compile |
| `includes/smarty/sysplugins/smarty_internal_compile_child.php` | Smarty_Internal_Compile_Child, compile |
| `includes/smarty/sysplugins/smarty_internal_compile_config_load.php` | Smarty_Internal_Compile_Config_Load, compile |
| `includes/smarty/sysplugins/smarty_internal_compile_debug.php` | compile, Smarty_Internal_Compile_Debug |
| `includes/smarty/sysplugins/smarty_internal_compile_eval.php` | compile, Smarty_Internal_Compile_Eval |
| `includes/smarty/sysplugins/smarty_internal_compile_extends.php` | compileInclude, Smarty_Internal_Compile_Extends, compileEndChild, compile |
| `includes/smarty/sysplugins/smarty_internal_compile_for.php` | compile, compile, Smarty_Internal_Compile_For, Smarty_Internal_Compile_Forclose, Smarty_Internal_Compile_Forelse, ... |
| `includes/smarty/sysplugins/smarty_internal_compile_foreach.php` | compileRestore, Smarty_Internal_Compile_Foreach, compile, Smarty_Internal_Compile_Foreachclose, compile, ... |
| `includes/smarty/sysplugins/smarty_internal_compile_function.php` | compile, Smarty_Internal_Compile_Function |
| `includes/smarty/sysplugins/smarty_internal_compile_if.php` | compile, compile, compile, Smarty_Internal_Compile_Else, Smarty_Internal_Compile_If, ... |
| `includes/smarty/sysplugins/smarty_internal_compile_include.php` | compile |
| `includes/smarty/sysplugins/smarty_internal_compile_include_php.php` | compile, Smarty_Internal_Compile_Include_Php |
| `includes/smarty/sysplugins/smarty_internal_compile_insert.php` | Smarty_Internal_Compile_Insert, compile |
| `includes/smarty/sysplugins/smarty_internal_compile_ldelim.php` | Smarty_Internal_Compile_Ldelim, compile |
| `includes/smarty/sysplugins/smarty_internal_compile_make_nocache.php` | Smarty_Internal_Compile_Make_Nocache, compile |
| `includes/smarty/sysplugins/smarty_internal_compile_nocache.php` | Smarty_Internal_Compile_Nocacheclose, Smarty_Internal_Compile_Nocache, compile, compile |
| `includes/smarty/sysplugins/smarty_internal_compile_private_block_plugin.php` | compile, Smarty_Internal_Compile_Private_Block_Plugin, setup |
| `includes/smarty/sysplugins/smarty_internal_compile_private_foreachsection.php` | compileSpecialVariable |
| `includes/smarty/sysplugins/smarty_internal_compile_private_modifier.php` | compile, Smarty_Internal_Compile_Private_Modifier |
| `includes/smarty/sysplugins/smarty_internal_compile_private_php.php` | parsePhp, quote, compile, Smarty_Internal_Compile_Private_Php |
| `includes/smarty/sysplugins/smarty_internal_compile_private_special_variable.php` | compile, Smarty_Internal_Compile_Private_Special_Variable |
| `includes/smarty/sysplugins/smarty_internal_compile_rdelim.php` | compile, Smarty_Internal_Compile_Rdelim |
| `includes/smarty/sysplugins/smarty_internal_compile_section.php` | compile, Smarty_Internal_Compile_Sectionclose, Smarty_Internal_Compile_Sectionelse, Smarty_Internal_Compile_Section, compile, ... |
| `includes/smarty/sysplugins/smarty_internal_compile_shared_inheritance.php` | Smarty_Internal_Compile_Shared_Inheritance, registerInit, postCompile |
| `includes/smarty/sysplugins/smarty_internal_compile_while.php` | compile, Smarty_Internal_Compile_Whileclose, compile, Smarty_Internal_Compile_While |
| `includes/smarty/sysplugins/smarty_internal_compilebase.php` | Smarty_Internal_CompileBase, closeTag, openTag, getAttributes |
| `includes/smarty/sysplugins/smarty_internal_method_loadplugin.php` | Smarty_Internal_Method_LoadPlugin, loadPlugin |
| `includes/smarty/sysplugins/smarty_internal_smartytemplatecompiler.php` | registerPostCompileCallback |
| `includes/smarty/sysplugins/smarty_internal_template.php` | _checkPlugins |
| `includes/smarty/sysplugins/smarty_internal_templatecompilerbase.php` | getPluginFromDefaultHandler, processNocacheCode, getRdelPreg, isVariable, getTagCompiler, ... |
| `includes/smarty/sysplugins/smarty_internal_templatelexer.php` | yy_r1_4, yy_r1_16, yy_r1_2 |
| `includes/smarty/sysplugins/smarty_internal_templateparser.php` | yy_r100, yy_r139, yy_r142, yy_r145, yy_r144, ... |
| `includes/smarty/sysplugins/smarty_security.php` | isTrustedPhpFunction, isTrustedModifier, isTrustedPHPDir, isTrustedSpecialSmartyVar, _checkDir, ... |

## Entry Points

- `includes/smarty/sysplugins/smarty_internal_compile_foreach.php::Smarty_Internal_Compile_Foreach.compile`
- `includes/smarty/sysplugins/smarty_internal_compile_section.php::Smarty_Internal_Compile_Section.compile`
- `includes/smarty/sysplugins/smarty_internal_compile_include.php::Smarty_Internal_Compile_Include.compile`
- `includes/smarty/sysplugins/smarty_internal_compile_private_modifier.php::Smarty_Internal_Compile_Private_Modifier.compile`
- `includes/smarty/sysplugins/smarty_internal_compile_private_special_variable.php::Smarty_Internal_Compile_Private_Special_Variable.compile`

## Connected Communities

- **smarty/sysplugins · count** (6 cross-edges)
- **smarty/sysplugins · Smarty_Internal_Templateparser** (4 cross-edges)
- **smarty/sysplugins · append_subtree** (3 cross-edges)
- **smarty/sysplugins · getIncludePath** (3 cross-edges)
- **smarty/sysplugins · scanForProperties** (2 cross-edges)
- **smarty/sysplugins · appendCode** (2 cross-edges)
- **smarty/sysplugins · compileTemplateSource** (2 cross-edges)
- **smarty/sysplugins · Smarty_Internal_Configfileparser** (2 cross-edges)
- **smarty/sysplugins · doParse** (1 cross-edges)
- **smarty/sysplugins · load · smarty_cacheresource** (1 cross-edges)

## How to Explore

```
get_communities with id: "community-107"
smart_context with task: "understand smarty/sysplugins · trigger_template_error", format: "gcx"
find_usages with id: "includes/smarty/sysplugins/smarty_internal_compile_foreach.php::Smarty_Internal_Compile_Foreach.compile", format: "gcx"
```

_`format: "gcx"` returns the [GCX1 compact wire format](../../docs/wire-format.md) — round-trippable, ~27% fewer tokens than JSON. Drop it for JSON output; agents using `@gortex/wire` or the Go `github.com/gortexhq/gcx-go` package decode either._
