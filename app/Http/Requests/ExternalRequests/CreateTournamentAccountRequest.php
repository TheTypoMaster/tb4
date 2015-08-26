<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 5/08/2015
 * Time: 11:15 AM
 */

namespace TopBetta\Http\Requests\ExternalRequests;

class CreateTournamentAccountRequest extends AuthorizedExternalRequest {

    public function rules()
    {
        return array(
            "tournament_username" => "required",
            "external_unique_identifier" => "required",
            "source_name" => "required"
        );
    }
}