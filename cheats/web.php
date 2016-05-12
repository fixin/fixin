<?php
$fixinPath = dirname(__DIR__);

// Autoloader
$classesPath = "{$fixinPath}/classes";
require "{$classesPath}/Fixin/Base/Autoloader/SimpleAutoloader.php";
$autoloader = new \Fixin\Base\Autoloader\SimpleAutoloader(['Fixin' => "{$classesPath}/Fixin"]);
$autoloader->register();

// Config
$config = require "{$fixinPath}/config/web.php";

// Application
return new \Fixin\Application\Application($config);