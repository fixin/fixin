<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace FixinTest\Resource\ResourceManager;

use Fixin\Resource\AbstractFactory\AbstractFactory;
use Fixin\Support\Types;

class TestFactory extends AbstractFactory
{
    public const
        TEST = 'test';

    protected const
        THIS_SETS = parent::THIS_SETS + [
            self::TEST => Types::STRING
        ];

    /**
     * @var string
     */
    protected $test;

    protected function canProduce(string $key): bool
    {
        return true;
    }

    protected function produce(string $key, array $options, string $name)
    {
        return new \ArrayObject([
            'factory' => $this,
            'nextFactory' => $this->next,
            'test' => $this->test
        ]);
    }
}
