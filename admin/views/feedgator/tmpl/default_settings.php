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

defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.mootools');
jimport('joomla.html.pane');
$options =  array('startOffset'=>0);
$pane =& JPane::getInstance('tabs',$options);

?>

<form name="adminForm" id="adminForm" method="post" action="index.php">

	<?php 
	if(J_VERSION < 1.6) {
		
		echo $pane->startPane('pane');
		echo $pane->startPanel(JText::_('FG_TAB_GLOBALS'),'panel1');
		
		echo $this->params->render('params','global_feed');
		
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('FG_TAB_PUBLISHING'),'panel2');
		
		echo $this->params->render('params','publishing_1');
		echo $this->params->render('params','publishing_2');
		
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('FG_TAB_PROCESSING_DUPS'),'panel3');
		
		echo $this->params->render('params','duplicates');
		
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('FG_TAB_TXT_HANDLING'),'panel4');
		
		echo $this->params->render('params','text_1');
		echo $this->params->render('params','text_2');
		
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('FG_TAB_LANGS'),'panel5');
		
		echo $this->fgParams->render('params','languages');
		
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('FG_TAB_IMGS_ENCS'),'panel6');
		
		echo $this->params->render('params','images_1');
		echo $this->params->render('params','images_2');
		echo $this->params->render('params','images_3');
	
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('FG_TAB_LINKS'),'panel7');
		
		echo $this->params->render('params','links');
		
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('FG_TAB_TXT_FLTRS'),'panel8');
		
		echo $this->params->render('params','text');
		
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('FG_TAB_HTML_FLTRS'),'panel9');
		
		echo $this->params->render('params','html');
		
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('FG_TAB_IMPORT_FLTRS'),'panel10');
		
		echo $this->params->render('params','import_1');
		echo $this->params->render('params','import_2');
		echo $this->params->render('params','import_3');
		
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('FG_TAB_TAGGING'),'panel11');
		
		echo $this->params->render('params','tagging');
		
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('FG_TAB_EMAIL'),'panel12');
		
		echo $this->params->render('params','email');
		
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('FG_TAB_ADV'),'panel13');
		
		echo $this->params->render('params','advanced');
		
		echo $pane->endPanel();
		echo $pane->endPane();
		
	} else {
	
		echo $pane->startPane('pane');
		echo $pane->startPanel(JText::_('FG_TAB_GLOBALS'),'panel1');
		
		echo FeedgatorHelper::renderForm($this->params->getParams('params','global_feed'));
		
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('FG_TAB_PUBLISHING'),'panel2');
		
		echo FeedgatorHelper::renderForm($this->params->getParams('params','publishing_1'));
		echo FeedgatorHelper::renderForm($this->params->getParams('params','publishing_2'));
		
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('FG_TAB_PROCESSING_DUPS'),'panel3');
		
		echo FeedgatorHelper::renderForm($this->params->getParams('params','duplicates'));
		
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('FG_TAB_TXT_HANDLING'),'panel4');
		
		echo FeedgatorHelper::renderForm($this->params->getParams('params','text_1'));
		echo FeedgatorHelper::renderForm($this->params->getParams('params','text_2'));
		
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('FG_TAB_LANGS'),'panel5');
		
		echo FeedgatorHelper::renderForm($this->fgParams->getParams('params','languages'));
		
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('FG_TAB_IMGS_ENCS'),'panel6');
		
		echo FeedgatorHelper::renderForm($this->params->getParams('params','images_1'));
		echo FeedgatorHelper::renderForm($this->params->getParams('params','images_2'));
		echo FeedgatorHelper::renderForm($this->params->getParams('params','images_3'));
	
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('FG_TAB_LINKS'),'panel7');
		
		echo FeedgatorHelper::renderForm($this->params->getParams('params','links'));
		
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('FG_TAB_TXT_FLTRS'),'panel8');
		
		echo FeedgatorHelper::renderForm($this->params->getParams('params','text'));
		
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('FG_TAB_HTML_FLTRS'),'panel9');
		
		echo FeedgatorHelper::renderForm($this->params->getParams('params','html'));
		
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('FG_TAB_IMPORT_FLTRS'),'panel10');
		
		echo FeedgatorHelper::renderForm($this->params->getParams('params','import_1'));
		echo FeedgatorHelper::renderForm($this->params->getParams('params','import_2'));
		echo FeedgatorHelper::renderForm($this->params->getParams('params','import_3'));
		
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('FG_TAB_TAGGING'),'panel11');
		
		echo FeedgatorHelper::renderForm($this->params->getParams('params','tagging'));
		
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('FG_TAB_EMAIL'),'panel12');
		
		echo FeedgatorHelper::renderForm($this->params->getParams('params','email'));
		
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('FG_TAB_ADV'),'panel13');
		
		echo FeedgatorHelper::renderForm($this->params->getParams('params','advanced'));
		
		echo $pane->endPanel();
		echo $pane->endPane();
	} ?>
	
	<input type="hidden" name="id" value="<?php echo $this->component->id;?>" />
	<input type="hidden" name="option" value="com_feedgator"/>
	<input type="hidden" value="" name="task"/>
	<?php echo JHTML::_( 'form.token' ); ?>
</form>