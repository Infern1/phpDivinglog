# TODO status

This file records the status of the historical TODO list against the current modernized codebase.

## Implemented

- [x] Get average depth and SAC for multiple dive tank sets working.
  - Average depth is calculated from profile samples, and SAC/RMV sums usable gas across all linked tanks when tank volume and start/end pressures are available.
- [x] Display related info from other tables in dive details, including dive site, city, country, shop/operator, trip, buddies, pictures, tanks, and user-defined fields.
- [x] Improve the photo gallery with resolved picture URLs, thumbnails, and lightbox display on dive details, dive-site, and gallery pages.
- [x] Show equipment items that are due soon for service.
- [x] Display diving certifications (from the Diving Log `Brevets` table) with front/back card scans.

## Partially implemented

- [ ] Extra profile charts.
  - Current support covers the depth profile and computes average depth plus ascent/descent rate series for the profile API, but the UI currently displays only the depth chart. Temperature, gas changes, tank pressure, and ppO2 charts are still pending.
- [ ] Extra dive statistics.
  - Current support includes totals, first/last dive, bottom time, duration, depth, air/water temperature, classification percentages, certifications, and depth distribution. A full Diving Log-equivalent statistics suite is still pending.
- [ ] Better RTF to HTML conversion.
  - A basic sanitized RTF/comment converter exists, but full Diving Log RTF fidelity is still pending.

## Still pending

- [ ] Show equipment items that are due for O2 clean.
- [ ] Protected access to buddy overviews/details.
