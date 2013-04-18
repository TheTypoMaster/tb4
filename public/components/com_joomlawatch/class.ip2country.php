<?php

//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
// CLASS: ip2country - Find country from netblocks
//
// Copyright (C) 1987-2004 Pascal Toussaint <pascal@pascalz.com>
//
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or any later
// version.
//
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
// or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for
// more details.
//
// You should have received a copy of the GNU General Public License along
// with this program; if not, write to the Free Software Foundation, Inc.,
// 59 Temple Place, Suite 330, Boston, MA 02111-1307, USA.
//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
// This script uses the IP-to-Country Database
// provided by WebHosting.Info (http://www.webhosting.info),
// available from http://ip-to-country.webhosting.info.
//
// Download latest database at
// http://ip-to-country.directi.com/downloads/ip-to-country.csv.zip
//
// Look the latest file date (format YYYYMMDD)
// http://ip-to-country.directi.com/downloads/latest
//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
// $Id: class.ip2country.php,v 1.4 2004/02/01 07:24:54 pascalz Exp $
//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-

/** ensure this file is being included by a parent file */
if( !defined( '_JEXEC' ) && !defined( '_VALID_MOS' ) ) die( 'Restricted access' );


// Unquote a string...if quoted
function unquote(& $str) {
	$str = trim($str);

	if (($str[0] == '"') && ($str[strlen($str) - 1] == '"')) {
		$str = substr($str, 1, strlen($str) - 2);
	}
}

// Recreate inet_aton function like in mySQL
// convert Internet dot address to network address
function inet_aton($ip) {
	$ip_array = explode(".", $ip);
	return ($ip_array[0] * pow(256, 3)) + ($ip_array[1] * pow(256, 2)) + ($ip_array[2] * 256) + $ip_array[3];
}

class ip2country {
	var $CVSFile; // the ip-to-country.csv file
	var $IP; // IP to looking for

	var $Prefix1; // Country prefix (2char) ex.: US
	var $Prefix2; // Country prefix (3char) ex.: USA
	var $Country; // Country name  ex.: UNITED STATE

	var $UseDB; // Use database instead csv file (more fast)

	// db values
	var $db_host; // host information for database connection
	var $db_login; // login information for database connection
	var $db_password; // password information for database connection
	var $db_basename; // base information for database connection
	var $db_tablename; // Your own table name
	var $db_ip_from_colname; // Your own ip_from column name
	var $db_ip_to_colname; // Your own ip_to column name
	var $db_prefix1_colname; // Your own prefix1 column name
	var $db_prefix2_colname; // Your own prefix2 column name
	var $db_country_colname; // Your own country column name

	var $_IPn; // Private - network address

	var $database;

	// Constructor
	function ip2country($ip, $usedb = false) {
		global $database;
		if (JOOMLAWATCH_JOOMLA_15) {
			$this->database = & JFactory :: getDBO();
		} else {
			$this->database = $database;
		}

		// TODO: Add regex to verify ip is valid
		if ($ip) {
			$this->_IPn = inet_aton($ip);
			$this->IP = $ip;
		}

		$this->CVSFile = dirname(__FILE__) . "\ip-to-country.csv";
		$this->UseDB = $usedb;

		// D�fault value
		$this->db_host = "dbweb";
		$this->db_tablename = "ip2c";
		$this->db_ip_from_colname = "start";
		$this->db_ip_to_colname = "end";
		$this->db_prefix1_colname = "cc";
		$this->db_prefix2_colname = "a3";
		$this->db_country_colname = "country";
	}

	// Look in file or database
	function LookUp() {
		if ($this->UseDB) {
			/*
			* The Fastest Way is to import the CSV file in your database and to
			* set UseDB to true !
			* I use MySQL but feel free to use your database functions ;)
			*/

			$query = "SELECT " . $this->db_country_colname . " FROM " . $this->db_tablename . " WHERE " . $this->_IPn . ">=" . $this->db_ip_from_colname . " AND " . $this->_IPn . "<=" . $this->db_ip_to_colname;
			$this->database->setQuery($query);
			$rows = $this->database->loadObjectList();

			$row = @ $rows[0];
			if ($row) {
				$country_colname = $this->db_country_colname;
				$this->Country = $row-> $country_colname;

				return true;
			} else
			return false;

			//			mysql_close($conn);

		}
	}
}

//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
?>