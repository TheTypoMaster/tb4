<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 18/06/2015
 * Time: 11:26 AM
 */

namespace TopBetta\Services\Tournaments;

use Lang;
use TopBetta\Repositories\Contracts\ConfigurationRepositoryInterface;
use TopBetta\Repositories\Contracts\TournamentRepositoryInterface;

class TournamentBuyInRulesService {

    const CONFIG_NAME = 'tournament_buyin_settings';

    public static $freeTournamentPeriods = array(
        'day' => 'Day',
        'week' => 'Week',
        'month' => 'Month',
    );

    private $messages = array();

    /**
     * @var ConfigurationRepositoryInterface
     */
    private $configurationRepository;
    /**
     * @var TournamentTicketService
     */
    private $tournamentTicketService;
    /**
     * @var TournamentRepositoryInterface
     */
    private $tournamentRepository;

    public function __construct(ConfigurationRepositoryInterface $configurationRepository, TournamentTicketService $tournamentTicketService, TournamentRepositoryInterface $tournamentRepository)
    {
        $this->configurationRepository = $configurationRepository;
        $this->tournamentTicketService = $tournamentTicketService;
        $this->tournamentRepository = $tournamentRepository;
    }

    public function canBuyin($tournament, $user)
    {
        if( is_int($tournament) ) {
            $tournament = $this->tournamentRepository->find($tournament);
        }

        $config = $this->configurationRepository->getConfigByName(self::CONFIG_NAME, true);

        foreach( $config as $rule => $data ) {
            if( ! $this->checkBuyinRule($tournament, $user, $rule, $data) ) {
                return false;
            }
        }

        return true;
    }

    public function checkBuyinRule($tournament, $user, $rule, $data)
    {
        switch($rule)
        {
            //check for max free tournament entries
            case 'max_free_tournament':
                if ( $tournament->buy_in == 0 &&
                    array_get($data, 'number', 0) &&
                    $this->tournamentTicketService->getFreeBuyinsForPeriod($user, array_get($data, 'period', 'month'), $tournament->start_date)->count() >= $data['number']
                ) {
                    $this->messages[] = Lang::get('tournaments.exceed_free_tournament_tickets', array(
                        'period' => array_get($data, 'period', 'month'),
                        'number' => $data['number']
                    ));
                    return false;
                }
        }

        return true;
    }

    public function getFirstMessage()
    {
        return array_get($this->messages, 0, '');
    }
}