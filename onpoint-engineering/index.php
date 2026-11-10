<?php
require __DIR__ . '/includes/layout.php';
$faqs = [
    ['What kind of projects do you take on?', 'Civil engineering and construction work, including roads, structures, water and sewer infrastructure, and buildings. If you\'re not sure whether your project fits, get in touch and we\'ll talk it through.'],
    ['How do I get a quote?', 'Contact us by phone, WhatsApp or the form below with a short description of the work and where it is. We\'ll arrange to discuss the details and, where needed, visit the site before preparing a quote.'],
    ['Do you handle both design and construction?', 'Yes. We can take a project from design through to completion, so you deal with one team throughout.'],
    ['How do you manage safety on site?', 'Risks are identified and planned for before work starts, everyone on site wears protective equipment and follows clear site rules, and work is inspected as it progresses.'],
    ['What is the best way to reach you?', 'Phone or WhatsApp is quickest. All our numbers are listed in the contact section below.'],
];

page_head([
    'title' => 'OnPoint Civils | Civil Engineering and Construction',
    'description' => site_profile()['home_description'],
    'path' => '/',
    'nodes' => array_merge(seo_home_nodes(), [['@type' => 'FAQPage', '@id' => SITE_URL . '/#faq', 'mainEntity' => array_map(static fn (array $f): array => ['@type' => 'Question', 'name' => $f[0], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f[1]]], $faqs)]]),
]);
page_header('home');
?>
  <section class="hero">
    <div class="container hero-inner">
      <div class="hero-copy">
        <span class="label">Civil Engineering and Construction</span>
        <h1>Built safely. <span class="hl">Built right.</span></h1>
        <p class="lead">Civil engineering and construction work, planned carefully and carried out with safety as the first priority.</p>
        <div class="hero-actions">
          <a href="#contact" class="btn">Request a quote</a>
          <a href="https://wa.me/263782381986" target="_blank" rel="noopener" class="btn btn-outline">WhatsApp us</a>
        </div>
        <div class="hero-points">
          <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12l4 4 10-10"/></svg>Civil engineering</span>
          <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12l4 4 10-10"/></svg>Construction</span>
          <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12l4 4 10-10"/></svg>Safety first</span>
        </div>
      </div>
      <div class="hero-photo">
        <img src="assets/images/team-cutout.png" alt="Two OnPoint Civils engineers in hard hats and high-visibility vests">
      </div>
    </div>
  </section>

  <!-- About -->
  <section class="section" id="about">
    <div class="container about-home">
      <div class="reveal">
        <span class="label">About us</span>
        <h2>Civil engineering and construction, done properly</h2>
      </div>
      <div class="reveal" style="--d:.1s">
        <p class="lead" style="max-width:none">OnPoint Civils takes on civil engineering and construction projects from design through to completion. We work closely with our clients so that decisions are made quickly and everyone knows where the project stands.</p>
        <p style="margin-top:16px;color:var(--muted)">Whatever the size of the job, we aim to plan it properly, price it fairly and build it well, without cutting corners on safety.</p>
        <div class="about-values">
          <div><strong>Safety</strong><span>No job is so urgent that it can't be done safely.</span></div>
          <div><strong>Quality</strong><span>Work is checked as it progresses.</span></div>
          <div><strong>Honesty</strong><span>Clear quotes and regular updates.</span></div>
        </div>
        <a href="pages/about.php" class="link-arrow">More about us <span>→</span></a>
      </div>
    </div>
  </section>


  <!-- Safety -->
  <section class="section alt">
    <div class="container">
      <div class="section-head reveal">
        <span class="label">Safety</span>
        <h2>Safety comes first on every job</h2>
        <p class="lead">A safe site is a well-run site. Safety is part of how we plan and carry out every job.</p>
      </div>
      <div class="safety-grid">
        <div class="card reveal">
          <div class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6l8-3z"/><path d="M8.5 12l2.5 2.5 4.5-5"/></svg></div>
          <h3>Planned before we start</h3>
          <p>Risks on site are identified and planned for before work begins.</p>
        </div>
        <div class="card reveal" style="--d:.1s">
          <div class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 14a8 8 0 0116 0v2H4z"/><path d="M12 6v4M2 18h20"/></svg></div>
          <h3>Protected on site</h3>
          <p>Protective equipment and clear site rules for everyone working on the job.</p>
        </div>
        <div class="card reveal" style="--d:.2s">
          <div class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h8M8 12h8M8 16h5"/></svg></div>
          <h3>Checked as we go</h3>
          <p>Work is inspected as it progresses so problems are caught early.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Services -->
  <section class="section">
    <div class="container">
      <div class="section-head reveal">
        <span class="label">Services</span>
        <h2>What we do</h2>
        <p class="lead">The main areas of work we take on.</p>
      </div>
      <div class="services">
        <div class="service reveal">
          <svg class="icon" viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 30L14 6M36 30L26 6M20 8v4M20 16v4M20 24v6"/></svg>
          <h3>Roads</h3>
          <p>Road construction and rehabilitation, earthworks, surfacing and drainage.</p>
        </div>
        <div class="service reveal" style="--d:.1s">
          <svg class="icon" viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2 28h36M6 28V18M34 28V18M2 18q18-14 36 0M14 28V13M26 28V13"/></svg>
          <h3>Structures</h3>
          <p>Bridges, culverts, retaining walls and reinforced concrete works.</p>
        </div>
        <div class="service reveal" style="--d:.2s">
          <svg class="icon" viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 4c7 9 11 15 11 21a11 11 0 01-22 0c0-6 4-12 11-21z"/></svg>
          <h3>Water and Sewer</h3>
          <p>Water reticulation, sewer lines, stormwater and related civil works.</p>
        </div>
        <div class="service reveal" style="--d:.3s">
          <svg class="icon" viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 36V12l12-6v30M16 36V16h20v20M2 36h36M21 21h4M29 21h4M21 28h4M29 28h4"/></svg>
          <h3>Buildings</h3>
          <p>Commercial, industrial and residential construction, including site works.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Fleet -->
  <section class="section dark" style="overflow:hidden">
    <div class="container split">
      <div class="reveal">
        <span class="label">Plant and Equipment</span>
        <h2>Plant and equipment</h2>
        <p class="lead" style="margin-top:16px">Well-maintained machinery in the hands of trained operators keeps work moving and sites safer.</p>
        <ul class="ticks">
          <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12l4 4 10-10"/></svg>Machines checked before use</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12l4 4 10-10"/></svg>Regular servicing</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12l4 4 10-10"/></svg>Trained operators</li>
        </ul>
        <a href="#contact" class="btn" style="margin-top:34px">Talk to us</a>
      </div>
      <img src="assets/images/excavator-tipper.png" alt="Excavator and tipper truck" class="fleet-img reveal-img" loading="lazy">
    </div>
  </section>

  <!-- Process -->
  <section class="section alt">
    <div class="container">
      <div class="section-head reveal">
        <span class="label">How we work</span>
        <h2>From first call to handover</h2>
      </div>
      <div class="steps">
        <div class="step reveal"><h3>Site visit</h3><p>We meet you, look at the site and agree on what needs to be done.</p></div>
        <div class="step reveal" style="--d:.1s"><h3>Design and pricing</h3><p>You receive the design, a programme and a quote.</p></div>
        <div class="step reveal" style="--d:.2s"><h3>Construction</h3><p>The work is carried out and you are kept updated on progress.</p></div>
        <div class="step reveal" style="--d:.3s"><h3>Handover</h3><p>Final inspection and handover of the completed work.</p></div>
      </div>
    </div>
  </section>

  <!-- FAQ -->
  <section class="section">
    <div class="container faq-wrap">
      <div class="reveal">
        <span class="label">Questions</span>
        <h2>Frequently asked questions</h2>
        <p class="lead" style="margin-top:16px">Can't find what you're looking for? Call or WhatsApp us.</p>
      </div>
      <div class="faq reveal" style="--d:.1s">
        <?php foreach ($faqs as [$q, $a]): ?>
        <details>
          <summary><?= e($q) ?></summary>
          <p><?= e($a) ?></p>
        </details>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Contact -->
  <section class="section alt" id="contact">
    <div class="container">
      <div class="section-head reveal">
        <span class="label">Contact us</span>
        <h2>Let's talk about your project</h2>
        <p class="lead">Call, WhatsApp or send us a message and we'll get back to you.</p>
      </div>
      <div class="contact-grid">
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
    </div>
  </section>

  
<?php page_footer();
