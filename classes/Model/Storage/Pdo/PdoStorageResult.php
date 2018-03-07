<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Model\Storage\Pdo;

use Fixin\Model\Storage\Exception;
use Fixin\Model\Storage\StorageResultInterface;
use Fixin\Resource\Prototype;
use Fixin\Support\Ground;
use PDOStatement;

class PdoStorageResult extends Prototype implements StorageResultInterface
{
    public const
        STATEMENT = 'statement';

    protected const
        REWIND_IS_NOT_ALLOWED_EXCEPTION = 'Rewind is not allowed',
        THIS_SETS = [
            self::STATEMENT => self::USING_SETTER
        ],
        TO_STRING_MASK = '%s {' . PHP_EOL . "    Position: %d" . PHP_EOL . "    Count: %d" . PHP_EOL . '}' . PHP_EOL;

    /**
     * @var mixed
     */
    protected $currentData;

    /**
     * @var bool
     */
    protected $currentFetched = false;

    /**
     * @var int
     */
    protected $position = 0;

    /**
     * @var PDOStatement
     */
    protected $statement;

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return Ground::toDebugBlock(sprintf(static::TO_STRING_MASK, get_class($this), $this->position, $this->count()));
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return $this->statement->rowCount();
    }

    /**
     * @inheritDoc
     */
    public function current()
    {
        $this->prefetch();

        return $this->currentData;
    }

    /**
     * @inheritDoc
     */
    public function key(): int
    {
        return $this->position;
    }

    /**
     * @inheritDoc
     */
    public function next(): void
    {
        $this->position++;
        $this->currentFetched = false;

        $this->prefetch();
    }

    /**
     * Prefetch data
     */
    protected function prefetch(): void
    {
        if (!$this->currentFetched) {
            $this->currentData = $this->statement->fetch();
            $this->currentFetched = true;
        }
    }

    /**
     * @inheritDoc
     */
    public function rewind(): void
    {
        if ($this->position > 0) {
            throw new Exception\RuntimeException(static::REWIND_IS_NOT_ALLOWED_EXCEPTION);
        }

        $this->prefetch();
    }

    /**
     * Set statement
     *
     * @param PDOStatement $statement
     */
    protected function setStatement(PDOStatement $statement): void
    {
        $this->statement = $statement;
        $this->currentFetched = false;
    }

    /**
     * @inheritDoc
     */
    public function valid(): bool
    {
        $this->prefetch();

        return $this->currentData !== false;
    }
}
