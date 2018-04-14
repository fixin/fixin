<?php
/**
 * /Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Model\Repository;

use Fixin\Base\Dictionary\DictionaryInterface;
use Fixin\Model\Entity\EntityIdInterface;
use Fixin\Model\Entity\EntityInterface;
use Fixin\Model\Entity\EntitySetInterface;
use Fixin\Model\Request\RequestInterface;
use Fixin\Support\Arrays;
use Generator;

class CachedRepository extends Repository
{
    public const
        CACHE = 'cache',
        PREFIX = 'prefix';

    protected const
        THIS_SETS = parent::THIS_SETS + [
            self::CACHE => [self::LAZY_LOADING => DictionaryInterface::class],
            self::PREFIX => self::USING_SETTER
        ];

    /**
     * @var DictionaryInterface
     */
    protected $cache;

    /**
     * @var string
     */
    protected $currentPrefix;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * @var int
     */
    protected $prefixCounter = 0;

    /**
     * @inheritDoc
     */
    public function delete(RequestInterface $request): int
    {
        $this->invalidateCache();

        return parent::delete($request);
    }

    /**
     * @inheritDoc
     */
    public function deleteByIds(array $ids): int
    {
        $this->cache->deleteMultiple(array_map([$this, 'makeKeyForId'], $ids));

        $request = $this->createRequest();
        $request->getWhere()->ids($ids);

        return parent::delete($request);
    }

    /**
     * @inheritDoc
     */
    public function getByIds(array $ids): EntitySetInterface
    {
        $keys = array_map([$this, 'makeKeyForId'], $ids);
        $idMap = array_combine($keys, $ids);

        $entities = $this->cache->getMultiple($keys);
        foreach ($entities as $entity) {
            // TODO: set resource manager
            unset($idMap[$this->makeKeyForId($entity->getEntityId())]);
        }

        return $this->resourceManager->clone(static::ENTITY_SET_PROTOTYPE, EntitySetInterface::class, [
            EntitySetInterface::ITEMS => array_merge($entities, array_values($idMap))
        ]);
    }

    /**
     * Invalidate cache
     */
    protected function invalidateCache(): void
    {
        $this->prefixCounter++;
        $this->currentPrefix = $this->prefix . '.' . $this->prefixCounter . '.';
    }

    /**
     * @inheritDoc
     */
    protected function iterateEntities(RequestInterface $request): Generator
    {
        echo 'cacheRepository.iterateEntities started', PHP_EOL;

        $entityPrototype = $this->getEntityPrototype();

        foreach ($this->selectRawData((clone $request)->setColumns([])) as $item) {
            echo "cacheRepository.iterateEntities foreach: ", print_r($item), PHP_EOL;

            $key = $this->makeKeyForId($this->createId(Arrays::intersectByKeyList($item, $this->primaryKey)));

            if ($entity = $this->cache->get($key)) {
                // TODO: set resource manager
                yield $entity;

                continue;
            }

            $entity = (clone $entityPrototype)->exchangeArray($item);
            $this->cache->set($key, $entity);

            yield $entity;
        }
    }

    /**
     * Make key for the dictionary
     *
     * @param EntityIdInterface $id
     * @return string
     */
    protected function makeKeyForId(EntityIdInterface $id): string
    {
        return $this->currentPrefix . $id;
    }

    /**
     * @inheritDoc
     */
    public function save(EntityInterface $entity): EntityIdInterface
    {
        if ($oldId = $entity->getEntityId()) {
            $this->cache->delete($this->makeKeyForId($oldId));
        }

        $newId = parent::save($entity);

        $this->cache->set($this->makeKeyForId($newId), $entity);

        return $newId;
    }

    /**
     * Set prefix
     *
     * @param string $prefix
     */
    protected function setPrefix(string $prefix): void
    {
        $this->prefix = $prefix;

        $this->invalidateCache();
    }

    /**
     * @inheritDoc
     */
    public function update(array $set, RequestInterface $request): int
    {
        $this->invalidateCache();

        return parent::update($set, $request);
    }
}
