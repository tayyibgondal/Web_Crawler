<?php
include('db_connector.php');

$query = "CREATE TABLE IF NOT EXISTS crawled_urls (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        url VARCHAR(255) NOT NULL,
        title VARCHAR(255),
        meta_description VARCHAR(255),
        html_content TEXT NOT NULL
        )";

$result = mysqli_query($conn, $query);

if ($result) {
    echo "Initial setup successful! <br>";
}
