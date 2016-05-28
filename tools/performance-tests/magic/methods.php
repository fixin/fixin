<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Tools;

use Fixin\Support\Performance;

include 'Test.php';
include 'TestB.php';

(function() {
    include dirname(__DIR__, 3) . '/cheats/minimal.php';

    define('LOOPS', 100000);
    $object = new Test();
    $objectB = new TestB();

    Performance::measureCode(function() use ($object) {
        // __call
        for ($i = 0; $i < LOOPS; $i++) {
            $object->escapeHtml('test');
        }
    });

    Performance::measureCode(function() use ($object) {
        // __get
        for ($i = 0; $i < LOOPS; $i++) {
            ($object->escapeHtml)('test');
        }
    });

    Performance::measureCode(function() use ($objectB) {
        // __get w/ public var
        for ($i = 0; $i < LOOPS; $i++) {
            ($objectB->escapeHtml)('test');
        }
    });
})();
