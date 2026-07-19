<?php

function healthFail($message, array $checks)
{
    header('Content-Type: application/json');
    header('HTTP/1.1 503 Service Unavailable');
    echo json_encode([
        'status' => 'error',
        'message' => $message,
        'checks' => $checks,
    ]);
    exit;
}

$checks = [];
$dbDriver = strtoupper(getenv('DB') ?: 'MYSQL');
$dbHost = getenv($dbDriver . '_HOST') ?: getenv('MYSQL_HOST') ?: 'mysqldb';
$dbPort = (int)(getenv('MYSQL_TCP_PORT') ?: 3306);
$dbUser = getenv('MYSQL_USER') ?: 'root';
$dbPassword = getenv('MYSQL_PASSWORD') ?: getenv('MYSQL_ROOT_PASSWORD') ?: '';
$dbName = getenv('MYSQL_DATABASE') ?: '';
$storagePath = rtrim(getenv('BALENA_STORAGE_PATH') ?: '/mnt/external-drives', '/');

// Check the runtime database dependency through the same credentials used by CakePHP.
$mysqli = @new mysqli($dbHost, $dbUser, $dbPassword, $dbName, $dbPort);
if ($mysqli->connect_error) {
    $checks['database'] = 'error';
    healthFail('MariaDB connection failed: ' . $mysqli->connect_error, $checks);
}
$mysqli->close();
$checks['database'] = 'ok';

// Check the shared Balena storage mount by writing a short-lived probe file.
if (!is_dir($storagePath)) {
    $checks['storage'] = 'error';
    healthFail('Storage path is missing: ' . $storagePath, $checks);
}

$probeFile = $storagePath . '/.healthcheck';
if (@file_put_contents($probeFile, "ok\n") === false) {
    $checks['storage'] = 'error';
    healthFail('Storage path is not writable: ' . $storagePath, $checks);
}
@unlink($probeFile);
$checks['storage'] = 'ok';

header('Content-Type: application/json');
echo json_encode([
    'status' => 'ok',
    'checks' => $checks,
]);
