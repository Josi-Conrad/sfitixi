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

$succ = true;
if ($mysqli->multi_query($sql)) {
    do {
        /* store first result set */
        if ($result = $mysqli->store_result()) {
            while ($row = $result->fetch_row()) {
                printf("%s\n", $row[0]);
            }
            $result->free();
        }
        if ($mysqli->error) {
            $succ = false;
            trigger_error($mysqli->error, E_USER_ERROR);
        }
        if (!$mysqli->more_results()) {
            break;
        }
        if (!$mysqli->next_result()) {
            break;
        }
    } while (true);
}

$mysqli->close();

if($succ){
    echo "Tables from " . $sql_file . " imported!" . "\n";
} else {
    echo "Some error happend!" . "\n";
}

?>