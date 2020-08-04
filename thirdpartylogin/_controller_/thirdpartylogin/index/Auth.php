<?php
namespace app\plugins\thirdpartylogin\index;
use anerg\OAuth2\OAuth;
use think\Controller;
// use think\facade\Config;
use app\service\UserService;
use app\service\PluginsService;
use app\plugins\thirdpartylogin\service\Service;

class Auth extends Controller
{
    private $config = array();
    public function bind($params = [])
    {
        $party = $params['party'];
        $data = Service::loadData();
        if ($data) {
            $config = Service::config();
            $this->config = array(
                'response_type' => 'code',
                'app_id' => $data[$party . '_appid'],
                'callback' => Service::PluginsHomeUrl(PluginsHomeUrl('thirdpartylogin', 'auth', 'callback', array('party' => $party))),
                'scope' => $config[$party]['scope']
            );
            if (isMobile()) {
                return redirect(OAuth::$party($this->config)->setDisplay('mobile')->getRedirectUrl());
            } else {
                return redirect(OAuth::$party($this->config)->getRedirectUrl());
            } 
        }
    }
    public function unbind($params = [])
    {
        $party = $params['party'];
        $db = $params['db'];
        $user = UserService::LoginUserInfo();
        if (!empty($user)) {
            return Service::do('unbind', array(
                'channel' => $party,
                'id' => $user['id'],
                'db' => $db
            ));
        } else {
            return DataReturn('解绑失败', -100);
        }
    }
    public function callback($params = [])
    {
        $party = $params['party'];
        $data = Service::loadData();
        if ($data) {
            $this->config = array(
                'app_id' => $data[$party . '_appid'],
                'app_secret' => isset($data[$party . '_secret']) ? $data[$party . '_secret'] : '',
                'callback' => Service::PluginsHomeUrl(PluginsHomeUrl('thirdpartylogin', 'auth', 'callback', array('party' => $party)), $party),
                'grant_type'    => 'authorization_code',
                'withUnionid' => 'true'
            );
            if ($party === 'alipay') {
                $this->config = array_merge($this->config, array(
                    'pem_private' => $data[$party . '_pem_private'],
                    'pem_public' => $data[$party . '_pem_public']
                ));
            }
            //获取格式化后的第三方用户信息
            $snsInfo = OAuth::$party($this->config)->userinfo();
            // 获取登录用户
            $user = UserService::LoginUserInfo();
            if (!empty($user)) { // 如果已经登陆了开始绑定流程
                Service::do('bind', array_merge($snsInfo, array('id' => $user['id'])));
                return redirect(MyUrl('index/personal/index'));
            } else {
                if (Service::do('find', $snsInfo) === false) {
                    $this->assign('plugin_thirdpartylogin_b', base64_encode(http_build_query($snsInfo)));
                    return $this->fetch('../../../plugins/view/thirdpartylogin/public/index/callback');
                } else {
                    $last_url = session(Service::$last_url);
                    session(Service::$last_url, null);
                    return redirect($last_url ? $last_url : '/');
                }
            } 
        }
    }
}
?>