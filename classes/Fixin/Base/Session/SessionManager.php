<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Session;

use Fixin\Base\Model\Repository\RepositoryInterface;
use Fixin\ResourceManager\Resource;

class SessionManager extends Resource implements SessionManagerInterface {

    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * Set repository by name
     *
     * @param string $name
     */
    protected function setRepositoryResource(string $name) {
        $this->repository = $this->container->get($name);
    }
}