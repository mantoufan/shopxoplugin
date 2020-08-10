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
namespace app\plugins\servicepro;

use think\Controller;
use app\service\PluginsService;
use app\service\UserService;

/**
 * 在线客服 - 钩子入口
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2016-12-01T21:51:08+0800
 */
class Hook extends Controller
{
    /**
     * 应用响应入口
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  1.0.0
     * @datetime 2019-02-09T14:25:44+0800
     * @param    [array]                    $params [输入参数]
     */
    public function run($params = [])
    {
        // 是否控制器钩子
        // is_backend 当前为后端业务处理
        // hook_name 钩子名称
        if(!PluginsService::PluginsStatus('servicepro'))
        {
            return '';
        }

        if(isset($params['is_backend']) && $params['is_backend'] === true && !empty($params['hook_name']))
        {
            // 参数一   描述
            // 参数二   0 为处理成功, 负数为失败
            // 参数三   返回数据
            return DataReturn('返回描述', 0);

        // 默认返回视图
        } else {
            if(!empty($params['hook_name']))
            {
                switch($params['hook_name'])
                {
                    case 'plugins_css' :
                        $ret = __MY_ROOT_PUBLIC__.'static/plugins/css/servicepro/index/style.css';
                        break;

                    case 'plugins_js' :
                        $ret = __MY_ROOT_PUBLIC__.'static/plugins/js/servicepro/index/style.js';
                        break;

                    case 'plugins_view_common_bottom' :
                        $params['type'] = 'common_bottom';
                        $ret = $this->html($params);
                        break;

                    case 'plugins_view_goods_detail_title' :
                        $params['type'] = 'title';
                        $ret = $this->html($params);
                        break;

                    case 'plugins_view_goods_detail_base_buy_nav_min_inside' :
                        $params['type'] = 'nav';
                        $ret = $this->html($params);
                        break;

                    case 'plugins_view_goods_detail_tabs_bottom' :
                        $params['type'] = 'goods_bottom';
                        $ret = $this->html($params);
                        break;

                    default :
                        $ret = '';
                }
                return $ret;
            } else {
                return '';
            }
        }
    }

    /**
     * 视图
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  1.0.0
     * @datetime 2019-02-06T16:16:34+0800
     * @param    [array]          $params [输入参数]
     */
    public function html($params = [])
    {
        // 当前模块/控制器/方法
        $module_name = strtolower(request()->module());
        $controller_name = strtolower(request()->controller());
        $action_name = strtolower(request()->action());

        // 获取应用数据
        $ret = PluginsService::PluginsData('servicepro', ['images']);
        if($ret['code'] == 0)
        {  
            $ret['data']['servicepro_sider_hide'] = FALSE;
                 
            // 客服
            $online_service = empty($ret['data']['online_service']) ? [] : explode("\n", $ret['data']['online_service']);
            $online_service_data = [];
            if(!empty($online_service))
            {
                foreach($online_service as $v)
                {
                    $items = explode('|', $v);
                    if(count($items) >= 2)
                    {
                        $online_service_data[] = $items;
                    }
                }
            }
            $ret['data']['online_service'] = $online_service_data;


            $user = array();
            $daovocie = array('display' => false, 'hidden'=> false);
            if (!empty($ret['data']['daovoice_app_id'])) {
                $daovocie['display'] = true;
                if (in_array($params['type'], array('common_bottom')))  {
                    $user = UserService::LoginUserInfo();
                }
                if (!empty($ret['data']['daovoice_scope'])) {
                    if ($ret['data']['daovoice_scope'] === '2' || $ret['data']['daovoice_scope'] === '1' && empty($user)) {
                        $daovocie['display'] = false;
                    }
                }
            }

            // 悬浮和浮动按钮显示
            if(IsMobile())
            {
                if (!isset($ret['data']['overall_display']) || in_array('mobile', explode(',', $ret['data']['overall_display']))) {
                    $ret['data']['servicepro_sider_hide'] = FALSE;
                } else {
                    $ret['data']['servicepro_sider_hide'] = TRUE;
                }
                if (!isset($ret['data']['daovoice_display']) || in_array('mobile', explode(',', $ret['data']['daovoice_display']))) {
                    $daovocie['hidden'] = FALSE;
                }else {
                    $daovocie['hidden'] = TRUE;
                }
            } else {
                if (!isset($ret['data']['overall_display']) || in_array('pc', explode(',', $ret['data']['overall_display']))) {
                    $ret['data']['servicepro_sider_hide'] = FALSE;
                } else {
                    $ret['data']['servicepro_sider_hide'] = TRUE;
                }
                if (!isset($ret['data']['daovoice_display']) || in_array('pc', explode(',', $ret['data']['daovoice_display']))) {
                    $daovocie['hidden'] = FALSE;
                }else {
                    $daovocie['hidden'] = TRUE;
                }
            }

            if(!isset($ret['data']['is_overall'])){
                $ret['data']['is_overall'] = '1';
            }    
            // 非全局
            if($ret['data']['is_overall'] === '0')
            {
                // 非首页则空
                if($module_name.$controller_name.$action_name != 'indexindexindex')
                {
                    $ret['data']['servicepro_sider_hide'] = TRUE;
                }
            }

            // 如果是微信
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== FALSE) {
                $ret['data']['isWeiXin'] = TRUE;
            } else {
                $ret['data']['isWeiXin'] = FALSE;
            }

            $this->assign('serviceproData', $ret['data']);
            $this->assign('serviceproType', $params['type']);
            $this->assign('serviceproUser', $user);
            $this->assign('serviceproDaovocie', $daovocie);
            return $this->fetch('../../../plugins/view/servicepro/index/public/content');
        } else {
            return $ret['msg'];
        }
    }
}
?>