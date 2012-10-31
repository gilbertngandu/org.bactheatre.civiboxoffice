<?php

/**
 *
 *
 * @version $Id: plugin.custom_orderdescription.php 1785 2012-05-12 07:10:13Z nielsNL $
 * @copyright 2010
 */
class plugin_Custom_Smartyplugins extends baseplugin {

	public $plugin_info		  = 'Smartyplugins';
	/**
	 * description - A full description of your plugin.
	 */
	public $plugin_description	= 'This plugin shows a hello World on the webshop.';
	/**
	 * version - Your plugin's version string. Required value.
	 */
	public $plugin_myversion		= '0.0.1';
	/**
	 * requires - An array of key/value pairs of basename/version plugin dependencies.
	 * Prefixing a version with '<' will allow your plugin to specify a maximum version (non-inclusive) for a dependency.
	 */
	public $plugin_requires	= null;
	/**
	 * author - Your name, or an array of names.
	 */
	public $plugin_author		= 'The FusionTicket team';
	/**
	 * contact - An email address where you can be contacted.
	 */
	public $plugin_email		= 'info@fusionticket.com';
	/**
	 * url - A web address for your plugin.
	 */
	public $plugin_url			= 'http://www.fusionticket.org';

  public $plugin_actions  = array ('install','uninstall','Smarty');

  function smartyFunctionTestMe($smarty, $params) {
    return "Hello World";
  }

}

?>