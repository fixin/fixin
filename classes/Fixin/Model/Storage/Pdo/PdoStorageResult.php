<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
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
        EXCEPTION_REWIND_IS_NOT_ALLOWED = 'Rewind is not allowed',
        MASK_TO_STRING = '%s {' . PHP_EOL . "    Position: %d" . PHP_EOL . "    Count: %d" . PHP_EOL . '}' . PHP_EOL,
        THIS_REQUIRES = [
            self::OPTION_STATEMENT => self::TYPE_INSTANCE
        ];

    public const
        OPTION_STATEMENT = 'statement';

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
        return Ground::debugText(sprintf(static::MASK_TO_STRING, get_class($this), $this->position, $this->count()));
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
            throw new Exception\RuntimeException(static::EXCEPTION_REWIND_IS_NOT_ALLOWED);
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
