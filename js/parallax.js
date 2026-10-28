(function () {
  'use strict';

  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  let frameRequested = false;

  function updateParallax() {
    frameRequested = false;

    if (reduceMotion.matches) {
      document.documentElement.style.setProperty('--parallax-offset', '0px');
      return;
    }

    const maxScroll = Math.max(0, document.documentElement.scrollHeight - window.innerHeight);
    const progress = maxScroll > 0 ? window.scrollY / maxScroll : 0.5;
    const offset = (0.5 - progress) * 56;

    document.documentElement.style.setProperty('--parallax-offset', `${offset.toFixed(1)}px`);
  }

  function requestUpdate() {
    if (frameRequested) return;
    frameRequested = true;
    window.requestAnimationFrame(updateParallax);
  }

  window.addEventListener('scroll', requestUpdate, { passive: true });
  window.addEventListener('resize', requestUpdate);
  reduceMotion.addEventListener?.('change', requestUpdate);
  updateParallax();
})();
