<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Request;

use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Model\Request\Where\WhereInterface;
use Fixin\Resource\PrototypeInterface;

interface JoinInterface extends PrototypeInterface
{
    public const
        OPTION_ALIAS = 'alias',
        OPTION_REPOSITORY = 'repository',
        OPTION_TYPE = 'type',
        OPTION_WHERE = 'where',
        TYPE_CROSS = 'cross',
        TYPE_INNER = 'inner',
        TYPE_LEFT = 'left',
        TYPE_RIGHT = 'right';

    public function getAlias(): string;
    public function getRepository(): RepositoryInterface;
    public function getType(): string;
    public function getWhere(): ?WhereInterface;
}
