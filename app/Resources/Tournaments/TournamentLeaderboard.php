<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 30/07/2015
 * Time: 12:11 PM
 */

namespace TopBetta\Resources\Tournaments;


use TopBetta\Resources\EloquentLeaderboardCollection;

class TournamentLeaderboard extends EloquentLeaderboardCollection {

    public function sort()
    {
        $this->collection = $this->collection->sort(function($a, $b){

            if (! $a->qualified() && $b->qualified()) return 1;

            else if ($a->qualified() && ! $b->qualified()) return -1;

            else if (! $a->qualified() && ! $b->qualified()) return 0;

            else if ($a->currency == $b->currency) return 0;

            return $a->currency < $b->currency ? 1 : -1;
        });

        return $this->assignPositions();
    }

    public function assignPositions()
    {
        $position = 1;
        $lastCurrency = $this->collection->first()->currency;
        $index = 1;

        foreach ($this->collection as $record) {
            if( $record->currency != $lastCurrency ) {
                $position = $index;
            }

            if( $record->qualified() ) {
                $record->setPosition($position);
            } else {
                $record->setPosition('-');
            }


            $lastCurrency = $record->currency;

            $index++;
        }

        return $this;
    }

}