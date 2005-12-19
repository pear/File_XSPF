<?php
require_once('File/XSPF.php');

$username = 'djg';

$recent = new File_XSPF("http://ws.audioscrobbler.com/1.0/user/$username/recenttracks.xspf");
$tracks = $recent->getTracks();
if (count($tracks)) {
    $latest = current($tracks);
    echo "The track I most recently listened to is...\n";
    echo '<a href="' . $latest->getInfo() . '">' . $latest->getTitle() . '</a> by ' . $latest->getCreator();
}

$favs   = new File_XSPF("http://ws.audioscrobbler.com/1.0/user/$username/toptracks.xspf");
$tracks = $favs->getTracks();
if (count($tracks)) {
    $top = current($tracks);
    echo "My currrent favourite track is...\n";
    echo '<a href="' . $top->getInfo() . '">' . $top->getTitle() . '</a> by ' . $top->getCreator();
}
?>