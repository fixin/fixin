<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace FixinTest\Resource;

use Fixin\Resource\AbstractFactory\DefaultFactory;
use Fixin\Resource\Exception\ClassNotFoundException;
use Fixin\Resource\Exception\UnexpectedResourceException;
use Fixin\Resource\Resource;
use Fixin\Resource\ResourceInterface;
use Fixin\Resource\ResourceManager;
use FixinTest\AbstractTest;

/**
 * @coversDefaultClass \Fixin\Resource\ResourceManager
 */
class ResourceManagerTest extends AbstractTest
{
    /**
     * @covers ::get
     */
    public function testGet(): void
    {
        $byInjection = new class(new ResourceManager([]), [], 'test') extends Resource {};
        $byClosure = clone $byInjection;

        $resources = new ResourceManager([
            ResourceManager::DEFINITIONS => [
                'byClosure' => function() use ($byClosure) {
                    return $byClosure;
                }
            ],
            ResourceManager::RESOURCES => [
                'byInjection' => $byInjection
            ],
        ]);

        $this->assertSame($byClosure, $resources->get('byClosure', ResourceInterface::class));
        $this->assertSame($byInjection, $resources->get('byInjection', ResourceInterface::class));
    }

    /**
     * @covers ::get
     */
    public function testGetException(): void
    {
        $resources = new ResourceManager([
            ResourceManager::DEFINITIONS => [
                'test' => \stdClass::class,
            ],
            ResourceManager::ABSTRACT_FACTORIES => [
                DefaultFactory::class
            ]
        ]);

        $this->expectExceptionMessage("Can't use prototype as resource 'test'");

        $resources->get('test', \stdClass::class);
    }

    /**
     * @covers ::has
     * @covers ::canProduceByAbstractFactories
     * @covers ::__construct
     */
    public function testHas(): void
    {
        $resources = new ResourceManager([
            ResourceManager::DEFINITIONS => [
                'byAbstractFactory' => \stdClass::class
            ],
            ResourceManager::RESOURCES => [
                'byInjection' => new \stdClass()
            ],
            ResourceManager::ABSTRACT_FACTORIES => [
                DefaultFactory::class
            ]
        ]);

        $this->assertTrue($resources->has('byInjection'), 'By injection');
        $this->assertTrue($resources->has('byAbstractFactory'), 'By abstract factory');

        $this->assertFalse($resources->has('nonExisting'), 'Non-existing class');
    }

    /**
     * @covers ::prepareResource
     * @dataProvider dataPrepareResourceException
     */
    public function testPrepareResourceException($definition): void
    {
        $resources = new ResourceManager([
            ResourceManager::DEFINITIONS => [
                'test' => $definition
            ]
        ]);

        $this->expectException(UnexpectedResourceException::class);
        $resources->clone('test', \stdClass::class);
    }

    public function dataPrepareResourceException(): array
    {
        return [
            'null' => [
                function() {
                    return null;
                }
            ],
            'resource' => [
                function() {
                    return new \ArrayObject();
                }
            ]
        ];
    }

    /**
     * @covers ::prepareResource
     * @covers ::produceResource
     */
    public function testProduceResource(): void
    {
        $byClosure = new \ArrayObject(['byClosure']);

        $resources = new ResourceManager([
            ResourceManager::DEFINITIONS => [
                'byRecursion' => [
                    'class' => 'byAbstractFactory',
                    'options' => [
                    ]
                ],
                'byAbstractFactory' => \stdClass::class,
                'byClosure' => function() use ($byClosure) {
                    return $byClosure;
                }
            ],
            ResourceManager::ABSTRACT_FACTORIES => [
                DefaultFactory::class
            ]
        ]);

        $this->assertInstanceOf(\stdClass::class, $resources->clone('byAbstractFactory', \stdClass::class), 'By abstract factory');
        $this->assertInstanceOf(\stdClass::class, $resources->clone('byRecursion', \stdClass::class), 'Recursive definition');
        $this->assertEquals($byClosure, $resources->clone('byClosure', \ArrayAccess::class), 'By closure');
    }

    /**
     * @covers ::produceResource
     */
    public function testProduceResourceException(): void
    {
        $resources = new ResourceManager([
            ResourceManager::DEFINITIONS => [
                'test' => 'UnknownClass'
            ],
            ResourceManager::ABSTRACT_FACTORIES => [
                DefaultFactory::class
            ]
        ]);

        $this->expectException(ClassNotFoundException::class);

        $resources->get('test', \stdClass::class);
    }
}
