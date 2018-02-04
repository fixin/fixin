<?php
/**
 * /Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Dictionary;

use Fixin\Base\Dictionary\Exception;
use Fixin\Resource\Resource;
use Fixin\Resource\ResourceManagerInterface;
use Fixin\Support\Types;

class MemcachedDictionary extends Resource implements DictionaryInterface
{
    protected const
        MISSING_SERVER_PARAMETER_EXCEPTION = "Missing '%s' server parameter exception for '%s'",
        THIS_SETS = [
            self::PERSISTENT_ID => [Types::STRING, Types::NULL],
            self::SERVERS => [Types::ARRAY]
        ];

    public const
        HOST = 'host',
        PERSISTENT_ID = 'persistentId',
        PORT = 'port',
        SERVERS = 'servers',
        WEIGHT = 'weight';

    /**
     * @var \Memcached
     */
    protected $memcached;

    /**
     * @var string
     */
    protected $persistentId;

    /**
     * @var array
     */
    protected $servers;

    public function __construct(ResourceManagerInterface $resourceManager, array $options = null, string $name = null)
    {
        parent::__construct($resourceManager, $options, $name);

        $this->connect();
    }

    /**
     * @inheritDoc
     */
    public function clear(): DictionaryInterface
    {
        $this->memcached->flush();

        return $this;
    }

    /**
     * Connect to the server(s)
     */
    protected function connect(): void
    {
        $servers = $this->prepareServerParameters();

        if (is_null($this->persistentId)) {
            $this->memcached = new \Memcached();
            $this->memcached->addServers($servers);

            return;
        }

        $this->memcached = new \Memcached($this->persistentId);

        if (count($this->memcached->getServerList()) === count($servers)) {
            return;
        }

        $this->memcached->resetServerList();
        $this->memcached->addServers($servers);
    }

    /**
     * @inheritDoc
     */
    public function decrement(string $key, int $step = 1): int
    {
        return $this->memcached->decrement($key, $step);
    }

    /**
     * @inheritDoc
     */
    public function delete(string $key): DictionaryInterface
    {
        $this->memcached->delete($key);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function deleteMultiple(array $keys): DictionaryInterface
    {
        $this->memcached->deleteMulti($keys);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function get(string $key)
    {
        $value = $this->memcached->get($key);

        return $this->memcached->getResultCode() === \Memcached::RES_SUCCESS ? $value : null;
    }

    /**
     * @inheritDoc
     */
    public function getMultiple(array $keys, $default = null): array
    {
        $values = $this->memcached->getMulti($keys);

        return $this->memcached->getResultCode() === \Memcached::RES_SUCCESS ? $values : array_fill_keys($keys, $default);
    }

    /**
     * @inheritDoc
     */
    public function increment(string $key, int $step = 1): int
    {
        return $this->memcached->increment($key, $step);
    }

    /**
     * Prepare server parameters
     *
     * @return array
     */
    protected function prepareServerParameters(): array
    {
        $servers = [];
        $parameterKeys = array_flip([static::HOST, static::PORT, static::WEIGHT]);
        $parameterCount = count($parameterKeys);

        foreach ($this->servers as $key => $server) {
            $orderedParameters = array_intersect_key((array) $server, $parameterKeys);

            if (count($orderedParameters) === $parameterCount) {
                $servers[] = $orderedParameters;

                continue;
            }

            $missingKeys = array_keys(array_diff_key($parameterKeys, $server));

            throw new Exception\InvalidArgumentException(sprintf(static::MISSING_SERVER_PARAMETER_EXCEPTION, implode("', '", $missingKeys), $key));
        }

        return $servers;
    }

    /**
     * @inheritDoc
     */
    public function set(string $key, $value, int $seconds = 0): DictionaryInterface
    {
        $this->memcached->set($key, $value, $seconds > 0 ? (new \DateTimeImmutable("now +$seconds seconds"))->getTimestamp() : $seconds);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setMultiple(array $items, int $seconds = 0): DictionaryInterface
    {
        $this->memcached->setMulti($items, $seconds > 0 ? (new \DateTimeImmutable("now +$seconds seconds"))->getTimestamp() : $seconds);

        return $this;
    }
}