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

// Check to ensure this file is within the rest of the framework
defined('_JEXEC') or die();

/**
 * Renders a select list of authors
 *
 * @package 	Joomla.Framework
 * @subpackage	Parameter
 * @since		1.5
 */

class JElementFGBase extends JElement
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'FGBase';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$base = substr(JURI::base(),0,strpos(JURI::base(),'administrator/'));
		
		return '<input type="text" name="'.$control_name.'['.$name.']'.'" id="'.$control_name.$name.'" value="'.$base.'" class="text_area" size="50">';
	}
}