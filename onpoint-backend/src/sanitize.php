<?php
declare(strict_types=1);

/**
 * Allow-list HTML sanitizer for post/project bodies written in the admin editor.
 * Runs on save, so the API can serve body_html as-is.
 * Unknown tags are unwrapped (their text is kept); dangerous tags are dropped with their content.
 */
function sanitize_html(string $html): string
{
    if (trim($html) === '') {
        return '';
    }

    $allowedTags = [
        'p' => [], 'br' => [], 'hr' => [],
        'h2' => ['id'], 'h3' => ['id'], 'h4' => ['id'],
        'strong' => [], 'b' => [], 'em' => [], 'i' => [], 'u' => [], 's' => [], 'sub' => [], 'sup' => [],
        'blockquote' => [], 'code' => [], 'pre' => [],
        'ul' => [], 'ol' => ['start'], 'li' => [],
        'a' => ['href', 'title', 'target', 'rel'],
        'img' => ['src', 'alt', 'title', 'width', 'height'],
        'figure' => [], 'figcaption' => [],
        'table' => [], 'thead' => [], 'tbody' => [], 'tr' => [], 'th' => ['colspan', 'rowspan', 'scope'], 'td' => ['colspan', 'rowspan'],
        'span' => [], 'div' => [],
        'iframe' => ['src', 'width', 'height', 'allowfullscreen', 'title'],
    ];
    // Removed entirely, content included
    $dropTags = ['script', 'style', 'object', 'embed', 'form', 'input', 'button', 'textarea', 'select', 'svg', 'math', 'link', 'meta', 'base', 'noscript', 'template'];
    // Only embeds from these hosts survive
    $iframeHosts = ['www.youtube.com', 'www.youtube-nocookie.com', 'player.vimeo.com', 'www.google.com'];

    $doc = new DOMDocument('1.0', 'UTF-8');
    $prev = libxml_use_internal_errors(true);
    $doc->loadHTML(
        '<?xml encoding="UTF-8"?><div id="__root">' . $html . '</div>',
        LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NONET
    );
    libxml_clear_errors();
    libxml_use_internal_errors($prev);

    $root = $doc->getElementById('__root');
    if (!$root) {
        return '';
    }

    $isSafeUrl = static function (string $url, bool $allowData = false): bool {
        $url = trim(preg_replace('/[\x00-\x20]+/', '', $url) ?? '');
        if ($url === '' || $url[0] === '/' || $url[0] === '#' || $url[0] === '?') {
            return true;
        }
        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        if ($scheme === '') {
            return !str_contains(strtolower($url), ':'); // relative path
        }
        return in_array($scheme, ['http', 'https', 'mailto', 'tel'], true);
    };

    $walk = static function (DOMNode $node) use (&$walk, $allowedTags, $dropTags, $iframeHosts, $isSafeUrl): void {
        // Iterate over a static copy because we mutate children
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child instanceof DOMComment || $child instanceof DOMProcessingInstruction) {
                $node->removeChild($child);
                continue;
            }
            if (!$child instanceof DOMElement) {
                continue;
            }
            $tag = strtolower($child->tagName);

            if (in_array($tag, $dropTags, true)) {
                $node->removeChild($child);
                continue;
            }

            if (!array_key_exists($tag, $allowedTags)) {
                // Unwrap: keep children, drop the element
                $walk($child);
                while ($child->firstChild) {
                    $node->insertBefore($child->firstChild, $child);
                }
                $node->removeChild($child);
                continue;
            }

            // Strip attributes not on the allow-list (this removes on*, style, class, etc.)
            foreach (iterator_to_array($child->attributes) as $attr) {
                $name = strtolower($attr->nodeName);
                if (!in_array($name, $allowedTags[$tag], true)) {
                    $child->removeAttribute($attr->nodeName);
                }
            }

            if ($tag === 'a') {
                $href = $child->getAttribute('href');
                if ($href !== '' && !$isSafeUrl($href)) {
                    $child->removeAttribute('href');
                }
                if ($child->getAttribute('target') === '_blank') {
                    $child->setAttribute('rel', 'noopener noreferrer');
                } else {
                    $child->removeAttribute('target');
                }
            }

            if ($tag === 'img') {
                $src = $child->getAttribute('src');
                if ($src === '' || !$isSafeUrl($src)) {
                    $node->removeChild($child);
                    continue;
                }
                $child->setAttribute('loading', 'lazy');
            }

            if ($tag === 'iframe') {
                $host = strtolower((string) parse_url($child->getAttribute('src'), PHP_URL_HOST));
                $scheme = strtolower((string) parse_url($child->getAttribute('src'), PHP_URL_SCHEME));
                if ($scheme !== 'https' || !in_array($host, $iframeHosts, true)) {
                    $node->removeChild($child);
                    continue;
                }
                $child->setAttribute('loading', 'lazy');
            }

            $walk($child);
        }
    };

    $walk($root);

    $out = '';
    foreach ($root->childNodes as $child) {
        $out .= $doc->saveHTML($child);
    }
    return trim($out);
}
