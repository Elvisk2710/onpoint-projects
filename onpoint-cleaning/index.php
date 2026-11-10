<?php
require __DIR__ . '/includes/layout.php';

$cleaningServices = [
    ['broom', 'Daily Contract Cleaning', 'Regular maintenance cleaning for offices, homes, and commercial spaces', ['Daily maintenance', 'Office cleaning', 'Commercial spaces']],
    ['factory', 'Industrial Deep Cleaning', 'Comprehensive industrial cleaning for factories and manufacturing plants', ['Factory cleaning', 'Manufacturing plants', 'Heavy-duty cleaning']],
    ['sofa', 'Carpet & Upholstery Cleaning', 'Deep cleaning for carpets, rugs, and furniture upholstery', ['Carpet deep clean', 'Upholstery cleaning', 'Stain removal']],
    ['window', 'Window Cleaning', 'Professional window cleaning including high-rise rope access', ['High-rise windows', 'Rope access', 'Crystal clear results']],
    ['home', 'Pre & Post Occupancy Cleaning', 'Complete cleaning before moving in or after moving out', ['Move-in cleaning', 'Move-out cleaning', 'Property preparation']],
    ['sparkle', 'Floor Surface Treatment', 'Slip resistance treatment and hard surface floor maintenance', ['Slip resistance', 'Floor polishing', 'Surface protection']],
    ['flask', 'Cleaning Chemicals & Fumigation', 'Professional cleaning chemicals and integrated fumigation services', ['Eco-friendly chemicals', 'Integrated solutions', 'Quality products']],
    ['bin', 'Sanitary Disposal', 'Safe and hygienic waste disposal services', ['Waste management', 'Hygienic disposal', 'Compliance certified']],
    ['car', 'Basement & Parking Cleaning', 'Specialized cleaning for basements and parking facilities', ['Basement cleaning', 'Parking bays', 'Large area cleaning']],
    ['drop', 'Flood Extraction & Drying', 'Emergency flood water extraction and drying services', ['Emergency response', 'Water extraction', 'Drying equipment']],
];
$hygienicServices = [
    ['restroom', 'Restroom Sanitation', 'Complete restroom hygiene solutions and dispensers'],
    ['pump', 'Sanitation Consumables', 'Supply of hygiene products and consumables'],
    ['gear', 'Dispenser Services', 'Installation and maintenance of hygiene dispensers'],
    ['building', 'Workplace Hygiene', 'Complete workplace hygiene management'],
];
$pestControl = [
    ['bug', 'Cockroach Control', 'Complete elimination of cockroach infestations'],
    ['bug', 'Flying Insect Control', 'Control of flies, mosquitoes, and other flying insects'],
    ['bug', 'Ant & Slug Elimination', 'Targeted solutions for ants and slugs'],
    ['bug', 'Bee & Wasp Removal', 'Safe removal of bees, wasps, and wood bees'],
    ['bug', 'Termite Control', 'Comprehensive termite prevention and elimination'],
    ['bug', 'Rodent Control', 'Complete rodent management solutions'],
];
$whyChooseUs = [
    ['team', 'Highly Professional Staff', 'Our team consists of trained and experienced professionals'],
    ['shield', '100% Satisfaction Guarantee', 'We stand behind our work with a complete satisfaction guarantee'],
    ['badge', 'Quality Control System', 'Rigorous quality checks ensure consistent excellent service'],
    ['leaf', 'Eco-Friendly Solutions', 'Environmentally responsible pest control and cleaning methods'],
    ['check', 'Compliance Certified', 'Fully licensed and insured with all necessary certifications'],
    ['phone', '24/7 Emergency Service', 'Round-the-clock availability for urgent cleaning and pest control needs'],
];
$testimonials = [
    ['Mr. Innocent Murisa', 'Sterkinekor Movies', 'Operations Manager', 'Professional service with exceptional attention to detail across all our branches. Highly recommended!', '+263 772 429 750'],
    ['Mr. Pride', 'Simbisa Branch (Chicken Inn)', 'Operations Manager', 'Reliable and thorough cleaning services that maintain our high standards of hygiene.', '+263 77 227 1231'],
    ['Mr. Adrian', 'All the Way Restaurant', 'Operations Manager', 'Their fumigation services have completely eliminated our pest problems. Excellent work!', '+263 74 955 865'],
    ['Mrs. Kim', 'Café Nush', 'Operations Manager', 'The hygienic services have transformed our restroom facilities. Our customers have noticed the difference.', '+263 78 869 6249'],
];
$stats = [['50+', 'Professional Staff'], ['1000+', 'Satisfied Clients'], ['24/7', 'Emergency Service'], ['100%', 'Satisfaction Guarantee']];

$latest = latest_posts(3);

