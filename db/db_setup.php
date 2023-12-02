<?php
include('db_connector.php');

$query = "
    -- Create a table named 'urls' to store URLs with an index
    CREATE TABLE IF NOT EXISTS urls (
        id INT AUTO_INCREMENT PRIMARY KEY,
        url VARCHAR(255)
    );

    -- Insert 10 records into the 'urls' table
    INSERT INTO urls (url) VALUES
        ('https://example.com/page1'),
        ('https://example.com/page2'),
        ('https://example.com/page3'),
        ('https://example.com/page4'),
        ('https://example.com/page5'),
        ('https://example.com/page6'),
        ('https://example.com/page7'),
        ('https://example.com/page8'),
        ('https://example.com/page9'),
        ('https://example.com/page10');
    ";

$result = mysqli_multi_query($conn, $query);

if ($result) {
    echo "Initial setup successful! <br>";
}
