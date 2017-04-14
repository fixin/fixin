<?php
namespace Fixin\Resource;

interface ManagedInterface
{
    public function __construct(ResourceManagerInterface $resourceManager, array $options = null, string $name = null);
}