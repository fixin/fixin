<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Session;

use Fixin\Base\Exception\InvalidArgumentException;
use Fixin\Base\Model\RepositoryInterface;
use Fixin\Resource\Resource;
use Fixin\Base\Exception\RuntimeException;

class SessionManager extends Resource implements SessionManagerInterface {

    const EXCEPTION_REPOSITORY_NOT_SET = 'Repository not set';

    /**
     * @var RepositoryInterface|string
     */
    protected $repository = 'Base\Session\SessionRepository';

    /**
     * {@inheritDoc}
     * @see \Fixin\Resource\Resource::configurationTests()
     */
    protected function configurationTests() {
        if (mb_strlen($this->repository) === 0) {
            throw new RuntimeException(static::EXCEPTION_REPOSITORY_NOT_SET);
        }
    }

    /**
     * @return RepositoryInterface
     */
    protected function getRepository(): RepositoryInterface {
        return is_object($this->repository) ? $this->repository : ($this->repository = $this->container->get($this->repository));
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Session\SessionManagerInterface::getSession()
     */
    public function getSession(string $name): SessionInterface {
        $options = [
            SessionInterface::OPTION_SESSION_MANAGER => $this
        ];

        return $this->container->clonePrototype('Base\Session\Session', $options);
    }

    /**
     * @param string|RepositoryInterface $repository
     * @throws InvalidArgumentException
     */
    protected function setRepository($repository) {
        if (is_string($repository) || $repository instanceof RepositoryInterface) {
            $this->repository = $repository;

            return;
        }

        throw new InvalidArgumentException(sprintf(InvalidArgumentException::MESSAGE, 'repository', 'string or RepositoryInterface'));
    }
}