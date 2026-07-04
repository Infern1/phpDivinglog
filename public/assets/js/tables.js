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
    const scrollKey = 'divelog:logbook-scroll-top';
    const selectedKey = 'divelog:logbook-selected-dive';

    const persistState = (selectedDive) => {
      window.sessionStorage.setItem(scrollKey, String(logbookPane.scrollTop));
      if (selectedDive) {
        window.sessionStorage.setItem(selectedKey, selectedDive);
      }
    };

    const restoreScroll = () => {
      const stored = window.sessionStorage.getItem(scrollKey);
      if (!stored) {
        return false;
      }

      const value = Number(stored);
      if (!Number.isFinite(value) || value < 0) {
        return false;
      }

      logbookPane.scrollTop = value;
      return true;
    };

    const activeItem = logbookPane.querySelector('.logbook-item.is-active');
    if (activeItem instanceof HTMLElement) {
      const activeDive = activeItem.getAttribute('data-dive-number') || undefined;
      activeItem.scrollIntoView({ block: 'center' });
      requestAnimationFrame(() => {
        persistState(activeDive);
      });
      window.setTimeout(() => {
        persistState(activeDive);
      }, 120);
    } else {
      restoreScroll();
      requestAnimationFrame(() => {
        restoreScroll();
      });
    }

    logbookPane.addEventListener('scroll', () => {
      persistState();
    }, { passive: true });

    const links = logbookPane.querySelectorAll('[data-logbook-link]');
    links.forEach((link) => {
      link.addEventListener('click', () => {
        const selectedDive = link.closest('[data-dive-number]')?.getAttribute('data-dive-number') || undefined;
        persistState(selectedDive);
      });
    });

    window.addEventListener('beforeunload', () => {
      persistState();
    });

    window.addEventListener('pagehide', () => {
      persistState();
    });
  }
})();
