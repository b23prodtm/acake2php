<?php
require dirname(__DIR__) . '/vendor/autoload.php';
require APP . "Config/Schema/CakeSchema.php";
Config\Schema\CakeSchema::main($argv);
