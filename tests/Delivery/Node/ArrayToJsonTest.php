<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace FixinTest\Delivery\Node;

use Fixin\Base\Json\JsonInterface;
use Fixin\Delivery\Cargo\CargoInterface;
use Fixin\Delivery\Node\ArrayToJson;
use FixinTest\AbstractTest;

/**
 * @coversDefaultClass \Fixin\Delivery\Node\ArrayToJson
 */
class ArrayToJsonTest extends AbstractTest
{
    /**
     * @var JsonInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $json;

    /**
     * @var ArrayToJson
     */
    protected $node;

    protected function setUp()
    {
        $this->json = $this->mockClass(JsonInterface::class);
        $this->node = $this->makeInstance(ArrayToJson::class, [], [
            '*\Base\Json\Json' => $this->json
        ]);
    }

    /**
     * @covers ::handle
     */
    public function testArray(): void
    {
        $cargo = $this->mockClass(CargoInterface::class);
        $decoded = ['test' => 'value'];
        $jsonData = 'json';

        $cargo->method('getContent')->willReturn($decoded);

        $this->json->method('encode')->with($decoded)->willReturn($jsonData);

        $cargo->expects($this->once())->method('setContent')->with($jsonData)->willReturn($cargo);
        $cargo->expects($this->once())->method('setContentType')->with('application/json');

        $this->node->handle($cargo);
    }

    /**
     * @covers ::handle
     */
    public function testNonArray(): void
    {
        $cargo = $this->mockClass(CargoInterface::class);

        $cargo->method('getContent')->willReturn('test');
        $cargo->expects($this->never())->method('setContent');
        $cargo->expects($this->never())->method('setContentType');

        $this->assertSame($cargo, $this->node->handle($cargo));
    }
}
