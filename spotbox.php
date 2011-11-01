<?php

if (DEBUG) {
     $start = microtime();
 }

function songResult($response) {
  if (count($response) < 1) {
     echo "No results";
     die();
  }
  if (isset($response['errorid'])) {
     echo "<pre>Error: " . $response['errorid'] . "\nMsg: " . $response['errormsg'] . "</pre>";
     die();
  }
  $out = "";
  $out .= "<p>Found " . count($response) . " items.</p>\n";
  $out .= "<ul>\n";
  foreach ($response as $content) {
     $out .= "\t<li>[<a href=\"details.php?id=" . $content->getURI() . "\">details</a> | <a href=\"/?add=" . $content->getURI() . "\">Add to the playlist</a>] :: <a href=\"" . $content->getURL() . "\">" . $content . "</a></li>\n";
  }
  $out .= "</ul>\n";
  return $out;
}


function queueTrack($uri) {
  file_put_contents('/var/www/spotbox-playlists/tracklists.txt', $uri, FILE_APPEND | LOCK_EX);
}

if (isset($_GET['add'])) {
  queueTrack($_GET['add'] . "\n");
}

if (isset($_POST['checkTime']) && $_POST['checkTime'] > time() - 60 * 5) {

  // Initiate the MetaTune object.
  $spotiy = MetaTune::getInstance();
  $out = '<div class="masterResult">';                

  if (!empty($_POST['track'])) {
     // Search and get a list of tracks/song. 
     $response = $spotiy->searchTrack($_POST['track']);
   
     $out .= "\t<div class=\"last\">\n\t<h2>Tracks</h2>\n";
     $out .= songResult($response);
     $out .= "\t</div>\n";
  }
  $out .= "</div>\n";
  echo $out;
}

if (DEBUG) {
  $end = microtime();
  echo "<pre>Debug time: " . ($end - $start) . "</pre>";
}
