(() => {
  const DIVE_PATH = /^\/dives\/(\d+)\/?$/;

  const getDetailPage = () => document.querySelector('.dive-detail-page');

  const diveUrlFromLink = (link) => {
    if (!(link instanceof HTMLAnchorElement)) {
      return null;
    }

    let url;
    try {
      url = new URL(link.href, window.location.origin);
    } catch (error) {
      return null;
    }

    if (url.origin !== window.location.origin) {
      return null;
    }

    if (!DIVE_PATH.test(url.pathname)) {
      return null;
    }

    return url;
  };

  const isPlainClick = (event) =>
    event.button === 0
    && !event.metaKey
    && !event.ctrlKey
    && !event.shiftKey
    && !event.altKey;

  const updateActiveLogbookItem = (number) => {
    const list = document.querySelector('[data-logbook-list]');
    if (!list) {
      return;
    }

    list.querySelectorAll('.logbook-item').forEach((item) => {
      const isActive = item.getAttribute('data-dive-number') === String(number);
      item.classList.toggle('is-active', isActive);
      const link = item.querySelector('[data-logbook-link]');
      if (link) {
        if (isActive) {
          link.setAttribute('aria-current', 'page');
        } else {
          link.removeAttribute('aria-current');
        }
      }
    });
  };

  const swapContent = (fragment) => {
    const detailPage = getDetailPage();
    const newRoot = fragment.querySelector('[data-dive-fragment]');
    if (!detailPage || !newRoot) {
      return false;
    }

    const newHero = newRoot.querySelector('.dive-hero');
    const newContent = newRoot.querySelector('.dive-content-column');
    const currentHero = detailPage.querySelector('.dive-hero');
    const currentContent = detailPage.querySelector('.dive-content-column');

    if (!newHero || !newContent || !currentHero || !currentContent) {
      return false;
    }

    currentHero.replaceWith(newHero);
    currentContent.replaceWith(newContent);

    const number = newRoot.getAttribute('data-dive-number') || '';
    const title = newRoot.getAttribute('data-dive-title') || '';
    if (number !== '') {
      updateActiveLogbookItem(number);
    }
    if (title !== '') {
      document.title = title;
    }

    if (window.DivelogProfileChart && typeof window.DivelogProfileChart.init === 'function') {
      window.DivelogProfileChart.init(newContent);
    }

    return true;
  };

  const swapTo = (url, pushHistory) => {
    const scrollY = window.scrollY;

    return fetch(url.href, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin',
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error(`Partial request failed with status ${response.status}`);
        }
        return response.text();
      })
      .then((html) => {
        const template = document.createElement('template');
        template.innerHTML = html.trim();

        if (!swapContent(template.content)) {
          throw new Error('Fragment did not contain the expected structure');
        }

        if (pushHistory) {
          window.history.pushState({ diveUrl: url.href }, '', url.pathname);
        }

        window.scrollTo(0, scrollY);
      })
      .catch(() => {
        window.location.assign(url.href);
      });
  };

  document.addEventListener('click', (event) => {
    if (!isPlainClick(event)) {
      return;
    }

    if (!getDetailPage()) {
      return;
    }

    const link = event.target instanceof Element
      ? event.target.closest('[data-logbook-link], .dive-sequence-nav a[href]')
      : null;
    if (!link) {
      return;
    }

    const url = diveUrlFromLink(link);
    if (!url) {
      return;
    }

    event.preventDefault();
    swapTo(url, true);
  });

  window.addEventListener('popstate', () => {
    if (!getDetailPage()) {
      return;
    }

    const url = new URL(window.location.href);
    if (!DIVE_PATH.test(url.pathname)) {
      return;
    }

    swapTo(url, false);
  });
})();
