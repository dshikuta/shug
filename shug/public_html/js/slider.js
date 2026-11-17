/* ==========================================================================
   SHUGIA SAFARIS — Hero slider
   Autoplay every 6s · arrows · dots · keyboard · pause on hover.
   ========================================================================== */
(function () {
  'use strict';

  const slider = document.querySelector('.slider');
  if (!slider) return;

  const slides = slider.querySelectorAll('.slide');
  if (slides.length === 0) return;

  const dots   = document.querySelectorAll('.slider-dot');
  const prevBtn = document.querySelector('.slider-btn.prev');
  const nextBtn = document.querySelector('.slider-btn.next');

  let current = 0;
  let interval = null;

  function show(index) {
    if (index < 0) index = slides.length - 1;
    if (index >= slides.length) index = 0;
    slides.forEach((s, i) => s.classList.toggle('active', i === index));
    dots.forEach((d, i) => d.classList.toggle('active', i === index));
    current = index;
  }

  function next() { show(current + 1); }
  function prev() { show(current - 1); }

  function startAuto() {
    stopAuto();
    interval = setInterval(next, 6000);
  }

  function stopAuto() {
    if (interval) {
      clearInterval(interval);
      interval = null;
    }
  }

  if (prevBtn) prevBtn.addEventListener('click', () => { prev(); startAuto(); });
  if (nextBtn) nextBtn.addEventListener('click', () => { next(); startAuto(); });

  dots.forEach((dot, i) => {
    dot.addEventListener('click', () => {
      show(i);
      startAuto();
    });
  });

  slider.addEventListener('mouseenter', stopAuto);
  slider.addEventListener('mouseleave', startAuto);

  document.addEventListener('keydown', e => {
    if (e.key === 'ArrowLeft') prev();
    if (e.key === 'ArrowRight') next();
  });

  show(0);
  startAuto();
})();