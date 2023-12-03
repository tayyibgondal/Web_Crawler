<?php
$db_server = 'localhost';
$db_user = 'root';
$db_password = '';
$db_name = 'urldb'; 

// Connecting to MySQL without selecting a database initially
$conn = mysqli_connect($db_server, $db_user, $db_password, $db_name); 

if (!$conn) {
    echo "Connection Unsuccessful! <br>";
}