<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Model\Entity;

use Fixin\Model\Entity\Cache\CacheInterface;
use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Model\Storage\StorageResultInterface;
use Fixin\Resource\Prototype;
use Fixin\Support\Types;

class EntitySet extends Prototype implements EntitySetInterface
{
    protected const
        THIS_SETS = [
            self::ENTITY_CACHE => [self::LAZY_LOADING => CacheInterface::class, Types::NULL],
            self::ID_FETCH_MODE => Types::BOOL,
            self::ITEMS => self::USING_SETTER,
            self::PREFETCH_SIZE => self::USING_SETTER,
            self::REPOSITORY => [self::LAZY_LOADING => RepositoryInterface::class, Types::NULL],
            self::STORAGE_RESULT => [self::USING_SETTER, Types::NULL]
        ];

    /**
     * @var CacheInterface|false|null
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
     * @var RepositoryInterface|false|null
     */
    protected $repository;

    /**
     * @var StorageResultInterface
     */
    protected $storageResult;

    public function count(): int
    {
        return $this->itemCount;
    }

    public function current(): ?EntityInterface
    {
        $this->prefetch();

        return $this->items[$this->position] ?? null;
    }

    protected function fetchEntitiesUntil(int $position): void
    {
        while ($this->fetchPosition <= $position) {
            $this->items[$this->fetchPosition] = $this->getEntityCache()->fetchResultEntity($this->storageResult);

            $this->fetchPosition++;
        }
    }

    protected function fetchIdsUntil(int $position): void
    {
        while ($this->fetchPosition <= $position) {
            $this->items[$this->fetchPosition] = $this->getRepository()->createId($this->storageResult->current());
            $this->storageResult->next();

            $this->fetchPosition++;
        }
    }

    protected function fetchUntil(int $position): void
    {
        $position = min($this->itemCount - 1, $position);

        // ID fetch mode
        if ($this->idFetchMode) {
            $this->fetchIdsUntil($position);

            return;
        }

        $this->fetchEntitiesUntil($position);
    }

    protected function getEntityCache(): CacheInterface
    {
        return $this->entityCache ?: $this->loadLazyProperty(static::ENTITY_CACHE);
    }

    public function getEntityIds(): array
    {
        $this->fetchUntil($this->itemCount - 1);

        $items = [];

        foreach ($this->items as $item) {
            $items[] = $item instanceof EntityInterface ? $item->getEntityId() : $item;
        }

        return $items;
    }

    public function getRepository(): RepositoryInterface
    {
        return $this->repository ?: $this->loadLazyProperty(static::REPOSITORY);
    }

    public function key(): int
    {
        return $this->position;
    }

    public function next(): void
    {
        $this->position++;

        $this->prefetch();
    }

    protected function prefetch(): void
    {
        if ($this->position < $this->itemCount) {
            if ($this->fetchPosition <= $this->position) {
                $this->fetchUntil($this->position + $this->prefetchSize - 1);
            }

            if ($this->items[$this->position] instanceof EntityIdInterface) {
                $this->prefetchBlock($this->position, $this->prefetchSize);
            }
        }
    }

    protected function prefetchAll(): void
    {
        $this->fetchUntil($this->itemCount - 1);
        $this->prefetchBlock(0, $this->itemCount);
    }

    /**
     * Prefetch block of items
     */
    protected function prefetchBlock(int $offset, int $length): void
    {
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

    public function rewind(): void
    {
        $this->position = 0;

        $this->prefetch();
    }

    protected function setItems(array $items): void
    {
        $this->items = $items;
        $this->itemCount = count($items);
    }

    protected function setPrefetchSize(int $prefetchSize): void
    {
        $this->prefetchSize = $prefetchSize ?: 1;
    }

    protected function setStorageResult(StorageResultInterface $storageResult): void
    {
        $this->storageResult = $storageResult;
        $this->items = [];
        $this->itemCount = $storageResult->count();
    }

    /**
     * @return $this
     */
    public function shuffle(): EntitySetInterface
    {
        $this->fetchUntil($this->itemCount - 1);

        shuffle($this->items);

        return $this;
    }

    public function valid(): bool
    {
        $this->prefetch();

        return isset($this->items[$this->position]);
    }
}
