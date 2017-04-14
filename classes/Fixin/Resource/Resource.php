<?php
namespace Fixin\Resource;

abstract class Resource extends Managed implements ResourceInterface
{
    public function __construct(ResourceManagerInterface $resourceManager, array $options = null, string $name = null)
    {
        parent::__construct($resourceManager, $options, $name);

        $this->configurationTest($name);
    }
}