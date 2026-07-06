(() => {
  const STORAGE_KEY = 'divelog:theme';
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
    const stored = safeGetItem(STORAGE_KEY);
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

  const updateToggle = (theme) => {
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

  const setTheme = (requestedTheme, shouldPersist) => {
    const applied = applyTheme(requestedTheme);
    if (shouldPersist) {
      safeSetItem(STORAGE_KEY, applied);
    }
    updateToggle(applied);
    window.dispatchEvent(new CustomEvent('themechange', { detail: { theme: applied } }));
  };

  document.addEventListener('DOMContentLoaded', () => {
    const initial = root.dataset.theme || preferredTheme();
    setTheme(initial, false);
    highlightActiveNav();

    const toggle = document.querySelector('[data-theme-toggle]');
    if (toggle instanceof HTMLButtonElement) {
      toggle.addEventListener('click', () => {
        const current = root.classList.contains('dark') ? 'dark' : 'light';
        setTheme(current === 'dark' ? 'light' : 'dark', true);
      });
    }

    if (typeof window.matchMedia === 'function') {
      try {
        const media = window.matchMedia('(prefers-color-scheme: dark)');
        media.addEventListener('change', (event) => {
          const stored = safeGetItem(STORAGE_KEY);
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
