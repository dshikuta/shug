(function () {
  'use strict';
  const form = document.getElementById('reviewFormEl');
  if (!form) return;

  function csrfHeaders() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? { 'X-CSRF-Token': meta.getAttribute('content') } : {};
  }

  const stars       = form.querySelectorAll('.star-rating__star');
  const ratingInput = document.getElementById('revRating');
  const feedback    = form.querySelector('.form-feedback');
  const submitBtn   = document.getElementById('revSubmit');
  const textArea    = document.getElementById('revText');
  const counter     = document.getElementById('revCount');

  stars.forEach(star => {
    star.addEventListener('click', () => {
      const v = parseInt(star.dataset.value, 10);
      ratingInput.value = v;
      stars.forEach(s => s.classList.toggle('is-filled', parseInt(s.dataset.value, 10) <= v));
    });
    star.addEventListener('mouseenter', () => {
      const v = parseInt(star.dataset.value, 10);
      stars.forEach(s => s.classList.toggle('is-hover', parseInt(s.dataset.value, 10) <= v));
    });
  });
  form.querySelector('.star-rating').addEventListener('mouseleave', () => {
    stars.forEach(s => s.classList.remove('is-hover'));
  });

  textArea.addEventListener('input', () => { counter.textContent = textArea.value.length; });

  form.addEventListener('submit', async e => {
    e.preventDefault();
    if (feedback) feedback.className = 'form-feedback';
    if ((parseInt(ratingInput.value, 10) || 0) < 1) {
      showError('Please choose a star rating.'); return;
    }
    submitBtn.disabled = true;
    submitBtn.querySelector('span').textContent = 'Posting…';
    try {
      const payload = {
        name:     form.name.value.trim(),
        email:    form.email.value.trim(),
        location: form.location.value.trim(),
        tour:     form.tour.value.trim(),
        rating:   parseInt(ratingInput.value, 10),
        text:     form.text.value.trim(),
        website:  form.website ? form.website.value : ''
      };
      const res = await fetch('/api/review.php', {
        method: 'POST',
        headers: Object.assign(
          { 'Content-Type': 'application/json' },
          csrfHeaders()
        ),
        body: JSON.stringify(payload)
      });
      const data = await res.json();
      if (res.ok && data.ok) {
        feedback.className = 'form-feedback success visible';
        feedback.textContent = data.message;
        form.reset();
        ratingInput.value = 0;
        stars.forEach(s => s.classList.remove('is-filled'));
        counter.textContent = '0';
      } else {
        showError(data.error || 'Could not post review.');
      }
    } catch {
      showError('Network error. Please try again.');
    } finally {
      submitBtn.disabled = false;
      submitBtn.querySelector('span').textContent = 'Post Review';
    }
  });

  function showError(msg) {
    feedback.className = 'form-feedback error visible';
    feedback.textContent = msg;
    feedback.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }
})();