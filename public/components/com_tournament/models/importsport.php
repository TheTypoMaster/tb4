<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.model');

class TournamentModelImportSport extends JModel
{
	const XML_ROOT = 'http://tatts.com/pagedata/';
	private $xml_doc = null;

	/**
	 * Method to sort Array by column name
	 * @param array $array
	 * @param String on [the coulumn we want to sort]
	 * @param bool ASC. false = DESC
	 */
	function sortArray($array, $on="id", $order=SORT_ASC)
	{
		$new_array = array();
		$sortable_array = array();

		if (count($array) > 0) {
			foreach ($array as $k => $v) {
				if (is_array($v)) {
					foreach ($v as $k2 => $v2) {
						if ($k2 == $on) {
							$sortable_array[$k] = $v2;
						}
					}
				} else {
					$sortable_array[$k] = $v;
				}
			}

			switch ($order) {
				case SORT_ASC:
					asort($sortable_array);
					break;
				case SORT_DESC:
					arsort($sortable_array);
					break;
			}

			foreach ($sortable_array as $k => $v) {
				$new_array[$k] = $array[$k];
			}
		}

		return $new_array;
	}

	/**
	 * Import Sport List from feed
	 * Return Array
	 */
	public function getImportSportList(){
		$file_path 		= self::XML_ROOT . "sports/sports.xml";
		$simple_xml 	= simplexml_load_file($file_path);

		if ($simple_xml) { //--load only if there is any contents from the xml

			$sports = $simple_xml->xpath('//Sport');
			/**
			 *  Grab the attributes for allthe extrenal sports &
			 *  generate an array to be used for the dropdown
			 */
			foreach ($sports as $sport){
				$ext_sport[] = array(
					"ext_sport_id" 		=> (int)$sport->attributes()->SportId,
                    "ext_sport_name" 	=> (string)$sport->attributes()->SportName
				);
			}

			/**
			 * Sorting the array in ASC on Sport name
			 */
			$ext_sport = $this->sortArray($ext_sport, "ext_sport_name");
			return $ext_sport;
		}

	}
	/**
	 * Function to get individual sport from the feed by Id
	 * @param $id to check
	 * Return Array
	 */
	public function getImportSportByExternalId($id=""){
		if ($id) {
			$file_path 		= self::XML_ROOT . "sports/sports.xml";
			$simple_xml		= null;
			
			if (TournamentHelper::remoteFileExists($file_path)) {
				$simple_xml = simplexml_load_file($file_path);
			}

			if ($simple_xml) { //--load only if there is any contents from the xml

				$sports = $simple_xml->xpath("//Sport[@SportId=$id]");

				foreach ($sports as $sport) {
					$ext_sport = array(
						"ext_sport_id" 		=> (int)$sport->attributes()->SportId,
	                    "ext_sport_name" 	=> (string)$sport->attributes()->SportName
					);

					return $ext_sport;
				}
			}// if ($simple_xml) ends
		}//if ($id) ends
	}

}