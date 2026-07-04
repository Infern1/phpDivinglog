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

  const overviewControls = document.querySelector('.dive-overview-controls');
  if (overviewControls) {
    const sortSelect = document.getElementById('dives-sort');
    sortSelect?.addEventListener('change', () => {
      if (overviewControls instanceof HTMLFormElement) {
        overviewControls.requestSubmit();
      }
    });
  }

  const clickableRows = document.querySelectorAll('table[data-dives-table] tbody tr[data-href]');
  clickableRows.forEach((row) => {
    const href = row.getAttribute('data-href');
    if (!href) {
      return;
    }

    row.addEventListener('click', (event) => {
      const target = event.target;
      if (target instanceof Element && target.closest('a, button, input, select, textarea')) {
        return;
      }

      window.location.href = href;
    });

    row.addEventListener('keydown', (event) => {
      if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();
        window.location.href = href;
      }
    });
  });

  const logbookPane = document.querySelector('[data-logbook-pane]');
  if (logbookPane instanceof HTMLElement) {
    const storageKey = 'divelog:logbook-scroll-top';

    const restoreScroll = () => {
      const stored = window.sessionStorage.getItem(storageKey);
      if (!stored) {
        return;
      }

      const value = Number(stored);
      if (Number.isFinite(value) && value >= 0) {
        logbookPane.scrollTop = value;
      }
    };

    restoreScroll();

    logbookPane.addEventListener('scroll', () => {
      window.sessionStorage.setItem(storageKey, String(logbookPane.scrollTop));
    }, { passive: true });

    const links = logbookPane.querySelectorAll('[data-logbook-link]');
    links.forEach((link) => {
      link.addEventListener('click', () => {
        window.sessionStorage.setItem(storageKey, String(logbookPane.scrollTop));
      });
    });

    window.addEventListener('beforeunload', () => {
      window.sessionStorage.setItem(storageKey, String(logbookPane.scrollTop));
    });
  }
})();
