<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Escaper;

class Escaper implements EscaperInterface {

    /**
     * @var int
     */
    protected $htmlEncodingOptions = ENT_COMPAT | ENT_HTML5 | ENT_SUBSTITUTE;

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Escaper\EscaperInterface::escapeHtml($string)
     */
    public function escapeHtml(string $string): string {
        return htmlspecialchars($string, $this->htmlEncodingOptions);
    }
}