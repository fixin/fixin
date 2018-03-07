<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Model\Storage\Pdo;

use DateTimeImmutable;
use Fixin\Base\Sentence\SentenceInterface;
use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Model\Request\RequestInterface;
use Fixin\Model\Storage\Exception;
use Fixin\Model\Storage\Grammar\GrammarInterface;
use Fixin\Model\Storage\StorageInterface;
use Fixin\Model\Storage\StorageResultInterface;
use Fixin\Resource\Resource;
use Fixin\Support\Types;
use PDO;
use PDOException;
use PDOStatement;

class PdoStorage extends Resource implements StorageInterface
{
    public const
        DSN = 'dsn',
        PASSWORD = 'password',
        USERNAME = 'username';

    protected const
        CONNECTION_ERROR_EXCEPTION = "Connection error: %s",
        GRAMMAR_CLASS_MASK = '*\Model\Storage\Grammar\%sGrammar',
        STORAGE_RESULT_PROTOTYPE = '*\Model\Storage\Pdo\PdoStorageResult',
        THIS_SETS = [
            self::DSN => Types::STRING,
            self::PASSWORD => [Types::STRING, Types::NULL],
            self::USERNAME => [Types::STRING, Types::NULL],
        ];

    /**
     * @var string
     */
    protected $dsn;

    /**
     * @var GrammarInterface
     */
    protected $grammar;

    /**
     * @var string|null
     */
    protected $password;

    /**
     * @var PDO
     */
    protected $resource;

    /**
     * @var string|null
     */
    protected $username;

    /**
     * Connect
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
            $this->grammar = $this->resourceManager->get(sprintf(static::GRAMMAR_CLASS_MASK, $class), GrammarInterface::class);

            $this->resource = $resource;
        }
        catch (PDOException $e) {
            throw new Exception\ConnectionErrorException(sprintf(static::CONNECTION_ERROR_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @inheritDoc
     */
    public function delete(RequestInterface $request): int
    {
        return $this->execute($this->getGrammar()->delete($request));
    }

    /**
     * Execute
     *
     * @param SentenceInterface $sentence
     * @return int
     */
    protected function execute(SentenceInterface $sentence): int
    {
        return $this->prepareStatement($sentence)->rowCount();
    }

    /**
     * Get grammar
     *
     * @return GrammarInterface
     */
    protected function getGrammar(): GrammarInterface
    {
        $this->connect();

        return $this->grammar;
    }

    /**
     * @inheritDoc
     */
    public function getLastInsertValue(): int
    {
        return $this->resource->lastInsertId();
    }

    /**
     * @inheritDoc
     */
    public function insert(RepositoryInterface $repository, array $set): int
    {
        return $this->execute($this->getGrammar()->insert($repository, $set));
    }

    /**
     * @inheritDoc
     */
    public function insertInto(RepositoryInterface $repository, RequestInterface $request): int
    {
        return $this->execute($this->getGrammar()->insertInto($repository, $request));
    }

    /**
     * @inheritDoc
     */
    public function insertMultiple(RepositoryInterface $repository, array $rows): int
    {
        return $this->execute($this->getGrammar()->insertMultiple($repository, $rows));
    }

    /**
     * Prepare statement
     *
     * @param SentenceInterface $sentence
     * @return PDOStatement
     */
    protected function prepareStatement(SentenceInterface $sentence): PDOStatement
    {
        $statement = $this->resource->prepare($sentence->getText());
        $statement->execute($sentence->getParameters());

        return $statement;
    }

    /**
     * Query data
     *
     * @param SentenceInterface $sentence
     * @param array $mode
     * @return StorageResultInterface
     */
    protected function query(SentenceInterface $sentence, array $mode): StorageResultInterface
    {
        $statement = $this->resource->prepare($sentence->getText());
        call_user_func_array([$statement, 'setFetchMode'], $mode);
        $statement->execute($sentence->getParameters());

        return $this->resourceManager->clone(static::STORAGE_RESULT_PROTOTYPE, StorageResultInterface::class, [
            PdoStorageResult::STATEMENT => $statement
        ]);
    }

    /**
     * @inheritDoc
     */
    public function select(RequestInterface $request): StorageResultInterface
    {
        return $this->selectData($request, [PDO::FETCH_ASSOC]);
    }

    /**
     * @inheritDoc
     */
    public function selectColumn(RequestInterface $request): StorageResultInterface
    {
        return $this->selectData($request, [PDO::FETCH_COLUMN, 0]);
    }

    /**
     * @inheritDoc
     */
    protected function selectData(RequestInterface $request, array $mode): StorageResultInterface
    {
        return $this->query($this->getGrammar()->select($request), $mode);
    }

    /**
     * @inheritDoc
     */
    public function selectExistsValue(RequestInterface $request): bool
    {
        return (bool) $this->prepareStatement($this->getGrammar()->selectExistsValue($request))->fetchColumn();
    }

    /**
     * @inheritDoc
     */
    public function toDateTime($value): ?DateTimeImmutable
    {
        return $this->getGrammar()->toDateTime($value);
    }

    /**
     * @inheritDoc
     */
    public function update(array $set, RequestInterface $request): int
    {
        return $this->execute($this->getGrammar()->update($set, $request));
    }
}
