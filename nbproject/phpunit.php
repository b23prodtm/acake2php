#!/usr/bin/env php
<?php
array_shift($argv);
foreach ($argv as $idx => $arg) {
	if (preg_match('/NetBeansSuite.php$/', $arg)) {
		$argv[$idx] = __DIR__ . DIRECTORY_SEPARATOR . 'NetBeansSuite.php' ;
	}
}
$command = '/Users/wwwb23prodtminfo/NetBeansProjects/OpenShift/phpcms/app/Vendor/phpunit/phpunit/composer/bin/phpunit';
$args = join(' ', $argv);

passthru($command . ' ' . $args);