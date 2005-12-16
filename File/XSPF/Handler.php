<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------------+
// | File_XSPF PEAR Package for Manipulating XSPF Playlists                     |
// | Copyright (c) 2005 David Grant <david@grant.org.uk>                        |
// +----------------------------------------------------------------------------+
// | This library is free software; you can redistribute it and/or              |
// | modify it under the terms of the GNU Lesser General Public                 |
// | License as published by the Free Software Foundation; either               |
// | version 2.1 of the License, or (at your option) any later version.         |
// |                                                                            |
// | This library is distributed in the hope that it will be useful,            |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of             |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU          |
// | Lesser General Public License for more details.                            |
// |                                                                            |
// | You should have received a copy of the GNU Lesser General Public           |
// | License along with this library; if not, write to the Free Software        |
// | Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA |
// +----------------------------------------------------------------------------+

/**
 * PHP version 4
 * 
 * @author      David Grant <david@grant.org.uk>
 * @copyright   Copyright (c) 2005 David Grant
 * @license     http://www.gnu.org/copyleft/lesser.html GNU LGPL
 * @link        http://www.xspf.org/
 * @package     File_XSPF
 * @version     CVS: $Id$
 */

/**
 * This class is used to parse an XSPF file.
 * 
 * @package     File_XSPF
 */
class File_XSPF_Handler
{
    /**
     * An instance of the File_XSPF class.
     *
     * @access  private
     * @var     File_XSPF
     */
    var $_xspf;
    /**
     * The current hierachy of tags being handled.
     *
     * @access private
     * @var array
     */
    var $_tag_stack = array();
    /**
     * The currently open File_XSPF_Link instance.
     *
     * @access  private
     * @var     File_XSPF_Link
     */
    var $_curr_link;
    /**
     * The currently open File_XSPF_Meta instance.
     *
     * @access  private
     * @var     File_XSPF_Meta
     */
    var $_curr_meta;
    /**
     * The currently open File_XSPF_Track instance.
     *
     * @access  private
     * @var     File_XSPF_Track
     */
    var $_curr_track;
    /**
     * The currently open File_XSPF_Extension instance.
     *
     * @access  private
     * @var     File_XSPF_Extension
     */
    var $_curr_extn;
    
    /**
     * Constructor for the XML importing object.
     * 
     * This method is the default constructor for the File_XSPF class, and
     * uses functionality from the XML_Parser PEAR class to parse an XSPF
     * playlist into our class structure.
     *
     * @access  public
     * @param   File_XSPF           $xspf an instance of the File_XSPF class.
     * @return  File_XSPF_Handler   an instance of this class.
     */
    function File_XSPF_Handler(&$xspf)
    {
        $this->_xspf =& $xspf;
    }

