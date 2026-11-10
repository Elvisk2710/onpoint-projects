(function () {
  // Header background on scroll
  var header = document.querySelector('.site-header');
  function onScroll() { if (header) header.classList.toggle('scrolled', window.scrollY > 30); }
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  // Mobile menu
  var toggle = document.querySelector('.menu-toggle');
  if (toggle) {
    toggle.addEventListener('click', function () {
      var open = document.body.classList.toggle('menu-open');
      toggle.setAttribute('aria-expanded', open);
    });
    document.querySelectorAll('.nav-links a').forEach(function (a) {
      a.addEventListener('click', function () { document.body.classList.remove('menu-open'); });
    });
  }

  // Reveal on scroll
  var targets = document.querySelectorAll('.reveal, .reveal-img');
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); }
      });
    }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });
    targets.forEach(function (t) { io.observe(t); });
  } else {
    targets.forEach(function (t) { t.classList.add('in'); });
  }

  // Contact form: opens a WhatsApp chat with the enquiry filled in (no server)
  var form = document.getElementById('contactForm');
  if (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var d = new FormData(form);
      // Also save the enquiry to the admin inbox (best effort; WhatsApp opens regardless)
      try {
        fetch('/api/contact', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            name: d.get('name'), phone: d.get('phone'), subject: 'Website enquiry: ' + d.get('service'),
            message: d.get('message'), website: ''
          }),
          keepalive: true
        }).catch(function () {});
      } catch (err) { /* ignore */ }
      var text = 'Hello OnPoint Civils,\n\n' + d.get('message') +
        '\n\nName: ' + d.get('name') + '\nPhone: ' + d.get('phone') + '\nService: ' + d.get('service');
      window.open('https://wa.me/' + form.getAttribute('data-wa') + '?text=' + encodeURIComponent(text), '_blank', 'noopener');
    });
  }

  document.querySelectorAll('[data-year]').forEach(function (el) { el.textContent = new Date().getFullYear(); });
})();
