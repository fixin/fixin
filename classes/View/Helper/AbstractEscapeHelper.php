<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\View\Helper;

use Fixin\Base\Escaper\EscaperInterface;
use Fixin\Resource\ResourceManagerInterface;

abstract class AbstractEscapeHelper extends AbstractHelper
{
    /**
     * @var EscaperInterface
     */
    protected $escaper;

    public function __construct(ResourceManagerInterface $resourceManager, array $options = null, string $name = null)
    {
        parent::__construct($resourceManager, $options, $name);

        $this->escaper = $resourceManager->get('*\Base\Escaper\Escaper', EscaperInterface::class);
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
