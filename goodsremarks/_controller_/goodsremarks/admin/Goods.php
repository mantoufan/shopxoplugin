<?php
// +----------------------------------------------------------------------
// | ShopXO 国内领先企业级B2C免费开源电商系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011~2019 http://shopxo.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Devil
// +----------------------------------------------------------------------
namespace app\plugins\goodsremarks\admin;

use think\Controller;
use app\plugins\goodsremarks\service\BaseService;

class Goods extends Controller
{
    public function Detail($params = [])
    {
        return BaseService::GoodsDetail($params);
    }

    public function Update($params = [])
    {
        return BaseService::GoodsUpdate($params);
    }
}
?>