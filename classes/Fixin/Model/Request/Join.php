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

class Join extends Prototype implements JoinInterface {

    const THIS_REQUIRES = [
        self::OPTION_ALIAS => self::TYPE_STRING,
        self::OPTION_REPOSITORY => self::TYPE_INSTANCE,
        self::OPTION_TYPE => self::TYPE_STRING
    ];

    /**
     * @var string
     */
    protected $alias;

    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var WhereInterface
     */
    protected $where;

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\JoinInterface::getAlias()
     */
    public function getAlias(): string {
        return $this->alias;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\JoinInterface::getRepository()
     */
    public function getRepository(): RepositoryInterface {
        return $this->repository;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\JoinInterface::getType()
     */
    public function getType(): string {
        return $this->type;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\JoinInterface::getWhere()
     */
    public function getWhere() {
        return $this->where;
    }

    /**
     * Set alias
     *
     * @param string $alias
     */
    protected function setAlias(string $alias) {
        $this->alias = $alias;
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
     * Set type
     *
     * @param string $type
     */
    protected function setType(string $type) {
        $this->type = $type;
    }

    /**
     * Set where
     *
     * @param WhereInterface $where
     */
    protected function setWhere(WhereInterface $where) {
        $this->where = $where;
    }
}