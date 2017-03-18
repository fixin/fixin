<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Model\Storage\Pdo;

use DateTime;
use Fixin\Base\Sentence\SentenceInterface;
use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Model\Request\RequestInterface;
use Fixin\Model\Storage\Exception;
use Fixin\Model\Storage\Grammar\GrammarInterface;
use Fixin\Model\Storage\StorageInterface;
use Fixin\Model\Storage\StorageResultInterface;
use Fixin\Resource\Resource;
use PDO;
use PDOException;
use PDOStatement;

class PdoStorage extends Resource implements StorageInterface
{
    protected const
        EXCEPTION_CONNECTION_ERROR = "Connection error: %s",
        GRAMMAR_CLASS_MASK = 'Model\Storage\Grammar\%sGrammar',
        PROTOTYPE_STORAGE_RESULT = 'Model\Storage\Pdo\PdoStorageResult';

    public const
        OPTION_DSN = 'dsn',
        OPTION_PASSWORD = 'password',
        OPTION_USERNAME = 'username';

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
     * @var PDO
     */
    protected $resource;

    /**
     * @var string
     */
    protected $username;

    /**
     * @throws Exception\RuntimeException
     */
    protected function connect(): void
    {
        if ($this->resource) {
            return;
        }

        try {
            $resource = new PDO($this->dsn, $this->username, $this->password);
            $resource->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $class = ucfirst(strtolower($resource->getAttribute(PDO::ATTR_DRIVER_NAME)));
            $this->grammar = $this->container->get(sprintf(static::GRAMMAR_CLASS_MASK, $class));

            $this->resource = $resource;
            $this->password = null;
        }
        catch (PDOException $e) {
            throw new Exception\RuntimeException(sprintf(static::EXCEPTION_CONNECTION_ERROR, $e->getMessage()));
        }
    }

    public function delete(RequestInterface $request): int
    {
        return $this->execute($this->getGrammar()->delete($request));
    }

    protected function execute(SentenceInterface $sentence): int
    {
        return $this->prepareStatement($sentence)->rowCount();
    }

    protected function getGrammar(): GrammarInterface
    {
        $this->connect();

        return $this->grammar;
    }

    public function getLastInsertValue(): int
    {
        return $this->resource->lastInsertId();
    }

    public function getValueAsDateTime($value): ?DateTime
    {
        return $this->getGrammar()->getValueAsDateTime($value);
    }

    public function insert(RepositoryInterface $repository, array $set): int
    {
        return $this->execute($this->getGrammar()->insert($repository, $set));
    }

    public function insertInto(RepositoryInterface $repository, RequestInterface $request): int
    {
        return $this->execute($this->getGrammar()->insertInto($repository, $request));
    }

    public function insertMultiple(RepositoryInterface $repository, array $rows): int
    {
        return $this->execute($this->getGrammar()->insertMultiple($repository, $rows));
    }

    protected function prepareStatement(SentenceInterface $sentence): PDOStatement
    {
        $statement = $this->resource->prepare($sentence->getText());
        $statement->execute($sentence->getParameters());

        return $statement;
    }

    protected function query(SentenceInterface $sentence, array $mode): StorageResultInterface
    {
        $statement = $this->resource->prepare($sentence->getText());
        call_user_func_array([$statement, 'setFetchMode'], $mode);
        $statement->execute($sentence->getParameters());

        return $this->container->clone(static::PROTOTYPE_STORAGE_RESULT, [
            PdoStorageResult::OPTION_STATEMENT => $statement
        ]);
    }

    public function select(RequestInterface $request): StorageResultInterface
    {
        return $this->selectData($request, [PDO::FETCH_ASSOC]);
    }

    public function selectColumn(RequestInterface $request): StorageResultInterface
    {
        return $this->selectData($request, [PDO::FETCH_COLUMN, 0]);
    }

    protected function selectData(RequestInterface $request, array $mode): StorageResultInterface
    {
        return $this->query($this->getGrammar()->select($request), $mode);
    }

    public function selectExistsValue(RequestInterface $request): bool
    {
        return (bool) $this->prepareStatement($this->getGrammar()->selectExistsValue($request))->fetchColumn();
    }

    protected function setDsn(string $dsn): void
    {
        $this->dsn = $dsn;
    }

    protected function setPassword(string $password): void
    {
        $this->password = $password;
    }

    protected function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function update(array $set, RequestInterface $request): int
    {
        return $this->execute($this->getGrammar()->update($set, $request));
    }
}
