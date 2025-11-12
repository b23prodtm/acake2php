#!/usr/bin/php -q
<?php
// Check platform requirements
require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'paths.php';
require CONFIG . 'requirements.php';
require ROOT .DS . 'vendor' . DS . 'autoload.php';

use App\Application;
use Cake\Console\CommandRunner;

// Build the runner with an application and root executable name.
$runner = new CommandRunner(new Application(CONFIG), 'cake');
exit($runner->run($argv));
