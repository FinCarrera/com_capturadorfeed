<?php

/**
* FeedGator - Aggregate RSS newsfeed content into a Joomla! database
* @version 2.3.3
* @package FeedGator
* @author Matt Faulds
* @email mattfaulds@gmail.com
* @copyright (C) 2010 Matthew Faulds - All rights reserved
* @license GNU/GPL: http://www.gnu.org/copyleft/gpl.html
*
**/
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.installer.installer');
jimport('joomla.filesystem.file');

require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_feedgator'.DS.'helpers'.DS.'feedgator.helper.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_feedgator'.DS.'helpers'.DS.'feedgator.utility.php');
JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_feedgator'.DS.'tables');
JModel::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_feedgator'.DS.'models');

$version = new JVersion();
define('J_VERSION', $version->getShortVersion());

JHTML::_('behavior.mootools');

$version = '2.3.6';

$doc = &JFactory::getDocument();
$doc->addStyleSheet('components/com_feedgator/css/styles.css');

$token = JUtility::getToken();
$base = JURI::base();
$script = "
	window.addEvent( 'domready', function() {
		var base = '$base';
		var btn = $('fgupgradebtn');
		if(btn) {
			btn.addEvent('click', function() {
				var log = $('fgupgrade');
				var url = base + 'index.php?option=com_feedgator&task=upgrade&$token=1';
				new Ajax(url, {
					onRequest: function() {
						btn.setStyle('display','none');
						log.empty().appendText('Upgrading feeds...').addClass('waiting');
					},
					update: log,
					onComplete: function() { log.removeClass('waiting'); }
				}).request();
			});
		}
	});
";
$doc->addScriptDeclaration($script);

$db = & JFactory::getDBO();

