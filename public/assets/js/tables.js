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
})();
