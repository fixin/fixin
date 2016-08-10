<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Entity;

use Fixin\Exception\InvalidArgumentException;
use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Resource\Prototype;

class EntitySet extends Prototype implements EntitySetInterface {

    const EXCEPTION_INVALID_ENTITY_SET = "Invalid entity set parameter '%s', EntitySet allowed";
    const THIS_REQUIRES = [
        self::OPTION_REPOSITORY => self::TYPE_INSTANCE
    ];
    const THIS_SETS_LAZY = [
        self::OPTION_REPOSITORY => RepositoryInterface::class
    ];

    /**
     * @var array
     */
    protected $items = [];

    /**
     * @var integer
     */
    protected $position = 0;

    /**
     * @var integer
     */
    protected $prefetchSize = 0;

    /**
     * @var RepositoryInterface|false|null
     */
    protected $repository;

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Entity\EntitySetInterface::append($entitySet)
     */
    public function append(EntitySetInterface $entitySet): EntitySetInterface {
        if ($entitySet instanceof self) {
            $this->items = array_merge($this->items, $entitySet->items);

            return $this;
        }

        throw new InvalidArgumentException(sprintf(static::EXCEPTION_INVALID_ENTITY_SET, get_class($entitySet)));
    }

    /**
     * {@inheritDoc}
     * @see Countable::count()
     */
    public function count() {
        return count($this->items);
    }

    /**
     * {@inheritDoc}
     * @see Iterator::current()
     */
    public function current() {
        $result = $this->items[$this->position] ?? null;

        if ($result instanceof EntityIdInterface) {
            // TODO prefetch

            $result =
            $this->items[key($this->items)] = $result->getEntity();
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Entity\EntitySetInterface::getColumn($name)
     */
    public function getColumn(string $name): array {
        $this->prefetchAll();

        $list = [];
        $getter = 'get' . $name;

        foreach ($this->items as $item) {
            $list[$item->getEntityId()] = $item->{$getter}();
        }

        return $list;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Entity\EntitySetInterface::getEntityIds()
     */
    public function getEntityIds(): array {
        $list = [];

        foreach ($this->items as $item) {
            $list[] = $item instanceof EntityInterface ? $item->getEntityId() : $item;
        }

        return $list;
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
    public function next(): EntitySetInterface {
        $this->position++;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Entity\EntitySetInterface::prefetchAll()
     */
    public function prefetchAll(): EntitySetInterface {
        // TODO: implementation

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see Iterator::rewind()
     */
    public function rewind(): EntitySetInterface {
        $this->position = 0;

        return $this;
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
        shuffle($this->items);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see Iterator::valid()
     */
    public function valid(): bool {
        return isset($this->items[$this->position]);
    }
}