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
        $config = require "{$fixinPath}/config/tools.php";
    }

    // Autoloader
    $classesPath = "{$fixinPath}/classes";

    require_once "{$classesPath}/Fixin/Base/Autoloader/SimpleAutoloader.php";
    new \Fixin\Base\Autoloader\SimpleAutoloader($config['loader']['prefixes'] ?? ['Fixin' => "{$classesPath}/Fixin", 'FixinTools' => "{$fixinPath}/tools/classes/FixinTools"]);

    // Resource Manager
    return new \Fixin\Resource\ResourceManager($config['resourceManager']);
})($config ?? null);
