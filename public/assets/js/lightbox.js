(() => {
  const dialog = document.createElement('dialog');
  dialog.className = 'lightbox-dialog';
  dialog.innerHTML = '<form method="dialog"><button aria-label="Close">×</button></form><img alt="Preview">';
  document.body.appendChild(dialog);
  const image = dialog.querySelector('img');

  document.addEventListener('click', (event) => {
    const anchor = event.target.closest('a[data-lightbox]');
    if (!anchor) {
      return;
    }

    event.preventDefault();
    image.src = anchor.getAttribute('href') || '';
    image.alt = anchor.querySelector('img')?.alt || 'Preview';
    dialog.showModal();
  });
})();
