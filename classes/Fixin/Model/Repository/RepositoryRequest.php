<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Repository;

use Fixin\Resource\Prototype;
use Fixin\Model\Repository\Where\WhereBetween;

class RepositoryRequest extends Prototype implements RepositoryRequestInterface {

    const THIS_REQUIRES = [
        self::OPTION_REPOSITORY => self::TYPE_INSTANCE
    ];
    const WHERE_BETWEEN_PROTOTYPE = 'Model\Repository\Where\WhereBetween';
    const WHERE_COMPARE_PROTOTYPE = 'Model\Repository\Where\WhereCompare';
    const WHERE_EXISTS_PROTOTYPE = 'Model\Repository\Where\WhereExists';
    const WHERE_IN_PROTOTYPE = 'Model\Repository\Where\WhereIn';
    const WHERE_NULL_PROTOTYPE = 'Model\Repository\Where\WhereNull';
    const WHERE_REQUEST_PROTOTYPE = 'Model\Repository\Where\WhereRequest';

    /**
     * @var int|null
     */
    protected $limit;

    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @var array
     */
    protected $wheres = [];

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RepositoryRequestInterface::delete()
     */
    public function delete(): int {
        return $this->repository->delete($this);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RepositoryRequestInterface::first()
     */
    public function first() {
        $copy = clone $this;

        return $copy->limit(1)->get()->current();
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RepositoryRequestInterface::get()
     */
    public function get(): EntitySetInterface {
        return $this->repository->selectEntities($this);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RepositoryRequestInterface::getRawData()
     */
    public function getRawData(): StorageResultInterface {
        return $this->repository->selectRawData($this);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RepositoryRequestInterface::getRepository()
     */
    public function getRepository(): RepositoryInterface {
        return $this->repository;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RepositoryRequestInterface::limit($limit)
     */
    public function limit(int $limit): RepositoryRequestInterface {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Set repository
     *
     * @param RepositoryInterface $repository
     */
    protected function setRepository(RepositoryInterface $repository) {
        $this->repository = $repository;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RepositoryRequestInterface::update($set)
     */
    public function update(array $set): int {
        return $this->repository->update($set, $this);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RepositoryRequestInterface::whereBetween($identifier, $min, $max)
     */
    public function whereBetween(string $identifier, $min, $max): RepositoryRequestInterface {
        $this->where[] = $this->container->clonePrototype(static::WHERE_BETWEEN_PROTOTYPE, [
            WhereBetween::OPTION_IDENTIFIER => $identifier,
            WhereBetween::OPTION_MIN => $min,
            WhereBetween::OPTION_MAX => $max
        ]);

        return $this;
    }
}