    /**
     * Handle character data from an XML file.
     * 
     * This method handles the character data that appears between tags in
     * the XSPF file.  If a tag has no attributes defined, this method
     * will be responsible for writing the tag directly, instead of suffering
     * the unnecessary overhead of handling the start and end tag events.
     *
     * @access  private
     * @param   resource    $parser an instance of XML_Parser
     * @param   string      $data   the character data to handle.
     */
    function cdataHandler($parser, $data)
    {
        // Because any valid markup can be written inside an extension
        // tag, we must first check the tag stack to see if we're inside
        // an open extension, and if so, write the data directly to that.
        if (in_array('EXTENSION', $this->_tag_stack)) {
            if (strlen(trim($data))) {
                $this->_curr_extn->_content = $data;
            }
            return;
        }
        // We're not inside an extension, so we can parse the data as
        // normal.
        $depth = count($this->_tag_stack);
        switch (end($this->_tag_stack)) {
        case "TITLE":
            if ($depth == 2) {
                // This is the content of the playlist title.
                $this->_xspf->setTitle($data);
            } elseif ($depth == 4) {
                // This is the content of thr track title.
                $this->_curr_track->setTitle($data);
            }
            break;
        case "CREATOR":
            if ($depth == 2) {
                // This is the content of the playlist creator.
                $this->_xspf->setCreator($data);
            } elseif ($depth == 4) {
                // This is the content of the track creator.
                $this->_curr_track->setCreator($data);
            }
            break;
        case "ANNOTATION":
            if ($depth == 2) {
                // This is the content of the playlist annotation.
                $this->_xspf->setAnnotation($data);
            } elseif ($depth == 4) {
                // This is the content of the track annotation.
                $this->_curr_track->setAnnotation($data);
            }
            break;
        case "INFO":
            if ($depth == 2) {
                // This is the content of the playlist info.
                $this->_xspf->setInfo($data);
            } elseif ($depth == 4) {
                // This is the content of the track info.
                $this->_curr_track->setInfo($data);
            }
            break;
        case "LOCATION":
            $location = new File_XSPF_Location($data);
            if ($depth == 2) {
                // This is the content of the playlist location.
                $this->_xspf->setLocation($location);
            } elseif ($depth == 3) {
                // This is the content of an attribution location.
                if (in_array("ATTRIBUTION", $this->_tag_stack))
                    $this->_xspf->addAttribution($location);
            } elseif ($depth == 4) {
                // This is the content of a track location.
                $this->_curr_track->addLocation($location);
            }
            break;
        case "IDENTIFIER":
            $identifier = new File_XSPF_Identifier($data);
            if ($depth == 2) {
                // This is the content of the playlist identifier.
                $this->_xspf->setIdentifier($identifier);
            } elseif ($depth == 3) {
                // This is the content of an attribution identifier.
                if (in_array("ATTRIBUTION", $this->_tag_stack))
                    $this->_xspf->addAttribution($identifier);
            } elseif ($depth == 4) {
                // This is the content of the track identifier.
                $this->_curr_track->setIdentifier($identifier);
            }
            break;
        case "IMAGE":
            if ($depth == 2) {
                // This is the content of the playlist image.
                $this->_xspf->setImage($data);
            } elseif ($depth == 4) {
                // This is the content of the track image.
                $this->_curr_track->setImage($data);
            }
            break;
        case "DATE":
            // This is the content of the playlist date.
            $this->_xspf->setDate($data);
            break;
        case "LICENSE":
            // This is the content of the playlist license.
            $this->_xspf->setLicense($data);
            break;
        case "DURATION":
            // This is the content of the track duration.
            $this->_curr_track->setDuration($data);
            break;
        case "TRACKNUM":
            // This is the content of the track number.
            $this->_curr_track->setTrackNumber($data);
            break;
        case "ALBUM":
            // This is the content of the track album.
            $this->_curr_track->setAlbum($data);
            break;
        case "META":
            // This is the content of a meta element.
            $this->_curr_meta->setContent($data);
            break;
        case "LINK":
            // This is the content of a link element.
            $this->_curr_link->setContent($data);
            break;
        }
    }
    
