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

$username = 'djg';

$path = "http://ws.audioscrobbler.com/1.0/user/$username/recenttracks.xspf";

$recent = new File_XSPF();
$recent->parseFile($path);

$tracks = $recent->getTracks();
if (count($tracks)) {
    $latest = current($tracks);
    echo "The track I most recently listened to is...\n";
    echo '<a href="' . $latest->getInfo() . '">' . $latest->getTitle() 
         . '</a> by ' . $latest->getCreator();
}

$path   = "http://ws.audioscrobbler.com/1.0/user/$username/toptracks.xspf";
$favs   = new File_XSPF($path);
$tracks = $favs->getTracks();
if (count($tracks)) {
    $top = current($tracks);
    echo "My currrent favourite track is...\n";
    echo '<a href="' . $top->getInfo() . '">' . $top->getTitle()
         . '</a> by ' . $top->getCreator();
}
?>
