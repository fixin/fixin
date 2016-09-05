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

    const DELETE_TAG = 'DELETE';
    const FROM_TAG = 'FROM ';
    const LIMIT_TAG = 'LIMIT ';
    const QUERY_PROTOTYPE = 'Model\Storage\Pdo\Query';

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\Pdo\GrammarInterface::delete($request)
     */
    public function delete(RequestInterface $request): QueryInterface {
        $query = $this->container->clonePrototype(static::QUERY_PROTOTYPE)->appendText(static::DELETE_TAG);

        $this
        ->fromString($request, $query)
        ->whereString($request, $query)
        ->orderByString($request, $query)
        ->limitString($request, $query);

        return $query;
    }

    protected function fromString(RequestInterface $request, QueryInterface $query): self {
        $query->appendText(static::FROM_TAG . $this->quoteIdentifier($request->getRepository()->getName()));

        return $this;
    }

    protected function limitString(RequestInterface $request, QueryInterface $query): self {
        $limit = '';

        if ($request->getOffset()) {
            // TODO
        }

        if ($request->getLimit()) {
            // TODO
        }

        if ($limit !== '') {
            $query->appendText(static::LIMIT_TAG . $limit);
        }

        return $this;
    }

    protected function orderByString(RequestInterface $request, QueryInterface $query): self {
        $order = '';

        foreach ($request->getOrderBy() as $key => $value) {
            // TODO
        }


        if ($order !== '') {
            $query->appendText(static::ORDER_TAG . $order);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\Pdo\GrammarInterface::insert($repository, $set)
     */
    public function insert(RepositoryInterface $repository, array $set): QueryInterface {
        // TODO
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\Pdo\GrammarInterface::insertInto($repository, $request)
     */
    public function insertInto(RepositoryInterface $repository, RequestInterface $request): QueryInterface {
        // TODO
    }

    /**
     * Quote identifier
     *
     * @param string $name
     * @return string
     */
    protected function quoteIdentifier(string $name): string {
        return "`$name`"; // TODO escape
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\Pdo\GrammarInterface::select($request)
     */
    public function select(RequestInterface $request): QueryInterface {
        // TODO
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\Pdo\GrammarInterface::update($set, $request)
     */
    public function update(array $set, RequestInterface $request): QueryInterface {
        // TODO
    }

    protected function whereString(RequestInterface $request, QueryInterface $query): self {
        // TODO
        return $this;
    }
}
