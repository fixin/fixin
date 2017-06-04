<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
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

    public function __construct(ResourceManagerInterface $resourceManager, array $options = null, string $name = null)
    {
        parent::__construct($resourceManager, $options, $name);

        $this->json = $resourceManager->get('*\Base\Json\Json', JsonInterface::class);
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
