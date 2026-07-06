# Front-end build workflow

The app runs directly from committed assets under `public/assets/` and does not require Node
at request time.

Optional build steps:

1. Install Node.js 20+.
2. Run `npm install`.
3. Run `npm run build`.

This copies runtime assets to `public/assets/vendor/` for release packaging.
