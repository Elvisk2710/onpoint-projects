// sitemap-generator.js

import { SitemapStream, streamToPromise } from 'sitemap';
import { createWriteStream } from 'fs';
import { resolve } from 'path';
import axios from 'axios';

// ----------------------------------------------------------------------
// CONFIGURATION
// ----------------------------------------------------------------------
// IMPORTANT: Use the root domain without a trailing slash
const HOSTNAME = 'https://shieldtrac.co.zw'; 
// Vite's default build output directory
const OUTPUT_DIR = 'dist'; 
// A static list of routes (must match your React Router paths)
const STATIC_ROUTES = [
    '/', 
    '/#about', 
    '/#contact', 
    '/#services',
    '/#pest-control',
    '/#clients',
    '/#contact',
    // Add any other static routes here
];

/**
 * Ensures a URL starts with a slash but is not just a single slash, 
 * helping prevent accidental double-slashes in the output.
 * @param {string} url 
 * @returns {string} Cleaned URL path
 */
const cleanUrlPath = (url) => {
    if (url === '/') return '/';
    return url.startsWith('/') ? url : `/${url}`;
}

const generateSitemap = async () => {
    // 1. Fetch Dynamic Routes (e.g., from an API)
    let dynamicRoutes = [];
    try {
        // Use a more specific endpoint if possible, e.g., '/api/posts/slugs'
        const response = await axios.get(`${HOSTNAME}/posts`); 
        
        // Ensure the data structure is correct and contains an iterable array
        if (Array.isArray(response.data)) {
            dynamicRoutes = response.data.map(post => cleanUrlPath(`/blog/${post.slug}`));
        } else {
            console.warn('API response data was not an array. Skipping dynamic routes.');
        }

    } catch (error) {
        console.error(`❌ Error fetching dynamic routes from ${HOSTNAME}/posts:`, error.message);
        // Continue generation with static routes if the API fails
    }

    // 2. Combine all URLs
    const allUrls = [...STATIC_ROUTES, ...dynamicRoutes];

    // 3. Map the URLs to the sitemap format
    const links = allUrls.map(url => ({
        url: cleanUrlPath(url),
        changefreq: url === '/' ? 'daily' : 'weekly', // Prioritize homepage with 'daily'
        priority: url === '/' ? 1.0 : (url.includes('/blog/') ? 0.7 : 0.8), // Different priorities
        // Use the current date as a sensible default 'lastmod'
        lastmod: new Date().toISOString(), 
    }));
    
    // 4. Create and write the XML file
    const stream = new SitemapStream({ hostname: HOSTNAME });
    const outputFilePath = resolve(`./${OUTPUT_DIR}`, 'sitemap.xml'); 
    
    // Pipe the links to the stream
    links.forEach(link => stream.write(link));
    stream.end();

    try {
        // Wait for the stream to resolve and write to the file system
        const sitemapData = await streamToPromise(stream);
        createWriteStream(outputFilePath).write(sitemapData.toString());
        console.log(`\n✅ Sitemap successfully created at ${outputFilePath}`);
    } catch (error) {
        console.error('\n❌ Failed to write sitemap file:', error);
    }
};

// Execute the function
generateSitemap();