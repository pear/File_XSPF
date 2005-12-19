<?php
require_once 'File/XSPF.php';

// Open my 'favourites' playlist.
$favs      =& new File_XSPF('/home/joe/tunes/favourites.xspf');
$radiohead =& new File_XSPF();

$radiohead->setCreator('Joe Bloggs');
$radiohead->setTitle('My Radiohead Playlist');

$tracks = $favs->getTracks();
// Loop through all the tracks in my favourite playlist.
foreach ($tracks as $track) {
    // If the track appears to be by Radiohead, add it to my Radiohead playlist.
    if (strpos($track->getCreator(), 'Radiohead') !== false) {
        $radiohead->addTrack($track);
    }
}
// Write out my Radiohead playlist to a file in my tunes folder.
$radiohead->toFile('/home/joe/tunes/radiohead.xspf');
?>