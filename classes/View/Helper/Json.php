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

class Json extends AbstractHelper
{
    /**
     * @var JsonInterface
     */
    protected $json;

    /**
     * @inheritDoc
     */
    public function __construct(ResourceManagerInterface $resourceManager, array $options)
    {
        parent::__construct($resourceManager, $options);

        $this->json = $resourceManager->get('*\Base\Json\Json', JsonInterface::class);
    }

    /**
     * Invoke encode()
     *
     * @param $value
     * @return string
     */
    public function __invoke($value): string
    {
        return $this->encode($value);
    }

    /**
     * @inheritDoc
     */
    public function encode($value): string
    {
        return $this->json->encode($value);
    }
}
