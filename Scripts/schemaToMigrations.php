<?php
require_once __DIR__."/../app/config/Schema/CakeSchema.php";
if($argc < 2) print "Usage: __FILE__ <file.php>\n";
else {
	for($i=1; $i < count($argv); $i++) {
		Config\Schema\CakeSchema::main(__DIR__."/".$argv[$i]);
	}
}
