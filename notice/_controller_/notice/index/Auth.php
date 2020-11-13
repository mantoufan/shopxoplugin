<?php
namespace app\plugins\notice\index;
use think\Controller;
use think\Db;
use app\service\PluginsService;

class Auth extends Controller
{
    public function wxpub() {
        $ret = PluginsService::PluginsData('notice');
        if($ret['code'] == 0)
        {
            if (!empty($_POST['code'])) {
                $data = $ret['data'];
                if (!empty($data['wxpub_appid']) && !empty($data['wxpub_appsecret'])) {
                    
                    $r = $this->curl('https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $data['wxpub_appid'] . '&secret=' . $data['wxpub_appsecret'] . '&code=' . $_POST['code'] . '&grant_type=authorization_code');
                    if (!empty($r)) {
                        $res = json_decode($r, true);
                        if (!empty($res)) {
                            if (!empty($res['openid'])) {
                                $openid = $res['openid'];
                                if (empty($params['uid'])) {
                                    $_p = dirname(__FILE__). '/data.php';
                                    if (is_file($_p)) {
                                        $data = include($_p);
                                    } else {
                                        $data = array();
                                    }
                                    if (empty($data['openid'])) {
                                        $data['openid'] = array($openid);
                                    } else {
                                        array_push($data['openid'], $openid);
                                    }
                                    file_put_contents($_p, '<?php return ' . var_export($data, true) . ';?>');
                                } else {
                                    Db::name('User')->where(array('id' => $params['uid']))->update(array(
                                        'weixin_web_openid' => $openid
                                    )); 
                                }
                                echo 'ok';
                            } else {
                                echo json_encode($res, true);
                            }
                        }
                    }
                }
            } else {
                $this->assign('data', $ret['data']);
                return $this->fetch('../../../plugins/view/notice/public/index/wxpub');
            }
        }
    }
    public function wxOpenid() {
        $_p = dirname(__FILE__). '/data.php';
        $openid = '';
        if (is_file($_p)) {
            $data = include($_p);
            if (!empty($data['openid'])) {
                $openid = implode(',', $data['openid']);
                unset($data['openid']);
                file_put_contents($_p, '<?php return ' . var_export($data, true) . ';?>');
            }
        }
        echo json_encode(array('code' => 1, 'data' => array(
            'openid' => $openid
        )));
    }
    public function qrcode($params = array()) {
        include_once(dirname(__FILE__) . '/vendor/autoload.php');
        $qr = \tinymeng\code\Generate::qr();
        $qr->create(PluginsHomeUrl('notice', 'auth', 'wxpub', empty($params['uid']) ? array() : array('uid' => $params['uid'])));
        exit;
    }
    private function curl($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}
?>