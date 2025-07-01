<?php
require_once __DIR__."/../app/Config/Schema/CakeSchema.php";
print "#!/usr/bin/env bash\n";
print "PATH=\"\$PATH:".__DIR__."/../app/bin/\"\n";
Config\Schema\CakeSchema::main($argv);
