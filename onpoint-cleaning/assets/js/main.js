(function () {
  'use strict';


  // Mobile menu
  var toggle = document.querySelector('.menu-toggle');
  var links = document.getElementById('nav-links');
  if (toggle && links) {
    toggle.addEventListener('click', function () {
      var open = links.classList.toggle('open');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      toggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
    });
    links.addEventListener('click', function (e) {
      if (e.target.closest('a')) {
        links.classList.remove('open');
        toggle.setAttribute('aria-expanded', 'false');
      }
    });
  }

  // Contact form -> /api/contact
  var form = document.querySelector('form[data-contact]');
  if (!form) return;
  var status = form.querySelector('.form-status');
  var button = form.querySelector('button[type=submit]');

  function setStatus(text, kind) {
    status.textContent = text;
    status.className = 'form-status' + (kind ? ' ' + kind : '');
  }
  function clearErrors() {
    form.querySelectorAll('.err').forEach(function (n) { n.remove(); });
  }

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    clearErrors();
    setStatus('', '');

    var data = {};
    new FormData(form).forEach(function (v, k) { data[k] = v; });

    if (!data.name || !data.message || (!data.email && !data.phone)) {
      setStatus('Please fill in your name, message and an email or phone number.', 'bad');
      return;
    }

    button.disabled = true;
    var original = button.textContent;
    button.textContent = 'Sending…';

    fetch(form.getAttribute('action'), {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify(data)
    })
      .then(function (r) { return r.json().catch(function () { return {}; }).then(function (j) { return { ok: r.ok, body: j }; }); })
      .then(function (res) {
        if (res.ok) {
          form.reset();
          setStatus('Thank you! We will contact you soon.', 'ok');
          return;
        }
        var fields = res.body.fields || {};
        Object.keys(fields).forEach(function (name) {
          var input = form.querySelector('[name="' + name + '"]');
          if (!input) return;
          var msg = document.createElement('div');
          msg.className = 'err';
          msg.textContent = fields[name];
          input.parentNode.appendChild(msg);
        });
        setStatus(res.body.error || 'Something went wrong. Please call us instead.', 'bad');
      })
      .catch(function () { setStatus('We couldn’t send your message. Please call or WhatsApp us.', 'bad'); })
      .then(function () { button.disabled = false; button.textContent = original; });
  });
})();
