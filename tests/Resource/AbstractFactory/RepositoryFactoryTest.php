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
use Fixin\Resource\AbstractFactory\RepositoryFactory;
use FixinTest\AbstractTest;
use FixinTest\ResourceManager;

/**
 * @coversDefaultClass \Fixin\Resource\AbstractFactory\RepositoryFactory
 */
class RepositoryFactoryTest extends AbstractTest
{
    /**
     * @var RepositoryFactory
     */
    protected $factory;

    protected function setUp()
    {
        $resources = new ResourceManager();

        $chainedFactory = new class($resources) extends AbstractFactory {
            protected function canProduce(string $key): bool
            {
                return $key !== 'nonExisting';
            }

            protected function produce(string $key, array $options, string $name)
            {
                return compact('key', 'options', 'name');
            }
        };

        $this->factory = new RepositoryFactory($resources, [
            RepositoryFactory::CLASS_PREFIX => '\FixinTest\\',
            RepositoryFactory::KEY_PREFIX => 'test.',
            RepositoryFactory::ENTITY_CACHE => 'entityCache',
            RepositoryFactory::STORAGE => 'storage',
            RepositoryFactory::NEXT => $chainedFactory
        ]);
    }

    /**
     * @covers ::canProduce
     * @covers ::__construct
     */
    public function testCanProduce(): void
    {
        $this->assertTrue($this->factory->canChainProduce('test.system.userHistory'));
        $this->assertFalse($this->factory->canChainProduce('nonExisting'));
    }

    /**
     * @covers ::produce
     */
    public function testProduce(): void
    {
        $result = $this->factory->chainProduce('test.system.userHistory', [], 'test');

        $this->assertSame([
            'key' => '\FixinTest\System\Repository\UserHistory',
            'options' => [
                'name' => 'System__UserHistory',
                'entityPrototype' => '\FixinTest\System\Entity\UserHistory',
                'entityCache' => 'entityCache',
                'storage' => 'storage'
            ],
            'name' => 'test'
        ], $result);

    }
}
