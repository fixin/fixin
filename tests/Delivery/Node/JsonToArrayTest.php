<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace FixinTest\Delivery\Node;

use Fixin\Base\Json\Exception\RuntimeException;
use Fixin\Base\Json\JsonInterface;
use Fixin\Delivery\Cargo\CargoInterface;
use Fixin\Delivery\Node\JsonToArray;
use FixinTest\AbstractTest;

/**
 * @coversDefaultClass \Fixin\Delivery\Node\JsonToArray
 */
class JsonToArrayTest extends AbstractTest
{
    /**
     * @var JsonInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $json;

    /**
     * @var JsonToArray
     */
    protected $node;

    protected function setUp()
    {
        $this->json = $this->mockClass(JsonInterface::class);
        $this->node = $this->makeInstance(JsonToArray::class, [], [
            '*\Base\Json\Json' => $this->json
        ]);
    }

    /**
     * @covers ::handle
     */
    public function testArray(): void
    {
        $cargo = $this->mockClass(CargoInterface::class);

        $cargo->method('getContent')->willReturn([]);
        $cargo->method('getContentType')->willReturn('application/json');

        $cargo->expects($this->never())->method('setContent');
        $cargo->expects($this->never())->method('setContentType');

        $this->assertSame($cargo, $this->node->handle($cargo));
    }

    /**
     * @covers ::handle
     */
    public function testInvalidJson(): void
    {
        $cargo = $this->mockClass(CargoInterface::class);
        $jsonData = 'json';
        $exception = new RuntimeException();

        $cargo->method('getContent')->willReturn($jsonData);
        $cargo->method('getContentType')->willReturn('application/json');

        $this->json->method('decode')->willThrowException($exception);

        $cargo->expects($this->once())->method('setContent')->with($exception);

        $this->assertSame($cargo, $this->node->handle($cargo));
    }

    /**
     * @covers ::handle
     * @dataProvider dataJson
     */
    public function testJson(string $type): void
    {
        $cargo = $this->mockClass(CargoInterface::class);
        $jsonData = 'json';
        $decoded = ['test' => 'value'];

        $cargo->method('getContent')->willReturn($jsonData);
        $cargo->method('getContentType')->willReturn($type);

        $this->json->method('decode')->with($jsonData)->willReturn($decoded);

        $cargo->expects($this->once())->method('setContent')->with($decoded);

        $this->assertSame($cargo, $this->node->handle($cargo));
    }

    public function dataJson(): array
    {
        return [
            ['application/json'],
            ['application/jsonml+json']
        ];
    }

    /**
     * @covers ::handle
     */
    public function testNonJson(): void
    {
        $cargo = $this->mockClass(CargoInterface::class);

        $cargo->expects($this->never())->method('setContent');
        $cargo->expects($this->never())->method('setContentType');

        $this->assertSame($cargo, $this->node->handle($cargo));
    }
}
