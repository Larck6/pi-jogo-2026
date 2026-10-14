/* =========================
   HORIZONTAL INFINITO
========================= */

const slides = document.querySelector('.slides');
const groups = document.querySelectorAll('.group');

const next = document.getElementById('next');
const prev = document.getElementById('prev');

const firstClone = groups[0].cloneNode(true);
slides.appendChild(firstClone);

let index = 0;
const size = 600;

function updateHorizontal() {
  slides.style.transition = "transform 0.5s ease";
  slides.style.transform = `translateX(-${(index + 1) * size}px)`;
}

/* posição inicial */
slides.style.transform = `translateX(-${size}px)`;

/* clone do último no início */
const lastClone = groups[groups.length - 1].cloneNode(true);
slides.insertBefore(lastClone, slides.firstChild);

index = 0;
slides.style.transform = `translateX(-${size}px)`;


next.addEventListener('click', () => {
  index++;
  updateHorizontal();
});

prev.addEventListener('click', () => {
  index--;
  updateHorizontal();
});


slides.addEventListener('transitionend', () => {

  if (index >= groups.length) {

    slides.style.transition = "none";
    index = 0;

    slides.style.transform =
      `translateX(-${size}px)`;
  }

  if (index < 0) {

    slides.style.transition = "none";
    index = groups.length - 1;

    slides.style.transform =
      `translateX(-${groups.length * size}px)`;
  }

});


/* =========================
   VERTICAL INFINITO
========================= */

const slideVertical =
  document.querySelector('.slides_vertical');

const groupsVertical =
  document.querySelectorAll('.group_vertical');

const firstVertical =
  groupsVertical[0].cloneNode(true);

slideVertical.appendChild(firstVertical);

const lastVertical =
  groupsVertical[groupsVertical.length - 1]
  .cloneNode(true);

slideVertical.insertBefore(
  lastVertical,
  slideVertical.firstChild
);

let indexVertical = 0;

const height = 150;

/* posição inicial */
slideVertical.style.transform =
  `translateY(-${height}px)`;


function updateVertical() {

  slideVertical.style.transition =
    "transform 0.5s ease";

  slideVertical.style.transform =
    `translateY(-${(indexVertical + 1) * height}px)`;
}


/* autoplay */
setInterval(() => {

  indexVertical++;
  updateVertical();

}, 2000);


/* reset invisível */
slideVertical.addEventListener(
  'transitionend',
  () => {

    if (indexVertical >= groupsVertical.length) {

      slideVertical.style.transition = "none";

      indexVertical = 0;

      slideVertical.style.transform =
        `translateY(-${height}px)`;
    }

  }
);