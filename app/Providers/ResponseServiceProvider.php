<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 29/06/2015
 * Time: 9:29 AM
 */

namespace TopBetta\Providers;

use Response;
use Illuminate\Contracts\Support\Arrayable;
use TopBetta\Helpers\LibSimpleXMLElement;
use Illuminate\Support\ServiceProvider;

class ResponseServiceProvider extends ServiceProvider {

    public $defer = false;


    public function boot()
    {
        Response::macro('xml', function($vars, $status = 200, array $header = array(), $rootElement = 'response', $xml = null)
        {
            if (is_object($vars) && $vars instanceof Arrayable) {
                $vars = $vars->toArray();
            }

            if (is_null($xml)) {
                $xml = new LibSimpleXMLElement('<' . $rootElement . '/>');
            }
            foreach ($vars as $key => $value) {
                if (is_array($value)) {
                    if (is_numeric($key)) {
                        Response::xml($value, $status, $header, $rootElement, $xml->addChild(str_singular($xml->getName())));
                    } else {
                        Response::xml($value, $status, $header, $rootElement, $xml->addChild($key));
                    }
                } else {
                    $xml->addChild($key, $value);
                }
            }
            if (empty($header)) {
                $header['Content-Type'] = 'application/xml';
            }
            return Response::make($xml->asXML(), $status, $header);
        });
    }

    public function register(){}
}