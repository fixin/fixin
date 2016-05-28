<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

use Fixin\Support\Performance;
use FixinTools\Performance\Magic\MethodsA;
use FixinTools\Performance\Magic\MethodsB;

(function() {
    include dirname(__DIR__, 3) . '/cheats/tools.php';

    define('LOOPS', 500000);
    $objectA = new MethodsA();
    $objectB = new MethodsB();

    Performance::measureCode(function() use ($objectA) {
        // __call
        for ($i = 0; $i < LOOPS; $i++) {
            $objectA->escapeHtml('test' . $i);
        }
    });

    Performance::measureCode(function() use ($objectA) {
        // __get
        for ($i = 0; $i < LOOPS; $i++) {
            ($objectA->escapeHtml)('test' . $i);
        }
    });

    Performance::measureCode(function() use ($objectB) {
        // __get w/ public var
        for ($i = 0; $i < LOOPS; $i++) {
            ($objectB->escapeHtml)('test' . $i);
        }
    });
})();
