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
 * The XmapPlugin
 * @author Guillermo Vargas, http://joomla.vargas.co.cr
 * @email guille@vargas.co.cr, http://joomla.vargas.co.cr
 * @package Xmap
*/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

class TableFGPlugin extends JTable {
	
	var $id			= '';
	var $extension 	= '';
	var $published	= 0;
	var $params		= '';

	function TableFGPlugin(&$_db) {
		parent::__construct( '#__capturadorfeed_plugins', 'id', $_db );
	}
}
