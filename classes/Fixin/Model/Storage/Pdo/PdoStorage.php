<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Storage\Pdo;

use Fixin\Base\Query\QueryInterface;
use Fixin\Exception\RuntimeException;
use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Model\Request\RequestInterface;
use Fixin\Model\Storage\Grammar\GrammarInterface;
use Fixin\Model\Storage\StorageInterface;
use Fixin\Model\Storage\StorageResultInterface;
use Fixin\Resource\Resource;

class PdoStorage extends Resource implements StorageInterface {

    const
        EXCEPTION_CONNECTION_ERROR = "Connection error: %s",
        GRAMMAR_CLASS_MASK = 'Model\Storage\Grammar\%sGrammar',
        OPTION_DSN = 'dsn',
        OPTION_PASSWORD = 'password',
        OPTION_USERNAME = 'username',
        PROTOTYPE_STORAGE_RESULT = 'Model\Storage\Pdo\PdoStorageResult'
    ;

    /**
     * @var string
     */
    protected $dsn;

    /**
     * @var GrammarInterface
     */
    protected $grammar;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var \PDO
     */
    protected $resource;

    /**
     * @var string
     */
    protected $username;

    /**
     * Connect
     *
     * @throws RuntimeException
     * @return self
     */
    public function connect(): self {
        if ($this->resource) {
            return $this;
        }

        try {
            $this->resource =
            $resource = new \PDO($this->dsn, $this->username, $this->password);
            $resource->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->password = null;

            $class = ucfirst(strtolower($resource->getAttribute(\PDO::ATTR_DRIVER_NAME)));
            $this->grammar = $this->container->get(sprintf(static::GRAMMAR_CLASS_MASK , $class));
        }
        catch (\PDOException $e) {
            throw new RuntimeException(sprintf(static::EXCEPTION_CONNECTION_ERROR, $e->getMessage()));
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\StorageInterface::delete($request)
     */
    public function delete(RequestInterface $request): int {
        $this->connect();

        return $this->execute($this->grammar->delete($request));
    }

    /**
     * Execute query
     *
     * @param QueryInterface $query
     * @return int
     */
    protected function execute(QueryInterface $query): int {
        $statement = $this->resource->prepare($query->getText());
        $statement->execute($query->getParameters());

        return $statement->rowCount();
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\StorageInterface::exists($request)
     */
    public function exists(RequestInterface $request): bool {
        $this->connect();

        return $this->query($this->grammar->exists($request)); // TODO to value
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\StorageInterface::getLastInsertValue()
     */
    public function getLastInsertValue(): int {
        return $this->resource->lastInsertId();
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\StorageInterface::insert($repository, $set)
     */
    public function insert(RepositoryInterface $repository, array $set): int {
        $this->connect();

        return $this->execute($this->grammar->insert($repository, $set));
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\StorageInterface::insertInto($repository, $request)
     */
    public function insertInto(RepositoryInterface $repository, RequestInterface $request): int {
        $this->connect();

        return $this->execute($this->grammar->insertInto($repository, $request));
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\StorageInterface::insertMultiple($repository, $rows)
     */
    public function insertMultiple(RepositoryInterface $repository, array $rows): int {
        $this->connect();

        return $this->execute($this->grammar->insertMultiple($repository, $rows));
    }

    /**
     * Query
     *
     * @param QueryInterface $query
     * @return StorageResultInterface
     */
    protected function query(QueryInterface $query): StorageResultInterface {
        $statement = $this->resource->prepare($query->getText());
        $statement->execute($query->getParameters());

        return $this->container->clonePrototype(static::PROTOTYPE_STORAGE_RESULT, [
            PdoStorageResult::OPTION_STATEMENT => $statement
        ]);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\StorageInterface::select($request)
     */
    public function select(RequestInterface $request): StorageResultInterface {
        $this->connect();

        return $this->query($this->grammar->select($request));
    }

    /**
     * Set dsn
     *
     * @param string $dsn
     */
    protected function setDsn(string $dsn) {
        $this->dsn = $dsn;
    }

    /**
     * Set password
     *
     * @param string $password
     */
    protected function setPassword(string $password) {
        $this->password = $password;
    }

    /**
     * Set username
     *
     * @param string $username
     */
    protected function setUsername(string $username) {
        $this->username = $username;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\StorageInterface::update($set, $request)
     */
    public function update(array $set, RequestInterface $request): int {
        $this->connect();

        return $this->execute($this->grammar->update($set, $request));
    }
}