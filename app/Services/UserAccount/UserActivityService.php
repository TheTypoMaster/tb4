<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 21/05/2015
 * Time: 4:48 PM
 */

namespace TopBetta\Services\UserAccount;


class UserActivityService {

    public function userTransactionHistory($user)
    {
        $data = $this->transactionData($user);

        return array_merge($data, $this->freeCreditData($user));
    }

    public function transactionData($user)
    {
        $transactionData = array();
        foreach($user->user->accountTransactions as $transaction) {
            //transaction info
            $data = array(
                $user->first_name,
                $user->last_name,
                $transaction->amount / 100,
                $transaction->notes,
                $transaction->transactionType->name,
            );

            //is there a bet record
            $bet = null;
            if($transaction->transactionType->keyword == 'betentry') {
                $bet = $transaction->bet;
            } else if ($transaction->transactionType->keyword == 'betwin') {
                $bet = $transaction->betResult;
            }

            //bet record so add bet data
            if( $bet ) {
                $selection = $bet->selection->first();

                if( $selection ) {
                    if ($bet->bet_type_id <= 3) {
                        $data[] = $selection->name;
                    } else {
                        $data[] = $bet->selection_string;
                    }

                    $data[] = $selection->market->name;
                    $data[] = $selection->market->event->name;
                    $data[] = $selection->market->event->competition->first()->name;

                    if ($selection->market->event->competition->first()->sport) {
                        $data[] = $selection->market->event->competition->first()->sport->name;
                    }
                }
            }

            $transactionData[] = $data;
        }

        return $transactionData;
    }

    public function freeCreditData($user)
    {
        $transactionData = array();

        foreach($user->user->freeCreditTransactions as $transaction) {
            //transaction data
            $data = array(
                $user->first_name,
                $user->last_name,
                $transaction->amount / 100 . '(FREE CREDIT)',
                $transaction->notes,
                $transaction->transactionType->name,
            );

            //get tournament/bet
            $bet = null;
            $tournament = null;
            if($transaction->transactionType->keyword == 'freebetentry') {
                $bet = $transaction->bet;
            } else if ($transaction->transactionType->keyword == 'entry') {
                $tournament = $transaction->tournamentEntry;

            } else if ($transaction->transactionType->keyword == 'buyin') {
                $tournament = $transaction->tournamentBuyin;
            }


            //bet record exists so get bet data
            if( $bet ) {
                $selection = $bet->selection->first();

                if( $selection ) {
                    if ($bet->bet_type_id <= 3) {
                        $data[] = $selection->name;
                    } else {
                        $data[] = $bet->selection_string;
                    }

                    $data[] = $selection->market->name;
                    $data[] = $selection->market->event->name;
                    $data[] = $selection->market->event->competition->first()->name;

                    if ($selection->market->event->competition->first()->sport) {
                        $data[] = $selection->market->event->competition->first()->sport->name;
                    }
                }
            }

            //tournament data exists so get tournament data
            if ( $tournament ) {
                $data = array_merge($data, array('', '', ''));

                if ($tournament->tournament->eventGroup) {
                    $data[] = $tournament->tournament->eventGroup->name;

                    if($tournament->tournament->eventGroup->sport) {
                        $data[] = $tournament->tournament->eventGroup->sport->name;
                    }
                }

                $data[] = $tournament->tournament->buy_in;
                $data[] = $tournament->tournament->entry_fee;
            }

            $transactionData[] = $data;
        }

        return $transactionData;
    }
}