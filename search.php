<?php
// Simulate a search query and return dummy data
$searchQuery = $_POST['data']; // Assuming 'data' is the name of the input field

// Perform a search or generate dummy data based on the received query
if ($searchQuery) {
    // Perform a search operation using $searchQuery (e.g., querying a database)
    // Replace this section with your actual search logic

    // For demonstration purposes, return dummy search results
    $searchResults = array(
        array('title' => 'Result 1', 'description' => 'Description 1'),
        array('title' => 'Result 2', 'description' => 'Description 2'),
        array('title' => 'Result 3', 'description' => 'Description 3')
    );

    // Convert the search results to JSON and send it as the response
    header('Content-Type: application/json');
    echo json_encode($searchResults);
} else {
    // If no search query is provided, return an error message or handle as needed
    echo "No search query provided.";
}
