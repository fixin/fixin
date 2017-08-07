<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace FixinTest\Resource\AbstractFactory;

use Fixin\Base\Json\Json;
use Fixin\Resource\AbstractFactory\NamespaceFallbackFactory;
use FixinTest\AbstractTest;
use FixinTest\ResourceManager;

/**
 * @coversDefaultClass \Fixin\Resource\AbstractFactory\NamespaceFallbackFactory
 */
class NamespaceFallbackFactoryTest extends AbstractTest
{
    /**
     * @var NamespaceFallbackFactory
     */
    protected $factory;

    protected function setUp()
    {
        $this->factory = new NamespaceFallbackFactory(new ResourceManager(), [NamespaceFallbackFactory::SEARCH_ORDER => ['Fixin']]);
    }

    /**
     * @covers ::canProduce
     */
    public function testCanProduce(): void
    {
        $this->assertTrue($this->factory->canChainProduce('*\Base\Json\Json'));
        $this->assertTrue($this->factory->canChainProduce('*\Base\Json\Json')); // cached
        $this->assertFalse($this->factory->canChainProduce('*\Base\Json\Json2'));
        $this->assertFalse($this->factory->canChainProduce(\stdClass::class));
        $this->assertFalse($this->factory->canChainProduce('nonExistingClass'));
    }

    /**
     * @covers ::produce
     */
    public function testProduce(): void
    {
        $this->assertInstanceOf(Json::class, $this->factory->chainProduce('*\Base\Json\Json', [], 'test'));
    }
}
