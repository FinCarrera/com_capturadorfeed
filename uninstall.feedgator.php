<?php

/**

* Capturador Feed - Importa las noticias de los feed a tu base de datos de Joomla
* @version 1.0
* @package capturadorfeed
* @author Andres y Javier Basado en FeedGator
* @Joomla 1.5 Version
* @copyright (C) 2012 
* @license GNU/GPL: http://www.gnu.org/copyleft/gpl.html

**/
// No tiene acceso directo
defined('_JEXEC') or die('Restricted access');

/**
 * Esta funci—n se llama cuando el componente est‡ instalado.
 */
function com_uninstall() {

	$version = '1.0';

	/** Borrar las tablas creadas por el componente */
	$db = & JFactory::getDBO();
	$querys[] = 'DROP TABLE IF EXISTS #__capturadorfeed';
	$querys[] = 'DROP TABLE IF EXISTS #__capturadorfeed_plugins';
	$querys[] = 'DROP TABLE iF EXISTS #__capturadorfeed_imports';
	foreach ($querys as $query) {
		$db->setQuery( $query );
		if( $db->query() === FALSE ) {
			echo stripslashes($db->getErrorMsg());
		}
	}
	?>
	<h2><?php echo JText::_('Estado de la desinstalacion del CapturadorFeeds'); ?></h2>
	<div>Desinstalando la version <strong><?php echo $version; ?></strong><br />
	<br />
	Tablas borradas de la base de datos. ÁDesinstalacion Completada!
	<br />
	</div>
	<?php
}