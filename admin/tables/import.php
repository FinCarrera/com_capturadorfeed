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

defined( '_JEXEC' ) or die( 'El acceso directo a esta ubicaci—n no esta permitido.' );

class TableImport extends JTable
{
	var $id			= null;
	var $content_id	= null;
	var $plugin		= null;
	var $feed_id	= null;
	var $hash	 	= null;

	function TableImport(&$_db)
	{
		parent::__construct( '#__capturadorfeed_imports', 'id', $_db );
	}
}
