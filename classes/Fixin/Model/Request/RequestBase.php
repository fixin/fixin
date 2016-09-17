<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Request;

use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Model\Request\Where\WhereInterface;
use Fixin\Resource\Prototype;

abstract class RequestBase extends Prototype implements RequestInterface {

    const
        PROTOTYPE_JOIN = 'Model\Request\Join',
        PROTOTYPE_UNION = 'Model\Request\Union',
        PROTOTYPE_WHERE = 'Model\Request\Where\Where',
        THIS_REQUIRES = [
            self::OPTION_REPOSITORY => self::TYPE_INSTANCE
        ];

    /**
     * @var array
     */
    protected $joins = [];

    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @var array
     */
    protected $unions = [];

    /**
     * Add join
     *
     * @param string $type
     * @param RepositoryInterface $repository
     * @param string $left
     * @param string $operator
     * @param string $right
     * @param string $alias
     * @return self
     */
    protected function addJoin(string $type, RepositoryInterface $repository, string $left, string $operator, $right, string $alias = null) {
        return $this->addJoinItem($type, $repository, $this->container->clonePrototype(static::PROTOTYPE_WHERE)->compare($left, $operator, $right, WhereInterface::TYPE_IDENTIFIER, WhereInterface::TYPE_IDENTIFIER), $alias);
    }

    /**
     * Add join intem
     *
     * @param string $type
     * @param RepositoryInterface $repository
     * @param WhereInterface $where
     * @param string $alias
     * @return self
     */
    protected function addJoinItem(string $type, RepositoryInterface $repository, WhereInterface $where = null, string $alias = null) {
        $this->joins[] = $this->container->clonePrototype(static::PROTOTYPE_JOIN, [
            JoinInterface::OPTION_TYPE => $type,
            JoinInterface::OPTION_REPOSITORY => $repository,
            JoinInterface::OPTION_ALIAS => $alias ?? $repository->getName(),
            JoinInterface::OPTION_WHERE => $where
        ]);

        return $this;
    }

    /**
     * Add join by where callback
     *
     * @param string $type
     * @param RepositoryInterface $repository
     * @param callable $callback
     * @param string $alias
     * @return self
     */
    protected function addJoinWhere(string $type, RepositoryInterface $repository, callable $callback, string $alias = null) {
        $where = $this->container->clonePrototype(static::PROTOTYPE_WHERE);
        $callback($where);

        return $this->addJoinItem($type, $repository, $where, $alias);
    }

    /**
     * Add union
     *
     * @param string $type
     * @param RequestInterface $request
     * @return self
     */
    protected function addUnion(string $type, RequestInterface $request) {
        $this->unions[] = $this->container->clonePrototype(static::PROTOTYPE_UNION, [
            UnionInterface::OPTION_TYPE => $type,
            UnionInterface::OPTION_REQUEST => $request
        ]);

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
}