<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 23/06/2015
 * Time: 4:41 PM
 */

namespace TopBetta\Services\Html;

use Collective\Html\HtmlBuilder as CollectiveHtmlBuilder;


class HtmlBuilder extends CollectiveHtmlBuilder {

    public function tableFilterLink($route, $field, $name, $order = array(), $query = array(), $attributes = array())
    {
        $linkOrder = array($field);
        $linkOrder[] = array_get($order, 0) == $field && array_get($order, 1) == 'ASC' ? 'DESC' : 'ASC';

        $link = link_to_route($route, $name, array_merge($linkOrder, $query), $attributes);

        $icon = "";
        if( array_get($order, 0) == $field ) {
            $icon = '<i class="fa fa-fw ' . ($order[1] == 'ASC' ? 'fa-arrow-up' : 'fa-arrow-down') . '" ></i>';
        }

        return $link . $icon;
    }
}