<?php

/**
* FeedGator - Aggregate RSS newsfeed content into a Joomla! database
* @version 2.3RC4
* @package FeedGator
* @author Matt Faulds
* @email mattfaulds@gmail.com
* @copyright (C) 2010 Matthew Faulds - All rights reserved
* @license GNU/GPL: http://www.gnu.org/copyleft/gpl.html
*
**/

/**
* @version		$Id: menuitem.php 11324 2008-12-05 19:06:24Z kdevine $
* @package		Joomla.Framework
* @subpackage	Parameter
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is within the rest of the framework
defined('_JEXEC') or die();

/**
 * Renders a select list of authors
 *
 * @package 	Joomla.Framework
 * @subpackage	Parameter
 * @since		1.5
 */

class JElementFGAuthors extends JElement
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'FGAuthors';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$db				= &JFactory::getDBO();
		
		$class	= $node->attributes('class');
		if (!$class) {
			$class = "inputbox";
		}
		
		// get list of Authors for dropdown filter
		if(J_VERSION < 1.6) {
			$query = 'SELECT u.id AS id, u.name AS text' .
				' FROM #__users AS u' .
				' WHERE u.gid > 18' . // above registered
				' GROUP BY u.name' .
				' ORDER BY u.name';
		} else {
			$query = 'SELECT u.id AS id, u.name AS text' .
					' FROM #__users AS u' .
					' INNER JOIN #__user_usergroup_map AS um ON um.user_id = u.id' .
					' WHERE u.block = 0' .
					' AND um.group_id != 2' .  // above registered
					' GROUP BY u.name' .
					' ORDER BY u.name';
		}
		$db->setQuery($query);
		$authors[] = JHTML::_('select.option', '0', '- '.JText::_('Select Author').' -', 'id', 'text');
		$authors = array_merge($authors, $db->loadObjectList());

		$javascript = '';
		
		//return JHTML::_('select.genericlist',  $options, ''.$control_name.'['.$name.']', $attributes, 'value', 'text', $value, $control_name.$name);
		//return JHTML::_('select.genericlist',  $options, ''.$control_name.'['.$name.']'.$multipleArray, $attributes, $key, $val, $value, $control_name.$name);		
		return JHTML::_('select.genericlist',  $authors, ''.$control_name.'['.$name.']', 'class="'.$class.'"'.$javascript, 'id', 'text', $value, $control_name.$name);
	}
}