<?php
// Include the SimplePie library
require_once(($_SERVER['DOCUMENT_ROOT'] . '/simplepie.php'));
 
// Because we're using multiple feeds, let's just set the headers here.
header("Content-type: text/xml; charset=utf-8"); 
// These are the feeds we want to use
$feeds = array(
	'http://www.theverge.com/rss/index.xml',
	'http://www.polygon.com/rss/index.xml',
	'http://feeds.sidebar.io/SidebarFeed?format=xml',
	'http://www.npr.org/rss/rss.php?id=1001',
	'http://hosted2.ap.org/atom/APDEFAULT/3d281c11a96b4ad082fe88aa0db04305',
	'http://feeds.feedburner.com/tympanus?format=xml',
	
	
);
 
// This array will hold the items we'll be grabbing.
$first_items = array();
 
// Let's go through the array, feed by feed, and store the items we want.
foreach ($feeds as $url)
{
    // Use the long syntax
    $feed = new SimplePie();
    $feed->set_feed_url($url);
    $feed->set_cache_duration (600); // Set the cache time
    $feed->enable_xml_dump(isset($_GET['xmldump']) ? true : false);
    $feed->enable_order_by_date(true);
		$feed->set_cache_location($_SERVER['DOCUMENT_ROOT'] . '/cache');
    $feed->init();
 
	// How many items per feed should we try to grab?
	$items_per_feed = 5;
 
	// As long as we're not trying to grab more items than the feed has, go through them one by one and add them to the array.
	for ($x = 0; $x < $feed->get_item_quantity($items_per_feed); $x++)
	{
		$first_items[] = $feed->get_item($x);
	}
 
    // We're done with this feed, so let's release some memory.
    unset($feed);
}
// We need to sort the items by date with a user-defined sorting function.  Since usort() won't accept "SimplePie::sort_items", we need to wrap it in a new function.




$xml = new SimpleXMLElement('<rss version="2.0"></rss>');
  $xml->addChild('channel'); 
    $xml->channel->addChild('title', 'Bird Feed'); 
    $xml->channel->addChild('link', 'http://jordancauley.com');

foreach($first_items as $item):
	$feed = $item->get_feed();
	
$track = $xml->channel->addChild('item');

$track->addChild('title', $item->get_title());
$track->addChild('link', $item->get_link());
$track->addChild('author', $feed->get_title());
$track->addChild('pubDate', $item->get_date('D, d M Y H:i:s T'));
$track->addChild('poster', $feed->get_image_url());
endforeach;

print($xml->asXML());
?>
