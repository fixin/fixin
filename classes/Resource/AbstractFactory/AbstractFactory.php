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
    protected const
        THIS_SETS = [
            self::NEXT => [self::class, Types::NULL]
        ];

    public const
        NEXT = 'next';

    /**
     * @var static
     */
    protected $next;

    public function canChainProduce(string $key): bool
    {
        return $this->canProduce($key) || ($this->next && $this->next->canChainProduce($key));
    }

    abstract protected function canProduce(string $key): bool;

    public function chainProduce(string $key, array $options, string $name)
    {
        if ($this->canProduce($key)) {
            return $this->produce($key, $options, $name);
        }

        return $this->next ? $this->next->chainProduce($key, $options, $name) : null;
    }

    abstract protected function produce(string $key, array $options, string $name);
}
