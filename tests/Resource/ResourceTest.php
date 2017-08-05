<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace FixinTest\Resource;

use Fixin\Resource\Resource;
use Fixin\Support\Types;
use FixinTest\AbstractTest;
use FixinTest\ResourceManager;

/**
 * @coversDefaultClass \Fixin\Resource\Resource
 */
class ResourceTest extends AbstractTest
{
    /**
     * @covers ::__construct
     * @covers \Fixin\Resource\Managed::configurationTest
     * @covers \Fixin\Resource\Managed::requirementTest
     * @dataProvider dataConfigurationTest
     */
    public function testConfigurationTest(array $options, $wrong): void
    {
        if ($wrong) {
            $this->expectExceptionMessage("'required' is required for sut");
        }

        $result = new class(new ResourceManager(), $options, 'sut') extends Resource {
            protected const
                THIS_SETS = [
                    'valid' => Types::STRING,
                    'validNullable' => [Types::INT, Types::NULL],
                    'required' => Types::INT
                ];

            protected $required;
            protected $valid;
            protected $validNullable;
        };

        if (!$wrong) {
            $this->assertInstanceOf(Resource::class, $result);
        }
    }

    public function dataConfigurationTest(): array
    {
        return [
            'invalid' => ['options' => ['valid' => 'test'], 'wrong' => true],
            'valid' => ['options' => ['valid' => 'test', 'required' => 2], 'wrong' => false]
        ];
    }
}
