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

    // Get
    Performance::measureCode(function() use ($loops, $objectA) {
        // Get: __get
        for ($i = 0; $i < $loops; $i++) {
            $objectA->summary;
        }
    });

    Performance::measureCode(function() use ($loops, $objectA) {
        // Get: offsetGet
        for ($i = 0; $i < $loops; $i++) {
            $objectA['summary'];
        }
    });

    Performance::measureCode(function() use ($loops, $objectA) {
        // Get: getter
        for ($i = 0; $i < $loops; $i++) {
            $objectA->getVariable('summary');
        }
    });

    Performance::measureCode(function() use ($loops, $objectB) {
        // Get: __get w/ public var
        for ($i = 0; $i < $loops; $i++) {
            $objectB->summary;
        }
    });

    // Is set
    Performance::measureCode(function() use ($loops, $objectA) {
        // Is set: __isset
        for ($i = 0; $i < $loops; $i++) {
            isset($objectA->summary);
        }
    });

    Performance::measureCode(function() use ($loops, $objectA) {
        // Is set: offsetExists
        for ($i = 0; $i < $loops; $i++) {
            isset($objectA['summary']);
        }
    });

    Performance::measureCode(function() use ($loops, $objectA) {
        // Is set: has
        for ($i = 0; $i < $loops; $i++) {
            $objectA->hasVariable('summary');
        }
    });

    Performance::measureCode(function() use ($loops, $objectB) {
        // Is set: __isset + __get w/ public var
        for ($i = 0; $i < $loops; $i++) {
            isset($objectB->summary);
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