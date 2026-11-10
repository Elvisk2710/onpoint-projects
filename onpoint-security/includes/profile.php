<?php
declare(strict_types=1);

/** Business facts for ShieldTrac Security. Feeds structured data, llms.txt, the sitemap and the RSS feed. */
function site_profile(): array
{
    return [
        'name' => 'ShieldTrac Security Zimbabwe',
        'short_name' => 'ShieldTrac Security',
        'home_title' => 'ShieldTrac Security Zimbabwe | Professional Security & Guard Services',
        'home_description' => 'ShieldTrac Security: guard services, CCTV, alarm systems, rapid response, patrols and 24/7 monitoring for businesses and homes across Zimbabwe.',
        'org_type' => 'ProfessionalService',
        'description' => 'ShieldTrac Security Zimbabwe provides professional security services including guard services, CCTV installation, alarm systems, rapid response, patrols, and 24/7 monitoring across Zimbabwe.',
        'logo' => '/assets/favicon.svg',
        'image' => '/assets/images/securityStanding.jpg',
        'phones' => ['+263788094724', '+263712734834', '+263242328474'],
        'emails' => ['info@shieldtrac.co.zw', 'richard@shieldtrac.co.zw'],
        'address' => ['street' => '26 Connaught Road, Avondale', 'locality' => 'Harare'],
        'knows_about' => ['Security guard services', 'CCTV installation', 'Alarm systems', 'Cash in transit', 'Access control', 'Private investigations', 'Rapid response'],
        'services' => [
            ['Manned Security', 'Highly trained security guards providing visible presence and threat deterrence'],
            ['Cash Management', 'Secure cash handling and transportation services'],
            ['Private Investigations', 'Discreet and professional investigative services'],
            ['Alarm Systems', 'Advanced alarm systems with emergency response'],
            ['CCTV Security', 'Complete CCTV installation and monitoring solutions'],
            ['Access Solutions', 'Electronic access control and perimeter security'],
        ],
        'blog_name' => 'Security Insights',
        'blog_description' => 'Advisories, practical tips and industry news from the ShieldTrac security team.',
        'static_pages' => ['/', '/blog/'],
    ];
}