$query = 'SHOW TABLES LIKE '.$db->Quote('#__capturadorfeed');
$db->setQuery($query);
if($db->loadResult()) {
	$query = 'SHOW COLUMNS FROM '.$db->nameQuote('#__capturadorfeed');
	$db->setQuery($query);
	if($rows = $db->loadAssocList()) {

		// 2.0.x to 2.3RC4
		if(!FeedgatorUtility::in_array_recursive('filtering',$rows)) {
			$query = 	"ALTER TABLE ".$db->nameQuote('#__capturadorfeed') .
						" ADD `last_email` int(11) NOT NULL default '0'," .
						" ADD `content_type` varchar(50) NULL," .
						" ADD `default_introtext` varchar(250) NULL," .
						" ADD `trackback_class` tinytext NOT NULL default ''," .
						" ADD `trackback_rel` tinytext NOT NULL default ''," .
						" ADD `filtering` tinyint(1) NOT NULL default '0'," .
						" ADD `filter_whitelist` text NOT NULL default ''," .
						" ADD `filter_blacklist` text NOT NULL default ''," .
						" ADD `params` text NOT NULL default ''," .
						" ADD `imports` text NOT NULL default '';";
			$db->setQuery($query);
			$db->query() ? $success = 1 : $success = 0;
			$from = '2.0.x';
			$upgrade = 1;
		}

		// 2.1.2 to 2.3RC4
		elseif(FeedgatorUtility::in_array_recursive('k2',$rows)) {
			$query = 	"ALTER TABLE ".$db->nameQuote('#__capturadorfeed') .
 						" ADD `last_email` int(11) NOT NULL default '0'," .
						" ADD `content_type` varchar(50) NULL," .
						" ADD `params` text NOT NULL default ''," .
						" ADD `imports` text NOT NULL default '',";
						" DROP `k2`;";
			$db->setQuery($query);
			$db->query() ? $success = 1 : $success = 0;
			$from = '2.1.2';
			$upgrade = 1;
		}

		// 2.1.1 or 2.1.3 to 2.3RC4
		elseif(!FeedgatorUtility::in_array_recursive('params',$rows)) {
			$query = 	"ALTER TABLE ".$db->nameQuote('#__capturadorfeed') .
 						" ADD `last_email` int(11) NOT NULL default '0'," .
						" ADD `content_type` varchar(50) NULL," .
						" ADD `params` text NOT NULL default ''," .
						" ADD `imports` text NOT NULL default '';";
			$db->setQuery($query);
			$db->query() ? $success = 1 : $success = 0;
			$from = '2.1.x';
			$upgrade = 1;
		}

		// 2.2dev to 2.3RC4
		elseif(!FeedgatorUtility::in_array_recursive('imports',$rows)) {
			$query = 	"ALTER TABLE ".$db->nameQuote('#__capturadorfeed') .
 						" ADD `last_email` int(11) NOT NULL default '0'," .
						" ADD `content_type` varchar(50) NULL," .
						" ADD `imports` text NOT NULL default '';";
			$db->setQuery($query);
			$db->query() ? $success = 1 : $success = 0;
			$from = '2.2'; //dev
			$upgrade = 1;
		}

		//2.2x to 2.3RC4
		elseif(!FeedgatorUtility::in_array_recursive('content_type',$rows)) {
			// convert old params ad remove table columns
			$query =	"SELECT * FROM ".$db->nameQuote('#__capturadorfeed');
			$db->setQuery($query);
			$erows = $db->loadObjectList();

			foreach($erows as $erow) {
				$txt = array ();
				foreach ($erow as $k => $v) {
					switch($k)
					{
						case 'shortlink':
						case 'trackback_class':
						case 'trackback_rel':
						case 'trim_to':
						case 'onlyintro':

						$txt[] = "$k=$v";

						break;
					}
				}
			//	if(substr($erow->params,-1,2) != "\n") $erow->params .= "\n";
				$erow->params = implode("\n", $txt)."\n".$erow->params;
				$db->updateObject( '#__capturadorfeed', $erow, 'id' );
			}
			unset($erows,$erow);

			$query = 	"ALTER TABLE ".$db->nameQuote('#__capturadorfeed') .
						" ADD `last_email` int(11) NOT NULL default '0'," .
						" ADD `content_type` varchar(50) NULL," .
						" DROP `shortlink`," .
						" DROP `trackback_class`," .
						" DROP `trackback_rel`," .
						" DROP `trim_to`," .
						" DROP `onlyintro`;";
			$db->setQuery($query);
			$db->query() ? $success = 1 : $success = 0;
			$from = '2.2.x';
			$upgrade = 1;
		}

		// 2.3bx to 2.3RC4+
		elseif(FeedgatorUtility::in_array_recursive('content_type',$rows)) {
			if(!FeedgatorUtility::in_array_recursive('last_email',$rows)) {
				$query = 	"ALTER TABLE ".$db->nameQuote('#__capturadorfeed') .
							" ADD `last_email` int(11) NOT NULL default '0';";
				$db->setQuery($query);
				$db->query() ? $success = 1 : $success = 0;
			}

			$query = 'SHOW COLUMNS FROM '.$db->nameQuote('#__capturadorfeed_imports');
			$db->setQuery($query);
			if(!$rows = $db->loadAssocList()) {
				$from = '2.3b1-4'; //dev
				$upgrade = 1;
			} else {
				$query = 	"ALTER TABLE ".$db->nameQuote('#__capturadorfeed_imports') .
							" DROP INDEX  `content_id` ," .
							" ADD INDEX `content_id` (`content_id`)";
				$db->setQuery($query);
				$db->query() ? $success = 1 : $success = 0;
				$from = '2.3b5-9/RC1-8.4'; //dev
				$upgrade = 1;
			}
		}
	}
} else {
	$query = 	"CREATE TABLE IF NOT EXISTS `#__capturadorfeed` (
				`id` int(10) NOT NULL auto_increment,
				`title` varchar(100) NOT NULL default 'Untitled',
				`feed` text NOT NULL default '',
				`content_type` varchar(50) NULL,
				`sectionid` int(10) NOT NULL default '0',
				`catid` int(10) NOT NULL default '0',
				`default_author` varchar(100) NULL,
				`default_introtext` varchar(250) NULL,
				`created_by` int(11) NOT NULL default '0',
				`created` datetime NOT NULL default '0000-00-00 00:00:00',
				`checked_out` int(11) unsigned NOT NULL default '0',
				`checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
				`last_run` datetime NOT NULL default '0000-00-00 00:00:00',
				`last_email` int(11) NOT NULL default '0',
				`published` tinyint(1) NOT NULL default '0',
				`front_page` tinyint(1) NOT NULL default '0',
				`filtering` tinyint(1) NOT NULL default '0',
				`filter_whitelist` text NOT NULL default '',
				`filter_blacklist` text NOT NULL default '',
				`params` text NOT NULL default '',
				`imports` text NOT NULL default '',
				PRIMARY KEY  (`id`)
				) ENGINE=MyISAM;";

	$db->setQuery($query);
	$db->query();
}

$query = 	"CREATE TABLE IF NOT EXISTS `#__capturadorfeed_imports` (
			`id` int(11) NOT NULL auto_increment,
			`content_id` int(11) NOT NULL,
			`plugin` text NOT NULL,
			`feed_id` int(11) NOT NULL,
			`hash` text NOT NULL,
			PRIMARY KEY (`id`),
			INDEX (`feed_id`),
			INDEX (`content_id`)
			) ENGINE=MyISAM;";

