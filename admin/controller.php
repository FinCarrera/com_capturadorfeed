<?php

/**

* FeedGator - Aggregate RSS newsfeed content into a Joomla! database

* @version 2.3.6

* @package FeedGator

* @author Original author Stephen Simmons

* @now continued and modified by Matt Faulds, Remco Boom & Stephane Koenig and others
* @email mattfaulds@gmail.com

* @Joomla 1.5 Version by J. Kapusciarz (mrjozo)

* @copyright (C) 2005 by Stephen Simmons, 2010 by Matt Faulds - All rights reserved.

* @license GNU/GPL: http://www.gnu.org/copyleft/gpl.html

*

**/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('joomla.client.helper');
JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_feedgator'.DS.'tables');

/**
 * Feedgator Component Controller
 *
 * @since 1.5
 */
class FeedgatorController extends JController
{
	function __construct( $config = array())
	{
		parent::__construct( $config );
		$this->_db = &JFactory::getDBO();

		define('FG_VERSION', '2.3.6');
	}

	// feedgator

	function cpanel()
	{
		$model = &FGFactory::getFeedModel();
		$view	= &$this->getView('Feedgator','html');
		$view->setModel($model);
		$view->display();
	}

	function feeds()
	{
		$model = &FGFactory::getFeedModel();
		$view	= &$this->getView('Feedgator','html');
		$view->setModel($model);
		$view->display('feeds');
	}

	function editFeed()
	{
		$model = &FGFactory::getFeedModel();
		$view	= &$this->getView('Feedgator','html');
		$view->setModel($model);
		$view->display('feed');
	}

	function settings()
	{
		$model = &FGFactory::getFeedModel();
		$view	= &$this->getView('Feedgator','html');
		$view->setModel($model);
		$view->display('settings');
	}

	function tools()
	{
		$model = &FGFactory::getFeedModel();
		$view	= &$this->getView('Feedgator','html');
		$view->setModel($model);
		$view->display('tools');
	}

	function imports()
	{
		$ajax = JRequest::getInt('ajax',0,'get');
		$model = &FGFactory::getFeedModel();
		$view	= &$this->getView('Feedgator','html');
		$view->setModel($model);
		if($ajax) {
			echo $view->display('imports');
			jexit();
		}
		$view->display('imports'); 
	}

	function about()
	{
		$model = &FGFactory::getFeedModel();
		$view	= &$this->getView('Feedgator','html');
		$view->setModel($model);
		$view->display('about');
	}

	function support()
	{
		$model = &FGFactory::getFeedModel();
		$view	= &$this->getView('Feedgator','html');
		$view->setModel($model);
		$view->display('support');
	}

	function saveSettings($apply = false)

	{
		JRequest::checkToken() or jexit( 'Invalid Token' );


		$component = JRequest::getCmd( 'option' );
		if(J_VERSION < 1.6) {
			$table = JTable::getInstance('component');
			$function = 'loadByOption';
			if ( !$table->$function( $component )) $error = 1;
		} else {
			$table = JTable::getInstance('extension');
			$function = 'find';
			$component = array('element'=>$component);
			$id = $table->$function( $component );
			if ( !$table->load( $id )) $error = 1;
		}

		if (isset($error))

		{

			JError::raiseWarning( 500, 'Not a valid component' );

			return false;

		}

		$post = array();
		$post['params'] = JRequest::getVar('params', array() ,'post', 'array');


		if(!$table->save( $post )) {

			JError::raiseWarning( 500, $table->getError() );

			return false;

		}


		$link = $apply ? 'index.php?option=com_feedgator&task=settings' : 'index.php?option=com_feedgator&task=feeds';
		$msg = $apply ? JText::_('Changes Applied') : JText::_('Settings Saved');

		$this->setRedirect($link,$msg);

	}

	function upgrade()
	{
		JRequest::checkToken('get') or die( 'Invalid Token' );

		$model = &FGFactory::getFeedModel();
		$model->upgradeComponentParams();

		echo $this->importAll($update=true);
		jexit();
	}

	// feed

	function saveFeed($apply = false)
	{
		JRequest::checkToken() or die( 'Invalid Token' );
		$cid = JRequest::getInt('cid');
		$post = JRequest::getVar('data', array() ,'post', 'array');
		$post['params'] = JRequest::getVar('params', array() ,'post', 'array');

		if($post['content_type'] == '-1') $post['content_type'] = 'com_content'; 		// force content_type if not set
		$model = &FGFactory::getFeedModel();
		$msg = $model->store($post) ? JText::_( 'Feed Saved' ) : JText::_( 'Error Saving Feed' );
		$model->checkin();

		// store plugin settings
		$pluginModel = &FGFactory::getPluginModel();
		$pluginModel->setExt($post['content_type']);
		$pluginModel->store($cid);

		$link = $apply ? 'index.php?option=com_feedgator&task=edit&cid[]='.$cid : 'index.php?option=com_feedgator&task=feeds';
		$this->setRedirect($link, $msg);
	}

	function publishFeeds($publish = 1, $action = 'publish')
	{
		JRequest::checkToken() or die( 'Invalid Token' );

		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
		if (count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item to '.$action ) );
		}
		$model = &FGFactory::getFeedModel();
		if(!$model->publish($cid, $publish)) {
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}
		$this->setRedirect( 'index.php?option=com_feedgator&task=feeds' );
	}