page_head([
    'title' => 'OnPoint Hygiene And Fumigation Zimbabwe | Professional Cleaning & Fumigation Services',
    'description' => site_profile()['home_description'],
    'path' => '/',
    'nodes' => seo_home_nodes(),
]);
page_header();
?>
<section class="hero">
  <div class="container hero-inner">
    <div class="hero-copy reveal">
      <span class="pill"><?= icon('sparkle') ?> Internationally recognised · Harare</span>
      <h1>Spotless spaces.<br><em>Pest-free</em> peace of mind.</h1>
      <p class="lead">Onpoint Hygiene &amp; Fumigation keeps offices, restaurants, factories and homes across Africa clean, hygienic and pest-free, with certified teams and eco-friendly methods.</p>
      <div class="actions">
        <a class="btn btn-primary" href="#contact">Book an appointment <?= icon('arrow') ?></a>
        <a class="btn btn-outline" href="#services">See our services</a>
      </div>
      <ul class="trust">
        <li><?= icon('check') ?> Licensed &amp; insured</li>
        <li><?= icon('leaf') ?> Eco-friendly products</li>
        <li><?= icon('phone') ?> 24/7 emergency line</li>
      </ul>
    </div>
    <div class="collage reveal" aria-hidden="true">
      <img class="c-main" src="/assets/images/cleaning_lady.png" alt="" width="460" height="520" fetchpriority="high">
      <img class="c-side" src="/assets/images/fumigation.png" alt="" width="240" height="240" loading="lazy">
      <div class="c-chip"><strong>100%</strong><span>satisfaction<br>guarantee</span></div>
    </div>
  </div>
</section>

<section class="stats" aria-label="Key figures">
  <div class="container">
    <dl class="stats-grid">
      <?php foreach ($stats as [$n, $t]): ?><div><dd><?= e($n) ?></dd><dt><?= e($t) ?></dt></div><?php endforeach; ?>
    </dl>
  </div>
</section>

<section class="section about" id="about">
  <div class="container about-inner reveal">
    <span class="eyebrow">About us · Consistency in excellence</span>
    <p class="about-statement">We're an internationally recognised cleaning and fumigation company delivering <em>authentic services, products and solutions</em> across Africa, so the places you work, eat and live stay healthy.</p>
  </div>
</section>

<section class="section bg-soft" id="services">
  <div class="container">
    <div class="section-head split">
      <div><span class="eyebrow">Cleaning services</span><h2>From daily upkeep to the deep, difficult jobs.</h2></div>
      <p class="muted">Ten specialised cleaning services.</p>
    </div>
    <div class="svc-grid">
      <?php foreach ($cleaningServices as $i => [$icon, $title, $desc, $features]): ?>
        <article class="svc reveal<?= $i < 2 ? ' svc-wide' : '' ?>">
          <span class="svc-ico"><?= icon($icon) ?></span>
          <h3><?= e($title) ?></h3>
          <p class="muted"><?= e($desc) ?></p>
          <ul class="chips"><?php foreach ($features as $f): ?><li><?= e($f) ?></li><?php endforeach; ?></ul>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section" id="hygiene">
  <div class="container hygiene-grid">
    <div class="hygiene-media reveal">
      <img src="/assets/images/office_cleaning.png" alt="Onpoint team cleaning an office" width="520" height="520" loading="lazy">
    </div>
    <div class="reveal">
      <span class="eyebrow">Hygienic solutions</span>
      <h2>Restrooms and workplaces your people feel good about.</h2>
      <p class="muted">Restroom sanitation, dispenser installation and servicing, and a steady supply of consumables, managed for you so your workforce stays healthy.</p>
      <ul class="hy-list">
        <?php foreach ($hygienicServices as [$icon, $title, $desc]): ?>
          <li><span class="hy-ico"><?= icon($icon) ?></span><div><h3><?= e($title) ?></h3><p class="muted"><?= e($desc) ?></p></div></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</section>

<section class="section pest" id="pest-control">
  <div class="container">
    <div class="section-head split">
      <div><span class="eyebrow">Pest control &amp; fumigation</span><h2>Safe for your family, pets and planet. Not for pests.</h2></div>
      <p>Fully licensed, insured and environmentally responsible: we protect the air you breathe and the water you drink.</p>
    </div>
    <div class="pest-grid">
      <?php foreach ($pestControl as [$icon, $title, $desc]): ?>
        <article class="pest-card reveal"><h3><?= e($title) ?></h3><p><?= e($desc) ?></p></article>
      <?php endforeach; ?>
    </div>

    <div class="pest-bottom">
      <ul class="why-list">
        <?php foreach ($whyChooseUs as [$icon, $title, $desc]): ?>
          <li class="reveal"><span class="why-icon"><?= icon($icon) ?></span><div><h4><?= e($title) ?></h4><p><?= e($desc) ?></p></div></li>
        <?php endforeach; ?>
      </ul>
      <div class="emergency reveal">
        <span class="em-label"><i class="live-dot" aria-hidden="true"></i> 24/7 emergency pest control</span>
        <p class="em-title">Dial us and consider it done.</p>
        <a class="em-num" href="tel:+263782381986">078 238 1986</a>
        <a class="em-num" href="tel:+263712734834">071 273 4834</a>
        <a class="btn btn-white btn-block" href="tel:+263782381986"><?= icon('phone') ?> Call now</a>
      </div>
    </div>
  </div>
