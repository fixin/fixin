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

class PdoStorageResult extends Prototype implements StorageResultInterface {

    const
        EXCEPTION_REWIND_IS_NOT_ALLOWED = 'Rewind is not allowed',
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

    /**
     * {@inheritDoc}
     * @see Countable::count()
     */
    public function count() {
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
        $this->currentData = $this->statement->fetch();
        $this->currentFetched = true;
        $this->position++;

        return $this->currentData;
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