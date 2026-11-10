<?php
declare(strict_types=1);

/** Business facts for OnPoint Civils. Feeds structured data, llms.txt, the sitemap and the RSS feed. */
function site_profile(): array
{
    return [
        'name' => 'OnPoint Civils',
        'short_name' => 'OnPoint Civils',
        'home_title' => 'OnPoint Civils | Civil Engineering and Construction',
        'home_description' => 'Civil engineering and construction in Zimbabwe: roads, structures, water and sewer infrastructure and buildings. Call or WhatsApp for a quote.',
        'org_type' => 'GeneralContractor',
        'description' => 'OnPoint Civils is a civil engineering and construction company covering roads, structures, water and sewer infrastructure and buildings. Call or WhatsApp for a quote.',
        'logo' => '/assets/favicon.svg',
        'image' => '/assets/images/team-machinery.png',
        'phones' => ['+263782381986', '+263788094724', '+263712734834', '+263242328474'],
        'emails' => ['info@onpointcivils.co.zw', 'richard@onpointcivils.co.zw', 'sales@onpointcivils.co.zw'],
        'address' => ['street' => '1 Ambrose, 86 Chinamano Avenue', 'locality' => 'Harare'],
        'knows_about' => ['Civil engineering', 'Road construction', 'Water and sewer infrastructure', 'Structural construction', 'Building construction'],
        'services' => [
            ['Roads', 'Road construction, rehabilitation and drainage'],
            ['Structures', 'Reinforced-concrete and structural construction'],
            ['Water and Sewer Infrastructure', 'Water reticulation, sewer and associated works'],
            ['Buildings', 'Commercial, institutional and residential building construction'],
        ],
        'blog_name' => 'Project Updates',
        'blog_description' => 'Progress reports, milestones and news from OnPoint Civils roads, water, sewer and building projects.',
        'static_pages' => ['/', '/pages/about.php', '/pages/contact.php', '/projects/', '/blog/'],
    ];
}
