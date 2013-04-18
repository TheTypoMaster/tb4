<?php

/**
 * Return the directory separator used by the current OS
 *
 * @return string
 */
function getDirectorySeparator() {
  $slash = '/';

  if(stristr(PHP_OS, 'WIN')) {
    $slash = '\\';
  }

  return $slash;
}

/**
 * Get a SimpleXML object for the server config file
 *
 * @param string $path
 * @return SimpleXML
 */
function getServerXML($path = null) {
  static $xml = null;

  if(is_null($xml)) {
    if(is_null($path)) {
      $sl = getDirectorySeparator();

      $path   = ($sl == '\\') ? 'C:' : '';
      $path  .= $sl . 'mnt' . $sl . 'web' . $sl . 'server.xml';
    }

    $xml = simplexml_load_file($path);
  }

  return $xml;
}

/**
 * Get a specific section of the server config file
 *
 * @param string $sectionName
 * @return mixed
 */
function getConfigSection($sectionName) {
  static $sectionList = array();

  if(!isset($sectionList[$sectionName])) {
    $xml = getServerXML();
    $section = $xml->xpath("/setting/section[@name='{$sectionName}']");

    if(empty($section)) {
      trigger_error("The section '{$sectionName}' was not found in the server config.", E_USER_ERROR);
    }

    if(count($section) > 1) {
      trigger_error("Server config contains multiple nodes for section '{$sectionName}'. Using first node.", E_USER_WARNING);
    }

    $sectionList[$sectionName] = $section[0];
  }

  return $sectionList[$sectionName];
}