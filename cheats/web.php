<?php
$fixinPath = dirname(__DIR__);

// Autoloader
$classesPath = "{$fixinPath}/classes";
require "$classesPath/Fixin/Loader/SimpleLoader.php";
$autoloader = new \Fixin\Loader\SimpleLoader(['Fixin' => "$classesPath/Fixin"]);
$autoloader->register();

// Config
$config = require "{$fixinPath}/config/web.php";

// Application
return new \Fixin\Application\Application($config);