	/**
	* Changes the frontpage state of one or more feeds
	*
	*/
	function frontpageFeeds($frontpage = 1, $action = 'front_yes')
	{
		JRequest::checkToken() or die( 'Invalid Token' );

		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
		if (count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item to '.$action ) );
		}
		$model = &FGFactory::getFeedModel();
		if(!$model->frontpage($cid, $frontpage)) {
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}
		$this->setRedirect( 'index.php?option=com_feedgator&task=feeds' );
	}

	function remove()
	{
		JRequest::checkToken() or die( 'Invalid Token' );

		// Initialize variables
		$cid		= JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
		if (count($cid) < 1) {
			$msg =  JText::_('Select an item to delete');
			$app->redirect('index.php?option=com_feedgator', $msg, 'error');
		}
		$model = &FGFactory::getFeedModel();
		if(!$model->delete($cid, $frontpage)) {
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}
		$msg = JText::sprintf('Item(s) deleted', count($cid));
		$this->setRedirect('index.php?option=com_feedgator&task=feeds', $msg);
	}

	/**
	* Cancels an edit operation
	*/
	function cancel()
	{
		JRequest::checkToken() or die( 'Invalid Token' );

		$model = &FGFactory::getFeedModel();
		$model->checkin();
		$this->setRedirect('index.php?option=com_feedgator&task=feeds');
	}

	function import($type=null)
	{
		JRequest::checkToken('get') or JRequest::checkToken() or die( 'Invalid Token' );

		if(!$type) $type = JRequest::getWord('type','','get');
		$formData = JRequest::getVar( 'cid', array(), 'get', 'array' );

		switch($type)
		{
			case 'all':

			if(JRequest::getCmd('task','') == 'pseudocron') {
				return $this->importAll();
			} else {
				$this->importAll();
			}

			break;

			case 'feed':

			$this->importFeed($formData);

			break;

			case 'preview':

			$this->importFeed( $formData, true );

			break;
		}
	}

	private function importAll($update=false)
	{
		$ajax = JRequest::getInt('ajax',0,'get');
		if($ajax) {
			$this->_db->setQuery( 'SELECT id, title FROM #__capturadorfeed WHERE published = 1 ORDER BY id' );
			$formData = $this->_db->loadAssocList();
			echo json_encode($formData);
			jexit();
		} else {
			$this->_db->setQuery( 'SELECT id FROM #__capturadorfeed WHERE published = 1 ORDER BY id' );
			$formData = $this->_db->loadResultArray();
			return $this->importFeed( $formData, $preview=false, $update);
		}
	}

	private function importFeed( $formData = '', $preview = false, $update = false)
	{
		$model = &FGFactory::getFeedModel();
		if($update) {
			echo $model->upgradeFeedParams($formData);
			jexit();
			//return $model->import($formData,$preview,$update);
		} else {
			if(JRequest::getCmd('task','') == 'pseudocron') {
				return $model->import($formData,$preview,$update);
			} else {
				echo $model->import($formData,$preview,$update);
				jexit();
			}
		}
	}

	// plugin

	function plugins()
	{
		$model = &FGFactory::getPluginModel();
		$view	= &$this->getView('plugin','html');
		$view->setModel($model);
		$view->display();
	}

	function pluginSettings()
	{
		$model = &FGFactory::getPluginModel();
		$view	= &$this->getView('plugin','html');
		$view->setModel($model);
		$view->display('settings');
	}

	function doInstall()
	{
		JRequest::checkToken() or JRequest::checkToken('get') or jexit( 'Invalid Token' );

		$model = &FGFactory::getPluginModel();
		$model->uploadPlugin();
	}

	function doUninstall()
	{
		JRequest::checkToken() or JRequest::checkToken('get') or jexit( 'Invalid Token' );

		$id = JRequest::getInt('id');

		$model = &FGFactory::getPluginModel();
		$model->uninstallPlugin($id);
	}

	function changePluginState()
	{
		$id = JRequest::getInt('id');
		$ext = JRequest::getCmd('ext','');

		//need to check component installed!
		$model = &FGFactory::getPluginModel();
		$plugin = $model->getPlugin($ext);
		if($plugin->componentCheck()) {
			$row = &JTable::getInstance('FGPlugin','Table');
			$row->load($id);
			$row->published = ($row->published ? 0 : 1);
			if ($row->store()) {
				$msg = $row->published ? JText::_('Plugin Published') : JText::_('Plugin Unpublished');
			} else {
				$msg = $this->_db->getErrorMsg();
			}
		} else {
			$msg = JText::_('Unable to publish plugin - component not installed!');
		}
		$this->setRedirect('index.php?option=com_feedgator&task=plugins', $msg);
	}

	function savePluginSettings()
	{
		$ext = JRequest::getCmd('ext','');

		if(!$ext) {
			$msg = JText::_('There has been an error.').' '.$ext;
		} else {
			$model = &FGFactory::getPluginModel();
			$model->setExt($ext);
			$msg = $model->store() ? JText::_('Plugin Settings Saved') : JText::_('Plugin Settings Not Saved');
		}

		$this->setRedirect('index.php?option=com_feedgator&task=plugins', $msg);
	}

	function getPluginParams()
	{
		$cid = JRequest::getInt('cid','','get');

		$model = &FGFactory::getPluginModel();
		echo $model->renderPluginParams($cid);
		jexit();
	}

	// tools

	function syncImports()
	{
		$model = &FGFactory::getToolsModel();
		$model->syncImports();
	}

	function ignoreDuplicate()
	{
		JRequest::checkToken('get') or JRequest::checkToken() or die( 'Invalid Token' );

		$model = &FGFactory::getToolsModel();
		$model->ignoreDuplicate();
	}
}