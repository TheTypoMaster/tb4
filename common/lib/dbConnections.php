<?php

/**
 * Load the database settings
 *
 * @return array
 */
function loadDBConfig() {
  static $dbNameArray = array();

  if(empty($dbNameArray)) {
    $section = getConfigSection('database');
    foreach($section->database as $db_section) {
      // Get DB connection data from XML
      $dbSectionName = (string)$db_section->attributes()->name;

      $dbHost       = (string)$db_section->host;
      $dbUser       = (string)$db_section->user;
      $dbPassword   = (string)$db_section->password;
      $dbName       = (string)$db_section->name;

      $dbNameArray[$dbSectionName] = array(
        'name'      => $dbName,
        'host'      => $dbHost,
        'user'      => $dbUser,
        'password'  => $dbPassword
      );
    }
  }

  return $dbNameArray;
}

/**
 * Connect to a database, change the current connection or close the connection
 *
 * @param string $dbname
 * @param string $state
 * @return mixed
 */
function dbConnectionFactory($dbname, $state){
  static $dbc;

  $dbConArray = loadDBConfig();

  // Get connection details for required DB
  $dbConnDets = $dbConArray[$dbname];

  $host		  =	$dbConnDets['host'];
  $user		  =	$dbConnDets['user'];
  $pass		  = $dbConnDets['password'];
  $database = $dbConnDets['name'];

  if ($state == "connect"){
    $dbc = mysql_connect ($host, $user, $pass) OR die ('Could not connect to MySQL.');
    mysql_select_db ($database) OR die ('Could not select the database: ' . $dbname);
  } else if ($state == "change"){
    return mysql_select_db ($database, $dbc) OR die ('Could not select the database: ' . $dbname);
  } else {
    return mysql_close($dbc) OR die ('Could not disconnect database: ' . $dbname);
  }

  return $dbc;
}

/**
 * Insert or update record based on finding matching record using specified criteria
 *
 * @param string $table
 * @param object $data
 * @return integer
 */
function insertOrUpdate($table, $data){

  $id = 0;

  /* SELECT to check for existing record */
  $select = 'SELECT id FROM %s WHERE %s';

  switch($table){
    case 'meeting':
    case 'racing_meeting':
      $where = " name = '{$data->name}' AND date = '{$data->date}'";
      break;
    case 'race':
    case 'racing_race':
      $where = " meeting_id = '{$data->meeting_id}' AND number = '{$data->number}'";
      break;
    case 'runner':
    case 'racing_runner':
      $where = " race_id = '{$data->race_id}' AND number = '{$data->number}'";
      break;
    default:
      die('Incorrect table name');
  }

  $query = sprintf($select, $table, $where);

  $result = mysql_query($query);

  $out = mysql_fetch_object($result);
  if($out){
    $id = $out->id;
  }

  /* If existing record UPDATE */
  if($id){

    $update = 'UPDATE %s SET %s WHERE id=%d';

    $values = array();
    foreach($data as $field => $value){
      if($field!='id'){
        $value = mysql_real_escape_string(trim($value));
        $values[] = "$field = '$value'";
      }
    }

    $value_string = implode(',',$values);

    $query = sprintf($update, $table, $value_string, $id);

  } else { /* else do an INSERT */

    $insert = 'INSERT INTO %s (%s) VALUES (%s)';

    $values = array();
    $fields = array();

    foreach($data as $field => $value){
      if($field!='id'){
       $fields[] = $field;
       $values[] = $value;
      }
    }

    $values = array_map('quote_value', $values);
    $value_string = implode(',', $values);
    $field_string = implode(',', $fields);

    $query = sprintf($insert, $table, $field_string, $value_string);
  }

  $result = mysql_query($query) or die(mysql_error());
  $id = mysql_insert_id() ? mysql_insert_id() : $id;

  return $id;
}

function quote_value($value){
  $value = mysql_real_escape_string(trim($value));
  return "'$value'";
}
