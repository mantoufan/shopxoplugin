<?php
namespace app\plugins\thirdpartylogin;
use think\Controller;
use app\service\UserService;
use app\service\PluginsService;
use app\plugins\thirdpartylogin\service\Service;
class Hook extends Controller
{
    public function run($params = [])
    {
        include_once('vendor/autoload.php');
        $ret = '';
        if(!empty($params['hook_name']))
        {
            // 当前模块/控制器/方法
            $module_name = strtolower(request()->module());
            $controller_name = strtolower(request()->controller());
            $action_name = strtolower(request()->action());
            // 获取登录用户
            $user = UserService::LoginUserInfo();
            switch($params['hook_name'])
            {
                // 系统运行开始
                case 'plugins_service_system_begin' :
                    if($module_name.$controller_name.$action_name != 'indexpluginsindex' && $module_name == 'index')
                    {
                        $ret = $this->SystemBegin($params, $user);
                    }
                    break;
                // 用户登录页面顶部钩子
                case 'plugins_view_user_login_info_top' :
                    if(empty($user))
                    {
                        $ret = $this->html($params);
                    }
                    break;
                // 用户中心-个人资料
                case 'plugins_service_users_personal_show_field_list_handle' :
                    $ret = $this->UserPersonalHtml($params, $user);
                    break;
                // 样式
                case 'plugins_css' :
                    $ret = __MY_ROOT_PUBLIC__.'static/plugins/css/thirdpartylogin/index/style.css';
                    break;
                // 脚本
                case 'plugins_js' :
                    $ret = __MY_ROOT_PUBLIC__.'static/plugins/js/thirdpartylogin/index/style.js';
                    break;
                // 用户登录前
                case 'plugins_service_user_login_begin':
                    $ret = $this->bind($params);
                    break;
                // 用户注册成功后
                case 'plugins_service_user_register_end':
                    $ret = $this->bind($params);
                    break;
            }
        }
        return $ret;
    }
    private function _attachment_dir() {
        return config('shopxo.attachment_host') . '/static/upload/images/plugins_thirdpartylogin/';
    }
    private function _tips($_res, $_type) {
        return $_res ? '<a href="javascript:;" class="submit-ajax" data-url="' . PluginsHomeUrl('thirdpartylogin', 'auth', 'unbind', array('party' => $_type, 'db'=> $_res['db'])) . '" data-id="1" data-view="reload" data-msg="解绑后不可恢复、确认操作吗？"><img src="' . $this -> _attachment_dir() . 'icon/' . $_type . '.png" alt="' . $_type . ' icon" width="15px" height="15px"/> 解绑</a>' :
                      '<a href="' . PluginsHomeUrl('thirdpartylogin', 'auth', 'bind', array('party' => $_type)) . '" class="am-text-success"><img src="' . $this -> _attachment_dir() . 'icon/' . $_type . '.png" alt="' . $_type . ' icon" width="15px" height="15px"/> 绑定</a>';
    }
    private function _enable_config() {
        $_config = array();
        $base = PluginsService::PluginsData('thirdpartylogin');
        if ($base['code'] === 0) {
            $config = Service::config();
            $isMobile = isMobile();
            foreach($config as $k => $v) {
                if (isset($base['data']['enable_' . $k])) {
                    if ($base['data']['enable_' . $k] === '1') {
                        if ($isMobile && isset($v['mua'])) {
                            foreach($v['mua'] as $mua_k => $mua_v) {
                                if (stripos(strtolower($_SERVER['HTTP_USER_AGENT']), strtolower($mua_v)) !== FALSE) {
                                    $_config[$k] = $v;
                                    break;
                                }
                            }  
                        } else {
                            $_config[$k] = $v;
                        }
                    }
                }
            }
        }
        return $_config;
    }
    public function isBind($user, $db) {
        $bind = false;
        foreach ($db as $k=>$v) {
            if (isset($user[$v]) && !empty($user[$v])) {
                $bind = array(
                    'id' => $user[$v],
                    'db' => $v
                );
                break;
            }
        }
        return $bind;
    }
    public function UserPersonalHtml($params = [], $user = [])
    {
        $ar = array();
        foreach($this -> _enable_config() as $k => $v) {
            $openid = $this->isBind($user, $v['db']);
            $ar[$k] = array(
                'is_ext'    => 1,
                'name'      => $v['n'] . '绑定',
                'value'     => $openid ? substr_replace($openid['id'], '******', 5, 6) : '未绑定',
                'tips'      => $this->_tips($openid, $k)
            );
        }
        $params['data'] = array_merge($params['data'], $ar);
    }
    private function html($params = [])
    {
        $base = PluginsService::PluginsData('thirdpartylogin');
        $this->assign('plugins_data', $base['data']);
        $this->assign('config_str', json_encode($this -> _enable_config()));
        $this->assign('attachment_dir', $this -> _attachment_dir());
        return $this->fetch('../../../plugins/view/thirdpartylogin/public/index/index');
    }
    public function bind($params = []) {
        $arv = array();
        if (isset($params['params']['plugins_thirdpartylogin_b']) && !empty($params['params']['plugins_thirdpartylogin_b'])) {
            parse_str(base64_decode($params['params']['plugins_thirdpartylogin_b']), $arv);
            return Service::todo('bind', array_merge($arv, array(
                'id' => $params['user_id'],
            )));
        }
    }
    public function SystemBegin() {
        if(empty($user))
        {
            // 当前页面
            if (!empty(__MY_VIEW_URL__))
            {
                if  (
                        (stripos(__MY_VIEW_URL__, '?') !== false &&
                         (stripos(__MY_VIEW_URL__, '/index/goods') !== false || 
                          stripos(__MY_VIEW_URL__, '/index/article') !== false)) ||
                        stripos(__MY_VIEW_URL__, '/goods-') !== false || 
                        stripos(__MY_VIEW_URL__, '/article-') !== false
                    ) {
                    session(Service::$last_url, __MY_VIEW_URL__);
                }
            }
        }
    }
}
?>