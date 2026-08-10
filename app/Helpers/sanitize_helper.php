<?php
/**
 * sanitize_helper.php
 *
 * SECURITY (audit finding #3 — Persistent XSS via WYSIWYG content):
 * Strips any HTML/JS that could execute in a visitor's browser from
 * rich-text content (news articles, static pages, etc.) while still
 * allowing normal formatting markup produced by the WYSIWYG editor.
 *
 * This is a self-contained allowlist sanitizer (no Composer/HTML Purifier
 * dependency available in this environment). It removes:
 *   - Any tag not on the allowlist (script, iframe, object, embed, style,
 *     link, meta, form, input, button, base, svg, math, etc.) — including
 *     their contents for tags like <script>/<style>.
 *   - Any attribute not on the per-tag allowlist, in particular all
 *     `on*` event handler attributes.
 *   - `href`/`src` values using dangerous schemes (javascript:, vbscript:,
 *     data: except data:image/* for <img>).
 */

if (!function_exists('sanitize_html')) {

    function sanitize_html($html) {
        $html = (string) $html;
        if (trim($html) === '') {
            return '';
        }

        $allowed_tags = array(
            'p', 'br', 'hr', 'strong', 'b', 'em', 'i', 'u', 's', 'small', 'sub', 'sup',
            'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
            'ul', 'ol', 'li', 'blockquote', 'pre', 'code',
            'a', 'img', 'figure', 'figcaption',
            'table', 'thead', 'tbody', 'tfoot', 'tr', 'td', 'th',
            'span', 'div',
        );

        $allowed_attrs = array(
            'a'   => array('href', 'title', 'target', 'rel'),
            'img' => array('src', 'alt', 'title', 'width', 'height'),
            '*'   => array('class'),
        );

        $dom = new DOMDocument();
        libxml_use_internal_errors(TRUE);
        // Wrap in UTF-8 meta + body so DOMDocument doesn't mangle encoding
        // or treat the fragment as a full document unexpectedly.
        $dom->loadHTML(
            '<?xml encoding="utf-8" ?><!DOCTYPE html><html><body>' . $html . '</body></html>',
            LIBXML_NOERROR | LIBXML_NOWARNING
        );
        libxml_clear_errors();

        $body = $dom->getElementsByTagName('body')->item(0);
        if ($body === NULL) {
            return '';
        }

        _sanitize_walk($dom, $body, $allowed_tags, $allowed_attrs);

        $out = '';
        foreach (iterator_to_array($body->childNodes) as $child) {
            $out .= $dom->saveHTML($child);
        }
        return $out;
    }

    function _sanitize_walk(DOMDocument $dom, DOMNode $node, array $allowed_tags, array $allowed_attrs) {
        $children = iterator_to_array($node->childNodes);

        foreach ($children as $child) {
            if ($child->nodeType === XML_ELEMENT_NODE) {
                $tag = strtolower($child->nodeName);

                if (!in_array($tag, $allowed_tags, TRUE)) {
                    // Tags whose content is dangerous even as text (script,
                    // style) are removed entirely, including their content.
                    // Anything else is unwrapped (content kept, tag stripped).
                    if (in_array($tag, array('script', 'style', 'iframe', 'object', 'embed', 'svg', 'math', 'template', 'noscript'), TRUE)) {
                        $node->removeChild($child);
                    } else {
                        // Recurse first so nested disallowed content is cleaned,
                        // then move children up before removing the wrapper.
                        _sanitize_walk($dom, $child, $allowed_tags, $allowed_attrs);
                        while ($child->firstChild) {
                            $node->insertBefore($child->firstChild, $child);
                        }
                        $node->removeChild($child);
                    }
                    continue;
                }

                // Strip disallowed attributes (including all on* handlers).
                if ($child->hasAttributes()) {
                    $allowed_for_tag = array_merge(
                        isset($allowed_attrs[$tag]) ? $allowed_attrs[$tag] : array(),
                        $allowed_attrs['*']
                    );
                    foreach (iterator_to_array($child->attributes) as $attr) {
                        $attr_name = strtolower($attr->name);
                        if (strpos($attr_name, 'on') === 0 || !in_array($attr_name, $allowed_for_tag, TRUE)) {
                            $child->removeAttribute($attr->name);
                            continue;
                        }
                        if (in_array($attr_name, array('href', 'src'), TRUE)) {
                            $value = trim($attr->value);
                            $decoded = html_entity_decode($value, ENT_QUOTES);
                            $normalized = strtolower(preg_replace('/\s+/', '', $decoded));

                            $is_dangerous_scheme = preg_match('/^(javascript|vbscript|data):/i', $normalized) &&
                                !($tag === 'img' && $attr_name === 'src' && preg_match('/^data:image\//i', $normalized));

                            if ($is_dangerous_scheme) {
                                $child->removeAttribute($attr->name);
                            }
                        }
                    }
                    if ($tag === 'a' && $child->getAttribute('target') === '_blank') {
                        $child->setAttribute('rel', 'noopener noreferrer nofollow');
                    }
                }

                _sanitize_walk($dom, $child, $allowed_tags, $allowed_attrs);
            } elseif ($child->nodeType === XML_COMMENT_NODE) {
                // Comments can hide conditional-comment style attacks in
                // old IE, and add no value in rendered content — drop them.
                $node->removeChild($child);
            }
        }
    }
}
