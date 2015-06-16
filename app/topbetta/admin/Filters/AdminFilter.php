<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 1/06/2015
 * Time: 3:34 PM
 */

namespace TopBetta\admin\Filters;

use App;
use Auth;
use Sentry;
use Redirect;
use Config;

class AdminFilter {

    const ROUTE_PREFIX = 'admin';

    public function filter($route, $request, $value = null)
    {
        $name = $route->getName();

        $matches = array();

        //get the route
        if( preg_match('/^' . self::ROUTE_PREFIX . '\.(.*)\.(index|show|create|edit|update|store|destroy)$/', $name, $matches) ) {
            $resource = $matches[1];
            $action = $matches[2];

            //check each action
            switch($action) {
                case 'index':
                case 'show':
                    $permission = $resource . '.view';
                    break;
                case 'create':
                case 'store':
                    $permission = $resource . '.create';
                    break;
                case 'edit':
                case 'update':
                    $permission = $resource . '.edit';
                    break;
                case 'destroy':
                    $permission = $resource . '.delete';
                    break;
                default:
                    throw new \Exception("Unknown Route");
            }
        } else {
            //no named route, so check custom routes for uri
            $uri = $route->getUri();

            //get the permission
            $permission = array_where(Config::get('adminresources.custom_routes'), function($k, $v) use ($uri) {
                return preg_match('/' . str_replace('/', '\/', $v['uri']) . '/', AdminFilter::ROUTE_PREFIX . '/' . $uri);
            });

            if ( empty($permission) ) {
                throw new \Exception("Unknown Route");
            }
            //permission name
            $permission = array_values($permission)[0]['permission'];
        }

        if( ! Auth::user() ) {
            return Redirect::guest('/admin/login');
        }

        if ( ! Sentry::findUserById(Auth::user()->id)->hasAccess(self::ROUTE_PREFIX . '.' . $permission) ) {
            App::abort(403, "Unauthorized action");
        }
    }
}