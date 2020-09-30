<?php
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

                    case 'plugins_common_header' :
                        $params['type'] = 'plugins_common_header';
                        $ret = $this->html($params);
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
        $ret = PluginsService::PluginsData('servicepro');
        if($ret['code'] == 0)
        {
            $conf = array(
                'type' => $params['type'],
                'isMobile' => isMobile(),
                'user' => array(),
                'isHome' => false
            );
            // 用户信息
            if (in_array($conf['type'], array('common_bottom'))) {
                // 基本配置
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
                $user = UserService::LoginUserInfo();
                $user = array_merge(array(
                    'id' => '',
                    'username' => '',
                    'nickname' => '',
                    'mobile' => '',
                    'email' => '',
                    'gender_text' => 0,
                    'avatar' => '',
                    'add_time' => '',
                    'upd_time' => ''
                ), $user ? $user : array());
                $conf['user'] = array(
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'nickname' => $user['nickname'],
                    'mobile' => $user['mobile'],
                    'email' => $user['email'],
                    'gender_text' => $user['gender_text'],
                    'avatar' => $user['avatar'],
                    'add_time' => $user['add_time'],
                    'upd_time' => $user['upd_time']
                );
                $conf['isHome'] = $module_name.$controller_name.$action_name === 'indexindexindex';
                $conf['isMicroMessenger'] = strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== FALSE;
                if (isset($ret['data']['css_pc'])) {
                    unset($ret['data']['css_pc']);
                }
                if (isset($ret['data']['css_mobile'])) {
                    unset($ret['data']['css_mobile']);
                }
            }
            $this->assign('data', $ret['data']);
            $this->assign('conf', $conf);
            return $this->fetch('../../../plugins/view/servicepro/index/public/content');
        } else {
            return $ret['msg'];
        }
    }
}
?>