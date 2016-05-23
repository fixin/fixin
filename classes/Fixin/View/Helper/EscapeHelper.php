<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\View\Helper;

use Fixin\Base\Escaper\EscaperInterface;
use Fixin\ResourceManager\ResourceManagerInterface;

abstract class EscapeHelper extends Helper {

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
     * Escape invoke
     *
     * @param mixed $value
     * @return string
     */
    public function __invoke($value) {
        if (is_array($value)) {
            return array_map([$this, 'escape'], $value);
        }

        return $this->escape($value);
    }

    /**
     * Escape value
     *
     * @param mixed $value
     * @return string
     */
    abstract public function escape($value): string;
}