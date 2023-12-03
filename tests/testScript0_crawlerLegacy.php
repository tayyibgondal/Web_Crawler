<?php
$website_to_crawl = "http://www.learningaboutelectronics.com";
$depth = 50; // Depth parameter to limit the crawling depth
$crawled = array(); // To store crawled URLs
$queue = array(); // Queue to maintain URLs to be crawled

// Function to get links from a URL up to a specified depth
function get_links($url, $depth)
{
    global $crawled, $queue, $website_to_crawl;

    if (!in_array($url, $crawled)) {
        $contents = @file_get_contents($url);
        $regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
        preg_match_all("/$regexp/siU", $contents, $matches);
        $path_of_url = parse_url($url, PHP_URL_HOST);

        $links_in_array = $matches[2];

        foreach ($links_in_array as $link) {
            if (!in_array($link, $crawled) && !in_array($link, $queue) && strpos($link, $website_to_crawl) !== false) {
                array_push($queue, $link);
            }
        }
        array_push($crawled, $url);
    }

    if ($depth > 0 && count($queue) > 0) {
        $nextUrl = array_shift($queue);
        get_links($nextUrl, $depth - 1);
    }
}

// Start crawling from the seed URL
get_links($website_to_crawl, $depth);

$count = count($crawled);

echo "There are $count links found by the crawler <br>";
foreach ($crawled as $link) {
    echo $link . "<br>";
}
