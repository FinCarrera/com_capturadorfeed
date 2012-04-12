#!/usr/bin/php
<?php

/**
* FeedGator - Aggregate RSS newsfeed content into a Joomla! database
* @version 2.3.3
* @package FeedGator
* @author Original author Stephen Simmons
* @now continued and modified by Matt Faulds, Remco Boom & Stephane Koenig and others
* @email mattfaulds@gmail.com
* @Joomla 1.5 Version by J. Kapusciarz (mrjozo)
* @copyright (C) 2005 by Stephen Simmons - All rights reserved
* @license GNU/GPL: http://www.gnu.org/copyleft/gpl.html
*
**/

// set error reporting to not warn of session headers already set by hashbang in process.php
error_reporting(E_ERROR | E_PARSE);

define( '_JEXEC', 1 );

define('DS', DIRECTORY_SEPARATOR);
define('JPATH_BASE', substr(__FILE__,0,strrpos(__FILE__, DS.'administrator')));

require_once(JPATH_BASE.DS.'includes'.DS.'defines.php' );
require_once(JPATH_ADMINISTRATOR.DS.'includes'.DS.'framework.php' );
require_once(JPATH_ADMINISTRATOR.DS.'includes'.DS.'helper.php' );
require_once(JPATH_ADMINISTRATOR.DS.'includes'.DS.'toolbar.php' );

$mainframe = &JFactory::getApplication('site');
$mainframe->initialise();

$version = new JVersion();
define('J_VERSION', $version->getShortVersion());

require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_feedgator'.DS.'controller.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_feedgator'.DS.'factory.feedgator.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_feedgator'.DS.'helpers'.DS.'feedgator.helper.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_feedgator'.DS.'helpers'.DS.'feedgator.utility.php');
if(file_exists(JPATH_ROOT.DS.'plugins'.DS.'system'.DS.'addkeywords.php')) {
	require_once(JPATH_ROOT.DS.'plugins'.DS.'system'.DS.'addkeywords.php');
}
FeedgatorUtility::profiling('Start cron');

define('SPIE_CACHE_AGE', 60*10);

require_once(JPATH_ROOT.DS.'libraries'.DS.'simplepie'.DS.'simplepie.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_feedgator'.DS.'inc'.DS.'simplepie'.DS.'overrides.php');
FeedgatorUtility::profiling('Loaded SimplePie');

JRequest::setVar('task','cron','get');
JRequest::setVar(JUtility::getToken(),'1','get');

$config = array('base_path'=>JPATH_ADMINISTRATOR.DS.'components'.DS.'com_feedgator');
$controller = new FeedgatorController($config);
$controller->import('all');

FeedgatorUtility::profiling('End');
echo 'Import finished';