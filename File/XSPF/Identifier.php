<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | File_XSPF PEAR Package for Manipulating XSPF Playlists                    |
// | Copyright (c) 2005 David Grant <david@grant.org.uk>                       |
// +---------------------------------------------------------------------------+
// | This library is free software; you can redistribute it and/or             |
// | modify it under the terms of the GNU Lesser General Public                |
// | License as published by the Free Software Foundation; either              |
// | version 2.1 of the License, or (at your option) any later version.        |
// |                                                                           |
// | This library is distributed in the hope that it will be useful,           |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU         |
// | Lesser General Public License for more details.                           |
// |                                                                           |
// | You should have received a copy of the GNU Lesser General Public          |
// | License along with this library; if not, write to the Free Software       |
// | Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301 USA |
// +---------------------------------------------------------------------------+

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
 * @package File_XSPF
 */
class File_XSPF_Identifier {
    /**
     * a valid URI.
     *
     * @access  private
     * @var     string
     */
    var $_uri;

    /**
     * Set the URI for this class.
     * 
     * This constructor provides an opportunity to set the URI for this identifier
     * instead of instantiating the class and using the setUri method.
     *
     * @access  public
     * @param   string $uri a valid URI
     * @return  File_XSPF_Identifier
     */
    function File_XSPF_Identifier($uri = null)
    {
        if (! is_null($uri)) {
            $this->setUri($uri);
        }
    }
    
    /**
     * Get the URI for this identifier.
     * 
     * This method returns the URI for this identifier, which is the canonical ID
     * for this resource.
     *
     * @access  public
     * @return  string a valid URI.
     */
    function getUri()
    {
        return $this->_uri;
    }
    
    /**
     * Set the URI for this identifier.
     * 
     * This method sets the URI of this identifier.  If the parameter is not a valid
     * URI, this method will fail.
     *
     * @access  public
     * @param   string $uri a valid URI
     */
    function setUri($uri)
    {
        if (File_XSPF::_validateURI($uri)) {
            $this->_uri = $uri;
        }
    }
    
    /**
     * Append this object to the parent xml node.
     * 
     * This method adds this object to the passed XML parent node, which is an
     * instance of XML_Tree_Node.
     *
     * @access  private
     * @param   XML_Tree_Node $parent
     */
    function _toXML(&$parent)
    {
        $parent->addChild('identifier', $this->getUri());
    }
}
?>