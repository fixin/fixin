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
    protected const
        REWIND_IS_NOT_ALLOWED_EXCEPTION = 'Rewind is not allowed',
        THIS_SETS = [
            self::STATEMENT => self::USING_SETTER
        ],
        TO_STRING_MASK = '%s {' . PHP_EOL . "    Position: %d" . PHP_EOL . "    Count: %d" . PHP_EOL . '}' . PHP_EOL;

    public const
        STATEMENT = 'statement';

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

    public function __toString(): string
    {
        return Ground::toDebugText(sprintf(static::TO_STRING_MASK, get_class($this), $this->position, $this->count()));
    }

    public function count(): int
    {
        return $this->statement->rowCount();
    }

    public function current()
    {
        $this->prefetch();

        return $this->currentData;
    }

    public function key(): int
    {
        return $this->position;
    }

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

    public function rewind(): void
    {
        if ($this->position > 0) {
            throw new Exception\RuntimeException(static::REWIND_IS_NOT_ALLOWED_EXCEPTION);
        }

        $this->prefetch();
    }

    protected function setStatement(PDOStatement $statement): void
    {
        $this->statement = $statement;
        $this->currentFetched = false;
    }

    public function valid(): bool
    {
        $this->prefetch();

        return $this->currentData !== false;
    }
}
