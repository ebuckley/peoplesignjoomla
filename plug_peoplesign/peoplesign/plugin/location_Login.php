<?php

/**
 * Peoplesign photo captcha plugin for Joomla! 1.5
 * @author		Ersin Buckley, <support@peoplesign.com>
 * @package		Joomla 
 * @subpackage	System
 * @category	Plugin
 * @version		1.0.0 Experimental
 * @copyright	Copyright (C) 2007 - Myricomp llc
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL 2
 * 
 * this is the base class for defining peoplesign addons
 */
 
defined('_JEXEC') or die('Restricted access');

#doc
#	classname:	addonPeoplesign
#	scope:		public
#/doc

class location_login
{
	#internal variables
	
	//form pattern we search for
	var $formPattern = '<input type="submit" name="Submit" class="button" value="Login" />';
	
	// unique name as identified through the params on admin side
	var $paramName = "Login";
	
	//required component to display this plugin
	var $com_name = "com_user";
	
	//view required to display this plugin
	var $requiredView = "login";
	
	
	function __construct()
	{
		// load plugin parameters
		$this->plugin = &JPluginHelper::getPlugin('system', 'peoplesign');
		$this->params = new JParameter($this->plugin->params);
		
		$this->option = JRequest::getVar('option');
		$this->view = JRequest::getVar('view');
		
		$this->requiredView = ($this->requiredView == "") ? $this->view : $this->requiredView;
		
	}
	###
	# checks to see if peoplesign should be displayed
	# @return	BOOLEAN		
	#returns a true if we are too display the challenge
	public function checkRules()
	{
#		echo strtolower($this->com_name) . " == " . $this->option . "&&" . $this->view.  " == " .$this->requiredView;
		if(strtolower($this->com_name) == $this->option && $this->view == $this->requiredView)
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}
	
	public function isPeoplesignCheckRequired()
	{
			if (JRequest::getVar('task') == "login")
			{
				JRequest::setVar('view','login');
				return 1;
			}
			else
			{
				return 0;
			}
	}
}
?>
