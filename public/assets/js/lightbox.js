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
    '<p class="lightbox-caption" data-lightbox-caption></p>',
  ].join('');
  document.body.appendChild(dialog);
  const image = dialog.querySelector('.lightbox-image');
  const counter = dialog.querySelector('[data-lightbox-counter]');
  const caption = dialog.querySelector('[data-lightbox-caption]');
  const previousButton = dialog.querySelector('[data-lightbox-prev]');
  const nextButton = dialog.querySelector('[data-lightbox-next]');

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
    if (!(image instanceof HTMLImageElement) || !(counter instanceof HTMLElement) || !(caption instanceof HTMLElement) || !(previousButton instanceof HTMLElement) || !(nextButton instanceof HTMLElement)) {
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
