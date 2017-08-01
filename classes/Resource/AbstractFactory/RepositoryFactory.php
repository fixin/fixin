<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Resource\AbstractFactory;

use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Resource\ResourceManagerInterface;
use Fixin\Support\Types;
use Fixin\Support\Words;

class RepositoryFactory extends AbstractFactory
{
    protected const
        TABLE_LAST_SEPARATOR = '__',
        TABLE_SEPARATOR = '_',
        TAG_SEPARATOR = '.',
        THIS_SETS = parent::THIS_SETS + [
            self::CLASS_PREFIX => Types::STRING,
            self::ENTITY_CACHE => Types::STRING,
            self::KEY_PREFIX => Types::STRING,
            self::STORAGE => Types::STRING
        ];

    public const
        CLASS_PREFIX = 'classPrefix',
        ENTITY_CACHE = 'entityCache',
        KEY_PREFIX = 'keyPrefix',
        STORAGE = 'storage';

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
    protected $keyPrefix;

    /**
     * @var int
     */
    protected $keyPrefixLength;

    /**
     * @var string
     */
    protected $storage;

    public function __construct(ResourceManagerInterface $resourceManager, array $options = null, string $name = null)
    {
        parent::__construct($resourceManager, $options, $name);

        $this->keyPrefixLength = strlen($this->keyPrefix);
    }

    protected function canProduce(string $key): bool
    {
        return strncasecmp($key, $this->keyPrefix, $this->keyPrefixLength) === 0;
    }

    protected function produce(string $key, array $options, string $name)
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
        return $this->next->chainProduce($classPrefix . 'Repository\\' . $basename, $options + [
            RepositoryInterface::NAME => $tableName,
            RepositoryInterface::ENTITY_PROTOTYPE => $classPrefix . 'Entity\\' . $basenameSingular,
            RepositoryInterface::ENTITY_CACHE => $this->entityCache,
            RepositoryInterface::STORAGE => $this->storage
        ], $name);
    }
}
