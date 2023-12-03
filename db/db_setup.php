<?php
$db_server = 'localhost';
$db_user = 'root';
$db_password = '';
$db_name = 'urldb'; 

// Connecting to MySQL without selecting a database initially
$conn = mysqli_connect($db_server, $db_user, $db_password, ''); 
// $conn = mysqli_connect($db_server, $db_user, $db_password, $db_name, 3307); 

if (!$conn) {
    echo "Connection Unsuccessful! <br>";
}

// Creating the database if it doesn't exist
$create_db_query = "CREATE DATABASE IF NOT EXISTS $db_name";
$result_db = mysqli_query($conn, $create_db_query);

if (!$result_db) {
    echo "Error creating database: " . mysqli_error($conn) . "<br>";
} 
 
// Selecting the created database
mysqli_select_db($conn, $db_name);

// Creating table crawled_urls
$create_table_query = "CREATE TABLE IF NOT EXISTS crawled_urls (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    url VARCHAR(255) NOT NULL,
    title VARCHAR(255),
    meta_description VARCHAR(255),
    html_content TEXT NOT NULL
)";
$result_table = mysqli_query($conn, $create_table_query);

if ($result_table) {
    echo "Table created or already exists! <br>";
} else {
    echo "Error creating table: " . mysqli_error($conn) . "<br>";
}
