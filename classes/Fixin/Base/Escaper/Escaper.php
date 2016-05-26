<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Escaper;

class Escaper implements EscaperInterface {

    const HTML_ENCODING_OPTIONS = ENT_COMPAT | ENT_HTML5 | ENT_SUBSTITUTE;
    const JS_ENCODING_OPTIONS = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION;
    const JS_REPLACES = ['"' => "'", ' ' => '\x20'];

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Escaper\EscaperInterface::encodeJsVariable($string)
     */
    public function encodeJsVariable($var): string {
        return strtr(json_encode($var, static::JS_ENCODING_OPTIONS), static::JS_REPLACES);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Escaper\EscaperInterface::escapeHtml($string)
     */
    public function escapeHtml(string $string): string {
        return htmlspecialchars($string, static::HTML_ENCODING_OPTIONS);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Escaper\EscaperInterface::escapeJs($string)
     */
    public function escapeJs(string $string): string {
        return mb_substr($this->encodeJsVariable($string), 1, -1);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Escaper\EscaperInterface::escapeUrl($url)
     */
    public function escapeUrl(string $url): string {
        return rawurlencode($url);
    }
}