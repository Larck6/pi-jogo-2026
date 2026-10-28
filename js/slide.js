/* =========================
   HORIZONTAL INFINITO
========================= */

const slidesEl   = document.querySelector('.slides');
const groupsEl   = document.querySelectorAll('.group');
const nextBtn    = document.getElementById('next');
const prevBtn    = document.getElementById('prev');
const sliderBox  = document.querySelector('.slider');
const sliderArea = document.querySelector('.slider-container');

if (slidesEl && groupsEl.length && nextBtn && prevBtn && sliderBox) {

  /* Lê a largura real do slider (responsivo) */
  function getSize() {
    return sliderBox.offsetWidth;
  }

  /* Clona primeiro e último grupo para o loop infinito */
  const firstClone = groupsEl[0].cloneNode(true);
  slidesEl.appendChild(firstClone);

  const lastClone = groupsEl[groupsEl.length - 1].cloneNode(true);
  slidesEl.insertBefore(lastClone, slidesEl.firstChild);

  let index = 0;
  let isAnimating = false;
  let isPaused = false;
  const autoplayDelay = 4500;

  /* Posição inicial: aponta para o primeiro grupo real (depois do clone do último) */
  slidesEl.style.transition = 'none';
  slidesEl.style.transform  = `translateX(-${getSize()}px)`;

  function goTo(newIndex) {
    if (isAnimating) return;
    isAnimating = true;
    index = newIndex;
    slidesEl.style.transition = 'transform 0.85s cubic-bezier(0.16, 1, 0.3, 1)';
    slidesEl.style.transform  = `translateX(-${(index + 1) * getSize()}px)`;
  }

  nextBtn.addEventListener('click', () => goTo(index + 1));
  prevBtn.addEventListener('click', () => goTo(index - 1));

  /* Avança sozinho e pausa enquanto o usuário interage com o carrossel. */
  const autoplay = window.setInterval(() => {
    const lightboxOpen = document.getElementById('lightbox-overlay')?.classList.contains('active');
    if (!isPaused && !isAnimating && !document.hidden && !lightboxOpen) {
      goTo(index + 1);
    }
  }, autoplayDelay);

  const pauseArea = sliderArea || sliderBox;
  pauseArea.addEventListener('mouseenter', () => { isPaused = true; });
  pauseArea.addEventListener('mouseleave', () => { isPaused = false; });
  pauseArea.addEventListener('focusin', () => { isPaused = true; });
  pauseArea.addEventListener('focusout', () => { isPaused = false; });

  /* Evita manter o temporizador ativo caso o carrossel seja removido da página. */
  window.addEventListener('pagehide', () => window.clearInterval(autoplay), { once: true });

  slidesEl.addEventListener('transitionend', () => {
    isAnimating = false;

    /* Chegou no clone do primeiro → salta para o real sem animação */
    if (index >= groupsEl.length) {
      index = 0;
      slidesEl.style.transition = 'none';
      slidesEl.style.transform  = `translateX(-${getSize()}px)`;
    }

    /* Chegou no clone do último → salta para o real sem animação */
    if (index < 0) {
      index = groupsEl.length - 1;
      slidesEl.style.transition = 'none';
      slidesEl.style.transform  = `translateX(-${(index + 1) * getSize()}px)`;
    }
  });

  /* Recalcula posição se a janela for redimensionada */
  window.addEventListener('resize', () => {
    slidesEl.style.transition = 'none';
    slidesEl.style.transform  = `translateX(-${(index + 1) * getSize()}px)`;
  });
}


/* =========================
   VERTICAL INFINITO
========================= */

const slideVertical  = document.querySelector('.slides_vertical');
const groupsVertical = document.querySelectorAll('.group_vertical');

if (slideVertical && groupsVertical.length) {

  const firstVertical = groupsVertical[0].cloneNode(true);
  slideVertical.appendChild(firstVertical);

  const lastVertical = groupsVertical[groupsVertical.length - 1].cloneNode(true);
  slideVertical.insertBefore(lastVertical, slideVertical.firstChild);

  let indexVertical = 0;

  function getHeight() {
    return groupsVertical[0].offsetHeight || 220;
  }

  slideVertical.style.transition = 'none';
  slideVertical.style.transform  = `translateY(-${getHeight()}px)`;

  function updateVertical() {
    slideVertical.style.transition = 'transform 0.75s cubic-bezier(0.22, 1, 0.36, 1)';
    slideVertical.style.transform  = `translateY(-${(indexVertical + 1) * getHeight()}px)`;
  }

  setInterval(() => {
    indexVertical++;
    updateVertical();
  }, 2500);

  slideVertical.addEventListener('transitionend', () => {
    if (indexVertical >= groupsVertical.length) {
      indexVertical = 0;
      slideVertical.style.transition = 'none';
      slideVertical.style.transform  = `translateY(-${getHeight()}px)`;
    }
    if (indexVertical < 0) {
      indexVertical = groupsVertical.length - 1;
      slideVertical.style.transition = 'none';
      slideVertical.style.transform  = `translateY(-${(indexVertical + 1) * getHeight()}px)`;
    }
  });
}
