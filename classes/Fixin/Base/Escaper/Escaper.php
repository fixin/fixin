<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Escaper;

use Fixin\Base\Json\Json;
use Fixin\ResourceManager\Resource;
use Fixin\ResourceManager\ResourceManagerInterface;

class Escaper extends Resource implements EscaperInterface {

    /**
     * @var int
     */
    protected $htmlEncodingOptions = ENT_COMPAT | ENT_HTML5 | ENT_SUBSTITUTE;

    /**
     * @var Json
     */
    protected $json;

    /**
     * @param ResourceManagerInterface $container
     * @param array $options
     * @param string $name
     */
    public function __construct(ResourceManagerInterface $container, array $options = null, string $name = null) {
        parent::__construct($container, $options, $name);

        $this->json = $container->get('Base\Json\Json');
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Escaper\EscaperInterface::escapeHtml($string)
     */
    public function escapeHtml(string $string): string {
        return htmlspecialchars($string, $this->htmlEncodingOptions);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Escaper\EscaperInterface::escapeJs($string)
     */
    public function escapeJs(string $string): string {
        return $this->escapeHtml($this->json->encode($string));
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Escaper\EscaperInterface::escapeUrl($url)
     */
    public function escapeUrl(string $url): string {
        return rawurlencode($url);
    }
}