<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

return (function (?array $config) {
    $fixinPath = dirname(__DIR__);

    // Config
    if (!isset($config)) {
        $config = require "{$fixinPath}/config/console.php";
    }

    // Autoloader
    if (!spl_autoload_functions()) {
        $classesPath = "{$fixinPath}/classes";

        require_once "{$classesPath}/Base/Autoloader/SimpleAutoloader.php";
        new \Fixin\Base\Autoloader\SimpleAutoloader($config['loader']['prefixes'] ?? ['Fixin' => $classesPath]);
    }

    // Application
    return new \Fixin\Application\Application($config);

})($config ?? null);
