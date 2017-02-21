<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\View\Helper;

use Fixin\Base\Escaper\EscaperInterface;
use Fixin\Resource\ResourceManagerInterface;

abstract class EscapeHelper extends Helper
{
    /**
     * @var EscaperInterface
     */
    protected $escaper;

    public function __construct(ResourceManagerInterface $container, array $options = null, string $name = null)
    {
        parent::__construct($container, $options, $name);

        $this->escaper = $container->get('Base\Escaper\Escaper');
    }

    /**
     * Normal escape or iterated for arrays
     */
    public function __invoke($value): string
    {
        if (is_array($value)) {
            return array_map([$this, 'escape'], $value);
        }

        return $this->escape($value);
    }

    abstract public function escape($value): string;
}
