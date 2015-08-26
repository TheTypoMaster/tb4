<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 5/08/2015
 * Time: 2:48 PM
 */

namespace TopBetta\Http\Requests\ExternalRequests;


class UserTokenRequest extends AuthorizedExternalRequest {

    public function rules()
    {
        return array(
            "tournament_username" => "required",
            "source_name" => "required"
        );
    }
}