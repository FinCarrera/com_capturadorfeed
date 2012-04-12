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

defined('_JEXEC') or die('Restricted access');

?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	
	<?php $text = $this->plugin->params->render('pluginparams');
	echo $text;
	
	if(strpos($text,JText::_('FG_PLG_NO_PARAMS')) === false) { ?>
	
		<input type="submit" name="submit" value="Submit" />
	<?php } ?>
	
	<input type="hidden" name="id" value="<?php echo $this->plugin->id; ?>" />
	<input type="hidden" name="ext" value="<?php echo $this->plugin->extension; ?>" />
	<input type="hidden" name="feedId" value="-1" />
	<input type="hidden" name="option" value="com_feedgator" />
	<input type="hidden" name="task" value="savePluginSettings" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>