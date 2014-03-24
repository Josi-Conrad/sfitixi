<?php
include 'mysql_connection.php';

//uses 1 Parameter for .sql file
$sql_file = $argv[1];

// connect
$mysqli = new mysqli($mysql_host, $mysql_username, $mysql_password, $mysql_database);
// check connection
if ($mysqli->connect_error) {
    trigger_error('Database connection failed: ' . $mysqli->connect_error, E_USER_ERROR);
}
$sql = file_get_contents($sql_file);
if (!$sql) {
    die ("Error opening file: " . $sql_file . "\n");
}
mysqli_multi_query($mysqli, $sql);
echo "Tables from " . $sql_file . " imported successfully!" .  "\n";
$mysqli->close();
?>