<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

return (function($config) {
    $fixinPath = dirname(__DIR__);

    // Autoloader
    $classesPath = "{$fixinPath}/classes";
    require_once "{$classesPath}/Fixin/Base/Autoloader/SimpleAutoloader.php";
    new \Fixin\Base\Autoloader\SimpleAutoloader(['Fixin' => "{$classesPath}/Fixin", 'FixinTools' => "{$fixinPath}/tools/classes/FixinTools"]);

    // Config
    if (!isset($config)) {
        $config = require "{$fixinPath}/config/tools.php";
    }

    // Resource Manager
    return new \Fixin\Resource\ResourceManager($config['resourceManager']);
})($config ?? null);