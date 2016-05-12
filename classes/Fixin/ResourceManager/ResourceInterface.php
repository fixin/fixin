<?php

namespace Fixin\ResourceManager;

use Fixin\Support\ContainerInterface;

interface ResourceInterface {

    /**
     * @param ContainerInterface $container
     * @param array $options
     */
    public function __construct(ContainerInterface $container, array $options = null);
}