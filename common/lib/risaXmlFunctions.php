<?php

/**
 * Risa XML Exception
 * @author geoff
 *
 */
class RisaXmlException extends Exception
{

}

/**
 * Risa XML Processor
 * @author geoff
 *
 */
class RisaXmlProcessor{

  /**
   * private data object
   * @access private
   * @var object
   */
  private $data;
  /**
   * @access private
   * @var string
   */
  private $location = '';

  /**
   * @access private
   * @var string
   */
  private $meet_type = '';

  /**
   * Getter method
   * @param string $name
   * @return mixed
   */
  public function __get($name){

    switch($name){
      case 'meeting':
        break;
      case 'races':
        if(is_null($this->data->meeting->id)){
          throw new RisaXmlException('Make sure meeting id is set for race data');
        }
        break;
      case 'entries':
        foreach($this->data->races as $race){
          if(is_null($race->id)){
            throw new RisaXmlException('Make sure race id is set for race entry data');
          }
        }
        break;
      default:
        throw new RisaXmlException('Cannot access property via __get()');
        return null;
    }

    return $this->data->{$name};
  }
  /**
   *
   * @access public
   * @param string $xml_string
   * @return void
   */
  public function processXmlData($xml_string){

    $xml = simplexml_load_string($xml_string);

    /* Type dictionary */
    $type_lookup = array('GALLOPS' => 'Galloping');

    /* common variables */
    $this->location = strtoupper($xml->Venue->attributes()->VenueName).' ('.$xml->StateDesc.')';
    $this->meet_type =  $type_lookup[(string) $xml->CodeType];
    $this->date = strtotime((string) $xml->MeetDate );

    /* xml to data */
    $this->data->meeting = $this->extractMeetingData($xml);
    $this->data->races    = $this->extractRaceData($xml);
    $this->data->entries = $this->extractEntryData($xml);
  }

  /**
   *
   * @access private
   * @param object $xml
   * @return object
   */
  private function extractMeetingData($xml){

    $meeting = new stdClass;

    $meeting->id = null;
    $meeting->name = $this->location;
    $meeting->events = (int) $xml->NumOfRaces;
    $meeting->type = $this->meet_type;
    $meeting->weather = (string) $xml->Weather;
    $meeting->date = $this->date;
    $meeting->track = '';

    return $meeting;
  }

   /**
   *
   * @access private
   * @param object $xml
   * @return array
   */
  private function extractRaceData($xml){

    $race_data = array();

    foreach($xml->Races->Race as $race){

      $race_num = (int) $race->RaceNumber;

      $race_data[$race_num] = new StdClass;
      $current_race =& $race_data[$race_num];

      $current_race->id = null;
      $current_race->meeting_id =& $this->data->meeting->id;

      $current_race->location = $this->location;
      $current_race->type = $this->meet_type;
      $current_race->number = (int) $race->RaceNumber;
      $current_race->name = (string) $race->NameRaceFull;

      /* date conversions */
      $date = new DateTime((string) $race->UtcTime, new DateTimeZone('UTC'));
      $date->setTimeZone(new DateTimeZone(date_default_timezone_get()));

      $current_race->time = $date->format('H:i');
      $current_race->date = strtotime($date->format('d-m-Y'));
      $current_race->dump_timestamp = time();
      $current_race->start_unixtimestamp = $date->format('U');
      $current_race->start_datetime = $date->format('Y-m-d H:i:s');

      $current_race->distance = (int) $race->RaceDistance.'M';
      $current_race->class = (string) $race->EntryConditions->EntryCondition->attributes()->Id;
      $current_race->status = '';

    }

    return $race_data;
  }

   /**
   *
   * @access private
   * @param object $xml
   * @return array
   */
  private function extractEntryData($xml){

    $horse_data = array();

    foreach($xml->Races->Race as $race){

      $race_num = (int) $race->RaceNumber;

      foreach($race->RaceEntries->RaceEntry as $entry){

        $entry_num = (int) $entry->TabNumber;
        $entry_name = (string) $entry->Horse->attributes()->HorseName;

        $horse_data[$race_num][$entry_num] = new stdClass;

        $current_entry = &$horse_data[$race_num][$entry_num];
        $current_entry->race_id = &$this->data->races[$race_num]->id;
        $current_entry->number = $entry_num;
        $current_entry->name = strtoupper($entry_name);
        $current_entry->associate = strtoupper((string) $entry->JockeyRaceEntry->Name);
        $current_entry->status = '';
        $current_entry->barrier = (int) $entry->BarrierNumber;
        $current_entry->handicap = (string) $entry->HandicapWeight;
        $current_entry->ident = $this->createIdentFromName($entry_name);
        $current_entry->date = $date;

      }
    }

    return $horse_data;
  }

  /**
   * Create IDENT from name by shifting all characters to lowercase and stripping all other characters and whitespace
   *
   * @param string $name
   * @return string
   */
  private function createIdentFromName($name){

    $ident = '';
    $name = trim(strtolower($name));

    for($i=0; $i<strlen($name); $i++){

      /* only allow lowercase characters */
      $ident .= (ord($name{$i}) >= 97 && ord($name{$i}) <= 122) ? $name{$i} : '';
    }

    return $ident;
  }

}