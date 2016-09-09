<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Storage\Grammar;

use Fixin\Base\Query\QueryInterface;
use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Model\Request\RequestInterface;
use Fixin\Resource\ResourceInterface;

interface GrammarInterface extends ResourceInterface {

    const IDENTIFIER_QUOTE = '`';
    const IDENTIFIER_SEPARATOR = '.';
    const STRING_QUOTE = "'";

    /**
     * Delete
     *
     * @param RequestInterface $request
     * @return QueryInterface
     */
    public function delete(RequestInterface $request): QueryInterface;

    /**
     * Exists
     *
     * @param RequestInterface $request
     * @return QueryInterface
     */
    public function exists(RequestInterface $request): QueryInterface;

    /**
     * Insert
     *
     * @param RepositoryInterface $repository
     * @param array $set
     * @return QueryInterface
     */
    public function insert(RepositoryInterface $repository, array $set): QueryInterface;

    /**
     * Insert into
     *
     * @param RepositoryInterface $repository
     * @param RequestInterface $request
     * @return QueryInterface
     */
    public function insertInto(RepositoryInterface $repository, RequestInterface $request): QueryInterface;

    /**
     * Insert multiple
     *
     * @param RepositoryInterface $repository
     * @param array $rows
     * @return QueryInterface
     */
    public function insertMultiple(RepositoryInterface $repository, array $rows): QueryInterface;

    /**
     * Select
     *
     * @param RequestInterface $request
     * @return QueryInterface
     */
    public function select(RequestInterface $request): QueryInterface;

    /**
     * Update
     *
     * @param array $set
     * @param RequestInterface $request
     * @return QueryInterface
     */
    public function update(array $set, RequestInterface $request): QueryInterface;
}