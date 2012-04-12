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
$tab = JRequest::getInt('fgtab');
$options =  array('startOffset'=>$tab);
$pane =& JPane::getInstance('tabs',$options);

in_array(JRequest::getCmd('task'),array('new','add')) ? $edit = false : $edit = true;

/*echo '<pre>'; 
print_r($this->fgParams);
echo '</pre>';*/

?>

<script language="javascript" type="text/javascript">
<!--

var contentsections = new Array;
var sectioncategories = new Array;

<?php

$i = 0;

foreach ($this->contentsections as $k=>$items) {
	foreach ($items as $v) {
		echo "contentsections[".$i++."] = new Array( '$k','".addslashes( $v->id )."','".addslashes( $v->title )."' );\n\t\t";
	}
}
$i = 0;

foreach ($this->sectioncategories as $k=>$items) {
	foreach ($items as $v) {
		echo "sectioncategories[".$i++."] = new Array( '$k','".addslashes( $v->id )."','".addslashes( $v->title )."' );\n\t\t";
	}
}
if(J_VERSION < 1.6) {
?>
   function submitbutton(pressbutton) {
<?php } else { ?>
	Joomla.submitbutton = function(pressbutton) {
<?php } ?>
     var form = document.adminForm;

     if (pressbutton == 'cancel') {
       submitform( pressbutton );
       return;
     }      // do field validation

     if (form.datafeed.value == "") {
       alert( "You must at least enter a feed." );
     } else if (form.datatitle.value == "") {
       alert( "You must enter a title." );
     } <?php if(J_VERSION < 1.6) echo 'else if (form.datasectionid.value == "") {
       alert( "You must enter a section" );
     }'; ?> else if (form.datacontent_type.value == "-1") {
       alert( "You must choose a content type" );
     } else if (form.datacatid.value == "") {
       alert( "You must enter a category" );
		} else {
       submitform( pressbutton );
     }
   }
-->
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	
	<?php
	if(J_VERSION < 1.6) {
		
		echo $pane->startPane('pane');
		echo $pane->startPanel(JText::_('FG_TAB_FEED_DETAILS'),'panel1');
		
		echo $this->fgParams->render('data','feed_3');
		echo $this->fgParams->render('data','feed_1');
		echo $this->fgParams->render('params','feed_2');
		
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('FG_TAB_PUBLISHING'),'panel2');
		
		echo $this->fgParams->render('data','publishing_1');
		echo $this->fgParams->render('params','publishing_2');
		
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('FG_TAB_PROCESSING_DUPS'),'panel3');
		
		echo $this->fgParams->render('params','duplicates');
		
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('FG_TAB_TXT_HANDLING'),'panel4');
		
		echo $this->fgParams->render('params','text_1');
		echo $this->fgParams->render('data','text_2');
		
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('FG_TAB_LANGS'),'panel5');
		
		echo $this->fgParams->render('params','languages');
		
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('FG_TAB_IMGS_ENCS'),'panel6');
		
		echo $this->fgParams->render('params','images_1');
		echo $this->fgParams->render('params','images_3');
		
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('FG_TAB_LINKS'),'panel7');
		
		echo $this->fgParams->render('params','links');
		
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('FG_TAB_TXT_FLTRS'),'panel8');
		
		echo $this->fgParams->render('params','text');
		
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('FG_TAB_HTML_FLTRS'),'panel9');
		
		echo $this->fgParams->render('params','html');
		
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('FG_TAB_IMPORT_FLTRS'),'panel10');
		
		echo $this->fgParams->render('data','import_1');
		echo $this->fgParams->render('params','import_2');
		echo $this->fgParams->render('data','import_3');
		
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('FG_TAB_TAGGING'),'panel11');
		
		echo $this->fgParams->render('params','tagging');
		
	} else {
		
		echo $pane->startPane('pane');
		echo $pane->startPanel(JText::_('FG_TAB_FEED_DETAILS'),'panel1');
		
		echo FeedgatorHelper::renderForm($this->fgParams->getParams('data','feed_3'),array('control'=>'data'));
		echo FeedgatorHelper::renderForm($this->fgParams->getParams('data','feed_1'),array('control'=>'data'));
		echo FeedgatorHelper::renderForm($this->fgParams->getParams('params','feed_2'));
		
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('FG_TAB_PUBLISHING'),'panel2');
		
		echo FeedgatorHelper::renderForm($this->fgParams->getParams('data','publishing_1'),array('control'=>'data'));
		echo FeedgatorHelper::renderForm($this->fgParams->getParams('params','publishing_2'));
		
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('FG_TAB_PROCESSING_DUPS'),'panel3');
		
		echo FeedgatorHelper::renderForm($this->fgParams->getParams('params','duplicates'));
		
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('FG_TAB_TXT_HANDLING'),'panel4');
		
		echo FeedgatorHelper::renderForm($this->fgParams->getParams('params','text_1'));
		echo FeedgatorHelper::renderForm($this->fgParams->getParams('data','text_2'),array('control'=>'data'));
		
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('FG_TAB_LANGS'),'panel5');
		
		echo FeedgatorHelper::renderForm($this->fgParams->getParams('params','languages'));
		
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('FG_TAB_IMGS_ENCS'),'panel6');
		
		echo FeedgatorHelper::renderForm($this->fgParams->getParams('params','images_1'));
		echo FeedgatorHelper::renderForm($this->fgParams->getParams('params','images_3'));
		
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('FG_TAB_LINKS'),'panel7');
		
		echo FeedgatorHelper::renderForm($this->fgParams->getParams('params','links'));
		
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('FG_TAB_TXT_FLTRS'),'panel8');
		
		echo FeedgatorHelper::renderForm($this->fgParams->getParams('params','text'));
		
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('FG_TAB_HTML_FLTRS'),'panel9');
		
		echo FeedgatorHelper::renderForm($this->fgParams->getParams('params','html'));
		
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('FG_TAB_IMPORT_FLTRS'),'panel10');
		
		echo FeedgatorHelper::renderForm($this->fgParams->getParams('data','import_1'),array('control'=>'data'));
		echo FeedgatorHelper::renderForm($this->fgParams->getParams('params','import_2'));
		echo FeedgatorHelper::renderForm($this->fgParams->getParams('data','import_3'),array('control'=>'data'));
		
		echo $pane->endPanel();
		echo $pane->startPanel(JText::_('FG_TAB_TAGGING'),'panel11');
		
		echo FeedgatorHelper::renderForm($this->fgParams->getParams('params','tagging'));
	}
	
	echo $pane->endPanel();
	echo $pane->startPanel(JText::_('FG_TAB_PLG_SETTINGS'),'panel12');
	
	echo '<div id="pluginparams">'.JText::_('FG_PLG_PARAMS_NOT_LOADED').'</div>';
	
	echo $pane->endPanel();
	echo $pane->startPanel(JText::_('FG_TAB_IMPORT_HX'),'panel13');
	
	JRequest::setVar('ajax',1);
	JRequest::setVar('filter_feedid',$this->fgParams->get('id'));
	echo $edit ? $this->display('imports') : '<div id="feedimports">'.JText::_('FG_FEED_IMPORTS').'</div>';
	
	echo $pane->endPanel();
	echo $pane->endPane(); ?>
	
	<input type="hidden" name="cid" value="<?php echo $this->fgParams->get('id');?>" />
	<input type="hidden" name="option" value="com_feedgator" />
	<input type="hidden" name="task" value="edit" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>