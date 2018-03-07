<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Resource\AbstractFactory;

use Fixin\Resource\Resource;
use Fixin\Support\Types;

abstract class AbstractFactory extends Resource
{
    public const
        NEXT = 'next';

    protected const
        THIS_SETS = [
            self::NEXT => [self::class, Types::NULL]
        ];

    /**
     * @var static
     */
    protected $next;

    /**
     * Determine the factory chain can produce for the key
     *
     * @param string $key
     * @return bool
     */
    public function canChainProduce(string $key): bool
    {
        return $this->canProduce($key) || ($this->next && $this->next->canChainProduce($key));
    }

    /**
     * Determine the factory can produce for the key
     *
     * @param string $key
     * @return bool
     */
    abstract protected function canProduce(string $key): bool;

    /**
     * Produce instance by the chain
     *
     * @param string $key
     * @param array $options
     * @return null
     */
    public function chainProduce(string $key, array $options)
    {
        if ($this->canProduce($key)) {
            return $this->produce($key, $options);
        }

        return $this->next ? $this->next->chainProduce($key, $options) : null;
    }

    /**
     * Produce instance by the factory
     *
     * @param string $key
     * @param array $options
     * @return mixed
     */
    abstract protected function produce(string $key, array $options);
}
