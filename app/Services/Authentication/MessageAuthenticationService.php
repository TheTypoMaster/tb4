<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 5/08/2015
 * Time: 9:37 AM
 */

namespace TopBetta\Services\Authentication;


use Illuminate\Contracts\Hashing\Hasher;

class MessageAuthenticationService {

    /**
     * @var Hasher
     */
    private $hasher;

    public function __construct(Hasher $hasher)
    {
        $this->hasher = $hasher;
    }

    public function createMessageToBeHashed($input, $secret)
    {
        $message = implode('', $input);

        return $message . $secret;
    }

    public function authenticateMessage($input, $secret, $tokenField = 'token')
    {
        if (! array_get($input, $tokenField)) {
            return false;
        }

        $message = $this->createMessageToBeHashed(array_except($input, $tokenField), $secret);

        return $this->hasher->check($message, $input[$tokenField]);
    }

    public function createHashedMessage($input, $secret)
    {
        return $this->hasher->make(
            $this->createMessageToBeHashed($input, $secret)
        );
    }
}