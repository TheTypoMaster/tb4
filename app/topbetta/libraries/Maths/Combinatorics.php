<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 15/05/2015
 * Time: 2:19 PM
 */
namespace TopBetta\libraries\Maths;

class Combinatorics {

    public static function factorial($n)
    {
        if($n < 2 ) {
            return 1;
        }

        $result = 1;

        for($i = 2; $i <= $n; $i++){
            $result *= $i;
        }

        return $result;
    }

    public static function combinations($n, $r)
    {
        return self::factorial($n)/(self::factorial($r)*self::factorial($n-$r));
    }

    public static function permutations($n, $r)
    {
        return self::factorial($n)/self::factorial($n-$r);
    }
}