<?php

/**
* FeedGator - Aggregate RSS newsfeed content into a Joomla! database
* @version 2.3 (stable)
* @package FeedGator
* @author Original author Stephen Simmons
* @now continued and modified by Matt Faulds, Remco Boom & Stephane Koenig and others
* @email mattfaulds@gmail.com
* @Joomla 1.5 Version by J. Kapusciarz (mrjozo)
* @copyright (C) 2005 by Stephen Simmons - All rights reserved
* @license GNU/GPL: http://www.gnu.org/copyleft/gpl.html
*
**/
// no direct access
defined('_JEXEC') or die('Restricted access');

class TOOLBAR_feedgator
{
	function _EDIT()
	{
		$cid = JRequest::getVar('cid',array(),'get','array');
		$edit =  (int)@$cid[0];

		$text = ( $edit ? JText::_( 'Edit' ) : JText::_( 'New' ) );

		JToolBarHelper::title( JText::_( 'Feed' ).': <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
		$edit ? JToolBarHelper::apply() : JToolBarHelper::apply( 'apply', JText::_( 'Save and Add Another' ));
		JToolBarHelper::save();
		$edit ? JToolBarHelper::cancel( 'cancel', 'Close' ) : JToolBarHelper::cancel();
		JToolBarHelper::help( 'screen.content.edit' );
		self::_CPanel();
	}

	function _SETTINGS()
	{
		JToolBarHelper::title( JText::_( 'Feed Gator Global Settings' ), 'config.png' );
		JToolBarHelper::apply('applySettings');
		JToolBarHelper::save('saveSettings');
		JToolBarHelper::cancel();
		self::_CPanel();
	}

	function _PLUGINS()
	{
		JToolBarHelper::title( JText::_( 'Feed Gator Plugins' ), ((J_VERSION < 1.6) ? 'config.png' : 'plugin') );
		self::_CPanel();
	}

	function _TOOLS()
	{
		JToolBarHelper::title( JText::_( 'Feed Gator Tools' ), ((J_VERSION < 1.6) ? 'config.ong' : 'cpanel') );
		self::_CPanel();
	}

	function _IMPORTS()
	{
		global $filter_state;

		JToolBarHelper::title( JText::_( 'Feed Gator Import History' ), ((J_VERSION < 1.6) ? 'config.png' : 'article') );
// The functions below cannot yet be supported for each content type and cause confusion for users
// The eventual aim will be to manipulate content from within FG

//		if ($filter_state == 'A' || $filter_state == NULL) {
//			JToolBarHelper::unarchiveList();
//		}
//		if ($filter_state != 'A') {
//			JToolBarHelper::archiveList();
//		}
//		JToolBarHelper::publishList();
//		JToolBarHelper::unpublishList();
//		JToolBarHelper::customX( 'movesect', 'move.png', 'move_f2.png', 'Move' );
//		JToolBarHelper::customX( 'copy', 'copy.png', 'copy_f2.png', 'Copy' );
//		JToolBarHelper::trash();
//		JToolBarHelper::editListX();
//		JToolBarHelper::addNewX();

		self::_CPanel();
	}

	function _ABOUT()
	{
		JToolBarHelper::title( JText::_( 'About FeedGator' ), 'systeminfo.png' );
		self::_CPanel();
	}

	function _SUPPORT()
	{
		JToolBarHelper::title( JText::_( 'Feed Gator Help and Support' ), 'help_header.png' );
		self::_CPanel();
	}

	function _FEEDS()
	{
		//fix for missing default style for refresh button
		$app =& JFactory::getApplication();
		$templateDir = JURI::base() . 'templates/' . $app->getTemplate();
		$doc = &JFactory::getDocument();
		$doc->addStyleDeclaration('.icon-32-refresh { background-image: url('.$templateDir.'/images/toolbar/icon-32-refresh.png); }');

		JToolBarHelper::title( JText::_( 'Manage RSS Feeds' ), ((J_VERSION < 1.6) ? 'addedit.png' : 'article') );

		// button hack

		$bar = & JToolBar::getInstance('toolbar');
		$bar->appendButton( 'Link', 'preview', 'Preview', '#" onclick="javascript: importFunc(\'preview\')"', true, false);
		$bar->appendButton( 'Link', 'refresh', 'Import All', '#" onclick="javascript: importFunc(\'all\')"', false, false);
		$bar->appendButton( 'Link', 'upload', 'Import', '#" onclick="javascript: importFunc(\'feed\')"', true, false);

		//

		JToolBarHelper::publishList('publish', 'Enable');
		JToolBarHelper::unpublishList('unpublish', 'Disable');
		JToolBarHelper::addNew();
		JToolBarHelper::editList();
		JToolBarHelper::deleteList();
		JToolBarHelper::custom( 'support', 'help.png', 'help_f2.png', 'Help', false );
		self::_CPanel();
	}

	function _DEFAULT()
	{
		JToolBarHelper::title( JText::_( 'Feed Gator the RSS Feed Import Component' ), 'config.png' );
	}

	function _CPanel()
	{
		$bar = & JToolBar::getInstance('toolbar');
		$bar->appendButton( 'Link', ((J_VERSION < 1.6) ? 'config' : 'options'), 'Control Panel', 'index.php?option=com_feedgator&task=cpanel', true, false);
	}
}