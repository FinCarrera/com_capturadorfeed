<?php
/**
 * FeedGator pseudo-cron automatic import plugin
 * @version	0.1
 * @author	Matt Faulds
 * @license	GPL 2
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );
jimport( 'joomla.html.parameter' );

class plgSystemFeedGatorPseudocron extends JPlugin
{
	protected $interval			= 300;

	function plgSystemFeedGatorPseudocron( &$subject, $params )
	{
		parent::__construct( $subject, $params );

		$this->plugin	=& JPluginHelper::getPlugin('system', 'feedgatorpseudocron');
		$this->params	= new JParameter($this->plugin->params);

		$this->interval	= (int) ($this->params->get('interval', 5)*60);

		// correct value if value is under the minimum
		if ($this->interval < 300) { $this->interval = 300; }
	}

	function onAfterRoute()
	{
		$app = &JFactory::getApplication();

		if ($app->isSite()) {
			$now = &JFactory::getDate();
			$now = $now->toUnix();
			if($last = $this->params->get('last_import')) {
				$diff = $now - $last;
			} else {
				$diff = $this->interval+1;
			}

			if ($diff > $this->interval) {

				$version = new JVersion();
				define('J_VERSION', $version->getShortVersion());

				require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_feedgator'.DS.'controller.php');
				require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_feedgator'.DS.'factory.feedgator.php');
				require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_feedgator'.DS.'helpers'.DS.'feedgator.helper.php');
				require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_feedgator'.DS.'helpers'.DS.'feedgator.utility.php');
				if(file_exists(JPATH_ROOT.DS.'plugins'.DS.'system'.DS.'addkeywords.php')) {
					require_once(JPATH_ROOT.DS.'plugins'.DS.'system'.DS.'addkeywords.php' );
				}
				FeedgatorUtility::profiling('Start pseudo-cron');

				define('SPIE_CACHE_AGE', 60*10);

				require_once(JPATH_ROOT.DS.'libraries'.DS.'simplepie'.DS.'simplepie.php');
				require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_feedgator'.DS.'inc'.DS.'simplepie'.DS.'overrides.php');
				FeedgatorUtility::profiling('Loaded SimplePie');

				JRequest::setVar('task','pseudocron','get');
				JRequest::setVar(JUtility::getToken(),'1','get');

				$config = array('base_path'=>JPATH_ADMINISTRATOR.DS.'components'.DS.'com_feedgator');
				$controller = new FeedgatorController($config);
				if($result = $controller->import('all')) {
					jimport( 'joomla.registry.format' );
					$db		= JFactory::getDbo();
					$this->params->set('last_import',$now);
					if(J_VERSION >= 1.6) {
						$handler = &JRegistryFormat::getInstance('json');
						$params = new JObject();
						$params->set('interval',$this->params->get('interval',5));
						$params->set('last_import',$now);
						$params = $handler->objectToString($params,array());

						$query = 	'UPDATE #__extensions'.
									' SET params='.$db->Quote($params).
									' WHERE element = '.$db->Quote('feedgatorpseudocron').
									' AND folder = '.$db->Quote('system').
									' AND enabled >= 1'.
									' AND type ='.$db->Quote('plugin').
									' AND state >= 0';
						$db->setQuery($query);
						$db->query();
					} else {
						$params = '';
						$params .= 'interval='.$this->params->get('interval',5)."\n";
						$params .= 'last_import='.$now."\n";

						$query = 	'UPDATE #__plugins'.
									' SET params='.$db->Quote($params).
									' WHERE element = '.$db->Quote('feedgatorpseudocron').
									' AND folder = '.$db->Quote('system').
									' AND published >= 1';
						$db->setQuery($query);
						$db->query();
					}
				}
				FeedgatorUtility::profiling('End');
			}
		}
	}
}