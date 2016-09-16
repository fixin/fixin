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

    const THIS_SETS_LAZY = [
        self::OPTION_ENTITY_CACHE => EntityCacheInterface::class,
        self::OPTION_REPOSITORY => RepositoryInterface::class
    ];

    /**
     * @var EntityCacheInterface
     */
    protected $entityCache;

    /**
     * @var int
     */
    protected $fetchPosition = 0;

    /**
     * @var bool
     */
    protected $idFetchMode = false;

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
    protected $prefetchSize = 1;

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
        $this->prefetch();

        return $this->items[$this->position] ?? null;
    }

    /**
     * Fetch entities until position
     *
     * @param int $position
     */
    protected function fetchEntitiesUntil(int $position) {
        while ($this->fetchPosition <= $position) {
            $this->items[$this->fetchPosition] = $this->getEntityCache()->fetchResultEntity($this->storageResult);

            $this->fetchPosition++;
        }
    }

    /**
     * Fetch IDs until position
     *
     * @param int $position
     */
    protected function fetchIdsUntil(int $position) {
        while ($this->fetchPosition <= $position) {
            $this->items[$this->fetchPosition] = $this->getRepository()->createId($this->storageResult->current());
            $this->storageResult->next();

            $this->fetchPosition++;
        }
    }

    /**
     * Fetch data until position
     *
     * @param int $position
     */
    protected function fetchUntil(int $position) {
        $position = min($this->itemCount - 1, $position);

        // ID fetch mode
        if ($this->idFetchMode) {
            $this->fetchIdsUntil($position);

            return;
        }

        $this->fetchEntitiesUntil($position);
    }

    /**
     * Get entity cache
     *
     * @return EntityCacheInterface
     */
    protected function getEntityCache(): EntityCacheInterface {
        return $this->entityCache ?: $this->loadLazyProperty(static::OPTION_ENTITY_CACHE);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Entity\EntitySetInterface::getEntityIds()
     */
    public function getEntityIds(): array {
        $this->fetchUntil($this->itemCount - 1);

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

        $this->prefetch();
    }

    /**
     * Prefetch
     */
    protected function prefetch() {
        if ($this->position < $this->itemCount) {
            if ($this->fetchPosition <= $this->position) {
                $this->fetchUntil($this->position + $this->prefetchSize - 1);
            }

            if ($this->items[$this->position] instanceof EntityIdInterface) {
                $this->prefetchBlock($this->position, $this->prefetchSize);
            }
        }
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Entity\EntitySetInterface::prefetchAll()
     */
    public function prefetchAll(): EntitySetInterface {
        $this->fetchUntil($this->itemCount - 1);
        $this->prefetchBlock(0, $this->itemCount);

        return $this;
    }

    /**
     * Prefetch block of items
     *
     * @param int $offset
     * @param int $length
     */
    protected function prefetchBlock(int $offset, int $length) {
        $length = min($length, $this->itemCount - $offset);
        $ids = [];

        $p = $offset;
        while ($length) {
            $item = $this->items[$p];
            if ($item instanceof EntityInterface) {
                break;
            }

            $ids[] = $item;
            $length--;
            $p++;
        }

        if ($ids) {
            array_splice($this->items, $offset, count($ids), $this->getEntityCache()->getByIds($ids));
        }
    }

    /**
     * {@inheritDoc}
     * @see Iterator::rewind()
     */
    public function rewind() {
        $this->position = 0;

        $this->prefetch();
    }

    /**
     * Set id fetch mode
     *
     * @param bool $idFetchMode
     */
    protected function setIdFetchMode(bool $idFetchMode) {
        $this->idFetchMode = $idFetchMode;
    }

    /**
     * Set items
     *
     * @param array $items
     */
    protected function setItems(array $items) {
        $this->items = $items;
        $this->itemCount = count($items);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Entity\EntitySetInterface::setPrefetchSize($prefetchSize)
     */
    public function setPrefetchSize(int $prefetchSize): EntitySetInterface {
        $this->prefetchSize = $prefetchSize ?: 1;

        return $this;
    }

    /**
     * Set storage result
     *
     * @param StorageResultInterface $storageResult
     */
    protected function setStorageResult(StorageResultInterface $storageResult) {
        $this->storageResult = $storageResult;
        $this->items = [];
        $this->itemCount = $storageResult->count();
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Entity\EntitySetInterface::shuffle()
     */
    public function shuffle(): EntitySetInterface {
        $this->fetchUntil($this->itemCount - 1);

        shuffle($this->items);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see Iterator::valid()
     */
    public function valid(): bool {
        $this->prefetch();

        return isset($this->items[$this->position]);
    }
}