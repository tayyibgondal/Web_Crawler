<?php
include('db/db_connector.php');

$userInputSanitized = 'example';

// Sql query
$searchQuery = "SELECT url FROM urls WHERE url LIKE '%$userInputSanitized%'";
$resultsOfQuery = mysqli_query($conn, $searchQuery);

if (mysqli_num_rows($resultsOfQuery) > 0) {
    while ($row = mysqli_fetch_assoc($resultsOfQuery)) {
        $searchResults[] = $row['url'];
    }
} else {
    $searchResults[] = "No search results found!";
}

echo json_encode($searchResults);
