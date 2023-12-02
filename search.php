<?php
include('db/db_connector.php');

$userInput = $_POST['data'];
$searchResults = array();

function sanitizeUserInput($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($userInput) {
    // Sanitize the query
    $userInputSanitized = sanitizeUserInput($userInput);

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

    // Convert the search results to JSON and send it as the response
    header('Content-Type: application/json');
    echo json_encode($searchResults);
} else {
    $searchResults[] = "Can't query emtpy input!";
    header('Content-Type: application/json');
    echo json_encode($searchResults);
}
