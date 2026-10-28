/**
 * lightbox.js
 * Abre qualquer imagem em tela cheia ao clicar.
 * Fecha ao clicar fora da imagem ou pressionar Esc.
 */
(function () {
  'use strict';

  /* --- Cria o overlay uma única vez --- */
  const overlay = document.createElement('div');
  overlay.id = 'lightbox-overlay';

  const img = document.createElement('img');
  img.alt = 'Visualização em tela cheia';
  overlay.appendChild(img);
  document.body.appendChild(overlay);

  /* --- Abre o lightbox --- */
  function openLightbox(src) {
    img.classList.remove('lightbox-landscape', 'lightbox-portrait');
    img.onload = function () {
      img.classList.add(
        img.naturalWidth >= img.naturalHeight
          ? 'lightbox-landscape'
          : 'lightbox-portrait'
      );
    };
    img.src = src;
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  /* --- Fecha o lightbox --- */
  function closeLightbox() {
    overlay.classList.remove('active');
    document.body.style.overflow = '';
    img.src = '';
    img.onload = null;
    img.classList.remove('lightbox-landscape', 'lightbox-portrait');
  }

  /* --- Fecha ao clicar no overlay (fora da imagem) --- */
  overlay.addEventListener('click', function (e) {
    if (e.target !== img) {
      closeLightbox();
    }
  });

  /* --- Fecha com Esc --- */
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && overlay.classList.contains('active')) {
      closeLightbox();
    }
  });

  /* --- Delega o clique para todas as imagens permitidas --- */
  const SELECTORS = [
    '.card img',
    '.forum img',
    '.lendas img',
    '.group img',
    '.about-page img',
  ].join(', ');

  document.addEventListener('click', function (e) {
    const el = e.target.closest(SELECTORS);
    if (!el) return;
    /* Ignora imagens marcadas com a classe no-lightbox */
    if (el.classList.contains('no-lightbox')) return;
    /* Ignora imagens sem src válido */
    if (!el.src || el.src === window.location.href) return;

    openLightbox(el.src);
  });
})();