$db->setQuery( $query );
$db->query();

// FG base plugin installation
$pluginInstaller = &JModel::getInstance('Plugin','FeedgatorModel');
$pluginInstaller->installBasePlugins() ? $success = 1 : $success = 0;

// System Plugin installation
//remove old FG plugin table file
$old_table = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_feedgator'.DS.'tables'.DS.'plugin.php';
if(file_exists($old_table)) {
	JFile::delete($old_table);
}
$installer = new JInstaller;

$src = $this->parent->getPath('source');
$installer->install($src.DS.'plg_feedgatorpseudocron');

$query = "SELECT * FROM #__plugins WHERE element='feedgatorpseudocron' AND folder='system'";
$db->setQuery($query);
$exist_sys_plg = $db->loadObject();
if(!$exist_sys_plg) {
	$table_type = (J_VERSION < 1.6) ? 'plugin' : 'extension';
	$className = 'JTable'.$table_type;
	require_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.$table_type.'.php');
	$p = new $className($db);
	$p->reorder('`folder` = "system"');
	$query = "UPDATE #__".$table_type."s SET ordering=0 WHERE element='feedgatorpseudocron' AND folder='system'";
	$db->setQuery($query);
	$sys_plg = $db->query() ? 1 : 0;
	$p->reorder('`folder` = "system"');
} else {
	$sys_plg = 1;
}

// remove a log file that might be left over from old versions!
$debug_file1 = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_feedgator'.DS.'FG_debug_log.txt';
$debug_file2 = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_feedgator'.DS.'fg_debug_log.html';
if(file_exists($debug_file1)) {
	JFile::delete($debug_file1);
}
if(file_exists($debug_file2)) {
	JFile::delete($debug_file2);
}

?>
<div style="padding-left:10px;padding-bottom:10px">
	<h2><?php echo JText::_('FeedGator Installation Status'); ?></h2>
	<?php if(!isset($upgrade)) { ?>
		Installing version: <strong class="blue"><?php echo $version; ?></strong>
		<br />
		<br />
		<strong class="green">Component and internal plugins installation successful!</strong>
		<?php if($sys_plg AND !$exist_sys_plg) { ?>
		<br />
		<br />
		<strong class="green">Pseudo-cron plugin installation successful!</strong>
		<?php } elseif($sys_plg AND $exist_sys_plg) { ?>
		<br />
		<br />
		<strong class="green">Pseudo-cron system plugin upgrade successful!</strong>
		<?php } ?>
		<br />
		<br />
		<br />
		<strong><a href="<?php echo 'index.php?option=com_feedgator&task=feeds'; ?>">Click here to set up your feeds</a></strong>
		<br />
	<?php } else { ?>
		Upgrading version: <strong class="red"><?php echo $from; ?></strong> to <strong class="blue"><?php echo $version; ?></strong>
		<?php if($from <= '2.3RC1') { ?>
			<?php if($success) { ?>
				<br />
				<br />
				<strong class="green">Database upgrade successful!</strong>
			<?php } else { ?>
				<br />
				<br />
				<strong class="red">Database upgrade unsuccessful!</strong>
				<br />
				Please uninstall FeedGator and then try again. <strong>Caution:</strong> you will lose all FeedGator settings...
			<?php } ?>
		<?php } ?>
		<?php if($sys_plg AND $exist_sys_plg) { ?>
		<br />
		<br />
		<strong class="green">Pseudo-cron system plugin installation successful!</strong>
		<?php } elseif($sys_plg AND !$exist_sys_plg) { ?>
		<br />
		<br />
		<strong class="green">Pseudo-cron system plugin upgrade successful!</strong>
		<?php } ?>
		<br />
		<br />
		<div id="fgupgrade">
			<?php if($from <= '2.3b9.3') { ?>
				You now need to upgrade your feeds.
				<div id="fgupgradebtn">Click here to upgrade your feeds</div>
				<br />
				<br />
			<?php } ?>
			<strong><a href="index.php?option=com_feedgator&task=feeds">Click here to go straight to your feeds</a></strong>
		</div>
		<br />
	<?php } ?>
	<br />
	<br />
</div>