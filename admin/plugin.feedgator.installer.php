<?php/*** FeedGator - Aggregate RSS newsfeed content into a Joomla! db* @version 2.3.3* @package FeedGator* @author Matt Faulds* @email mattfaulds@gmail.com* @copyright (C) 2010 Matthew Faulds - All rights reserved* @license GNU/GPL: http://www.gnu.org/copyleft/gpl.html***/// no direct accessrequire_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_installer'.DS.'models'.DS.'install.php');JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_feedgator'.DS.'tables');jimport( 'joomla.utilities.simplexml' );defined( '_JEXEC' ) or die( 'Restricted access' );/** * Plugin installer * */class FeedgatorPluginInstaller extends JObject{	/**	 * Constructor	 *	 * @access	protected	 * @param	object	$parent	Parent object [JInstaller instance]	 * @return	void	 * @since	1.5	 */	function __construct(&$parent)	{		$this->parent =& $parent;	}	/**	 * Custom install method	 *	 * @access	public	 * @return	boolean	True on success	 * @since	1.5	 */	function install()	{		// Get a db connector object		$db =& $this->parent->getDBO();		// Get the plugin manifest object		$manifest =& $this->parent->getManifest();		(J_VERSION < 1.6) ? $this->manifest =& $manifest->document : $this->manifest =& $manifest;		/**		 * ---------------------------------------------------------------------------------------------		 * Manifest Document Setup Section		 * ---------------------------------------------------------------------------------------------		 */		// Set the plugins name		(J_VERSION < 1.6) ? $name =& $this->manifest->getElementByPath('name') : $name =& $this->manifest->name;		$name = JFilterInput::clean($name->data(), 'cmd');		$this->set('name', $name);		// Get the component description		(J_VERSION < 1.6) ? $description =& $this->manifest->getElementByPath('description') : $description =& $this->manifest->description;		if (is_a($description, (J_VERSION < 1.6) ? 'JSimpleXMLElement' : 'JXMLElement')) {			$this->parent->set('message', $description->data());		} else {			$this->parent->set('message', '' );		}		/*		 * Backward Compatability		 * @todo Deprecate in future version		 */		$type = (J_VERSION < 1.6) ? $this->manifest->attributes('type') : $this->manifest->attributes()->type;		// Set the installation path		(J_VERSION < 1.6) ? $element =& $this->manifest->getElementByPath('files') : $element =& $this->manifest->files;		if (is_a($element, (J_VERSION < 1.6) ? 'JSimpleXMLElement' : 'JXMLElement') && count($element->children())) {			$files =& $element->children();			foreach ($files as $file) {				if ((J_VERSION < 1.6) ? $file->attributes($type) : $file->attributes()->$type) {					$pname = (J_VERSION < 1.6) ? $file->attributes($type) : $file->attributes()->$type->data();					break;				}			}		}		if ( !empty ($pname) ) {			$this->parent->setPath('extension_root', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_feedgator'.DS.'plugins'.DS.$pname);		} else {			$this->parent->abort(JText::_('Plugin').' '.JText::_('Install').': '.JText::_('No plugin file specified'));			return false;		}		/**		 * ---------------------------------------------------------------------------------------------		 * Filesystem Processing Section		 * ---------------------------------------------------------------------------------------------		 */		// If the plugin directory does not exist, lets create it		$created = false;		(J_VERSION < 1.6) ? $extension_root = &$this->parent->getPath('extension_root') : $extension_root = &$this->parent->extension_root;		if (!file_exists($extension_root)) {			if (!$created = JFolder::create($extension_root)) {				$this->parent->abort(JText::_('Plugin').' '.JText::_('Install').': '.JText::_('Failed to create directory').': "'.$extension_root.'"');				return false;			}		}		/*		 * If we created the plugin directory and will want to remove it if we		 * have to roll back the installation, lets add it to the installation		 * step stack		 */		if ($created) {			$this->parent->pushStep(array ('type' => 'folder', 'path' => $extension_root));		}		// Copy all necessary files		if ($this->parent->parseFiles($element, -1) === false) {			// Install failed, roll back changes			$this->parent->abort();			return false;		}		/**		 * ---------------------------------------------------------------------------------------------		 * db Processing Section		 * ---------------------------------------------------------------------------------------------		 */		// Check to see if a plugin by the same name is already installed		$query = 'SELECT `id`' .				' FROM `#__capturadorfeed_plugins`' .				' WHERE extension = '.$db->Quote($pname);		$db->setQuery($query);		if (!$db->Query()) {			// Install failed, roll back changes			$this->parent->abort('Plugin Install: '.$db->stderr(true));			return false;		}		$id = $db->loadResult();		// Was there a plugin already installed with the same name?		if ($id) {			if (!$this->parent->getOverwrite())			{				// Install failed, roll back changes				$this->parent->abort('Plugin Install: Plugin "' . $pname . '" already exists!' );				return false;			}		}		$row = &JTable::getInstance('FGPlugin','Table');		$row->load($id);;		$row->extension = $pname;		//published defaults to 0 but preserves published state		$row->params = $this->parent->getParams();		if (!$row->store()) {			// Install failed, roll back changes			$this->parent->abort(JText::_('Plugin').' '.JText::_('Install').': '.$db->stderr(true));			return false;		}		// Since we have created a plugin item, we add it to the installation step stack		// so that if we have to rollback the changes we can undo it.		$this->parent->pushStep(array ('type' => 'fgplugin', 'id' => $row->id));		/**		 * ---------------------------------------------------------------------------------------------		 * Finalization and Cleanup Section		 * ---------------------------------------------------------------------------------------------		 */		// Lastly, we will copy the manifest file to its appropriate place.		if (!$this->parent->copyManifest(-1)) {			// Install failed, rollback changes			$this->parent->abort(JText::_('Plugin').' '.JText::_('Install').': '.JText::_('Could not copy setup file'));			return false;		}		return true;	}	/**	 * Custom uninstall method	 *	 * @access	public	 * @param	int		$cid	The id of the plugin to uninstall	 * @param	int		$clientId	The id of the client (unused)	 * @return	boolean	True on success	 * @since	1.5	 */	function uninstall($id, $clientId )	{		// Initialize variables		$row	= null;		$retval = true;		$db		=& $this->parent->getDBO();		// First order of business will be to load the plugin object table from the db.		// This should give us the necessary information to proceed.		$row = &JTable::getInstance('FGPlugin','Table');		$row->load($id);		// Set the plugin root path		$pluginBaseDir = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_feedgator'.DS.'plugins'.DS.$row->extension;		$this->parent->setPath('extension_root', $pluginBaseDir);		// Because plugins don't have their own folders we cannot use the standard method of finding an installation manifest		$manifestFile = $pluginBaseDir.DS.$row->extension.'.xml';		if (file_exists($manifestFile))		{			$xml = new JSimpleXML;			// If we cannot load the xml file return null			if (!$xml->loadFile($manifestFile)) {				JError::raiseWarning(100, JText::_('Plugin').' '.JText::_('Uninstall').': '.JText::_('Could not load manifest file'));				return false;			}			/*			 * Check for a valid XML root tag.			 * @todo: Remove backwards compatability in a future version			 * Should be 'install', but for backward compatability we will accept 'mosinstall'.			 */			$root =& $xml->document;			if ($root->name() != 'install' && $root->name() != 'mosinstall') {				JError::raiseWarning(100, JText::_('Plugin').' '.JText::_('Uninstall').': '.JText::_('Invalid manifest file'));				return false;			}			// Remove the plugin files			$this->parent->removeFiles((J_VERSION < 1.6) ? $root->getElementByPath('images') : $root->manifest->images, -1);			$this->parent->removeFiles((J_VERSION < 1.6) ? $root->getElementByPath('files') : $root->manifest->files, -1);			JFile::delete($manifestFile);			// Remove all media and languages as well			$this->parent->removeFiles((J_VERSION < 1.6) ? $root->getElementByPath('media') : $root->manifest->media);			$this->parent->removeFiles((J_VERSION < 1.6) ? $root->getElementByPath('languages') : $root->manifest->languages, 1);		} else {			JError::raiseWarning(100, 'Plugin Uninstall: Manifest File invalid or not found');			return false;		}		// Now we will no longer need the plugin object, so lets delete it		$row->delete($row->id);		unset ($row);		return $retval;	}	/**	 * Custom rollback method	 * 	- Roll back the plugin item	 *	 * @access	public	 * @param	array	$arg	Installation step to rollback	 * @return	boolean	True on success	 * @since	1.5	 */	function _rollback_plugin($arg)	{		// Get db connector object		$db =& $this->parent->getDBO();		// Remove the entry from the plugin table		$query = 'DELETE' .				' FROM `#__capturadorfeed_plugins`' .				' WHERE id='.(int)$arg['id'];		$db->setQuery($query);		return ($db->query() !== false);	}}