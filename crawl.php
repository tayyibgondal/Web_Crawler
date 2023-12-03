<?php
$website_to_crawl = "https://en.wikipedia.org/wiki/Ice_cream";
$crawled = array();
$queue = array();
$depth = 3;
$htmls = array(); // Array to store HTML contents with titles

function saveToDatabase($url, $htmlContent)
{
    include('db/db_connector.php');
    // echo "$url <br>";

    // Check if the URL already exists in the database
    $stmt = $conn->prepare("SELECT * FROM crawled_urls WHERE url = ?");
    $stmt->bind_param("s", $url);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) { // If the URL doesn't exist in the database
        // Extract title and meta description
        $doc = new DOMDocument();
        @$doc->loadHTML($htmlContent);
        $xpath = new DOMXPath($doc);

        // Extract title
        $titles = $xpath->query('//title');
        $title = $titles->length > 0 ? $titles[0]->textContent : '';
        // Extract meta description
        $metaDescriptions = $xpath->query('//meta[@name="description"]');
        $metaDescription = $metaDescriptions->length > 0 ? $metaDescriptions[0]->getAttribute('content') : '';
        // Clean data
        $title = htmlspecialchars(trim($title));
        $metaDescription = htmlspecialchars(trim($metaDescription));
        $htmlContent = htmlspecialchars(trim($htmlContent));

        // Save to the database
        // Prepare and execute SQL statement to insert data into the table
        $stmt = $conn->prepare("INSERT INTO crawled_urls (url, title, meta_description, html_content) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $url, $title, $metaDescription, $htmlContent);
        $stmt->execute();

        // Close connection
        $stmt->close();
    } else {
        // echo "URL already exists in the database: $url<br>";
    }

    $conn->close();
}

function removeApostrophesFromStartEnd($str)
/* 
*  Helper function for 'get_links_helper' function
*/
{
    $str = trim($str, "'`/"); // Trim specified characters from both ends of the string
    $str = trim($str, "'`/"); // Trim specified characters from both ends of the string
    return $str;
}

function retrieveHTMLContent($url)
/* 
*  Helper function for 'get_links_helper' function
*/
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects

    $contents = curl_exec($ch);
    if ($contents === false) {
        $error = curl_error($ch);
        // Handle the error accordingly, log or display
        $contents = "Failed to retrieve content: " . $error;
    }

    curl_close($ch);
    return $contents;
}

function get_links_helper($url)
{
    global $crawled, $queue;

    // Extract content from the current URL using cURL
    $contents = retrieveHTMLContent($url);

    // Extract anchor tags
    $regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
    preg_match_all("/$regexp/siU", $contents, $matches);

    // If they haven't been in crawl list, add them to it. 
    // Also put them in a queue, so that they know their children will also be crawled
    $links_in_html_content = $matches[2];
    foreach ($links_in_html_content as $link) {
        if (
            !in_array($link, $crawled) &&
            !in_array($link, $queue) &&
            (strpos($link, 'http://') === 0 || strpos($link, 'https://') === 0)
        ) {
            $link = removeApostrophesFromStartEnd($link);
            array_push($crawled, $link); // Mark as crawled
            array_push($queue, $link);  // To hunt for this URL later
            // Save information regarding crawled link to Database
            $htmlContent = retrieveHTMLContent($link);
            saveToDatabase($link, $htmlContent);
        }
    }

    // Remove from the queue
    array_shift($queue);
}

function get_links($website_to_crawl, $depth)
{
    global $queue, $crawled;
    array_push($crawled, $website_to_crawl); // Mark as crawled
    array_push($queue, $website_to_crawl);  // Say that i'll hunt you URL!
    // Save information regarding crawled link to Database
    $htmlContent = retrieveHTMLContent($website_to_crawl);
    saveToDatabase($website_to_crawl, $htmlContent);

    get_links_helper($website_to_crawl);  // Hunt the url

    for ($i = 0; $i < $depth; $i++) {
        foreach ($queue as $link_to_go_after) {
            get_links_helper($link_to_go_after);
        }
    }
}

get_links($website_to_crawl, $depth);
