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

defined('_JEXEC') or die('Restricted access');

$folder = '/administrator/components/com_feedgator/plugins';
$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_feedgator'.DS.'plugins';

JHTML::_('behavior.modal', 'a.modal-button');
?>

<form name="adminForm" enctype="multipart/form-data" method="post" action="index.php">
	<fieldset class="paramform">
		<legend><?php echo JText::_('FG_INSTALLED_PLGS'); ?></legend>
		<div id="plugins">
			<?php if (!count($this->plugins)) { ?>
				<div><?php echo JText::_('FG_NO_PLGS_INSTALLED'); ?></div>
			<?php } else {
				foreach ($this->plugins as &$row) {
					if (!$row->pub_count AND !isset($warning)) { ?>
						<div class="warning"><?php echo JText::_('FG_NO_PLGS_PUBLISHED'); ?></div>
						<?php $warning = 1;
					} ?>
					<div class="plugin <?php echo $row->published ? 'published' : 'unpublished'; ?> <?php  echo $row->componentInstalled ? 'installed' : 'orphaned'; ?>">
				        <div class="titlebar">
			        		<img src="<?php echo JURI::base().'components/com_feedgator/plugins/'.$row->extension.'/'.$row->extension.'.png'; ?>" alt="<?php echo $row->name; ?>" class="pluginlogo" />
				            <div class="pluginname red"><?php echo $row->name; ?></div>
				            <div class="plugincomponent <?php echo $row->componentInstalled ? 'green' : 'red'; ?>"><?php echo JText::_('Component').': ' . ($row->componentInstalled ? 'Installed' : 'Not Installed'); ?></div>
							<div class="pluginversion"><?php echo JText::_('FG_PLG_VERSION').': ' . (@$row->version ? $row->version : "&nbsp;"); ?></div>
				            <div class="spacer"></div>
				        </div>
				        <div class="insidebox">
							<div class="plugindate"><?php echo JText::_('FG_PLG_CREATED').': ' . (@$row->creationdate ? $row->creationdate : "&nbsp;"); ?></div>
							<div class="plugindate"><?php echo JText::_('FG_PLG_UPDATED').': ' . (@$row->updateddate ? $row->updateddate : "&nbsp;"); ?></div>							<div class="pluginauthor"><?php echo JText::_('FG_PLG_AUTHOR') .': ' . (@$row->author ? $row->author : JText::_('FG_UNKNOWN_AUTHOR')); ?></div>
							<div class="pluginemail"><?php echo  JText::_('FG_PLG_AUTHOR_EMAIL') .': ' . (@$row->authorEmail ? ' &lt;'.$row->authorEmail.'&gt;' : "&nbsp;"); ?></div>
							<div class="pluginauthorurl"><?php  echo JText::_('FG_PLG_AUTHOR_URL') .': ' . (@$row->authorUrl ? "<a href=\"" .(substr( $row->authorUrl, 0, 7) == 'http://' ? $row->authorUrl : 'http://'.$row->authorUrl) ."\" target=\"_blank\">$row->authorUrl</a>" : "&nbsp;"); ?></div>
				            <div class="plugintaskbar">
				            	<a href="index.php?option=com_feedgator&task=doUninstall&id=<?php echo $row->id; ?>&<?php echo JUtility::getToken(); ?>=1" onclick="return confirm('<?php echo JText::_('FG_PLG_CONFIRM_UNINSTALL'); ?>');"><span class="uninstall_img"><?php echo JText::_('FG_PLG_UNINSTALL'); ?></span></a>
				            	<?php if($row->componentInstalled) { ?>
					            	<a href="index.php?option=com_feedgator&task=pluginSettings&ext=<?php echo $row->extension; ?>&tmpl=component" class="modal-button"><span class="options_img"><?php echo JText::_('FG_PLG_OPTIONS'); ?></span></a>
									<a href="index.php?option=com_feedgator&task=changePluginState&ext=<?php echo $row->extension; ?>&id=<?php echo $row->id; ?>"><span class="<?php echo $row->published ? 'unpublished_img' : 'published_img'; ?>"><?php echo $row->published ? JText::_('FG_PLG_PUBLISHED') : JText::_('FG_PLG_UNPUBLISHED'); ?></span></a>
								<?php } ?>
							</div>
				         </div>
			        </div>
				<?php } ?>
			<?php } ?>
		</div>
		<div id="divloading" style="display:none;"><?php echo FG_LOADING; ?></div>
	</fieldset>
	<fieldset class="paramform">
		<legend><?php echo JText::_('FG_INSTALL_PLG'); /*JText::_('FG_INSTALL_NEW_PLG')*/ ?></legend>
		<table class="content">
			<tr>
				<td class="item"><?php echo $folder; ?></td>
				<td align="left">
				<?php echo is_writable( $path ) ? '<strong><font color="green">'.JText::_('FG_WRITEABLE').'</font></strong>' : '<strong><font color="red">'.JText::_('FG_UNWRITEABLE').'</font></strong>'; ?>
				</td>
			</tr>
		</table>
		<div class="warning">
			<?php echo JText::_('FG_INSTALL_3PD_WARN'); ?>
		</div>
		<script language="javascript" type="text/javascript">
		function submitbutton3(pressbutton) {
			var form = document.adminForm_dir;

			// do field validation
			if (form.install_directory.value == ""){
				alert( "<?php echo str_replace('"','\\"',JText::_('FG_MSG_SELECT_FOLDER')); ?>" );
			} else {
				form.submit();
			}
		}
		</script>
		<table class="adminform">
			<tr>
				<th>
				<?php echo JText::_('FG_UPLOAD_PKG_FILE'); ?>
				</th>
			</tr>
			<tr>
				<td align="left">
				<?php echo JText::_('Package File') . ':'; ?>
				<input class="text_area" name="install_package" type="file" size="40"/>
				<input class="button" type="submit" value="<?php echo JText::_('FG_UPLOAD_AND_INSTALL'); ?>" />
				</td>
			</tr>
		</table>
	
		<table class="adminform">
			<tr>
				<th>
				<?php echo JText::_('FG_INSTALL_F_DIRECTORY'); ?>
				</th>
			</tr>
			<tr>
				<td align="left">
				<?php echo JText::_('FG_INSTALL_DIRECTORY'); ?>:&nbsp;
				<input type="text" name="install_directory" class="text_area" size="60" value="<?php echo $folder; ?>"/>&nbsp;
				<input type="button" class="button" value="<?php echo JText::_('FG_INSTALL'); ?>" onclick="submitbutton3()" />
				</td>
			</tr>
		</table>
	</fieldset>
	<input type="hidden" name="installtype" value="upload">
	<input type="hidden" name="task" value="doInstall">
	<input type="hidden" name="option" value="com_feedgator"/>
	<?php echo JHTML::_( 'form.token' ); ?>
</form>