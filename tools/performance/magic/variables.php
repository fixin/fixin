<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

use Fixin\Support\Performance\Performance;
use FixinTools\Performance\Magic\VariablesA;
use FixinTools\Performance\Magic\VariablesB;

(function() {
    include dirname(__DIR__, 3) . '/cheats/tools.php';

    $loops = 2000000;
    $objectA = new VariablesA();
    $objectB = new VariablesB();
    $result = null;

    // Get
    echo Performance::measureCode(function() use ($loops, &$result, $objectA) {
        // Get: __get
        for ($i = 0; $i < $loops; $i++) {
            $result = $objectA->summary;
        }
    });

    echo Performance::measureCode(function() use ($loops, &$result, $objectA) {
        // Get: offsetGet
        for ($i = 0; $i < $loops; $i++) {
            $result = $objectA['summary'];
        }
    });

    echo Performance::measureCode(function() use ($loops, &$result, $objectA) {
        // Get: getter with name
        for ($i = 0; $i < $loops; $i++) {
            $result = $objectA->getVariable('summary');
        }
    });

    echo Performance::measureCode(function() use ($loops, &$result, $objectA) {
        // Get: getter
        for ($i = 0; $i < $loops; $i++) {
            $result = $objectA->getTest();
        }
    });

    echo Performance::measureCode(function() use ($loops, &$result, $objectB) {
        // Get: __get with public var
        for ($i = 0; $i < $loops; $i++) {
            $result = $objectB->summary;
        }
    });

    // Is set
    echo Performance::measureCode(function() use ($loops, &$result, $objectA) {
        // Is set: __isset
        for ($i = 0; $i < $loops; $i++) {
            $result = isset($objectA->summary);
        }
    });

    echo Performance::measureCode(function() use ($loops, &$result, $objectA) {
        // Is set: offsetExists
        for ($i = 0; $i < $loops; $i++) {
            $result = isset($objectA['summary']);
        }
    });

    echo Performance::measureCode(function() use ($loops, &$result, $objectA) {
        // Is set: has
        for ($i = 0; $i < $loops; $i++) {
            $result = $objectA->hasVariable('summary');
        }
    });

    echo Performance::measureCode(function() use ($loops, &$result, $objectB) {
        // Is set: __isset + __get with public var
        for ($i = 0; $i < $loops; $i++) {
            $result = isset($objectB->summary);
        }
    });

    // Set
    echo Performance::measureCode(function() use ($loops, $objectA) {
        // Set: __set
        for ($i = 0; $i < $loops; $i++) {
            $objectA->summary = 'test' . $i;
        }
    });

    echo Performance::measureCode(function() use ($loops, $objectA) {
        // Set: offsetSet
        for ($i = 0; $i < $loops; $i++) {
            $objectA['summary'] = 'test' . $i;
        }
    });

    echo Performance::measureCode(function() use ($loops, $objectA) {
        // Set: setter
        for ($i = 0; $i < $loops; $i++) {
            $objectA->setVariable('summary', 'test' . $i);
        }
    });

    echo Performance::measureCode(function() use ($loops, $objectB) {
        // Set: __set with public var
        for ($i = 0; $i < $loops; $i++) {
            $objectB->summary = 'test' . $i;
        }
    });

    // Set multiple
    echo Performance::measureCode(function() use ($loops, $objectA) {
        // Set multiple: __set
        for ($i = 0; $i < $loops; $i++) {
            $objectA->summary1 = 'test' . $i;
            $objectA->summary2 = 'test' . $i;
            $objectA->summary3 = 'test' . $i;
        }
    });

    echo Performance::measureCode(function() use ($loops, $objectA) {
        // Set multiple: offsetSet
        for ($i = 0; $i < $loops; $i++) {
            $objectA['summary1'] = 'test' . $i;
            $objectA['summary2'] = 'test' . $i;
            $objectA['summary3'] = 'test' . $i;
        }
    });

    echo Performance::measureCode(function() use ($loops, $objectA) {
        // Set multiple: setter
        for ($i = 0; $i < $loops; $i++) {
            $objectA->setVariable('summary1', 'test' . $i);
            $objectA->setVariable('summary2', 'test' . $i);
            $objectA->setVariable('summary3', 'test' . $i);
        }
    });

    echo Performance::measureCode(function() use ($loops, $objectA) {
        // Set multiple: multi-setter
        for ($i = 0; $i < $loops; $i++) {
            $objectA->setVariables([
                'summary1' => 'test' . $i,
                'summary2' => 'test' . $i,
                'summary3' => 'test' . $i
            ]);
        }
    });

    echo Performance::measureCode(function() use ($loops, $objectB) {
        // Set multiple: __set with public var
        for ($i = 0; $i < $loops; $i++) {
            $objectB->summary1 = 'test' . $i;
            $objectB->summary2 = 'test' . $i;
            $objectB->summary3 = 'test' . $i;
        }
    });
})();
