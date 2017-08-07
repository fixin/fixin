<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace FixinTest\Resource\AbstractFactory;

use Fixin\Resource\AbstractFactory\DefaultFactory;
use FixinTest\AbstractTest;
use FixinTest\ResourceManager;

/**
 * @coversDefaultClass \Fixin\Resource\AbstractFactory\DefaultFactory
 */
class DefaultFactoryTest extends AbstractTest
{
    /**
     * @var DefaultFactory
     */
    protected $factory;

    protected function setUp()
    {
        $this->factory = new DefaultFactory(new ResourceManager());
    }

    /**
     * @covers ::canProduce
     */
    public function testCanProduce(): void
    {
        $this->assertTrue($this->factory->canChainProduce(\stdClass::class));
        $this->assertFalse($this->factory->canChainProduce('nonExistingClass'));
    }

    public function testProduce(): void
    {
        $this->assertInstanceOf(\stdClass::class, $this->factory->chainProduce(\stdClass::class, [], 'test'));
    }
}
