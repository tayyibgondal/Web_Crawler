<?php
$website_to_crawl = "https://en.wikipedia.org/wiki/Hippopotomonstrosesquipedaliophobia";
$crawled = array();
$queue = array();
$depth = 1;
$htmls = array(); // Array to store HTML contents with titles

function saveToDatabaseHelper($url, $htmlContent)
{
    include('testScript4_db_connector.php');

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
    $conn->close();
}

function saveToDatabase()
{
    global $crawled;
    foreach ($crawled as $link) {
        // Save information regarding crawled link to Database
        $htmlContent = retrieveHTMLContent($link);
        saveToDatabase($link, $htmlContent);
    }
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

function get_links_helper($url, $depth)
{
    global $crawled, $queue;

    if ($depth <= 0) {
        return; // Terminate if depth is reached
    }

    // Hunt the URL
    $contents = retrieveHTMLContent($url);

    // Extract anchor tags
    $regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
    preg_match_all("/$regexp/siU", $contents, $matches);

    // Process links found in the content
    $links_in_html_content = $matches[2];
    foreach ($links_in_html_content as $link) {
        $link = removeApostrophesFromStartEnd($link);
        if (
            !in_array($link, $crawled) &&
            (strpos($link, 'http://') === 0 || strpos($link, 'https://') === 0)
        ) {
            array_push($crawled, $link); // Mark as crawled
            array_push($queue, $link);

            echo "$link <br>"; // For demonstration, output the crawled link

            // Recursive call to continue crawling
            get_links_helper($link, $depth - 1);
        }
    }
}

function get_links($website_to_crawl, $depth)
{
    global $crawled, $queue;
    array_push($crawled, $website_to_crawl); // Mark as crawled
    array_push($queue, $website_to_crawl);  // Say that i'll hunt you URL!
    echo "$website_to_crawl <br>";

    get_links_helper($website_to_crawl, $depth);
}

get_links($website_to_crawl, $depth);
saveToDatabase();