<?php
namespace Fixin\Resource;

interface FactoryInterface
{
    public function __construct(ResourceManagerInterface $resourceManager);

    /**
     * Produce resource
     *
     * @return object|null
     */
    public function __invoke(array $options = null, string $name = null);
}