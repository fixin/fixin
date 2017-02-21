<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\View\Helper;

use Fixin\Base\Escaper\EscaperInterface;
use Fixin\Resource\ResourceManagerInterface;

class JsVariable extends Helper
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
     * Invoke encode()
     */
    public function __invoke($value): string
    {
        return $this->encode($value);
    }

    public function encode($value): string
    {
        return $this->escaper->encodeJsVariable($value);
    }
}
