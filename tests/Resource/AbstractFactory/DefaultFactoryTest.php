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
     * @covers ::canProduce
     */
    public function testCanProduce(): void
    {
        $factory = new DefaultFactory(new ResourceManager());

        $this->assertTrue($factory->canProduce(\stdClass::class));
        $this->assertFalse($factory->canProduce('nonExistingClass'));
    }

    public function testProduce(): void
    {
        $factory = new DefaultFactory(new ResourceManager());

        $this->assertInstanceOf(\stdClass::class, $factory->produce(\stdClass::class, [], 'test'));
    }
}
