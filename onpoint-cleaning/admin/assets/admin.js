(function () {
  'use strict';

  // Mobile sidebar
  document.querySelectorAll('[data-toggle-sidebar]').forEach(function (el) {
    el.addEventListener('click', function () { document.body.classList.toggle('nav-open'); });
  });

  // Confirm destructive forms
  document.querySelectorAll('form[data-confirm]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      if (!window.confirm(form.getAttribute('data-confirm'))) e.preventDefault();
    });
  });

  // Character counters: <input data-count="160">
  document.querySelectorAll('[data-count]').forEach(function (input) {
    var max = parseInt(input.getAttribute('data-count'), 10);
    var out = document.createElement('div');
    out.className = 'counter';
    input.insertAdjacentElement('afterend', out);
    function update() {
      out.textContent = input.value.length + ' / ' + max;
      out.classList.toggle('over', input.value.length > max);
    }
    input.addEventListener('input', update);
    update();
  });

  // Shrink big photos in the browser before upload (phone photos are often 3-10 MB).
  // Resolves with the original file if it is small, not shrinkable, or shrinking fails.
  window.OPImage = {
    shrink: function (file, maxSide, quality) {
      maxSide = maxSide || 2000;
      quality = quality || 0.85;
      return new Promise(function (resolve) {
        if (!file || !/^image\/(jpeg|png|webp)$/.test(file.type) || file.size < 700 * 1024 || !window.createImageBitmap) {
          return resolve(file);
        }
        createImageBitmap(file).then(function (bmp) {
          var scale = Math.min(1, maxSide / Math.max(bmp.width, bmp.height));
          var w = Math.round(bmp.width * scale), h = Math.round(bmp.height * scale);
          var canvas = document.createElement('canvas');
          canvas.width = w; canvas.height = h;
          var ctx = canvas.getContext('2d');
          var type = file.type === 'image/png' ? 'image/png' : 'image/jpeg';
          if (type === 'image/jpeg') { ctx.fillStyle = '#fff'; ctx.fillRect(0, 0, w, h); }
          ctx.drawImage(bmp, 0, 0, w, h);
          if (bmp.close) bmp.close();
          canvas.toBlob(function (blob) {
            if (!blob || blob.size >= file.size) return resolve(file);
            var base = file.name.replace(/\.[^.]+$/, '') || 'image';
            resolve(new File([blob], base + (type === 'image/png' ? '.png' : '.jpg'), { type: type }));
          }, type, quality);
        }).catch(function () { resolve(file); });
      });
    },
    // Upload one image to the admin upload endpoint; resolves with its public URL.
    upload: function (file, csrf) {
      return window.OPImage.shrink(file).then(function (f) {
        var fd = new FormData();
        fd.append('file', f, f.name);
        return fetch('upload.php', { method: 'POST', body: fd, headers: { 'X-CSRF-Token': csrf }, credentials: 'same-origin' })
          .then(function (r) {
            return r.json().catch(function () { return {}; }).then(function (j) {
              if (!r.ok) throw new Error(j.error || 'Upload failed (' + r.status + ')');
              return j.location;
            });
          });
      });
    }
  };

  // File inputs with data-preview: shrink the chosen photo, swap it into the input, show a preview.
  document.querySelectorAll('input[type=file][data-preview]').forEach(function (input) {
    input.addEventListener('change', function () {
      var target = document.querySelector(input.getAttribute('data-preview'));
      var file = input.files && input.files[0];
      if (!target || !file) return;
      window.OPImage.shrink(file).then(function (f) {
        if (f !== file) {
          try {
            var dt = new DataTransfer();
            dt.items.add(f);
            input.files = dt.files;
          } catch (e) { /* older browser: upload the original */ }
        }
        var img = target.querySelector('img') || target.appendChild(document.createElement('img'));
        img.src = URL.createObjectURL(input.files[0] || f);
        img.alt = '';
        target.hidden = false;
      });
    });
  });

  // "Add image" button next to the editor: opens the editor's image dialog (Upload tab included)
  document.querySelectorAll('[data-add-image]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      if (window.tinymce && tinymce.activeEditor) {
        tinymce.activeEditor.focus();
        tinymce.activeEditor.execCommand('mceImage');
      }
    });
  });

  // Slug auto-fill from title until the slug is edited by hand
  var title = document.querySelector('[data-slug-source]');
  var slug = document.querySelector('[data-slug-target]');
  if (title && slug) {
    var touched = slug.value !== '';
    slug.addEventListener('input', function () { touched = slug.value !== ''; });
    title.addEventListener('input', function () {
      if (touched) return;
      slug.placeholder = title.value.toLowerCase().normalize('NFKD').replace(/[̀-ͯ]/g, '')
        .replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '').slice(0, 180);
    });
  }

  // Warn before leaving a form with unsaved changes
  document.querySelectorAll('form[data-dirty-guard]').forEach(function (form) {
    var dirty = false;
    form.addEventListener('input', function () { dirty = true; });
    form.addEventListener('change', function () { dirty = true; });
    form.addEventListener('submit', function () {
      dirty = false;
      if (window.tinymce && tinymce.get) [].concat(tinymce.get() || []).forEach(function (ed) { ed.setDirty(false); });
    });
    window.addEventListener('beforeunload', function (e) {
      if (dirty || (window.tinymce && tinymce.activeEditor && tinymce.activeEditor.isDirty())) {
        e.preventDefault();
        e.returnValue = '';
      }
    });
  });
})();
