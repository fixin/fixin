<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace FixinTest\Resource;

use Fixin\Resource\Prototype;
use Fixin\Resource\ResourceManagerInterface;
use Fixin\Support\Types;
use FixinTest\AbstractTest;
use FixinTest\ResourceManager;

/**
 * @coversDefaultClass \Fixin\Resource\Prototype
 */
class PrototypeTest extends AbstractTest
{
    /**
     * @covers ::withOptions
     */
    public function testWithOptions(): void
    {
        $original = 'original';
        $changed = 'changed';

        $prototype = new class(new ResourceManager(), ['test' => $original]) extends Prototype {
            protected const
                THIS_SETS = [
                    'test' => Types::STRING
                ];

            protected $test;

            public function getTest(): string
            {
                return $this->test;
            }
        };

        $new = $prototype->withOptions(['test' => $changed]);
        $this->assertNotSame($prototype, $new);
        $this->assertSame($original, $prototype->getTest());
        $this->assertSame($changed, $new->getTest());
    }

    /**
     * @covers ::withResourceManager
     */
    public function testWithResourceManager(): void
    {
        $prototype = new class(new ResourceManager()) extends Prototype {
            public function getResourceManager(): ResourceManagerInterface
            {
                return $this->resourceManager;
            }
        };

        $new = $prototype->withResourceManager(new ResourceManager());
        $this->assertNotSame($prototype->getResourceManager(), $new->getResourceManager());

    }
}
