<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Tools;

use Fixin\Support\Performance;

/**
 * @author attilajenei
 *
 * @method escapeHtml
 * @property escapeHtml
 */
class Test {

    protected $helpers = [];

    /**
     * Helper
     */
    public function __construct() {
        $this->helpers['escapeHtml'] = function($text) {
            return htmlspecialchars($text);
        };
    }

    /**
     * Non-accessible method
     *
     * @param string $name
     * @param array $args
     * @return mixed
     */
    public function __call(string $name, $args) {
        if (isset($this->helpers[$name])) {
            return call_user_func_array($this->helpers[$name], $args);
        }

        return null;
    }

    /**
     * Non-accessible property
     *
     * @param string $name
     * @return mixed
     */
    public function __get(string $name) {
        return $this->helpers[$name] ?? null;
    }
}

/**
 * @author attilajenei
 *
 */
class TestB extends Test {
    public function __get(string $name) {
        return $this->$name = $this->helpers[$name] ?? null;
    }
}

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
