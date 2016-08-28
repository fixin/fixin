<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Storage\Pdo;

use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Model\Request\RequestInterface;
use Fixin\Resource\Resource;

class MysqlGrammar extends Resource implements GrammarInterface {

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\Pdo\GrammarInterface::delete($request)
     */
    public function delete(RequestInterface $request): string {
        // TODO
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\Pdo\GrammarInterface::insert($repository, $set)
     */
    public function insert(RepositoryInterface $repository, array $set): string {
        // TODO
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\Pdo\GrammarInterface::insertInto($repository, $request)
     */
    public function insertInto(RepositoryInterface $repository, RequestInterface $request): string {
        // TODO
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\Pdo\GrammarInterface::select($request)
     */
    public function select(RequestInterface $request): string {
        // TODO
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\Pdo\GrammarInterface::update($set, $request)
     */
    public function update(array $set, RequestInterface $request): string {
        // TODO
    }
}
