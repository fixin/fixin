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
     * @covers ::handle
     */
    public function testHandleArray(): void
    {
        $cargo = $this->mockClass(CargoInterface::class);
        $content = ['test' => 'value'];

        $cargo->method('getContent')
            ->willReturn($content);

        $json = $this->mockClass(JsonInterface::class);
        $encoded = 'encoded';

        $json->method('encode')
            ->with($content)
            ->willReturn($encoded);

        $cargo->expects($this->once())
            ->method('setContent')
            ->with($encoded)
            ->willReturn($cargo);

        $cargo->expects($this->once())
            ->method('setContentType')
            ->with('application/json');

        $node = $this->makeInstance(ArrayToJson::class, [], [
            '*\Base\Json\Json' => $json
        ]);
        $node->handle($cargo);
    }

    public function testHandleNonArray(): void
    {
        $cargo = $this->mockClass(CargoInterface::class);

        $cargo->method('getContent')
            ->willReturn('test');

        $cargo->expects($this->never())
            ->method('setContent');

        $cargo->expects($this->never())
            ->method('setContentType');

        $node = $this->makeInstance(ArrayToJson::class);
        $node->handle($cargo);
    }
}
