<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\View\Helper;

use Fixin\ResourceManager\ResourceManagerInterface;

class JsVariable extends Helper {

    /**
     * @var EscaperInterface
     */
    protected $escaper;

    /**
     * @param ResourceManagerInterface $container
     * @param array $options
     * @param string $name
     */
    public function __construct(ResourceManagerInterface $container, array $options = null, string $name = null) {
        parent::__construct($container, $options, $name);

        $this->escaper = $container->get('Base\Escaper\Escaper');
    }

    /**
     * Normal escape or iterated for arrays
     *
     * @param mixed $value
     * @return string
     */
    public function __invoke($value) {
        return $this->encode($value);
    }

    /**
     * Encode value
     *
     * @param mixed $value
     * @return string
     */
    public function encode($value): string {
        return $this->escaper->encodeJsVariable($value);
    }
}