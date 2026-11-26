<?php

namespace App\Support;

use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

/**
 * Renders admin-entered Markdown. Raw HTML is stripped and unsafe link schemes are removed.
 */
class Markdown
{
    /**
     * @var array{html_input: string, allow_unsafe_links: bool}
     */
    private const array OPTIONS = [
        'html_input' => 'strip',
        'allow_unsafe_links' => false,
    ];

    /**
     * Render Markdown as block-level HTML.
     */
    public static function block(?string $markdown): HtmlString
    {
        return new HtmlString(Str::markdown((string) $markdown, self::OPTIONS));
    }

    /**
     * Render Markdown as inline HTML, without wrapping paragraphs.
     */
    public static function inline(?string $markdown): HtmlString
    {
        return new HtmlString(Str::inlineMarkdown((string) $markdown, self::OPTIONS));
    }
}
