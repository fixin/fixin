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
            $this->grammar = $this->container->get(sprintf(static::GRAMMAR_CLASS_MASK, $class));
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
        return $this->execute($this->getGrammar()->delete($request));
    }

    /**
     * Execute query
     *
     * @param QueryInterface $query
     * @return int
     */
    protected function execute(QueryInterface $query): int {
        return $this->prepareStatement($query)->rowCount();
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\StorageInterface::exists($request)
     */
    public function exists(RequestInterface $request): bool {
        return (bool) $this->prepareStatement($this->getGrammar()->exists($request))->fetchColumn();
    }

    /**
     * Get grammar
     *
     * @return GrammarInterface
     */
    protected function getGrammar(): GrammarInterface {
        $this->connect();

        return $this->grammar;
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
        return $this->execute($this->getGrammar()->insert($repository, $set));
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\StorageInterface::insertInto($repository, $request)
     */
    public function insertInto(RepositoryInterface $repository, RequestInterface $request): int {
        return $this->execute($this->getGrammar()->insertInto($repository, $request));
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\StorageInterface::insertMultiple($repository, $rows)
     */
    public function insertMultiple(RepositoryInterface $repository, array $rows): int {
        return $this->execute($this->getGrammar()->insertMultiple($repository, $rows));
    }

    /**
     * Prepare statement
     *
     * @param QueryInterface $query
     * @return \PDOStatement
     */
    protected function prepareStatement(QueryInterface $query): \PDOStatement {
        $statement = $this->resource->prepare($query->getText());
        $statement->execute($query->getParameters());

        return $statement;
    }

    /**
     * Query
     *
     * @param QueryInterface $query
     * @param array $mode
     * @return StorageResultInterface
     */
    protected function query(QueryInterface $query, array $mode): StorageResultInterface {
        $statement = $this->resource->prepare($query->getText());
        call_user_func_array([$statement, 'setFetchMode'], $mode);
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
        return $this->selectRequest($request, [\PDO::FETCH_ASSOC]);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\StorageInterface::selectColumn($request)
     */
    public function selectColumn(RequestInterface $request): StorageResultInterface {
        return $this->selectRequest($request, [\PDO::FETCH_COLUMN, 0]);
    }

    /**
     * Select request
     *
     * @param RequestInterface $request
     * @param array $mode
     * @return StorageResultInterface
     */
    protected function selectRequest(RequestInterface $request, array $mode): StorageResultInterface {
        return $this->query($this->getGrammar()->select($request), $mode);
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
        return $this->execute($this->getGrammar()->update($set, $request));
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\StorageInterface::valueToDateTime()
     */
    public function valueToDateTime($value) {
        return $this->getGrammar()->valueToDateTime($value);
    }
}