</section>

<section class="section" id="testimonials">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Trusted by industry leaders</span>
      <h2>Kind words from our clients</h2>
    </div>
    <div class="quotes">
      <?php foreach ($testimonials as [$name, $company, $role, $quote, $phone]): ?>
        <figure class="quote reveal">
          <blockquote><?= e($quote) ?></blockquote>
          <figcaption>
            <span class="avatar" aria-hidden="true"><?= e(mb_substr($company, 0, 1)) ?></span>
            <span><strong><?= e($name) ?></strong><span class="muted sm"><?= e($role) ?>, <?= e($company) ?></span></span>
          </figcaption>
        </figure>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php if ($latest): ?>
<section class="section bg-soft" id="blog">
  <div class="container">
    <div class="section-head split">
      <div><span class="eyebrow">Hygiene tips</span><h2>Advice from our team</h2></div>
      <a class="btn btn-outline" href="<?= e(rtrim(BLOG_PATH, '/')) ?>/">All articles <?= icon('arrow') ?></a>
    </div>
    <div class="cards cards-3">
      <?php foreach ($latest as $p): ?>
        <article class="card post-card">
          <?php if ($p['cover_image']): ?>
            <a href="<?= e(post_path($p['slug'])) ?>" tabindex="-1" aria-hidden="true"><img src="<?= e(upload_url($p['cover_image'])) ?>" alt="" loading="lazy"></a>
          <?php endif; ?>
          <div class="post-card-body">
            <time class="muted sm" datetime="<?= e(date('Y-m-d', strtotime($p['published_at']))) ?>"><?= e(format_post_date($p['published_at'])) ?></time>
            <h3><a href="<?= e(post_path($p['slug'])) ?>"><?= e($p['title']) ?></a></h3>
            <p class="muted"><?= e(post_summary($p)) ?></p>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="section" id="contact">
  <div class="container">
    <div class="contact-wrap">
      <div class="contact-info">
        <span class="eyebrow">Get in touch</span>
        <h2>Let's make your space shine.</h2>
        <p>Free consultation and quote for homes, offices, restaurants and industrial sites.</p>
        <ul class="contact-list">
          <li><span class="ci"><?= icon('phone') ?></span><div><strong>Call us</strong>
            <a href="tel:+263782381986">+263 78 238 1986</a> · <a href="tel:+263712734834">+263 71 273 4834</a><br>Landline 242328474/5</div></li>
          <li><span class="ci"><?= icon('chat') ?></span><div><strong>WhatsApp</strong>
            <a href="https://wa.me/263782381986" target="_blank" rel="noopener noreferrer">+263 78 238 1986</a> · <a href="https://wa.me/263775934109" target="_blank" rel="noopener noreferrer">+263 77 593 4109</a></div></li>
          <li><span class="ci"><?= icon('mail') ?></span><div><strong>Email</strong>
            <a href="mailto:info@onpointhygienezw.co.zw">info@onpointhygienezw.co.zw</a><br>
            <a href="mailto:sales@onpointhygienezw.co.zw">sales@onpointhygienezw.co.zw</a><br>
            <a href="mailto:elroy@onpointhygienezw.co.zw">elroy@onpointhygienezw.co.zw</a><br><a href="mailto:richard@onpointhygienezw.co.zw">richard@onpointhygienezw.co.zw</a></div></li>
          <li><span class="ci"><?= icon('pin') ?></span><div><strong>Visit us</strong>26 Connaught Rd, Avondale, Harare</div></li>
        </ul>
      </div>
      <form class="contact-form" data-contact action="/api/contact" method="post" novalidate>
        <h3>Send us a message</h3>
        <div class="field"><label for="c-name">Your name</label><input id="c-name" name="name" type="text" autocomplete="name" required maxlength="120"></div>
        <div class="field-row">
          <div class="field"><label for="c-email">Email</label><input id="c-email" name="email" type="email" autocomplete="email" maxlength="190"></div>
          <div class="field"><label for="c-phone">Phone</label><input id="c-phone" name="phone" type="tel" autocomplete="tel" maxlength="40"></div>
        </div>
        <div class="field"><label for="c-message">How can we help?</label><textarea id="c-message" name="message" rows="4" required maxlength="5000"></textarea></div>
        <div class="hp" aria-hidden="true"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
        <button class="btn btn-primary btn-block" type="submit">Send message</button>
        <p class="form-status" role="status" aria-live="polite"></p>
      </form>
    </div>
  </div>
</section>

<?php page_footer();
