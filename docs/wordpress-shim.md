# WordPress shim guide (future)

If WordPress and phpDivingLog share one domain, run phpDivingLog in a dedicated
subfolder such as `/divelog` and route that prefix to phpDivingLog front
controllers before WordPress catch-all rewrites.

See:

- `docs/nginx.conf.example`
- `docs/apache-htaccess.example`

The WordPress consumer is intentionally out of scope for this rewrite. To add it later,
create a shortcode plugin that calls the standalone JSON API.

Recommended pattern:

1. Store API base URL and optional token in WordPress options.
2. In shortcode callback, request `/api/dives`, `/api/dives/{id}`, or `/api/stats`.
3. Render returned JSON with WP-safe escaping (`esc_html`, `esc_attr`, etc.).
4. Cache responses in transients to reduce API round-trips.

This keeps WordPress as a thin consumer while core logic remains in phpDivingLog.
