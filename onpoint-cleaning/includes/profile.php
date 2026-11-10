<?php
declare(strict_types=1);

/** Business facts for OnPoint Hygiene & Fumigation. Feeds structured data, llms.txt, the sitemap and the RSS feed. */
function site_profile(): array
{
    return [
        'name' => 'OnPoint Hygiene And Fumigation Zimbabwe',
        'short_name' => 'OnPoint Hygiene & Fumigation',
        'home_title' => 'OnPoint Hygiene And Fumigation Zimbabwe | Professional Cleaning & Fumigation Services',
        'home_description' => 'Professional cleaning, fumigation and pest control for homes, offices and industry across Zimbabwe. Certified team, 24/7 emergency service. Free quotes.',
        'org_type' => 'ProfessionalService',
        'description' => 'OnPoint Hygiene And Fumigation Zimbabwe provides professional cleaning, fumigation, pest control, office cleaning, deep cleaning, and industrial hygiene solutions. Trusted cleaning experts in Zimbabwe.',
        'logo' => '/assets/favicon.svg',
        'image' => '/assets/images/office_cleaning.png',
        'phones' => ['+263782381986', '+263712734834', '+263242328474'],
        'emails' => ['info@onpointhygienezw.co.zw', 'sales@onpointhygienezw.co.zw'],
        'address' => ['street' => '26 Connaught Road, Avondale', 'locality' => 'Harare'],
        'knows_about' => ['Commercial cleaning', 'Industrial cleaning', 'Pest control', 'Fumigation', 'Restroom hygiene', 'Carpet and upholstery cleaning', 'Window cleaning'],
        'services' => [
            ['Daily Contract Cleaning', 'Regular maintenance cleaning for offices, homes and commercial spaces'],
            ['Industrial Deep Cleaning', 'Industrial cleaning for factories and manufacturing plants'],
            ['Carpet & Upholstery Cleaning', 'Deep cleaning for carpets, rugs and furniture upholstery'],
            ['Window Cleaning', 'Professional window cleaning including high-rise rope access'],
            ['Pre & Post Occupancy Cleaning', 'Complete cleaning before moving in or after moving out'],
            ['Floor Surface Treatment', 'Slip resistance treatment and hard-surface floor maintenance'],
            ['Cleaning Chemicals & Fumigation', 'Professional cleaning chemicals and integrated fumigation services'],
            ['Sanitary Disposal', 'Safe and hygienic waste disposal services'],
            ['Basement & Parking Cleaning', 'Specialised cleaning for basements and parking facilities'],
            ['Flood Extraction & Drying', 'Emergency flood water extraction and drying services'],
            ['Restroom & Workplace Hygiene', 'Restroom sanitation, hygiene dispensers and consumables'],
            ['Pest Control', 'Eco-friendly control of cockroaches, flying insects, ants, bees and wasps, termites and rodents'],
        ],
        'blog_name' => 'Hygiene Tips',
        'blog_description' => 'Practical cleaning, hygiene and pest-control advice from the Onpoint team.',
        'static_pages' => ['/', '/blog/'],
    ];
}
