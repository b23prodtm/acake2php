<?php

$servername = getenv(strtoupper(getenv("DB"))."_SERVICE_HOST");
$username = getenv("DATABASE_USER");
$password = getenv("MYSQL_ROOT_PASSWORD");

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    header("HTTP/1.1 503 Service Unavailable");
    die("Connection failed: " . $conn->connect_error);
}
echo "OK";
?>
