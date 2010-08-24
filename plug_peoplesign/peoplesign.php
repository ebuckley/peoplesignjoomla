<?php
/**
 * Peoplesign photo captcha plugin for Joomla! 1.5
 * @author	Ersin Buckley, <support@peoplesign.com>
 * @package	Joomla 
 * @subpackage	System
 * @category	Plugin
 * @version	1.7.0
 * @copyright	Copyright (C) 2010 - Myricomp llc
 * @license	http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die('Restricted access');
define('PEOPLESIGN_WRAPPER_VERSION', 'psJoomla_1.7.0');


include_once( 'peoplesign/peoplesignClient.php');

class plgSystemPeoplesign extends JPlugin
{
	function plgSystemPeoplesign(&$subject)
	{
	parent::__construct($subject);

	// load plugin parameters
	$this->plugin = &JPluginHelper::getPlugin('system', 'peoplesign');
	$this->params = new JParameter($this->plugin->params);
		
		
	$this->isError = false;
	$this->regkey = $this->params->get('peoplesignKey');
	$this->configString = $this->params->get('peoplesignConfigurationString');
	}
	
	/*
	*This function is called after joomla is initialised but before the document buffer has been displayed.
	*/
	function onAfterDispatch()
	{	
		$document = JFactory::getDocument();
		$content = $document->getBuffer('component');
		
		$classFilesPath = dirname(__FILE__) . DS .'peoplesign' . DS . 'plugin';	
		//lists all files in the folderpath given. The regex simply eliminates php files not starting with location		
		$classFiles = JFolder::files($classFilesPath, '^location_.*?\.php$');
		foreach ($classFiles as $key => $class)
		{
			require_once('peoplesign/plugin/' . $class);
			$class = substr($class, 0,-4);
			$location = new $class;
			$islocationEnabled = $this->params->get("enableFor".$location->paramName);
			if($location->checkRules() && $islocationEnabled == "Yes")
			{
				$newContent = str_replace($location->formPattern,$this->getHTML().$location->formPattern,$content);
				$document->setBuffer($newContent,'component');
				continue;
			}
			
		}		
	}
	
	/*
	*onAfterRoute is called right after the client has submitted a request to go to a diffrent joomla page but before the new document has 
	*been displayed or put in the buffer
	*/
	function onAfterRoute()
	{
		$application = &JFactory::getApplication();
		$classFilesPath = dirname(__FILE__) . DS .'peoplesign' . DS . 'plugin';	
		//lists all files in the folderpath given. The regex simply eliminates php files not starting with location		
		$classFiles = JFolder::files($classFilesPath, '^location_.*?\.php$');
		foreach ($classFiles as $key => $class)
		{
			require_once('peoplesign/plugin/' . $class);
			$class = substr($class, 0,-4);
			$location = new $class;
			$isLocationEnabled = $this->params->get("enableFor".$location->paramName);
		
			if(	isset($_REQUEST[PEOPLESIGN_CHALLENGE_RESPONSE_NAME])     )
			{
				if ($location->isPeoplesignCheckRequired() && $isLocationEnabled = "Yes")
				{
					if(!isPeoplesignResponseCorrect(null,null,"default",$this->regkey))
					{
						if (isset($location->redirect)) $application->redirect($_SERVER['HTTP_REFERER']);
						JRequest::setVar('task', '');
					}
				}
			}
		}
		
	}
	
        /*
	 *Return: list of forms that are enabled
         */
	function isAddonEnabled()
	{
		$regex = "/^enableFor/";
		$params = get_object_vars($this->params);
		$params = $params['_raw'];
		$params = explode("\n", $params);
		$params = preg_grep($regex,$params);

		foreach ($params as $value)
		{
			$key = explode("=",$value);
			$enabledForms[$key[0]] = $key[1];		
		}
		return $enabledForms;
	}
	
	/*
	* Returns he peoplesign html code
	*
	*@Return string		returns string of javascript that loads the peoplesign code
	*/
	private function getHTML()
	{
		require_once("peoplesign/peoplesignClient.php");
		return getPeoplesignHTML($this->params->get('peoplesignKey'),
                                         $this->params->get('peoplesignConfigurationString'),
                                         "default",
					 PEOPLESIGN_WRAPPER_VERSION);
	}
	/*
	*echos the contents of the $var passedto it in a readable format
	*@Return	null		no return variables
	*/
	function test($var)
	{
		echo "test worked";
		echo "<pre style='background-color:white;'>";
		print_r($var);
		echo "</pre>";
	}
}
