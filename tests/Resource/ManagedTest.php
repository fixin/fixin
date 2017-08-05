<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace FixinTest\Resource;

use Fixin\Resource\Managed;
use Fixin\Support\Types;
use FixinTest\AbstractTest;
use FixinTest\ResourceManager;

/**
 * @coversDefaultClass \Fixin\Resource\Managed
 */
class ManagedTest extends AbstractTest
{
    /**
     * @covers ::__construct
     * @covers ::configureWithOptions
     * @covers ::setLazyLoadingProperty
     * @covers ::loadLazyProperty
     */
    public function testConfigure(): void
    {
        $options = [
            'class' => new \ArrayObject(['a' => 1]),
            'commonTypes' => 1,
            'multiple' => '2',
            'lazyLoadedByName' => 'lazyResource',
            'lazyLoadedByInstance' => new \ArrayObject(['b' => 2]),
            'setter' => 'setterTest'
        ];

        $resources = [
            'lazyResource' => new \ArrayObject(['c' => 3])
        ];

        $managed = new class(new ResourceManager($resources), $options) extends Managed {
            protected const
                THIS_SETS = [
                    'class' => \ArrayObject::class,
                    'commonTypes' => Types::INT,
                    'multiple' => [Types::INT, Types::STRING],
                    'lazyLoadedByName' => [self::LAZY_LOADING => \ArrayObject::class],
                    'lazyLoadedByInstance' => [self::LAZY_LOADING => \ArrayObject::class],
                    'setter' => self::USING_SETTER
                ];

            public function getVars(): array
            {
                $this->loadLazyProperty('lazyLoadedByName');
                $this->loadLazyProperty('missingLazyLoaded');

                return get_object_vars($this);
            }

            protected function setSetter(string $setter): void
            {
                $this->setter = $setter;
            }
        };

        $vars = $managed->getVars();
        unset($vars['resourceManager']);

        $options['lazyLoadedByName'] = $resources['lazyResource'];
        $options['missingLazyLoaded'] = null;

        $this->assertEquals($options, $vars);
    }

    /**
     * @covers ::__construct
     * @covers ::configureWithOptions
     * @covers ::setLazyLoadingProperty
     * @dataProvider dataExceptions
     */
    public function testExceptions(array $options, string $exception): void
    {
        $this->expectExceptionMessage($exception);

        new class(new ResourceManager([]), $options) extends Managed {
            protected const
                THIS_SETS = [
                    'common' => Types::STRING,
                    'lazy' => [self::LAZY_LOADING => \stdClass::class]
                ];
        };
    }

    public function dataExceptions(): array
    {
        return [
            'type error' => [
                'options' => ['common' => 2],
                'exception' => "Invalid 'common' argument: string allowed"
            ],
            'non-existing' => [
                'options' => ['nonExisting' => true],
                'exception' => "Invalid option 'nonExisting'"
            ],
            'invalid class for lazy-loading' => [
                'options' => ['lazy' => new \ArrayObject()],
                'exception' => 'Invalid \'lazy\' argument: string or stdClass allowed'
            ]
        ];
    }
}
