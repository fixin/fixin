<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace FixinTest\Resource;

use Fixin\Resource\AbstractFactory\AbstractFactory;
use Fixin\Resource\AbstractFactory\DefaultFactory;
use Fixin\Resource\Exception\ClassNotFoundException;
use Fixin\Resource\Exception\InvalidArgumentException;
use Fixin\Resource\Exception\UnexpectedResourceException;
use Fixin\Resource\Prototype;
use Fixin\Resource\PrototypeInterface;
use Fixin\Resource\Resource;
use Fixin\Resource\ResourceInterface;
use Fixin\Resource\ResourceManager;
use FixinTest\AbstractTest;
use FixinTest\Resource\ResourceManager\TestFactory;

/**
 * @coversDefaultClass \Fixin\Resource\ResourceManager
 */
class ResourceManagerTest extends AbstractTest
{
    /**
     * @covers ::clone
     */
    public function testClone(): void
    {
        $byInjection = new \ArrayObject(['byInjection']);
        $byClosure = new \ArrayObject(['byClosure']);

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

        $byClosureClone = $resources->clone('byClosure', \ArrayObject::class);
        $this->assertNotSame($byClosure, $byClosureClone);
        $this->assertEquals($byClosure, $byClosureClone);

        $byInjectionClone = $resources->clone('byInjection', \ArrayObject::class);
        $this->assertNotSame($byInjection, $byInjectionClone);
        $this->assertEquals($byInjection, $byInjectionClone);
    }

    /**
     * @covers ::clone
     */
    public function testCloneWithResource(): void
    {
        $resources = new ResourceManager([
            ResourceManager::RESOURCES => [
                'test' => new class(new ResourceManager([])) extends Resource {}
            ]
        ]);

        $this->expectExceptionMessage("Can't use resource as prototype 'test'");

        $resources->clone('test', ResourceInterface::class);
    }

    /**
     * @covers ::clone
     */
    public function testCloneWithOptionsCall(): void
    {
        $prototype = new class(new ResourceManager([])) extends Prototype {
            public $withOptionsCalls = 0;

            public function withOptions(array $options): PrototypeInterface
            {
                $this->withOptionsCalls++;

                return parent::withOptions($options);
            }
        };

        $resources = new ResourceManager([
            ResourceManager::DEFINITIONS => [
                'prototype' => function() use ($prototype) {
                    return $prototype;
                }
            ]
        ]);

        $cloned = $resources->clone('prototype', PrototypeInterface::class);
        $this->assertSame(1, $cloned->withOptionsCalls);
    }

    /**
     * @covers ::get
     */
    public function testGet(): void
    {
        $byInjection = new class(new ResourceManager([])) extends Resource {};
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
    public function testGetWithNonResource(): void
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
     * @covers ::prepareAbstractFactoryChain
     */
    public function testPrepareAbstractFactoryChain(): void
    {
        $resources = new ResourceManager([
            ResourceManager::ABSTRACT_FACTORIES => [
                [
                    'class' => TestFactory::class,
                    'options' => [
                        TestFactory::TEST => 'testValue'
                    ]
                ],
                DefaultFactory::class
            ]
        ]);

        $created = $resources->clone('test', \ArrayObject::class);
        $this->assertInstanceOf(AbstractFactory::class, $created['factory']);
        $this->assertInstanceOf(AbstractFactory::class, $created['nextFactory']);
        $this->assertSame('testValue', $created['test']);
    }

    /**
     * @covers ::prepareAbstractFactoryChain
     */
    public function testPrepareAbstractFactoryChainException(): void
    {
        $resources = new ResourceManager([
            ResourceManager::ABSTRACT_FACTORIES => [
                \stdClass::class
            ]
        ]);

        $this->expectException(InvalidArgumentException::class);

        $resources->clone('test', \stdClass::class);
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

    /**
     * @covers ::__toString
     */
    public function testToString(): void
    {
        $resources = new ResourceManager([
            ResourceManager::DEFINITIONS => [
                'defintion' => 'stdClass'
            ],
            ResourceManager::RESOURCES => [
                'instance' => new \stdClass()
            ]
        ]);

        $text = "Fixin\Resource\ResourceManager {" . PHP_EOL . PHP_EOL
            . "    defintion                                          defined," . PHP_EOL
            . "    instance                                           {stdClass} prototype" . PHP_EOL
            . "}";

        $this->assertSame($text, (string) $resources);
    }
}
