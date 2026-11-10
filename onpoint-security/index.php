<?php
require __DIR__ . '/includes/layout.php';

$services = [
    ['guard', 'Manned Security', 'Highly trained security guards providing visible presence and threat deterrence', ['24/7 Guard Services', 'Access Control', 'Patrol Services']],
    ['cash', 'Cash Management', 'Secure cash handling and transportation services', ['Cash in Transit', 'Secure Transport', 'Armored Vehicles']],
    ['search', 'Private Investigations', 'Discreet and professional investigative services', ['Background Checks', 'Surveillance', 'Corporate Investigations']],
    ['alarm', 'Alarm Systems', 'Advanced alarm systems with emergency response', ['Fire Alarms', 'Emergency Response', '24/7 Monitoring']],
    ['cctv', 'CCTV Security', 'Complete CCTV installation and monitoring solutions', ['HD Surveillance', 'Remote Monitoring', 'Recording Systems']],
    ['gate', 'Access Solutions', 'Electronic access control and perimeter security', ['Electric Gates', 'Fencing Systems', 'Access Control']],
];
$why = [
    ['team', 'Expert Security Team', 'Thoroughly checked, screened, and vetted security professionals'],
    ['medal', 'Proven Experience', 'Over a decade of security service with excellent reputation'],
    ['star', 'Professional Standards', 'Highest level of professionalism and ethical conduct'],
    ['layers', 'Customized Solutions', "Tailored security plans for each client's unique needs"],
    ['check', 'Quality Assurance', 'Unmatchable quality at competitive prices'],
    ['phone', '24/7 Support', 'Round-the-clock customer support and emergency response'],
];
$testimonials = [
    ['Retail Client', 'Major Retail Chain', 'Security Manager', "ShieldTrac's manned security services have significantly improved our store safety and reduced incidents by over 60%."],
    ['Corporate Client', 'Financial Institution', 'Operations Director', "Their cash management services are reliable and professional. We've trusted them with our cash operations for 5 years."],
    ['Industrial Client', 'Manufacturing Plant', 'Facility Manager', 'The CCTV and access control systems installed by ShieldTrac have transformed our site security.'],
    ['Commercial Client', 'Office Complex', 'Property Manager', 'Professional, reliable, and always responsive. ShieldTrac delivers exceptional security services.'],
];
$stats = [['10+', 'Years Experience'], ['100+', 'Local Businesses'], ['500+', 'Security Personnel'], ['24/7', 'Support Available']];
$values = ['Integrity', 'Professionalism', 'Customer Focus', 'Trust'];
$latest = latest_posts(3);

page_head([
    'title' => 'ShieldTrac Security Zimbabwe | Professional Security & Guard Services',
    'description' => site_profile()['home_description'],
    'path' => '/',
    'nodes' => seo_home_nodes(),
]);
page_header();
?>
<section class="hero">
  <div class="hero-grid" aria-hidden="true"></div>
  <div class="container hero-inner">
    <div class="hero-copy reveal">
      <span class="eyebrow">Zimbabwe-approved private security</span>
      <h1>Professional <span class="c-orange">security</span> solutions for your business.</h1>
      <p class="lead">ShieldTrac Security Services Limited is a Zimbabwe-approved private security company protecting businesses of all sizes with vetted security guards, cash management, alarm and CCTV systems, and 24/7 support.</p>
      <div class="actions">
        <a class="btn btn-primary" href="#contact">Get protected <?= icon('arrow') ?></a>
        <a class="btn btn-ghost-light" href="#services">Explore services</a>
      </div>
    </div>
    <aside class="hero-panel reveal" aria-label="Contact">
            <p class="panel-label">Call us, 24/7 support</p>
      <a class="panel-num" href="tel:+263788094724">+263 78 809 4724</a>
      <ul class="panel-list">
        <li><?= icon('guard') ?> 500+ security personnel</li>
        <li><?= icon('cctv') ?> CCTV &amp; alarm systems with 24/7 monitoring</li>
        <li><?= icon('pin') ?> Based in Harare, serving businesses throughout Zimbabwe</li>
      </ul>
    </aside>
  </div>
</section>

<section class="section" id="about">
  <div class="container about-grid">
    <div class="about-media reveal">
      <img src="/assets/images/security_guards.png" alt="ShieldTrac security officers on duty" loading="lazy" width="560" height="560">
      <div class="media-badge"><strong>10+</strong><span>years protecting<br>Zimbabwean business</span></div>
    </div>
    <div class="reveal">
      <span class="eyebrow">About ShieldTrac</span>
      <h2>Trusted security partner for over a decade.</h2>
      <p class="muted">ShieldTrac Security Services Limited is a Zimbabwe-approved private security company protecting businesses of all sizes across the private and public sectors, combining proven security technology with experienced guards throughout the country.</p>
      <p class="muted">For over a decade we have provided trained, thoroughly vetted security guards for all business sectors, with high quality at competitive prices.</p>
      <div class="mv">
        <div><h3>Mission</h3><p class="muted">Exceptional security services that exceed expectations, in a culture of trust, integrity and professionalism.</p></div>
        <div><h3>Vision</h3><p class="muted">To lead in innovative security solutions that keep clients and communities safe.</p></div>
      </div>
      <ul class="values"><?php foreach ($values as $v): ?><li><?= e($v) ?></li><?php endforeach; ?></ul>
    </div>
  </div>
</section>

