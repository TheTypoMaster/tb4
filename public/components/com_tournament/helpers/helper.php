<?php
/**
 * Joomla! 1.5 component payment
 *
 * @version $Id: helper.php 2010-08-08 23:27:25 svn $
 * @author Fei Sun
 * @package Joomla
 * @subpackage payment
 * @license GNU/GPL
 *
 * This is payment component
 *
 * This component file was created using the Joomla Component Creator by Not Web Design
 * http://www.notwebdesign.com/joomla_component_creator/
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * payment Helper
 *
 */
class TournamentHelper
{
  /**
  * Method to replace variables
  *
  * @param array an associate array of replacement values
  * @param string the text which contains replacement variables
  * @return string the text after replacement
  */
  function callProcessingServer( $params )
  {
    //get config
    $config = self::getConfig();
    $host = $config['topbetta-processing']['host'];
    $api_key = $config['topbetta-processing']['api_key'];

    //talk to processing server
    $result = self::connectProcessingServer( $host, $api_key, $params );
    
    if( !is_object($result) || $result->status != 'accepted' )
    {
      //talk to fallback server
      $host = $config['topbetta-processing-fallback']['host'];
      $api_key = $config['topbetta-processing-fallback']['api_key'];
      $result = self::connectProcessingServer( $host, $api_key, $params, true );
    }

    return $result;
  }

  function connectProcessingServer( $host, $api_key, $params, $is_fallback=false )
  {

    $options = array(
      CURLOPT_RETURNTRANSFER => true,     // return web page
      CURLOPT_HEADER         => false,    // don't return headers
      CURLOPT_FOLLOWLOCATION => true,     // follow redirects
      CURLOPT_ENCODING       => "",       // handle all encodings
      CURLOPT_USERAGENT      => "spider", // who am i
      CURLOPT_AUTOREFERER    => true,     // set referer on redirect
      CURLOPT_CONNECTTIMEOUT => 1,      // timeout on connect
      CURLOPT_TIMEOUT        => 1,      // timeout on response
      CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
    );
    
    //increase fallback timeout value to 3
    if( $is_fallback )
    {
    	$options[CURLOPT_CONNECTTIMEOUT] = 3;
    	$options[CURLOPT_TIMEOUT] = 3;
    }

    $params['api'] = $api_key;
    $encodedQuery = array();
    foreach( $params as $name => $value )
    {
    	$encodedQuery[] = (urlencode($name) . '=' . urlencode($value));
    }
    $encodedQuery = join( '&', $encodedQuery );

    $ch = curl_init();
    curl_setopt ($ch, CURLOPT_POST, 1);
    curl_setopt_array( $ch, $options );
    curl_setopt($ch, CURLOPT_URL, $host);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedQuery);
    $postReturn = curl_exec ($ch);
    $postReturn = json_decode($postReturn);
    curl_close($ch);

    if( $postReturn )
    {
      return ($postReturn->ServiceClass->confirm_acceptance);
    }
    return($postReturn);
  }

  function getConfig()
  {
  	$xmlFileName = '/mnt/web/server.xml';
    $xmlHandler= @fopen($xmlFileName,"r");
    
    $content= fread($xmlHandler,filesize($xmlFileName));
    fclose($xmlHandler);

    $xml = new SimpleXMLElement($content);
    $dbconnection = array();

    foreach( $xml->children() as $name => $node )
    {
      $nodeDatabase = $node->service;
      if( $nodeDatabase )
      {
        $attributes = array();
        for( $i = 0; $i< count($nodeDatabase); $i++)
        {
          foreach( $nodeDatabase[$i]->attributes() as $k => $v )
          {
            $attributes[$k] = (string)$v;
          }
          $dbconnection[$attributes['name']] = array(
            'host' => (string)$nodeDatabase[$i]->host,
            'api_key' => (string)$nodeDatabase[$i]->api_key,
          );
        }

      }
    }
    return $dbconnection;
  }
  

  /**
   * Get module content
   *
   * @param string $module
   * @return void
   */
  function getModule($module)
  {
    //import right modules
    jimport('joomla.application.module.helper');
	$modules = JModuleHelper::getModules($module);
	$content = '';
	foreach($modules as $module)
	{
		$content .= JModuleHelper::renderModule($module);
	}
	
	return $content;
  }
}
?>