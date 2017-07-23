<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

return (function(?array $config) {
    $fixinPath = dirname(__DIR__);

    // Config
    if (!isset($config)) {
        $config = require "{$fixinPath}/config/minimal.php";
    }

    // Autoloader
    if (!spl_autoload_functions()) {
        $classesPath = "{$fixinPath}/classes";

        require_once "{$classesPath}/Fixin/Base/Autoloader/SimpleAutoloader.php";
        new \Fixin\Base\Autoloader\SimpleAutoloader($config['loader']['prefixes'] ?? ['Fixin' => "{$classesPath}/Fixin"]);
    }

    // Resource Manager
    return new \Fixin\Resource\ResourceManager($config['resourceManager']);
})($config ?? null);
