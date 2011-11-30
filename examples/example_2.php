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
 * PHP version 5
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

// Open my 'favourites' playlist.
$favs = new File_XSPF();
$favs->parseFile('/home/joe/tunes/favourites.xspf');
$radiohead = new File_XSPF();

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
