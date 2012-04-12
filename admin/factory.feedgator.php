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

// No direct access
defined('_JEXEC') or die();

/**
 * FeedGator Factory class
 *
 */
abstract class FGFactory
{
	public static $pluginModel = null;
	public static $toolsModel = null;
	public static $feedModel = null;

	public static function getFeedModel()
	{
		if (!self::$feedModel) {
			self::$feedModel = &JModel::getInstance('Feed','FeedgatorModel');
		}

		return self::$feedModel;
	}
	
	public static function getToolsModel()
	{
		if (!self::$toolsModel) {
			self::$toolsModel = &JModel::getInstance('Tools','FeedgatorModel');
		}

		return self::$toolsModel;
	}
	
	public static function getPluginModel()
	{
		if (!self::$pluginModel) {
			self::$pluginModel = &JModel::getInstance('Plugin','FeedgatorModel');
		}

		return self::$pluginModel;
	}

}
