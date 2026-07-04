(() => {
  const tables = document.querySelectorAll('table[data-sortable]');
  tables.forEach((table) => {
    const headers = table.querySelectorAll('th');
    headers.forEach((header, index) => {
      header.tabIndex = 0;
      header.addEventListener('click', () => sortTable(table, index));
      header.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' || event.key === ' ') {
          event.preventDefault();
          sortTable(table, index);
        }
      });
    });
  });

  function sortTable(table, index) {
    const body = table.tBodies[0];
    const rows = Array.from(body.rows);
    const asc = table.dataset.sortAsc !== 'true';
    rows.sort((a, b) => {
      const av = a.cells[index]?.textContent?.trim() ?? '';
      const bv = b.cells[index]?.textContent?.trim() ?? '';
      return asc ? av.localeCompare(bv, undefined, { numeric: true }) : bv.localeCompare(av, undefined, { numeric: true });
    });
    rows.forEach((row) => body.appendChild(row));
    table.dataset.sortAsc = asc ? 'true' : 'false';
  }

  const diveTable = document.querySelector('[data-dives-table]');
  if (!diveTable) {
    return;
  }

  const body = diveTable.tBodies[0];
  const searchInput = document.getElementById('dives-search');
  const sortSelect = document.getElementById('dives-sort');

  function applyDiveFilters() {
    const rows = Array.from(body.querySelectorAll('tr'));
    const search = (searchInput?.value || '').trim().toLowerCase();
    const sort = sortSelect?.value || 'newest';

    rows.sort((a, b) => {
      const tsA = Number(a.dataset.ts || 0);
      const tsB = Number(b.dataset.ts || 0);
      const depthA = Number(a.dataset.depth || 0);
      const depthB = Number(b.dataset.depth || 0);
      const durationA = Number(a.dataset.duration || 0);
      const durationB = Number(b.dataset.duration || 0);

      if (sort === 'oldest') return tsA - tsB;
      if (sort === 'deepest') return depthB - depthA;
      if (sort === 'longest') return durationB - durationA;
      return tsB - tsA;
    });

    rows.forEach((row) => {
      const haystack = `${row.dataset.location || ''} ${row.dataset.number || ''}`.toLowerCase();
      const visible = search === '' || haystack.includes(search);
      row.style.display = visible ? '' : 'none';
      body.appendChild(row);
    });
  }

  searchInput?.addEventListener('input', applyDiveFilters);
  sortSelect?.addEventListener('change', applyDiveFilters);
  applyDiveFilters();
})();
