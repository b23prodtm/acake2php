<?php
require dirname(__DIR__) . DIRECTORY_SEPARATOR .
 'Config' . DIRECTORY_SEPARATOR . 'paths.php';
require ROOT . DS . 'vendor' . DS . 'autoload.php';
require CONFIG . 'Schema' . DS . 'CakeSchema.php';
Config\Schema\CakeSchema::main($argv);
