<?php namespace TopBetta\Http\Controllers\Admin;

use Illuminate\Support\Facades\App;
use TopBetta\Http\Controllers\Controller;

use TopBetta\Repositories\Contracts\IconTypeRepositoryInterface;
use TopBetta\Repositories\Contracts\MarketTypeRepositoryInterface;

use View;
use Request;
use Input;
use Redirect;

class MarketTypeController extends CrudResourceController {

    protected $repositoryName = 'TopBetta\Repositories\Contracts\MarketTypeRepositoryInterface';

    protected $iconType = IconTypeRepositoryInterface::TYPE_MARKET_TYPE;

    protected $modelName = 'Market Types';

    protected $indexRoute = 'admin.markettypes.index';

    protected $editRoute = 'admin.markettypes.edit';

    protected $createRoute = 'admin.markettypes.create';

    protected $storeRoute  = 'admin.markettypes.store';

    protected $updateRoute = 'admin.markettypes.update';

    protected $deleteRoute = 'admin.markettypes.destroy';

    protected $indexView = 'admin.eventdata.markettypes.index';

    protected $createView = 'admin.eventdata.markettypes.create';

    protected $editView = 'admin.eventdata.markettypes.edit';

    public function index($relations = array(), $extraData = array())
    {

        $relations[] = 'markettypegroup';

        $extraData = array(
            "Market Rules" => array(
                "field" => "market_rules",
                "type" => "text"
            )
        );

        return parent::index($relations, $extraData);
    }

    public function create($extraData = array())
    {
        $market_type_group_service = App::make('TopBetta\Services\Markets\MarketTypeGroupService');
        $market_type_group_list = $market_type_group_service->getMarketTypeGroupList();
        $extraData = array(
            "Market Rules" => array(
                "field" => "market_rules",
                "type" => "text",
                "market_type_group_list" => $market_type_group_list

            ),
        );

        return parent::create($extraData);
    }

    public function edit($id, $extraData = array())
    {
        $market_type_group_service = App::make('TopBetta\Services\Markets\MarketTypeGroupService');
        $market_type_group_list = $market_type_group_service->getMarketTypeGroupList();
        $extraData = array(
            "Market Rules" => array(
                "field" => "market_rules",
                "type" => "text",
                "market_type_group_list" => $market_type_group_list
            )
        );

        return parent::edit($id, $extraData);
    }

}
