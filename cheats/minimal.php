<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

return (function() {
    $fixinPath = dirname(__DIR__);

    // Autoloader
    $classesPath = "{$fixinPath}/classes";
    require "{$classesPath}/Fixin/Base/Autoloader/SimpleAutoloader.php";
    $autoloader = new \Fixin\Base\Autoloader\SimpleAutoloader(['Fixin' => "{$classesPath}/Fixin"]);
})();