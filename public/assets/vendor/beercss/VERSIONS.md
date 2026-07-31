# Vendored Asset Versions

- Beer CSS: `4.0.23`
  - Source package: `beercss@4.0.23`
  - Files: `beer.min.css`, `beer.min.js`, Material Symbols `.woff2` files in `fonts/`
- Material Dynamic Colors: `1.1.4`
  - Source package: `material-dynamic-colors@1.1.4`
  - File: `material-dynamic-colors.min.js`
- Inter font subset: `5.0.18`
  - Source package: `@fontsource/inter@5.0.18`
  - Files: `inter-latin-{400,600,700}-normal.woff2`

Note: `beer.min.css` was patched to remove fallback CDN font URLs and use local `fonts/` assets
only. That first patch left a duplicate, non-prefixed `url(material-symbols-*.woff2)` reference
alongside the working `url(fonts/material-symbols-*.woff2)` one in each `@font-face` rule, which
404'd on every page load; the duplicate has since been removed, keeping only the `fonts/`-prefixed
reference.
