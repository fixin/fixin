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

    /**
     * @inheritDoc
     */
    public function __construct(ResourceManagerInterface $resourceManager, array $options)
    {
        parent::__construct($resourceManager, $options);

        $this->escaper = $resourceManager->get('*\Base\Escaper\Escaper', EscaperInterface::class);
    }

    /**
     * Normal escape or iterated for arrays
     *
     * @param $value
     * @return string
     */
    public function __invoke($value): string
    {
        if (is_array($value)) {
            return array_map([$this, 'escape'], $value);
        }

        return $this->escape($value);
    }

    /**
     * Escape value
     *
     * @param $value
     * @return string
     */
    abstract public function escape($value): string;
}
