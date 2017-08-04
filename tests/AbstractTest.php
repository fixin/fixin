<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace FixinTest;

use PHPUnit\Framework\TestCase;

abstract class AbstractTest extends TestCase
{
    protected function makeInstance(string $class, array $options = [], array $resources = [])
    {
        return new $class(new ResourceManager($resources), $options, 'sut');
    }

    protected function mockClass(string $class): \PHPUnit_Framework_MockObject_MockObject
    {
        return $this->getMockBuilder($class)->disableOriginalConstructor()->getMock();
    }
}
