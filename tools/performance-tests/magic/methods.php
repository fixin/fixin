<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Tools;

use Fixin\Support\Performance;

class Test {

    protected $helpers = [];

    public function __construct() {
        $this->helpers['escapeHtml'] = function($text) {
            return htmlspecialchars($text);
        };
    }

    public function __call(string $name, $args) {
        return call_user_func_array($this->helpers[$name], $args);
    }

    public function __get(string $name) {
        return $this->helpers[$name];
    }
}

class TestB extends Test {
    public function __get(string $name) {
        return $this->$name = $this->helpers[$name];
    }
}

(function() {
    include dirname(__DIR__, 3) . '/cheats/minimal.php';

    define('LOOPS', 100000);
    $object = new Test();
    $objectB = new TestB();

    // __call
    Performance::measureCode(function() use ($object) {
        for ($i = 0; $i < LOOPS; $i++) {
            $object->escapeHtml('test');
        }
    });

    // __get
    Performance::measureCode(function() use ($object) {
        for ($i = 0; $i < LOOPS; $i++) {
            ($object->escapeHtml)('test');
        }
    });

    // __get w/ public var
    Performance::measureCode(function() use ($objectB) {
        for ($i = 0; $i < LOOPS; $i++) {
            ($objectB->escapeHtml)('test');
        }
    });
})();
