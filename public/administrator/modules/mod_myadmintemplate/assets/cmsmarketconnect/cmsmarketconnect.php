<?php
defined('_JEXEC') or die();
/****************************************************
* CMS Market Connect
* Quickly add updatable version information, support page, and product links to your extension
* Copyright: CMS Market, LLC.  All rights reserved
* License: You may freely distribute or modify the contents of this file as long as:
*  1) This section remains intact
*  2) Any output derived from these functions to an end user must have one of the following attributions contained within a link to "http://www.cmsmarket.com/":
*   a) The text "CMS Market" or similar, ex: "Information provided by CMS Market" etc.
*   b) The CMS Market connect logo, available at "http://www.cmsmarket.com/images/cmsmconnect_logo.jpg"
*
* VERSION: 1.0
****************************************************/

jimport('phpxmlrpc.xmlrpc');
class CMSMarketConnect
{
	var $supportPage;
	var $name;
	var $currentVersion;
	var $installedVersion;
	var $rating;
	var $url;
	var $dataReturned = false;

	function display($extensionName, $installedVersion = null)
	{
		$this->_getExtensionData('joomla', $extensionName);
		if ($installedVersion != null)
			$this->installedVersion = $installedVersion;
		else
			$this->installedVersion = $this->_getJoomlaInstalledVersion($extensionName);

			#if ($tmplFile == null)
			#$tmplFile = dirname(__FILE__).DS.'template.php';

		include_once(dirname(__FILE__).DS.'template.php');
	}

	/**
	* Returns current version number, support link, and reviews for the passed in extension name. Input 1=cms name, input 2=extension name.
	* @return string (or an xmlrpcresp obj instance if call fails)
	*/
	function _getExtensionData ($cmsName, $extensionName) {
		#$client =& new xmlrpc_client('/cmsmarket/xmlrpc/index.php', 'localhost', 80);
		$client =& new xmlrpc_client('/xmlrpc/index.php', 'www.cmsmarket.com', 80);
		$client->return_type = 'xmlrpcvals';
		$msg =& new xmlrpcmsg('CMSMarketItems.getExtensionDataBasic');
		$p1 =& new xmlrpcval($cmsName, 'string');
		$msg->addparam($p1);
		$p2 =& new xmlrpcval($extensionName, 'string');
		$msg->addparam($p2);
		$res =& $client->send($msg, 0, '');
		if ($res->faultcode())
  			return $res;
		else
		{
	  		$data = php_xmlrpc_decode($res->value());

			if (isset($data['url']))
			{
				$this->dataReturned = true;
				$this->name = $data['name'];
				$this->url = $data['url'];
				$this->supportPage = $data['support_page'];
				$this->currentVersion = $data['current_version'];
				$this->rating = html_entity_decode($data['rating']);
			}
			else
				$this->dataReturned = false;
		}
	}

	function _getJoomlaInstalledVersion($extensionName)
	{
		$version = "Undetermined";
		if (JFolder::exists(JPATH_ADMINISTRATOR.DS.'components'.DS.$extensionName))
			$dir = JPATH_ADMINISTRATOR.DS.'components'.DS.$extensionName;
		else if (JFolder::exists(JPATH_SITE.DS.'components'.DS.$extensionName))
			$dir = JPATH_SITE.DS.'components'.DS.$extensionName;
		else if (JFolder::exists(JPATH_SITE.DS.'modules'.DS.$extensionName))
			$dir = JPATH_SITE.DS.'modules'.DS.$extensionName;
		else if (JFolder::exists(JPATH_ADMINISTRATOR.DS.'modules'.DS.$extensionName))
			$dir = JPATH_ADMINISTRATOR.DS.'modules'.DS.$extensionName;
		else if (JFolder::exists(JPATH_SITE.DS.'templates'.DS.$extensionName))
			$dir = JPATH_SITE.DS.'templates'.DS.$extensionName;
		else if (JFolder::exists(JPATH_ADMINISTRATOR.DS.'templates'.DS.$extensionName))
			$dir = JPATH_ADMINISTRATOR.DS.'templates'.DS.$extensionName;
		else
			return $version;

			
		$xmlFiles = JFolder::files($dir, '.xml$');

		if (count($xmlFiles) > 0)
			foreach ($xmlFiles as $xmlFile)
			{
				$xmlParser = new JSimpleXML;
				if ($xmlParser->loadFile($dir.DS.$xmlFile))
					if (is_object($xmlParser->document))
						if ($xmlParser->document->name() == 'install')
						{
							$versionElement =& $xmlParser->document->version[0];
							if ($versionElement != null)
								$version = $versionElement->_data;
						}
			}

		return $version;
	}

}

class JElementCMSMarketConnect extends JElement
{
	function fetchElement($name, $value, &$node, $control_name)
	{
		include_once(dirname(__FILE__).DS.'cmsmarketconnect.php');
		$CMSMarket = new CMSMarketConnect();
		ob_start();
		$CMSMarket->display($name);
		$output = ob_get_clean();
		return $output;
	}
}

?>
