<?php

/**
 * Load the SOAP config settings
 *
 * @return array
 */
function loadSoapConfig() {
  static $config = array();

  if(empty($config[$connection])) {
    $xml = getConfigSection('soap');

    $configParams = $xml->soap;
    if(is_object($configParams)) {
      $configParams = array($configParams);
    }

    foreach($xml as $section) {
      $name = (string)$section->attributes()->name;
      $config[$name] = array(
        'user'      => (string)$section->user,
        'password'  => (string)$section->password,
        'host'      => (string)$section->host
      );
    }
  }

  return $config;
}

/**
 * Get settings for a particular SOAP service
 *
 * @param string $connection
 * @return array
 */
function getSoapConfig($connection) {
  $config = loadSoapConfig();

  if(empty($config[$connection])) {
    trigger_error("No config found for connection '{$connection}'.", E_USER_ERROR);
  }

  return $config[$connection];
}