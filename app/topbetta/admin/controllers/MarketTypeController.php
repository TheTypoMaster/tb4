<?php namespace TopBetta\admin\Controllers;

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

    protected $indexView = 'admin::eventdata.markettypes.index';

    protected $createView = 'admin::eventdata.markettypes.create';

    protected $editView = 'admin::eventdata.markettypes.edit';

    public function index($relations = array(), $extraData = array())
    {
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
        $extraData = array(
            "Market Rules" => array(
                "field" => "market_rules",
                "type" => "text"
            )
        );

        return parent::create($extraData);
    }

    public function edit($id, $extraData = array())
    {
        $extraData = array(
            "Market Rules" => array(
                "field" => "market_rules",
                "type" => "text"
            )
        );

        return parent::edit($id, $extraData);
    }

}
