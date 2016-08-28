<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Request;

use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Resource\Prototype;
use Fixin\Model\Entity\EntitySetInterface;
use Fixin\Model\Storage\StorageResultInterface;
use Fixin\Model\Request\Where\WhereInterface;

class Request extends Prototype implements RequestInterface {

    const BETWEEN_TAG_PROTOTYPE = 'Model\Request\Where\Tag\BetweenTag';
    const COMPARE_TAG_PROTOTYPE = 'Model\Request\Where\Tag\CompareTag';
    const EXISTS_TAG_PROTOTYPE = 'Model\Request\Where\Tag\ExistsTag';
    const IN_TAG_PROTOTYPE = 'Model\Request\Where\Tag\InTag';
    const NULL_TAG_PROTOTYPE = 'Model\Request\Where\Tag\NullTag';
    const REQUEST_TAG_PROTOTYPE = 'Model\Request\Where\Tag\RequestTag';
    const THIS_REQUIRES = [
        self::OPTION_REPOSITORY => self::TYPE_INSTANCE
    ];
    const WHERE_PROTOTYPE = 'Model\Request\Where\Where';

    /**
     * @var WhereInterface
     */
    protected $having;

    /**
     * @var int|null
     */
    protected $limit;

    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @var WhereInterface
     */
    protected $where;

    /**
     * @var array
     */
    protected $wheres = [];

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::delete()
     */
    public function delete(): int {
        return $this->repository->delete($this);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::first()
     */
    public function first() {
        $copy = clone $this;

        return $copy->limit(1)->get()->current();
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::get()
     */
    public function get(): EntitySetInterface {
        return $this->repository->selectEntities($this);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::getRawData()
     */
    public function getRawData(): StorageResultInterface {
        return $this->repository->selectRawData($this);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::getRepository()
     */
    public function getRepository(): RepositoryInterface {
        return $this->repository;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::having()
     */
    public function having(): WhereInterface {
        return $this->having ?? ($this->having = $this->container->clonePrototype(static::WHERE_PROTOTYPE));
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::limit($limit)
     */
    public function limit(int $limit): RequestInterface {
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
     * @see \Fixin\Model\Request\RequestInterface::update($set)
     */
    public function update(array $set): int {
        return $this->repository->update($set, $this);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::where()
     */
    public function where(): WhereInterface {
        return $this->where ?? ($this->where = $this->container->clonePrototype(static::WHERE_PROTOTYPE));
    }
}