<section class="section section-dark" id="services">
  <div class="container">
    <div class="section-head split">
      <div><span class="eyebrow">Services</span><h2>Our security services</h2></div>
      <p>Complete security solutions tailored to protect your people, property and assets.</p>
    </div>
    <div class="svc-grid">
      <?php foreach ($services as $i => [$icon, $title, $desc, $features]): ?>
        <article class="svc reveal">
          <div class="svc-top"><span class="svc-ico"><?= icon($icon) ?></span><span class="mono">0<?= $i + 1 ?></span></div>
          <h3><?= e($title) ?></h3>
          <p><?= e($desc) ?></p>
          <ul class="ticks"><?php foreach ($features as $f): ?><li><?= e($f) ?></li><?php endforeach; ?></ul>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section" id="why-us">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Why ShieldTrac</span>
      <h2>Why businesses choose ShieldTrac</h2>
    </div>
    <div class="why-grid">
      <?php foreach ($why as [$icon, $title, $desc]): ?>
        <div class="why reveal">
          <span class="why-ico"><?= icon($icon) ?></span>
          <div><h3><?= e($title) ?></h3><p class="muted"><?= e($desc) ?></p></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section bg-light" id="testimonials">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Client voices</span>
      <h2>What our clients say</h2>
    </div>
    <div class="quotes">
      <?php foreach ($testimonials as [$name, $company, $role, $quote]): ?>
        <figure class="quote reveal">
          <blockquote><?= e($quote) ?></blockquote>
          <figcaption><span class="avatar" aria-hidden="true"><?= e(substr($company, 0, 1)) ?></span><span><strong><?= e($company) ?></strong><span class="muted sm"><?= e($role) ?></span></span></figcaption>
        </figure>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php if ($latest): ?>
<section class="section" id="blog">
  <div class="container">
    <div class="section-head split">
      <div><span class="eyebrow">Security insights</span><h2>Tips &amp; advisories</h2></div>
      <a class="btn btn-outline" href="<?= e(rtrim(BLOG_PATH, '/')) ?>/">All articles <?= icon('arrow') ?></a>
    </div>
    <div class="cards cards-3">
      <?php foreach ($latest as $p): $href = post_path($p['slug']); ?>
        <article class="card advisory-card">
          <?php if ($p['cover_image']): ?><a href="<?= e($href) ?>" tabindex="-1" aria-hidden="true"><img src="<?= e(upload_url($p['cover_image'])) ?>" alt="" loading="lazy"></a><?php endif; ?>
          <div class="advisory-body">
            <span class="advisory-tag">SECURITY INSIGHT</span>
            <h3><a href="<?= e($href) ?>"><?= e($p['title']) ?></a></h3>
            <p class="muted"><?= e(post_summary($p)) ?></p>
            <p class="sm muted advisory-meta"><time datetime="<?= e(date('Y-m-d', strtotime($p['published_at']))) ?>"><?= e(format_post_date($p['published_at'])) ?></time></p>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="section section-dark contact" id="contact">
  <div class="container grid-2">
    <div>
      <span class="eyebrow">Get in touch</span>
      <h2>Contact ShieldTrac Security</h2>
      <p class="on-dark-p">Contact us for a free security assessment and a customised quote.</p>
      <ul class="contact-list">
        <li><span class="ci"><?= icon('phone') ?></span><div><strong>Phone</strong>
          <a href="tel:+263788094724">+263 78 809 4724</a> · <a href="tel:+263712734834">+263 71 273 4834</a><br>Landline 242328474/5</div></li>
        <li><span class="ci wa"><?= icon('chat') ?></span><div><strong>WhatsApp</strong>
          <a href="https://wa.me/263782381986" target="_blank" rel="noopener noreferrer">+263 78 238 1986</a> · <a href="https://wa.me/263775934109" target="_blank" rel="noopener noreferrer">+263 77 593 4109</a></div></li>
        <li><span class="ci"><?= icon('mail') ?></span><div><strong>Email</strong>
          <a href="mailto:info@shieldtrac.co.zw">info@shieldtrac.co.zw</a> · <a href="mailto:richard@shieldtrac.co.zw">richard@shieldtrac.co.zw</a><br><a href="mailto:shieldtrac@gmail.co.zw">shieldtrac@gmail.co.zw</a></div></li>
        <li><span class="ci"><?= icon('pin') ?></span><div><strong>Head office</strong>26 Connaught, Avondale, Harare</div></li>
      </ul>
    </div>
    <form class="contact-form" data-contact action="/api/contact" method="post" novalidate>
      <h3>Request a quote</h3>
      <div class="field"><label for="c-name">Full name</label><input id="c-name" name="name" type="text" autocomplete="name" required maxlength="120"></div>
      <div class="field-row">
        <div class="field"><label for="c-email">Email</label><input id="c-email" name="email" type="email" autocomplete="email" maxlength="190"></div>
        <div class="field"><label for="c-phone">Phone</label><input id="c-phone" name="phone" type="tel" autocomplete="tel" maxlength="40"></div>
      </div>
      <div class="field"><label for="c-message">What do you need protected?</label><textarea id="c-message" name="message" rows="5" required maxlength="5000"></textarea></div>
      <div class="hp" aria-hidden="true"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
      <button class="btn btn-primary btn-block" type="submit">Send request</button>
      <p class="form-status" role="status" aria-live="polite"></p>
    </form>
  </div>
</section>

<?php page_footer();