    /**
     * Handle the closure of an XML tag.
     * 
     * This method handles to closure of an XML tag, generally tidying up
     * instances that were created in the startHandler method.
     *
     * @access  private
     * @param   resource    $parser an instance of XML_Parser
     * @param   string      $name   the closing tag to be handled (e.g. '</track>')
     */
    function endHandler($parser, $name)
    {
        // Any valid markup can be included inside an extension, so
        // we must ensure that it is handled properly, and doesn't
        // interfere with the standard elements.
        if (in_array('EXTENSION', $this->_tag_stack) && $name != 'EXTENSION') {
            $this->_curr_extn->_content = '</' . strtolower($name) . '>';
            return;
        }
        // This variable stores the current depth of the current tag.  This
        // is used for determining to which parent a shared element definition
        // belongs.
        $depth = count($this->_tag_stack);
        switch ($name) {
        case "TRACK":
            // This is the end of the current track element.  This means
            // we can now close it and add it to the playlist.
            $this->_xspf->addTrack($this->_curr_track);
            break;
        case "LINK":
            // This is the end of the current link element.  This means
            // we can now close it and add it to the right context.
            if ($depth == 2) {
                // This link element belongs to the playlist.
                $this->_xspf->addLink($this->_curr_link);
            } elseif ($depth == 4) {
                // This link element belongs to a track.
                $this->_curr_track->addLink($this->_curr_link);
            }
            break;
        case "META":
            // This is the end of the current meta element.  This means
            // we can now close it and add it to the right context.
            if ($depth == 2) {
                // This meta element belongs to the playlist.
                $this->_xspf->addMeta($this->_curr_meta);
            } elseif ($depth == 4) {
                // This meta element belongs to a track.
                $this->_curr_track->addMeta($this->_curr_meta);
            }
            break;
        case "EXTENSION":
            // This is the end of the current extension element.  This
            // means we can now close it and add it to the right context.
            if ($depth == 2) {
                // This extension element belongs to the playlist.
                $this->_xspf->addExtension($this->_curr_extn);
            } elseif ($depth == 4) {
                // This extension element belongs to a track.
            	$this->_curr_track->addExtension($this->_curr_extn);
            }
            break;
        }
        // Remove the current element from the tag stack.
        array_pop($this->_tag_stack);
    }

    /**
     * Handle the opening of an XML tag.
     * 
     * This method handles the opening of an XML tag in the XSPF playlist file.
     * The handler instantiates various support classes when it encounters
     * appropriate tags, which are later closed by the endHandler() method.
     *
     * @access  private
     * @param   resource    $parser     an instance of XML_Parser
     * @param   string      $name       the name of the tag being handled (e.g. '<track>')
     * @param   array       $attribs    a list of attributes for the tag.
     */
    function startHandler($parser, $name, $attribs)
    {
        // The extension element can contain any valid markup, so it
        // is handled up front to prevent mishandling of elements that
        // belong in the specification but appear under the extension
        // as well.
        if (in_array("EXTENSION", $this->_tag_stack)) {
            // Write out the tag name.
            $this->_curr_extn->_content .= '<' . strtolower($this->_tag_stack);
            // Iterate the tag attributes.
            if (count($attribs)) {
                foreach ($attribs as $param => $value) {
                    $this->_curr_extn->_content .= ' ' . strtolower($param) . '="' . $value . '"';
                }
            }
            $this->_curr_extn->_content .= '>';
            return;
        }
        switch ($name) {
        case "PLAYLIST":
            // This is the start of a playlist element.
            if (isset($attribs['VERSION'])) {
                $this->_xspf->_version = $attribs['VERSION'];
            }
            if (isset($attribs['XMLS'])) {
                $this->_xspf->_xmls = $attribs['XMLNS'];
            }
            break;
        case "TRACK":
            // This is the start of a track element.  The element is
            // stored in an object parameter to allow adding of sub-
            // elements to the track.
            $this->_curr_track = new File_XSPF_Track();
            break;
        case "LINK":
            // This is the start of a link element.  The element is
            // stored in an object parameter to allow adding of link
            // content.
            $this->_curr_link  = new File_XSPF_Link();
            if (isset($attribs['REL'])) {
                $this->_curr_link->setRelationship($attribs['REL']);
            }
            break;
        case "META":
            // This is the start of a meta element.  The element is
            // stored in an object parameter to allow adding of meta
            // content.
            $this->_curr_meta = new File_XSPF_Meta();
            if (isset($attribs['REL'])) {
                $this->_curr_meta->setRelationship($attribs['REL']);
            }
            break;
        case "EXTENSION":
            // This is the start of a extension object.  The element
            // is stored in an object parameter to allow adding of
            // extension content.
            $this->_curr_extn = new File_XSPF_Extension();
            if (isset($attribs['APPLICATION'])) {
                $this->_curr_extn->setApplication($attribs['APPLICATION']);
            }
            break;
        }
        // Add the current element to the tag stack.
        array_push($this->_tag_stack, $name);
    }
}
?>
