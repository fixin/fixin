<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

use Fixin\Support\Performance\Performance;
use FixinTools\Performance\Magic\MethodsA;
use FixinTools\Performance\Magic\MethodsB;

(function() {
    include dirname(__DIR__, 3) . '/boosts/tools.php';

    $loops = 500000;
    $objectA = new MethodsA();
    $objectB = new MethodsB();

    echo Performance::measureCode(function() use ($loops, $objectA) {
        // __call
        for ($i = 0; $i < $loops; $i++) {
            $objectA->escapeHtml('test' . $i);
        }
    });

    echo Performance::measureCode(function() use ($loops, $objectA) {
        // __get
        for ($i = 0; $i < $loops; $i++) {
            ($objectA->escapeHtml)('test' . $i);
        }
    });

    echo Performance::measureCode(function() use ($loops, $objectB) {
        // __get with public var
        for ($i = 0; $i < $loops; $i++) {
            ($objectB->escapeHtml)('test' . $i);
        }
    });

    echo Performance::measureCode(function() use ($loops, $objectB) {
        // Existing method
        for ($i = 0; $i < $loops; $i++) {
            $objectB->existing('test' . $i);
        }
    });
})();
