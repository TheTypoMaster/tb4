<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 19/06/2015
 * Time: 12:41 PM
 */

namespace TopBetta\admin\Composers;

use Auth;
use Config;
use Sentry;
use Route;
use TopBetta\admin\Filters\AdminFilter;

class NavigationComposer {

    private $user;
    /**
     * @var
     */
    private $adminFilter;

    public function __construct(AdminFilter $adminFilter)
    {
        $this->adminFilter = $adminFilter;
    }

    /**
     * View composer function
     * @param $view
     * @return mixed
     */
    public function compose($view)
    {
        $navItems = Config::get('adminresources.navigation', array());

        return $view->with(array("navItems" => $this->filterItems($navItems)));
    }

    /**
     * Filter the menu items
     * @param $menuItems
     * @return array
     */
    public function filterItems($menuItems)
    {
        if( ! Auth::check() ) {
            return array();
        }

        $this->user = Sentry::findUserById(Auth::user()->id);

        return $this->_filterItems($menuItems);
    }

    /**
     * Recursively filter navigation links based on user permissions
     * @param $menuItems
     * @return array
     * @throws \Exception
     */
    private function _filterItems($menuItems)
    {
        $menu = array();

        foreach($menuItems as $item) {
            //has children so is a parent menu item
            if( $children = array_get($item, 'children', null) ) {
                $childItems = $this->_filterItems($children);

                if( count($childItems) ) {

                    //check the route to see if we need to make the parent active
                    if(in_array(Route::current()->getName(), array_fetch($childItems, 'route')) ||
                        in_array(Route::current()->getUri(), array_fetch($childItems, 'url'))) {
                        $item['active'] = true;
                    }

                    $item['children'] = $childItems;
                    $menu[] = $item;
                }
            } else {
                //check user has permissions to this route/url
                if( ($route = array_get($item, 'route', null)) &&  $this->user->hasAccess($this->adminFilter->getPermissionForRouteName($route)) ) {
                    $item['link'] = route($route);
                    $menu[] = $item;
                } else if ( ($url = array_get($item, 'url', null)) && $this->user->hasAccess($this->adminFilter->getPermissionForUri($url)) ) {
                    $item['link'] = url($url);
                    $menu[] = $item;
                }
            }
        }

        return $menu;
    }
}