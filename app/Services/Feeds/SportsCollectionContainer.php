<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 10/09/2015
 * Time: 3:12 PM
 */

namespace TopBetta\Services\Feeds;


class SportsCollectionContainer {

    protected $sports = array();

    protected $regions = array();

    protected $baseCompetitions = array();

    protected $competitions = array();

    protected $events = array();

    protected $markets = array();

    protected $marketTypes = array();

    protected $teams = array();

    protected $players = array();

    /**
     * @return array
     */
    public function getSports()
    {
        return $this->sports;
    }

    /**
     * @param array $sports
     * @return $this
     */
    public function setSports($sports)
    {
        $this->sports = $sports;
        return $this;
    }

    public function addSport($sport, $key)
    {
        $this->sports[$key] = $sport;
        return $this;
    }
    
    public function getSport($id)
    {
        return array_get($this->sports, $id);
    }

    /**
     * @return array
     */
    public function getRegions()
    {
        return $this->regions;
    }

    /**
     * @param array $regions
     * @return $this
     */
    public function setRegions($regions)
    {
        $this->regions = $regions;
        return $this;
    }

    public function addRegion($region, $key)
    {
        $this->regions[$key] = $region;
        return $this;
    }

    public function getRegion($id)
    {
        return array_get($this->regions, $id);
    }

    /**
     * @return array
     */
    public function getBaseCompetitios()
    {
        return $this->baseCompetitions;
    }

    /**
     * @param array $baseCompetitios
     * @return $this
     */
    public function setBaseCompetitios($baseCompetitios)
    {
        $this->baseCompetitions = $baseCompetitios;
        return $this;
    }

    public function addBaseCompetition($baseCompetition, $key)
    {
        $this->baseCompetitions[$key] = $baseCompetition;
        return $this;
    }

    public function getBaseCompetition($id)
    {
        return array_get($this->baseCompetitions, $id);
    }

    /**
     * @return array
     */
    public function getCompetitions()
    {
        return $this->competitions;
    }

    /**
     * @param array $competitions
     * @return $this
     */
    public function setCompetitions($competitions)
    {
        $this->competitions = $competitions;
        return $this;
    }

    public function addCompetition($competition, $key)
    {
        $this->competitions[$key] = $competition;
        return $this;
    }

    public function getCompetition($id)
    {
        return array_get($this->competitions, $id);
    }

    /**
     * @return array
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * @param array $events
     * @return $this
     */
    public function setEvents($events)
    {
        $this->events = $events;
        return $this;
    }

    public function addEvent($event, $key)
    {
        $this->events[$key] = $event;
        return $this;
    }

    public function getEvent($id)
    {
        return array_get($this->events, $id);
    }

    /**
     * @return array
     */
    public function getMarkets()
    {
        return $this->markets;
    }

    /**
     * @param array $markets
     * @return $this
     */
    public function setMarkets($markets)
    {
        $this->markets = $markets;
        return $this;
    }

    public function addMarket($market, $key)
    {
        $this->markets[$key] = $market;
        return $this;
    }

    public function getMarket($id)
    {
        return array_get($this->markets, $id);
    }

    /**
     * @return array
     */
    public function getTeams()
    {
        return $this->teams;
    }

    /**
     * @param array $teams
     * @return $this
     */
    public function setTeams($teams)
    {
        $this->teams = $teams;
        return $this;
    }

    public function addTeam($team, $key)
    {
        $this->teams[$key] = $team;
        return $this;
    }

    public function getTeam($id)
    {
        return array_get($this->teams, $id);
    }

    /**
     * @return array
     */
    public function getPlayers()
    {
        return $this->players;
    }

    /**
     * @param array $players
     * @return $this
     */
    public function setPlayers($players)
    {
        $this->players = $players;
        return $this;
    }

    public function addPlayer($player, $key)
    {
        $this->players[$key] = $player;
        return $this;
    }

    public function getPlayer($id)
    {
        return array_get($this->players, $id);
    }

    /**
     * @return array
     */
    public function getMarketTypes()
    {
        return $this->marketTypes;
    }

    /**
     * @param array $marketTypes
     * @return $this
     */
    public function setMarketTypes($marketTypes)
    {
        $this->marketTypes = $marketTypes;
        return $this;
    }

    public function addMarketType($marketType, $key)
    {
        $this->marketTypes[$key] = $marketType;
        return $this;
    }

    public function getMarketType($id)
    {
        return array_get($this->marketTypes, $id);
    }
}