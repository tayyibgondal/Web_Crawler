<?php
include('db/db_connector.php');

$userInput = $_POST['data'];
// $userInput = $_GET['data'];
$searchResults = array();

function sanitizeUserInput($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($userInput) {
    $userInputSanitized = sanitizeUserInput($userInput);

    $searchQuery = "SELECT url, title, meta_description FROM crawled_urls WHERE title LIKE '%$userInputSanitized%' OR meta_description LIKE '%$userInputSanitized%' OR html_content LIKE '%$userInputSanitized%'";
    $resultsOfQuery = mysqli_query($conn, $searchQuery);

    if (mysqli_num_rows($resultsOfQuery) > 0) {
        while ($row = mysqli_fetch_assoc($resultsOfQuery)) {
            $searchResults[] = array(
                'url' => $row['url'],
                'title' => $row['title'],
                'meta_description' => $row['meta_description']
            );
        }
    } else {
        $searchResults[] = array('url' => '', 'title' => 'No search results found!', 'meta_description' => '');
    }

    // Send the JSON response with appropriate headers
    header('Content-Type: application/json');
    echo json_encode($searchResults);
} else {
    $searchResults[] = array('url' => '', 'title' => "Can't query empty input!", 'meta_description' => '');
    header('Content-Type: application/json');
    echo json_encode($searchResults);
}
