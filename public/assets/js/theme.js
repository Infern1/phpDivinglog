(() => {
  const THEME_STORAGE_KEY = 'divelog:theme';
  const PALETTE_STORAGE_KEY = 'divelog:palette';
  const PALETTES = ['reef', 'sunset', 'kelp', 'abyss'];
  const root = document.documentElement;

  const safeGetItem = (key) => {
    try {
      return window.localStorage.getItem(key);
    } catch {
      return null;
    }
  };

  const safeSetItem = (key, value) => {
    try {
      window.localStorage.setItem(key, value);
    } catch {
      // ignore storage failures
    }
  };

  const preferredTheme = () => {
    const stored = safeGetItem(THEME_STORAGE_KEY);
    if (stored === 'light' || stored === 'dark') {
      return stored;
    }

    if (typeof window.matchMedia === 'function') {
      try {
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
      } catch {
        return 'light';
      }
    }

    return 'light';
  };

  const preferredPalette = () => {
    const stored = safeGetItem(PALETTE_STORAGE_KEY);
    if (stored && PALETTES.includes(stored)) {
      return stored;
    }

    const fromDom = root.dataset.palette;
    if (fromDom && PALETTES.includes(fromDom)) {
      return fromDom;
    }

    return 'reef';
  };

  const applyTheme = (theme) => {
    const next = theme === 'dark' ? 'dark' : 'light';
    root.classList.remove('light', 'dark');
    root.classList.add(next);
    root.setAttribute('data-theme', next);
    document.body?.classList.remove('light', 'dark');
    document.body?.classList.add(next);
    document.body?.setAttribute('data-theme', next);
    return next;
  };

  const applyPalette = (palette) => {
    const next = PALETTES.includes(palette) ? palette : 'reef';
    root.setAttribute('data-palette', next);
    document.body?.setAttribute('data-palette', next);
    return next;
  };

  const updateThemeToggle = (theme) => {
    const toggle = document.querySelector('[data-theme-toggle]');
    if (!(toggle instanceof HTMLButtonElement)) {
      return;
    }

    const isDark = theme === 'dark';
    toggle.setAttribute('aria-pressed', isDark ? 'true' : 'false');
    const label = isDark ? 'Switch to light theme' : 'Switch to dark theme';
    toggle.setAttribute('aria-label', label);
    toggle.title = label;

    const icon = toggle.querySelector('[data-theme-icon]');
    if (icon) {
      icon.textContent = isDark ? 'light_mode' : 'dark_mode';
      icon.setAttribute('aria-hidden', 'true');
    }

    const text = toggle.querySelector('[data-theme-label]');
    if (text) {
      text.textContent = isDark ? 'Light' : 'Dark';
    }
  };

  const updatePaletteToggle = (palette) => {
    const select = document.querySelector('[data-palette-toggle]');
    if (!(select instanceof HTMLSelectElement)) {
      return;
    }

    select.value = PALETTES.includes(palette) ? palette : 'reef';
  };

  const highlightActiveNav = () => {
    const currentPath = window.location.pathname.replace(/\/+$/, '') || '/';
    const links = document.querySelectorAll('[data-nav-link]');
    links.forEach((link) => {
      if (!(link instanceof HTMLAnchorElement)) {
        return;
      }

      const href = link.getAttribute('href') || '';
      const normalizedHref = href.replace(/\/+$/, '') || '/';
      const isActive = normalizedHref === '/'
        ? currentPath === '/'
        : currentPath === normalizedHref || currentPath.startsWith(`${normalizedHref}/`);

      if (isActive) {
        link.setAttribute('aria-current', 'page');
        link.classList.add('is-active');
      } else {
        link.removeAttribute('aria-current');
        link.classList.remove('is-active');
      }
    });
  };

  const dispatchThemeChange = (theme, palette) => {
    window.dispatchEvent(new CustomEvent('themechange', { detail: { theme, palette } }));
  };

  const setTheme = (requestedTheme, shouldPersist) => {
    const appliedTheme = applyTheme(requestedTheme);
    const appliedPalette = applyPalette(preferredPalette());
    if (shouldPersist) {
      safeSetItem(THEME_STORAGE_KEY, appliedTheme);
    }
    updateThemeToggle(appliedTheme);
    updatePaletteToggle(appliedPalette);
    dispatchThemeChange(appliedTheme, appliedPalette);
  };

  const setPalette = (requestedPalette, shouldPersist) => {
    const appliedPalette = applyPalette(requestedPalette);
    const appliedTheme = root.classList.contains('dark') ? 'dark' : 'light';
    if (shouldPersist) {
      safeSetItem(PALETTE_STORAGE_KEY, appliedPalette);
    }
    updatePaletteToggle(appliedPalette);
    dispatchThemeChange(appliedTheme, appliedPalette);
  };

  document.addEventListener('DOMContentLoaded', () => {
    const initialTheme = root.dataset.theme || preferredTheme();
    const initialPalette = root.dataset.palette || preferredPalette();
    applyPalette(initialPalette);
    setTheme(initialTheme, false);
    highlightActiveNav();

    const toggle = document.querySelector('[data-theme-toggle]');
    if (toggle instanceof HTMLButtonElement) {
      toggle.addEventListener('click', () => {
        const current = root.classList.contains('dark') ? 'dark' : 'light';
        setTheme(current === 'dark' ? 'light' : 'dark', true);
      });
    }

    const paletteSelect = document.querySelector('[data-palette-toggle]');
    if (paletteSelect instanceof HTMLSelectElement) {
      paletteSelect.addEventListener('change', () => {
        setPalette(paletteSelect.value, true);
      });
    }

    if (typeof window.matchMedia === 'function') {
      try {
        const media = window.matchMedia('(prefers-color-scheme: dark)');
        media.addEventListener('change', (event) => {
          const stored = safeGetItem(THEME_STORAGE_KEY);
          if (stored === 'light' || stored === 'dark') {
            return;
          }
          setTheme(event.matches ? 'dark' : 'light', false);
        });
      } catch {
        // ignore media query listener failures
      }
    }
  });
})();
