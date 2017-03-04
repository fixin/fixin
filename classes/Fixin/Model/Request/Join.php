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

class Join extends Prototype implements JoinInterface
{
    protected const
        THIS_REQUIRES = [
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
     * @var WhereInterface|null
     */
    protected $where;

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function getRepository(): RepositoryInterface
    {
        return $this->repository;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getWhere(): ?WhereInterface
    {
        return $this->where;
    }

    protected function setAlias(string $alias): void
    {
        $this->alias = $alias;
    }

    protected function setRepository(RepositoryInterface $repository): void
    {
        $this->repository = $repository;
    }

    protected function setType(string $type): void
    {
        $this->type = $type;
    }

    protected function setWhere(?WhereInterface $where): void
    {
        $this->where = $where;
    }
}
