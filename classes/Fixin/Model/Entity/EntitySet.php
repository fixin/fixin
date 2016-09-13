<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Entity;

use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Model\Storage\StorageResultInterface;
use Fixin\Resource\Prototype;

class EntitySet extends Prototype implements EntitySetInterface {

    const THIS_REQUIRES = [
        self::OPTION_REPOSITORY => self::TYPE_INSTANCE,
        self::OPTION_ID_FETCH => self::TYPE_BOOL
    ];

    const THIS_SETS_LAZY = [
        self::OPTION_REPOSITORY => RepositoryInterface::class
    ];

    /**
     * @var int
     */
    protected $fetchPosition = 0;

    /**
     * @var bool
     */
    protected $idFetch = false;

    /**
     * @var int
     */
    protected $itemCount = 0;

    /**
     * @var array
     */
    protected $items = [];

    /**
     * @var int
     */
    protected $position = 0;

    /**
     * @var int
     */
    protected $prefetchSize = 0;

    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @var StorageResultInterface
     */
    protected $storageResult;

    /**
     * {@inheritDoc}
     * @see Countable::count()
     */
    public function count(): int {
        return $this->itemCount;
    }

    /**
     * {@inheritDoc}
     * @see Iterator::current()
     */
    public function current() {
        $this->fetchUntil($this->position);

        $result = $this->items[$this->position] ?? null;

        if ($result instanceof EntityIdInterface) {
            $this->prefetchNextChunk();

            return $this->items[$this->position];
        }

        return $result;
    }

    /**
     * Fetch all data
     */
    protected function fetchAll() {
        $this->fetchUntil($this->itemCount);
    }

    /**
     * Fetch IDs until position
     *
     * @param int $position
     */
    protected function fetchIdsUntil(int $position) {
        while ($this->fetchPosition <= $position) {
            $items[$this->fetchPosition] = $this->getRepository()->createId($this->storageResult->current());
            $this->fetchPosition++;

            $this->storageResult->next();
        }
    }

    /**
     * Fetch data until position
     *
     * @param int $position
     */
    protected function fetchUntil(int $position) {
        $position = min($this->itemCount - 1, $position);

        // Already fetched
        if ($position < $this->fetchPosition) {
            return;
        }

        // ID fetch mode
        if ($this->idFetch) {
            $this->fetchIdsUntil($position);

            return;
        }
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Entity\EntitySetInterface::getEntityIds()
     */
    public function getEntityIds(): array {
        $this->fetchAll();

        return array_map(function($item) {
            return $item instanceof EntityInterface ? $item->getEntityId() : $item;
        }, $this->items);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Entity\EntitySetInterface::getPrefetchSize()
     */
    public function getPrefetchSize(): int {
        return $this->prefetchSize;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Entity\EntitySetInterface::getRepository()
     */
    public function getRepository(): RepositoryInterface {
        return $this->repository ?: $this->loadLazyProperty(static::OPTION_REPOSITORY);
    }

    /**
     * {@inheritDoc}
     * @see Iterator::key()
     */
    public function key(): int {
        return $this->position;
    }

    /**
     * {@inheritDoc}
     * @see Iterator::next()
     */
    public function next() {
        $this->position++;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Entity\EntitySetInterface::prefetchAll()
     */
    public function prefetchAll(): EntitySetInterface {
        $this->prefetchBlock(0, $this->itemCount - 1);

        return $this;
    }

    /**
     * Prefetch block of items
     *
     * @param int $offset
     * @param int $length
     */
    protected function prefetchBlock(int $offset, int $length) {
        $this->fetchUntil($offset + $length - 1);

        $entities = $this->getRepository()->fetchEntities(array_filter(array_slice($this->items, $offset, $length), function($item) {
            return $item instanceof EntityIdInterface;
        }));

        // TODO: implementation
    }

    /**
     * Prefecth next chunk
     */
    protected function prefetchNextChunk() {
        $this->prefetchBlock($this->position, $this->prefetchSize);
    }

    /**
     * {@inheritDoc}
     * @see Iterator::rewind()
     */
    public function rewind() {
        $this->position = 0;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Entity\EntitySetInterface::setPrefetchSize($prefetchSize)
     */
    public function setPrefetchSize(int $prefetchSize): EntitySetInterface {
        $this->prefetchSize = $prefetchSize;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Entity\EntitySetInterface::shuffle()
     */
    public function shuffle(): EntitySetInterface {
        $this->fetchAll();

        shuffle($this->items);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see Iterator::valid()
     */
    public function valid(): bool {
        $this->fetchUntil($this->position);

        return isset($this->items[$this->position]);
    }
}