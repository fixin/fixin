<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Resource\AbstractFactory;

use Fixin\Model\Repository\CachedRepository;
use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Resource\ResourceManagerInterface;
use Fixin\Support\Types;
use Fixin\Support\Words;

class RepositoryFactory extends AbstractFactory
{
    public const
        CLASS_PREFIX = 'classPrefix',
        ENTITY_CACHE = 'entityCache',
        ENTITY_CACHE_PREFIX = 'entityCachePrefix',
        KEY_PREFIX = 'keyPrefix',
        STORAGE = 'storage';

    protected const
        TABLE_LAST_SEPARATOR = '__',
        TABLE_SEPARATOR = '_',
        TAG_SEPARATOR = '.',
        THIS_SETS = parent::THIS_SETS + [
            self::CLASS_PREFIX => Types::STRING,
            self::ENTITY_CACHE => Types::STRING,
            self::ENTITY_CACHE_PREFIX => Types::STRING,
            self::KEY_PREFIX => Types::STRING,
            self::STORAGE => Types::STRING
        ];

    /**
     * @var string
     */
    protected $classPrefix;

    /**
     * @var string
     */
    protected $entityCache;

    /**
     * @var string
     */
    protected $entityCachePrefix = 'entity.';

    /**
     * @var string
     */
    protected $keyPrefix;

    /**
     * @var int
     */
    protected $keyPrefixLength;

    /**
     * @var string
     */
    protected $storage;

    /**
     * @inheritDoc
     */
    public function __construct(ResourceManagerInterface $resourceManager, array $options)
    {
        parent::__construct($resourceManager, $options);

        $this->keyPrefixLength = strlen($this->keyPrefix);
    }

    /**
     * @inheritDoc
     */
    protected function canProduce(string $key): bool
    {
        return strncasecmp($key, $this->keyPrefix, $this->keyPrefixLength) === 0;
    }

    /**
     * @inheritDoc
     */
    protected function produce(string $key, array $options)
    {
        // CamelCase tags
        $length = strlen($key);
        $index = $this->keyPrefixLength;
        $needBig = true;
        while ($index < $length) {
            $ch = $key[$index];
            if ($needBig && ctype_lower($ch)) {
                $key[$index] = strtoupper($ch);
            }

            $needBig = $ch === static::TAG_SEPARATOR;
            $index++;
        }

        // Basename
        $separatorPosition = strrpos($key, static::TAG_SEPARATOR, $this->keyPrefixLength) ?: $this->keyPrefixLength - 1;

        $basename = substr($key, $separatorPosition + 1);
        $basenameSingular = Words::toSingular($basename);
        $tableName = $basenameSingular;
        $classPrefix = $this->classPrefix;

        // Prefix
        if ($separatorPosition > $this->keyPrefixLength) {
            $prefix = substr($key, $this->keyPrefixLength, $separatorPosition - $this->keyPrefixLength);

            $classPrefix .= strtr($prefix, static::TAG_SEPARATOR, '\\') . '\\';
            $tableName = strtr($prefix, static::TAG_SEPARATOR, static::TABLE_SEPARATOR) . static::TABLE_LAST_SEPARATOR . $tableName;
        }

        // Chaining
        $className = $classPrefix . 'Repository\\' . $basename;

        $options += [
            RepositoryInterface::NAME => $tableName,
            RepositoryInterface::ENTITY_PROTOTYPE => $classPrefix . 'Entity\\' . $basenameSingular,
            RepositoryInterface::STORAGE => $this->storage
        ];

        if (is_subclass_of($className, CachedRepository::class)) {
            $options + [
                CachedRepository::CACHE => $this->entityCache,
                CachedRepository::PREFIX => $this->entityCachePrefix . $tableName
            ];
        }

        return $this->next->chainProduce($className, $options);
    }
}
