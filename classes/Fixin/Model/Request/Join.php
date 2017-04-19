<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Model\Request;

use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Model\Request\Where\WhereInterface;
use Fixin\Resource\Prototype;
use Fixin\Support\Types;

class Join extends Prototype implements JoinInterface
{
    protected const
        THIS_SETS = [
            self::ALIAS => Types::STRING,
            self::REPOSITORY => RepositoryInterface::class,
            self::TYPE => Types::STRING,
            self::WHERE => [WhereInterface::class, Types::NULL]
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
}
