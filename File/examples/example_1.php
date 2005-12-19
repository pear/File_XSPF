<?php
require_once 'File/XSPF.php';

$xspf =& new File_XSPF();
$xspf->setCreator('Joe Bloggs');
$xspf->setLicense('LGPL');

$track =& new File_XSPF_Track();
$track->setAlbum('OK Computer');
$track->setDuration(324000);
$track->setTitle('The Tourist');
$track->setTrackNumber('12');
$track->setCreator('Radiohead');

$xspf->addTrack($track);
$xspf->toStream();
?>