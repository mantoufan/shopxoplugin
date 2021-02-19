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
namespace app\plugins\goodsremarks;

use think\Controller;
use app\plugins\goodsremarks\service\BaseService;

class Hook extends Controller
{

    public function run($params = [])
    {
        // 钩子名称
        $ret = '';
        if(!empty($params['hook_name']))
        {
            // 当前模块/控制器/方法
            $module_name = strtolower(request()->module());
            $controller_name = strtolower(request()->controller());
            $action_name = strtolower(request()->action());

            // 仅后端订单管理页面有效
            if($module_name.$controller_name.$action_name == 'adminorderindex')
            {
                switch($params['hook_name'])
                {
                    case 'plugins_admin_css' :
                        $ret = 'static/plugins/css/goodsremarks/public/style.css?v=' . MyC('home_static_cache_version');
                        break;

                    case 'plugins_admin_js' :
                        $ret = 'static/plugins/js/goodsremarks/public/style.js?v=' . MyC('home_static_cache_version');
                        break;

                    // 弹窗
                    case 'plugins_admin_view_common_bottom' :
                        $ret = $this->AdminCommonBottomPopupHtml($params);
                        break;

                    // 修改按钮
                    case 'plugins_view_admin_order_list_operation' :
                    case 'plugins_view_admin_order_list_operate' : /* 兼容shopXO 1.8.1 及以上版本 */
                        $ret = $this->AdminOrderViewListHtml($params);
                        break;
                }
            }
        }
        return $ret;
    }


    private function AdminCommonBottomPopupHtml($params = [])
    {
        return $this->fetch('../../../plugins/view/goodsremarks/admin/public/popup');
    }

    private function AdminOrderViewListHtml($params = [])
    {
        if(in_array($params['data']['status'], BaseService::$operate_order_status))
        {
            $ret = BaseService::OrderAdminNote($params['data']['order_no']);
            if ($ret) {
                $params['data']['admin_note'] = $ret['admin_note'];
                $params['data']['upd_time'] = $ret['upd_time'];
            }
            $this->assign('order', $params['data']);
            return $this->fetch('../../../plugins/view/goodsremarks/admin/public/list');
        }
    }
}
?>