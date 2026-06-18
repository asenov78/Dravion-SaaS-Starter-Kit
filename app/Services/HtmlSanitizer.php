<?php

namespace App\Services;

class HtmlSanitizer
{
    private const ALLOWED_TAGS = [
        'p','br','strong','em','b','i','u','ul','ol','li',
        'h1','h2','h3','h4','h5','h6','a','img','blockquote',
        'code','pre','table','thead','tbody','tr','th','td','hr','span','div',
    ];

    private const ALLOWED_ATTRS = [
        'a'   => ['href','title','target','rel'],
        'img' => ['src','alt','title','width','height'],
        '*'   => ['class','id','style'],
    ];

    public function sanitize(?string $html): ?string
    {
        if ($html === null || trim($html) === '') {
            return $html;
        }

        $dom = new \DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        $dom->loadHTML('<!DOCTYPE html><html><head><meta charset="utf-8"></head><body>' . $html . '</body></html>');
        libxml_clear_errors();

        $body = $dom->getElementsByTagName('body')->item(0);
        if (!$body) {
            return strip_tags($html);
        }

        $this->walkNode($body);

        $out = '';
        foreach ($body->childNodes as $child) {
            $out .= $dom->saveHTML($child);
        }

        return $out;
    }

    private function walkNode(\DOMNode $node): void
    {
        $remove = [];
        foreach ($node->childNodes as $child) {
            if (! $child instanceof \DOMElement) {
                continue;
            }

            $tag = strtolower($child->tagName);

            if (! in_array($tag, self::ALLOWED_TAGS, true)) {
                $remove[] = $child;
                continue;
            }

            $permitted  = array_merge(self::ALLOWED_ATTRS['*'], self::ALLOWED_ATTRS[$tag] ?? []);
            $attrRemove = [];

            foreach ($child->attributes as $attr) {
                $name = strtolower($attr->name);
                if (! in_array($name, $permitted, true) || str_starts_with($name, 'on')) {
                    $attrRemove[] = $name;
                }
            }

            // Block javascript: / data: in URL attributes
            foreach (['href', 'src', 'action'] as $urlAttr) {
                if ($child->hasAttribute($urlAttr)) {
                    $val = trim(strtolower(preg_replace('/\s+/', '', $child->getAttribute($urlAttr))));
                    if (str_starts_with($val, 'javascript:') || str_starts_with($val, 'data:')) {
                        $attrRemove[] = $urlAttr;
                    }
                }
            }

            // Strip CSS data-exfiltration vectors from style attribute
            if ($child->hasAttribute('style')) {
                $clean = preg_replace('/\b(url|expression|behavior|vbscript)\s*\(/i', 'BLOCKED(', $child->getAttribute('style'));
                $child->setAttribute('style', $clean ?? '');
            }

            foreach (array_unique($attrRemove) as $a) {
                $child->removeAttribute($a);
            }

            $this->walkNode($child);
        }

        foreach ($remove as $n) {
            $node->removeChild($n);
        }
    }
}
