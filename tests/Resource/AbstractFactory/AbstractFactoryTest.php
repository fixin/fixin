<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace FixinTest\Resource\AbstractFactory;

use Fixin\Resource\AbstractFactory\AbstractFactory;
use FixinTest\AbstractTest;
use FixinTest\ResourceManager;

/**
 * @coversDefaultClass \Fixin\Resource\AbstractFactory\AbstractFactory
 */
class AbstractFactoryTest extends AbstractTest
{
    protected function makeFactory($canProduce, ?AbstractFactory $next): AbstractFactory
    {
        $resources = new ResourceManager();

        $instance = new class($resources, [AbstractFactory::NEXT => $next], 'test', $canProduce) extends AbstractFactory {
            public $canProduce;

            protected function canProduce(string $key): bool
            {
                return $this->canProduce;
            }

            protected function produce(string $key, array $options, string $name)
            {
                return new \stdClass();
            }
        };
        $instance->canProduce = $canProduce;

        return $instance;
    }

    /**
     * @covers ::canChainProduce
     */
    public function testCanChainProduce(): void
    {
        $chain = $this->makeFactory(false, $this->makeFactory(false, null));
        $this->assertFalse($chain->canChainProduce('test'));

        $chain = $this->makeFactory(false, $this->makeFactory(true, null));
        $this->assertTrue($chain->canChainProduce('test'));
    }

    /**
     * @covers ::chainProduce
     */
    public function testChainProduce(): void
    {
        $chain = $this->makeFactory(false, $this->makeFactory(false, null));
        $this->assertNull($chain->chainProduce('test', [], 'test'));

        $chain = $this->makeFactory(false, $this->makeFactory(true, null));
        $this->assertInstanceOf(\stdClass::class, $chain->chainProduce('test', [], 'test'));
    }
}
