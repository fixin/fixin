<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\View\Helper;

use Fixin\Base\Json\JsonInterface;
use Fixin\Resource\ResourceManagerInterface;

class Json extends Helper
{
    /**
     * @var JsonInterface
     */
    protected $json;

    public function __construct(ResourceManagerInterface $container, array $options = null, string $name = null)
    {
        parent::__construct($container, $options, $name);

        $this->json = $container->get('Base\Json\Json');
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
        return $this->json->encode($value);
    }
}
