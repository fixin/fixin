<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Session;

use Fixin\Base\Exception\RuntimeException;
use Fixin\Base\Model\RepositoryInterface;
use Fixin\Resource\Resource;

class SessionManager extends Resource implements SessionManagerInterface {

    const EXCEPTION_REPOSITORY_NOT_SET = 'Repository not set';

    /**
     * @var RepositoryInterface|false|null
     */
    protected $repository;

    /**
     * {@inheritDoc}
     * @see \Fixin\Resource\Resource::configurationTests()
     */
    protected function configurationTests(): Resource {
        if (!isset($this->repository)) {
            throw new RuntimeException(static::EXCEPTION_REPOSITORY_NOT_SET);
        }

        return $this;
    }

    /**
     * Get repository instance
     *
     * @return RepositoryInterface
     */
    protected function getRepository(): RepositoryInterface {
        return $this->repository ?: $this->loadLazyProperty('repository');
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Session\SessionManagerInterface::getSession($name)
     */
    public function getSession(string $name): SessionInterface {
        $options = [
            SessionInterface::OPTION_SESSION_MANAGER => $this
        ];

        return $this->container->clonePrototype('Base\Session\Session', $options);
    }

    /**
     * Set repository
     *
     * @param string|RepositoryInterface $repository
     */
    protected function setRepository($repository) {
        $this->setLazyLoadingProperty('repository', RepositoryInterface::class, $repository);
    }
}