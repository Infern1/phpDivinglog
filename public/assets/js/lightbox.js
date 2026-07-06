(() => {
  const dialog = document.createElement('dialog');
  dialog.className = 'lightbox-dialog';
  dialog.innerHTML = [
    '<div class="lightbox-toolbar">',
    '  <span class="lightbox-counter" data-lightbox-counter aria-live="polite"></span>',
    '  <form method="dialog">',
    '    <button class="lightbox-close" aria-label="Close">×</button>',
    '  </form>',
    '</div>',
    '<div class="lightbox-stage">',
    '  <button type="button" class="lightbox-nav lightbox-prev" aria-label="Previous image" data-lightbox-prev>‹</button>',
    '  <img class="lightbox-image" alt="Preview">',
    '  <button type="button" class="lightbox-nav lightbox-next" aria-label="Next image" data-lightbox-next>›</button>',
    '</div>',
    '<div class="lightbox-info" data-lightbox-info hidden>',
    '  <p class="lightbox-info-title" data-lightbox-info-title></p>',
    '  <p class="lightbox-info-meta" data-lightbox-info-meta></p>',
    '  <a class="lightbox-info-link" data-lightbox-info-link href="#">view dive</a>',
    '</div>',
    '<p class="lightbox-caption" data-lightbox-caption></p>',
  ].join('');
  document.body.appendChild(dialog);
  const image = dialog.querySelector('.lightbox-image');
  const counter = dialog.querySelector('[data-lightbox-counter]');
  const caption = dialog.querySelector('[data-lightbox-caption]');
  const previousButton = dialog.querySelector('[data-lightbox-prev]');
  const nextButton = dialog.querySelector('[data-lightbox-next]');
  const infoPanel = dialog.querySelector('[data-lightbox-info]');
  const infoTitle = dialog.querySelector('[data-lightbox-info-title]');
  const infoMeta = dialog.querySelector('[data-lightbox-info-meta]');
  const infoLink = dialog.querySelector('[data-lightbox-info-link]');

  const allTriggers = () => Array.from(document.querySelectorAll('a[data-lightbox]'));

  const getGroupName = (anchor) => {
    if (!(anchor instanceof Element)) {
      return '';
    }

    if (anchor.hasAttribute('data-lightbox-group')) {
      return anchor.getAttribute('data-lightbox-group') || '';
    }

    const groupRoot = anchor.closest('[data-lightbox-group]');
    if (!groupRoot) {
      return '';
    }

    return groupRoot.getAttribute('data-lightbox-group') || '';
  };

  const resolveGroup = (anchor) => {
    const explicitGroup = getGroupName(anchor);
    if (explicitGroup) {
      return allTriggers().filter((trigger) => getGroupName(trigger) === explicitGroup);
    }

    const gallery = anchor.closest('ul.gallery-grid');
    if (gallery) {
      return Array.from(gallery.querySelectorAll('a[data-lightbox]'));
    }

    return [anchor];
  };

  let currentGroup = [];
  let currentIndex = 0;

  const renderCurrentImage = () => {
    if (!(image instanceof HTMLImageElement) || !(counter instanceof HTMLElement) || !(caption instanceof HTMLElement) || !(previousButton instanceof HTMLElement) || !(nextButton instanceof HTMLElement) || !(infoPanel instanceof HTMLElement) || !(infoTitle instanceof HTMLElement) || !(infoMeta instanceof HTMLElement) || !(infoLink instanceof HTMLAnchorElement)) {
      return;
    }

    const currentAnchor = currentGroup[currentIndex];
    if (!currentAnchor) {
      image.src = '';
      image.alt = 'Preview';
      caption.textContent = '';
      caption.hidden = true;
      counter.textContent = '';
      counter.hidden = true;
      previousButton.hidden = true;
      nextButton.hidden = true;
      infoPanel.hidden = true;
      infoTitle.textContent = '';
      infoMeta.textContent = '';
      infoLink.textContent = 'view dive';
      infoLink.setAttribute('href', '#');
      return;
    }

    image.src = currentAnchor.getAttribute('href') || '';

    const alt = currentAnchor.querySelector('img')?.getAttribute('alt') || 'Preview';
    image.alt = alt;
    caption.textContent = alt === 'Preview' ? '' : alt;
    caption.hidden = caption.textContent === '';

    const hasNavigation = currentGroup.length > 1;
    previousButton.hidden = !hasNavigation;
    nextButton.hidden = !hasNavigation;
    counter.hidden = !hasNavigation;
    counter.textContent = hasNavigation ? `${currentIndex + 1} / ${currentGroup.length}` : '';

    const diveNumber = (currentAnchor.getAttribute('data-dive-number') || '').trim();
    if (diveNumber === '') {
      infoPanel.hidden = true;
      infoTitle.textContent = '';
      infoMeta.textContent = '';
      infoLink.textContent = 'view dive';
      infoLink.setAttribute('href', '#');
      return;
    }

    const diver = (currentAnchor.getAttribute('data-diver') || '').trim();
    infoTitle.textContent = diver !== '' ? `Dive ${diveNumber} by ${diver}` : `Dive ${diveNumber}`;

    const metaParts = [
      (currentAnchor.getAttribute('data-location') || '').trim(),
      (currentAnchor.getAttribute('data-site') || '').trim(),
      (currentAnchor.getAttribute('data-when') || '').trim(),
    ].filter((value) => value !== '');

    infoMeta.textContent = metaParts.join(' | ');
    const diveUrl = (currentAnchor.getAttribute('data-dive-url') || '').trim();
    if (diveUrl !== '') {
      infoLink.setAttribute('href', diveUrl);
      infoLink.hidden = false;
    } else {
      infoLink.hidden = true;
      infoLink.setAttribute('href', '#');
    }

    infoLink.textContent = 'view dive';
    infoPanel.hidden = false;
  };

  const openLightbox = (anchor) => {
    currentGroup = resolveGroup(anchor);
    currentIndex = currentGroup.indexOf(anchor);

    if (currentIndex < 0) {
      currentGroup = [anchor];
      currentIndex = 0;
    }

    renderCurrentImage();

    if (!dialog.open) {
      dialog.showModal();
    }
  };

  const showNext = () => {
    if (currentGroup.length <= 1) {
      return;
    }

    currentIndex = (currentIndex + 1) % currentGroup.length;
    renderCurrentImage();
  };

  const showPrevious = () => {
    if (currentGroup.length <= 1) {
      return;
    }

    currentIndex = (currentIndex - 1 + currentGroup.length) % currentGroup.length;
    renderCurrentImage();
  };

  previousButton?.addEventListener('click', (event) => {
    event.preventDefault();
    showPrevious();
  });

  nextButton?.addEventListener('click', (event) => {
    event.preventDefault();
    showNext();
  });

  dialog.addEventListener('keydown', (event) => {
    if (!dialog.open) {
      return;
    }

    if (event.key === 'ArrowRight') {
      event.preventDefault();
      showNext();
    }

    if (event.key === 'ArrowLeft') {
      event.preventDefault();
      showPrevious();
    }
  });

  document.addEventListener('click', (event) => {
    const anchor = event.target.closest('a[data-lightbox]');
    if (!anchor) {
      return;
    }

    event.preventDefault();
    openLightbox(anchor);
  });
})();
