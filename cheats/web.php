<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

return (function(?array $config) {
    $fixinPath = dirname(__DIR__);

    // Config
    if (!isset($config)) {
        $config = require "{$fixinPath}/config/web.php";
    }

    // Autoloader
    $classesPath = "{$fixinPath}/classes";

    require_once "{$classesPath}/Fixin/Base/Autoloader/SimpleAutoloader.php";
    new \Fixin\Base\Autoloader\SimpleAutoloader($config['loader']['prefixes'] ?? ['Fixin' => "{$classesPath}/Fixin"]);

    // Application
    return new \Fixin\Application\Application($config);

})($config ?? null);
