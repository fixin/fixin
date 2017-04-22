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
    require "{$classesPath}/Fixin/Base/Autoloader/SimpleAutoloader.php";
    new \Fixin\Base\Autoloader\SimpleAutoloader(['Fixin' => "{$classesPath}/Fixin"]);

    // Config
    if (!isset($config)) {
        $config = require "{$fixinPath}/config/web.php";
    }

    // Application
    return new \Fixin\Application\Application($config);
})($config ?? null);
