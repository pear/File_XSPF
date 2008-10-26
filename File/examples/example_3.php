<?php
/**
 * +---------------------------------------------------------------------------+
 * | File_XSPF PEAR Package for Manipulating XSPF Playlists                    |
 * | Copyright (c) 2005 David Grant <david@grant.org.uk>                       |
 * +---------------------------------------------------------------------------+
 * | This library is free software; you can redistribute it and/or             |
 * | modify it under the terms of the GNU Lesser General Public                |
 * | License as published by the Free Software Foundation; either              |
 * | version 2.1 of the License, or (at your option) any later version.        |
 * |                                                                           |
 * | This library is distributed in the hope that it will be useful,           |
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of            |
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU         |
 * | Lesser General Public License for more details.                           |
 * |                                                                           |
 * | You should have received a copy of the GNU Lesser General Public          |
 * | License along with this library; if not, write to the Free Software       |
 * | Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301 USA |
 * +---------------------------------------------------------------------------+
 *
 * PHP version 4
 *
 * @category  File
 * @package   File_XSPF
 * @author    David Grant <david@grant.org.uk>
 * @copyright 2005 David Grant
 * @license   http://www.gnu.org/copyleft/lesser.html GNU LGPL
 * @version   CVS: $Id$
 * @link      http://www.xspf.org/
 */
require_once 'File/XSPF.php';
require_once 'File/Ogg.php';
require_once 'MP3/Id.php';

/**
 * Recurse over a library.
 *
 * @param string    $path  File path
 * @param File_XSPF &$xspf File_XSPF instance to populate
 *
 * @return void
 */
function recurseLibrary($path, &$xspf)
{
    // Open the directory for reading.
    $dp = opendir($path);
    if (! is_resource($dp)) {
        return;
    }

    // Recurse the directory.
    while ($file = readdir($dp)) {
        // Set the absolute path of this file.
        $realpath = realpath($path . DIRECTORY_SEPARATOR . $file);
        if (is_dir($realpath) && $file != "." && $file != "..") {
            // Continue down to the next level.
            recurseLibrary($realpath, $xspf);
        } else {
            // Check the basic file information.
            $pathinfo = pathinfo($realpath);
            if (!isset($pathinfo['extension'])) {
                continue;
            }

            if (!in_array($pathinfo['extension'], array('ogg', 'mp3'))) {
                continue;
            }

                
            if ($pathinfo['extension'] == 'ogg') {
                // Instantiate a new File_Ogg instance.
                $ogg =& new File_Ogg($realpath);
    
                // Check for a vorbis logical stream.
                if (! $ogg->hasStream(OGG_STREAM_VORBIS)) {
                    continue;
                }
    
                // Extract the streams from the audio file.
                $streams = $ogg->listStreams(OGG_STREAM_VORBIS);
                $serial  = current($streams);
                $vorbis  = $ogg->getStream($serial);
                if (! is_object($vorbis)) {
                    continue;
                }
    
                // Extract the information from the audio stream.
                $album  = $vorbis->getAlbum();
                $artist = $vorbis->getArtist();
                $title  = $vorbis->getTitle();
                $number = $vorbis->getTrackNumber();
                $length = $vorbis->getLength();
            } else {
                $id3 =& new MP3_Id();
                
                $id3->read($realpath);
                
                $album  = $id3->getTag('album');
                $artist = $id3->getTag('artists');
                $title  = $id3->getTag('name');
                $number = $id3->getTag('track');
                $length = $id3->lengths;
            }
            
            // Instantiate the new File_XSPF_Track instance.
            $track =& new File_XSPF_Track();
            $track->setAlbum($album);
            $track->setCreator($artist);
            $track->setTitle($title);
            $track->setTrackNumber($number);
            $track->setDuration($length * 1000);
            $track->addLocation("file://" . $realpath);
            $track->setIdentifier("sha1://" . sha1_file($realpath));
            
            // Add the track to this playlist.
            $xspf->addTrack($track);
        }
    }
    closedir($dp);
}

// Instantiate the new playlist.
$xspf =& new File_XSPF();
$xspf->setAnnotation('This is a collection of all my Ogg Vorbis tracks.');
$xspf->setCreator('Joe Bloggs');
// This playlist is automatically generated.
$xspf->setDate(time());
$xspf->setLicense('http://www.gnu.org/copyleft/gpl.html');
// This is where Joe will store this playlist.
$xspf->setLocation('http://www.example.org/~joe/vorbis.xspf');
$xspf->setTitle('All Ogg Vorbis Tracks');

// Recurse Joe's music library.
$path = "/home/joe/tunes/";
recurseLibrary($path, $xspf);

// Upload Joe's music library playlist to his FTP account.
$xspf->toFile("ftp://joe:bloggs@ftp.example.org/vorbis.xspf");
?>
