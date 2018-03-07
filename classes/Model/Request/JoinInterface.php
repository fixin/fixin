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
use Fixin\Resource\PrototypeInterface;

interface JoinInterface extends PrototypeInterface
{
    public const
        ALIAS = 'alias',
        REPOSITORY = 'repository',
        TYPE = 'type',
        TYPE_CROSS = 'cross',
        TYPE_INNER = 'inner',
        TYPE_LEFT = 'left',
        TYPE_RIGHT = 'right',
        WHERE = 'where';

    /**
     * Get alias
     *
     * @return string
     */
    public function getAlias(): string;

    /**
     * Get repository
     *
     * @return RepositoryInterface
     */
    public function getRepository(): RepositoryInterface;

    /**
     * Get type
     *
     * @return string
     */
    public function getType(): string;

    /**
     * Get where
     *
     * @return WhereInterface|null
     */
    public function getWhere(): ?WhereInterface;
}
