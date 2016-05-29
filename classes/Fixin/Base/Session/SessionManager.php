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

class SessionManager extends Resource implements SessionManagerInterface {

    const EXCEPTION_INVALID_REPOSITORY_TYPE = 'Invalid repository type';

    /**
     * @var RepositoryInterface|string
     */
    protected $repository = 'Base\Session\SessionRepository';

    /**
     * {@inheritDoc}
     * @see \Fixin\Resource\Resource::configureWithOptions()
     */
    protected function configureWithOptions(array $options) {
        parent::configureWithOptions($options);

        if (mb_strlen($this->repository) === 0) {
            throw new InvalidArgumentException(static::EXCEPTION_INVALID_REPOSITORY_TYPE);
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
    public function getSession(string $name) {
        return $this->getRepository()->get($name);
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

        throw new InvalidArgumentException(static::EXCEPTION_INVALID_REPOSITORY_TYPE);
    }
}