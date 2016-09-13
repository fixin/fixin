<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Storage\Pdo;

use Fixin\Exception\RuntimeException;
use Fixin\Model\Storage\StorageResultInterface;
use Fixin\Resource\Prototype;
use Fixin\Support\Ground;

class PdoStorageResult extends Prototype implements StorageResultInterface {
    const
        EXCEPTION_REWIND_IS_NOT_ALLOWED = 'Rewind is not allowed',
        MASK_TO_STRING = '%s {' . PHP_EOL . "    Position: %d" . PHP_EOL . "    Count: %d" . PHP_EOL . '}' . PHP_EOL,
        OPTION_STATEMENT = 'statement',
        THIS_REQUIRES = [
            self::OPTION_STATEMENT => self::TYPE_INSTANCE
        ]
    ;

    /**
     * @var mixed
     */
    protected $currentData;

    /**
     * @var bool
     */
    protected $currentFetched = false;

    /**
     * @var integer
     */
    protected $position = 0;

    /**
     * @var \PDOStatement
     */
    protected $statement;

    public function __toString(): string {
        return Ground::debugText(sprintf(static::MASK_TO_STRING, get_class($this), $this->position, $this->count()));
    }

    /**
     * {@inheritDoc}
     * @see Countable::count()
     */
    public function count(): int {
        return $this->statement->rowCount();
    }

    /**
     * {@inheritDoc}
     * @see Iterator::current()
     */
    public function current() {
        if (!$this->currentFetched) {
            $this->currentData = $this->statement->fetch();
            $this->currentFetched = true;
        }

        return $this->currentData;
    }

    /**
     * {@inheritDoc}
     * @see Iterator::key()
     */
    public function key() {
        return $this->position;
    }

    /**
     * {@inheritDoc}
     * @see Iterator::next()
     */
    public function next() {
        $this->position++;
        $this->currentFetched = false;
    }

    /**
     * {@inheritDoc}
     * @see Iterator::rewind()
     */
    public function rewind() {
        if ($this->position > 0) {
            throw new RuntimeException(static::EXCEPTION_REWIND_IS_NOT_ALLOWED);
        }

        $this->currentData = $this->statement->fetch();
        $this->currentFetched = true;
    }

    /**
     * Set statement
     *
     * @param \PDOStatement $statement
     */
    protected function setStatement(\PDOStatement $statement) {
        $this->statement = $statement;
    }

    /**
     * {@inheritDoc}
     * @see Iterator::valid()
     */
    public function valid() {
        return $this->currentData !== false;
    }
}