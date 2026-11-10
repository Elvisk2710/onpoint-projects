<?php
require __DIR__ . '/../includes/layout.php';

page_head([
    'title' => 'About Us | OnPoint Civils',
    'description' => 'Learn about OnPoint Civils, a civil engineering and construction company that takes projects from design through to completion with safety as the first priority.',
    'path' => '/pages/about.php',
    'nodes' => [seo_webpage_node(SITE_URL . '/pages/about.php', 'About Us | OnPoint Civils', 'Learn about OnPoint Civils, a civil engineering and construction company that takes projects from design through to completion with safety as the first priority.', 'AboutPage'), seo_breadcrumb_node([['Home', '/'], ['About Us', '/pages/about.php']], SITE_URL . '/pages/about.php')],
]);
page_header('about');
?>
  <section class="page-hero">
    <div class="container">
      <span class="label" style="color:var(--yellow)">About us</span>
      <h1>Engineers and builders you can rely on</h1>
      <p class="lead" style="color:#b7c1cd">OnPoint Civils is a civil engineering and construction company. We take on projects from design through to completion.</p>
    </div>
  </section>

  <section class="section">
    <div class="container split">
      <div class="reveal">
        <span class="label">Who we are</span>
        <h2>A practical approach to engineering</h2>
        <p style="margin-top:20px;color:var(--muted)">We work closely with our clients so that decisions are made quickly and everyone knows where the project stands.</p>
        <p style="margin-top:16px;color:var(--muted)">Our work covers roads, structures, water and sewer infrastructure, and buildings. Whatever the size of the job, we aim to plan it properly, price it fairly and build it well.</p>
      </div>
      <div class="reveal" style="--d:.1s">
        <div class="values">
          <div class="card"><h3>Safety</h3><p>No job is so urgent that it can't be done safely.</p></div>
          <div class="card"><h3>Quality</h3><p>Work is checked as it progresses, not only at the end.</p></div>
          <div class="card"><h3>Honesty</h3><p>Clear quotes, realistic timelines and regular updates.</p></div>
          <div class="card"><h3>Accountability</h3><p>We take responsibility for the work we do.</p></div>
        </div>
      </div>
    </div>
  </section>

  <section class="section dark" style="padding-bottom:0;overflow:hidden">
    <div class="container">
      <div class="section-head reveal">
        <span class="label">Our team</span>
        <h2>Our team</h2>
        <p class="lead">Our people work in protective equipment and follow clear safety procedures on site.</p>
      </div>
      <img src="../assets/images/team-machinery.png" alt="OnPoint Civils team in safety gear with an excavator and tipper truck" class="people-img reveal" loading="lazy">
    </div>
  </section>

  <section class="cta-band">
    <div class="container">
      <div>
        <h2>Let's talk about your project</h2>
        <p>Call, WhatsApp or send us a message.</p>
      </div>
      <a href="contact.php" class="btn btn-dark">Contact us</a>
    </div>
  </section>

  
<?php page_footer();
