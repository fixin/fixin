<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

use Fixin\Support\Performance;
use FixinTools\Performance\Magic\VariablesA;
use FixinTools\Performance\Magic\VariablesB;

(function() {
    include dirname(__DIR__, 3) . '/cheats/tools.php';

    $loops = 2000000;
    $objectA = new VariablesA();
    $objectB = new VariablesB();
    $result = null;

    // Get
    Performance::measureCode(function() use ($loops, &$result, $objectA) {
        // Get: __get
        for ($i = 0; $i < $loops; $i++) {
            $result = $objectA->summary;
        }
    });

    Performance::measureCode(function() use ($loops, &$result, $objectA) {
        // Get: offsetGet
        for ($i = 0; $i < $loops; $i++) {
            $result = $objectA['summary'];
        }
    });

    Performance::measureCode(function() use ($loops, &$result, $objectA) {
        // Get: getter w/ name
        for ($i = 0; $i < $loops; $i++) {
            $result = $objectA->getVariable('summary');
        }
    });

    Performance::measureCode(function() use ($loops, &$result, $objectA) {
        // Get: getter
        for ($i = 0; $i < $loops; $i++) {
            $result = $objectA->getTest();
        }
    });

    Performance::measureCode(function() use ($loops, &$result, $objectB) {
        // Get: __get w/ public var
        for ($i = 0; $i < $loops; $i++) {
            $result = $objectB->summary;
        }
    });

    // Is set
    Performance::measureCode(function() use ($loops, &$result, $objectA) {
        // Is set: __isset
        for ($i = 0; $i < $loops; $i++) {
            $result = isset($objectA->summary);
        }
    });

    Performance::measureCode(function() use ($loops, &$result, $objectA) {
        // Is set: offsetExists
        for ($i = 0; $i < $loops; $i++) {
            $result = isset($objectA['summary']);
        }
    });

    Performance::measureCode(function() use ($loops, &$result, $objectA) {
        // Is set: has
        for ($i = 0; $i < $loops; $i++) {
            $result = $objectA->hasVariable('summary');
        }
    });

    Performance::measureCode(function() use ($loops, &$result, $objectB) {
        // Is set: __isset + __get w/ public var
        for ($i = 0; $i < $loops; $i++) {
            $result = isset($objectB->summary);
        }
    });

    // Set
    Performance::measureCode(function() use ($loops, $objectA) {
        // Set: __set
        for ($i = 0; $i < $loops; $i++) {
            $objectA->summary = 'test' . $i;
        }
    });

    Performance::measureCode(function() use ($loops, $objectA) {
        // Set: offsetSet
        for ($i = 0; $i < $loops; $i++) {
            $objectA['summary'] = 'test' . $i;
        }
    });

    Performance::measureCode(function() use ($loops, $objectA) {
        // Set: setter
        for ($i = 0; $i < $loops; $i++) {
            $objectA->setVariable('summary', 'test' . $i);
        }
    });

    Performance::measureCode(function() use ($loops, $objectB) {
        // Set: __set w/ public var
        for ($i = 0; $i < $loops; $i++) {
            $objectB->summary = 'test' . $i;
        }
    });

    // Set multiple
    Performance::measureCode(function() use ($loops, $objectA) {
        // Set multiple: __set
        for ($i = 0; $i < $loops; $i++) {
            $objectA->summary1 = 'test' . $i;
            $objectA->summary2 = 'test' . $i;
            $objectA->summary3 = 'test' . $i;
        }
    });

    Performance::measureCode(function() use ($loops, $objectA) {
        // Set multiple: offsetSet
        for ($i = 0; $i < $loops; $i++) {
            $objectA['summary1'] = 'test' . $i;
            $objectA['summary2'] = 'test' . $i;
            $objectA['summary3'] = 'test' . $i;
        }
    });

    Performance::measureCode(function() use ($loops, $objectA) {
        // Set multiple: setter
        for ($i = 0; $i < $loops; $i++) {
            $objectA->setVariable('summary1', 'test' . $i);
            $objectA->setVariable('summary2', 'test' . $i);
            $objectA->setVariable('summary3', 'test' . $i);
        }
    });

    Performance::measureCode(function() use ($loops, $objectA) {
        // Set multiple: multi-setter
        for ($i = 0; $i < $loops; $i++) {
            $objectA->setVariables([
                'summary1' => 'test' . $i,
                'summary2' => 'test' . $i,
                'summary3' => 'test' . $i
            ]);
        }
    });

    Performance::measureCode(function() use ($loops, $objectB) {
        // Set multiple: __set w/ public var
        for ($i = 0; $i < $loops; $i++) {
            $objectB->summary1 = 'test' . $i;
            $objectB->summary2 = 'test' . $i;
            $objectB->summary3 = 'test' . $i;
        }
    });
})();