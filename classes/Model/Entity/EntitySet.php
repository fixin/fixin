<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Model\Entity;

use Fixin\Resource\Prototype;

class EntitySet extends Prototype implements EntitySetInterface
{
    protected const
        INVALID_PREFETCH_SIZE_EXCEPTION = 'Invalid prefetch size',
        ITEMS_AND_ITERATOR_PRESENT_EXCEPTION = 'Items and iterator present',
        THIS_SETS = [
            self::ITEMS => self::USING_SETTER,
            self::ITERATOR => self::USING_SETTER,
            self::PREFETCH_SIZE => self::USING_SETTER
        ];

    /**
     * @var int|null
     */
    protected $count;

    /**
     * @var bool
     */
    protected $fetchInProgress = true;

    /**
     * @var EntityInterface[]|EntityIdInterface[]
     */
    protected $items = [];

    /**
     * @var \Iterator|null
     */
    protected $iterator;

    /**
     * @var int
     */
    protected $position = 0;

    /**
     * @var int
     */
    protected $prefetchSize = 1;

    /**
     * @inheritDoc
     */
    public function count()
    {
        return $this->count
            ?? function () {
                if ($this->fetchInProgress) {
                    $this->prefetchAllItems();
                }

                return $this->count = count($this->items);
            };
    }

    /**
     * @inheritDoc
     */
    public function current()
    {
        if ($this->fetchInProgress) {
            $this->prefetchItems();
        }

        $item = $this->items[$this->position] ?? null;

        if ($item instanceof EntityIdInterface) {
            $this->prefetchEntities();

            return $this->items[$this->position];
        }

        return $item;
    }

    /**
     * @inheritDoc
     */
    public function getEntityIds(): array
    {
        if ($this->fetchInProgress) {
            $this->prefetchAllItems();
        }

        $items = [];
        foreach ($this->items as $item) {
            $items[] = $item instanceof EntityInterface ? $item->getEntityId() : $item;
        }

        return $items;
    }

    /**
     * @inheritDoc
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * @inheritDoc
     */
    public function next()
    {
        $this->position++;
    }

    /**
     * Prefetch all items
     */
    protected function prefetchAllItems(): void
    {
        while ($this->iterator->valid()) {
            $this->items[] = $this->iterator->current();

            $this->iterator->next();
        }

        $this->fetchInProgress = false;
    }

    /**
     * Prefetch entities
     */
    protected function prefetchEntities(): void
    {
        $ids = $this->prefetchEntityIds();

        $entities = [];
        foreach ($ids[0]->getRepository()->getByIds($ids) as $entity) {
            $entities[$entity->getEntityId()] = $entity;
        }

        $position = $this->position;
        foreach ($ids as $id) {
            $this->items[$position] = $entities[$id];
            $position++;
        }
    }

    /**
     * Prefetch entity IDs
     *
     * @return EntityIdInterface[]
     */
    protected function prefetchEntityIds(): array
    {
        $repository = $this->items[$this->position]->getRepository();

        $position = $this->position + 1;
        $before = min($this->position + $this->prefetchSize, count($this->items));

        while ($position < $before) {
            $item = $this->items[$position];

            if (!$item instanceof EntityIdInterface || $item->getRepository() !== $repository) {
                break;
            }

            $position++;
        }

        return array_slice($this->items, $this->position, $position - $this->position);
    }

    /**
     * Prefetch items
     */
    protected function prefetchItems(): void
    {
        $count = $this->prefetchSize;

        while ($count && $this->iterator->valid()) {
            $this->items[] = $this->iterator->current();

            $this->iterator->next();
            $count--;
        }

        $this->fetchInProgress = $this->iterator->valid();
    }

    /**
     * @inheritDoc
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * Set items
     *
     * @param array $items
     * @throws Exception\InvalidArgumentException
     */
    protected function setItems(array $items): void
    {
        if (isset($this->iterator)) {
            throw new Exception\InvalidArgumentException(static::ITEMS_AND_ITERATOR_PRESENT_EXCEPTION);
        }

        $this->items = $items;
        $this->count = count($items);
        $this->fetchInProgress = false;
    }

    /**
     * Set iterator
     *
     * @param \Iterator $iterator
     * @throws Exception\InvalidArgumentException
     */
    protected function setIterator(\Iterator $iterator): void
    {
        if (isset($this->items)) {
            throw new Exception\InvalidArgumentException(static::ITEMS_AND_ITERATOR_PRESENT_EXCEPTION);
        }

        $this->iterator = $iterator;

        if ($iterator instanceof \Countable) {
            $this->count = $iterator->count();
        }
    }

    /**
     * Set prefetch size
     *
     * @param int $prefetchSize
     * @throws Exception\InvalidArgumentException
     */
    protected function setPrefetchSize(int $prefetchSize): void
    {
        if ($prefetchSize < 1) {
            throw new Exception\InvalidArgumentException(static::INVALID_PREFETCH_SIZE_EXCEPTION);
        }

        $this->prefetchSize = $prefetchSize;
    }

    /**
     * @inheritDoc
     */
    public function shuffle(): EntitySetInterface
    {
        if ($this->fetchInProgress) {
            $this->prefetchAllItems();
        }

        shuffle($this->items);

        $this->position = 0;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function valid()
    {
        if ($this->fetchInProgress) {
            $this->prefetchItems();
        }

        return isset($this->items[$this->position]);
    }
}
