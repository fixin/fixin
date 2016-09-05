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

interface JoinInterface extends PrototypeInterface {

    const OPTION_ALIAS = 'alias';
    const OPTION_REPOSITORY = 'repository';
    const OPTION_TYPE = 'type';
    const OPTION_WHERE = 'where';
    const TYPE_CROSS = 'cross';
    const TYPE_INNER = 'inner';
    const TYPE_LEFT = 'left';
    const TYPE_RIGHT = 'right';

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
    public function getWhere();
}