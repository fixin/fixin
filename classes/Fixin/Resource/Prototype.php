<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Resource;

abstract class Prototype extends Resource implements PrototypeInterface
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(ResourceManagerInterface $resourceManager, array $options = null, string $name = null)
    {
        $this->resourceManager = $resourceManager;

        // Options
        if (isset($options)) {
            $this->configureWithOptions($options);
        }
    }

    /**
     * @return static
     */
    public function withOptions(array $options)
    {
        return (clone $this)
            ->configureWithOptions($options)
            ->configurationTest(get_class($this));
    }
}
