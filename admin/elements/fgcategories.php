<?php

/**
* FeedGator - Aggregate RSS newsfeed content into a Joomla! database
* @version 2.3.2
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
 * Renders a select list of content_type/sects/cats
 *
 * @package 	Joomla.Framework
 * @subpackage	Parameter
 * @since		1.5
 */

class JElementFGCategories extends JElement
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'FGCategories';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$user			= &JFactory::getUser();
		$db				= &JFactory::getDBO();
		$feedModel		= &FGFactory::getFeedModel();
		$task			= JRequest::getWord('task');
		$sectionid		= 0;
				
		//node attributes var -> category/section
		$type = $node->attributes('var');
		$class	= $node->attributes('class');
		if (!$class) {
			$class = "inputbox";
		}

		$fgParams = JComponentHelper::getParams ('com_feedgator');
		if($task == 'edit') {
			$cid = JRequest::getVar( 'cid', array() );
		} else {
			$cid = array(0);
		}
		
		$plugin = $feedModel->getPlugin(); // use feed model to allow access to feed data/params
		$plugin->params = new JParameter( $feedModel->pluginModel->getParams(), $feedModel->pluginModel->getXmlPath() );

		if($type == 'section') {
			$javascript = ' onchange="changeDynaList( \'datacatid\', sectioncategories, document.adminForm.datasectionid.options[document.adminForm.datasectionid.selectedIndex].value, 0, 0);"';
			($cid[0] OR $fgParams->get('default_type')) ? $options = $plugin->getSectionList($fgParams) : $options = array(JHTML::_('select.option', -1, '- '.JText::_('Select Section').' -', 'id', 'title'));
		} elseif($type == 'category') {
			$javascript = '';
			($cid[0] OR $fgParams->get('default_type')) ? $options = $plugin->getCategoryList($fgParams) : $options = array(JHTML::_('select.option', -1, '- '.JText::_('Select Category').' -', 'id', 'title'));
		}
						
		//return JHTML::_('select.genericlist',  $options, ''.$control_name.'['.$name.']', $attributes, 'value', 'text', $value, $control_name.$name);
		//return JHTML::_('select.genericlist',  $options, ''.$control_name.'['.$name.']'.$multipleArray, $attributes, $key, $val, $value, $control_name.$name);		
		return JHTML::_('select.genericlist',  $options, ''.$control_name.'['.$name.']', 'class="'.$class.'"'.$javascript, 'id', 'title', $value, $control_name.$name);
	}
}