<?php
require __DIR__ . '/../includes/layout.php';

page_head([
    'title' => 'Contact OnPoint Civils | Get a Quote',
    'description' => 'Contact OnPoint Civils at 1 Ambrose, 86 Chinamano Avenue. Call, WhatsApp or email for civil engineering and construction enquiries and quotes.',
    'path' => '/pages/contact.php',
    'nodes' => [seo_webpage_node(SITE_URL . '/pages/contact.php', 'Contact OnPoint Civils | Get a Quote', 'Contact OnPoint Civils at 1 Ambrose, 86 Chinamano Avenue. Call, WhatsApp or email for civil engineering and construction enquiries and quotes.', 'ContactPage'), seo_breadcrumb_node([['Home', '/'], ['Contact OnPoint Civils', '/pages/contact.php']], SITE_URL . '/pages/contact.php')],
]);
page_header('contact');
?>
  <section class="page-hero">
    <div class="container">
      <span class="label" style="color:var(--yellow)">Contact</span>
      <h1>Get in touch</h1>
      <p class="lead" style="color:#b7c1cd">Call, WhatsApp or fill in the form and we'll get back to you.</p>
    </div>
  </section>

  <section class="section alt">
    <div class="container contact-grid">
      <div class="info">
        <div class="info-item reveal">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 3h4l2 5-2.5 1.5a11 11 0 006 6L16 13l5 2v4a2 2 0 01-2 2A17 17 0 013 5a2 2 0 012-2z"/></svg>
          <div>
            <h4>Mobile</h4>
            <a href="tel:+263782381986">+263 78 238 1986</a>
            <a href="tel:+263788094724">+263 78 809 4724</a>
            <a href="tel:+263712734834">+263 71 273 4834</a>
          </div>
        </div>
        <div class="info-item reveal" style="--d:.1s">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M7 9h10M7 13h6"/></svg>
          <div>
            <h4>Landline</h4>
            <p><a href="tel:+263242328474" style="display:inline">242 328474</a> / <a href="tel:+263242328475" style="display:inline">5</a></p>
          </div>
        </div>
        <div class="info-item reveal" style="--d:.2s">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 20l1.3-4A8 8 0 1112 20a8 8 0 01-4-1z"/></svg>
          <div>
            <h4>WhatsApp</h4>
            <a href="https://wa.me/263782381986" target="_blank" rel="noopener">+263 78 238 1986</a>
            <a href="https://wa.me/263788094724" target="_blank" rel="noopener">+263 78 809 4724</a>
          </div>
        </div>
        <div class="info-item reveal">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg>
          <div>
            <h4>Email</h4>
            <a href="mailto:info@onpointcivils.co.zw">info@onpointcivils.co.zw</a>
            <a href="mailto:richard@onpointcivils.co.zw">richard@onpointcivils.co.zw</a>
            <a href="mailto:sales@onpointcivils.co.zw">sales@onpointcivils.co.zw</a>
          </div>
        </div>
        <div class="info-item reveal">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 21s7-6.2 7-11.5A7 7 0 005 9.5C5 14.800 12 21 12 21z"/><circle cx="12" cy="9.500" r="2.500"/></svg>
          <div>
            <h4>Address</h4>
            <p>1 Ambrose, 86 Chinamano Avenue</p>
          </div>
        </div>
      </div>

      <form class="form reveal" id="contactForm" data-wa="263782381986" style="--d:.1s">
        <h3>Send us a message</h3>
        <p>This opens WhatsApp with your message ready to send.</p>
        <div class="field-row">
          <div class="field"><label for="name">Name</label><input type="text" id="name" name="name" required></div>
          <div class="field"><label for="phone">Phone</label><input type="tel" id="phone" name="phone" required></div>
        </div>
        <div class="field">
          <label for="service">Service</label>
          <select id="service" name="service">
            <option>Roads</option>
            <option>Structures</option>
            <option>Water and Sewer</option>
            <option>Buildings</option>
            <option>Other</option>
          </select>
        </div>
        <div class="field"><label for="message">Message</label><textarea id="message" name="message" required></textarea></div>
        <button type="submit" class="btn btn-dark">Send on WhatsApp</button>
      </form>
    </div>
  </section>

  
<?php page_footer();
