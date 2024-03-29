<?php
namespace app\plugins\notice\service;
use app\service\UserService;
use app\service\PluginsService;

class Service
{
    public static function root() {
        return str_replace('\\', '/', dirname(__FILE__)) . '/../../../../';
    }
    
    public static function dataJS() {
        $user = UserService::LoginUserInfo();
        $ret = PluginsService::PluginsData('notice');
        if($ret['code'] == 0) {
            return '<script>var plugins_notice_data = ' . json_encode(array(
                'is_weixin' => strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false,
                'is_mobile' => isMobile(),
                'weixin_web_openid' => !empty($user['weixin_web_openid']) ? $user['weixin_web_openid'] : '',
                'wxpub_qrcode' => !empty($ret['data']['wxpub_qrcode']) ? $ret['data']['wxpub_qrcode'] : '',
                'wxpub_qrcode_auth' => PluginsHomeUrl('notice', 'auth', 'qrcode', array('uid' => !empty($user['id']) ? $user['id'] : ''))
            )) . ';</script>';
        }
        return '';
    }

    public static function curl($url, $post = '') {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
        curl_setopt($ch, CURLOPT_TIMEOUT, 8);
        if($post){
            $json = json_encode($post);
			curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);  
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($json))
            );
		}
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    public static function accessToken($type = 'wxpub', $appid, $appsecret) {
        $_p = dirname(__FILE__) . '/data.php';
        $data = array();
        if (is_file($_p)) {
            $data = include $_p;
            if (!empty($data['weixin_access_token_'. $type]) && !empty($data['weixin_access_token_expires_in_'. $type])) {
                if ($data['weixin_access_token_expires_in_'. $type] > time()) {
                    return $data['weixin_access_token_'. $type];
                }
            }
        }
        $ret = self::curl('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $appsecret);
        if ($ret) {
            $res = json_decode($ret, true);
            if (!empty($res)) {
                if (!empty($res['access_token'])) {
                    $data['weixin_access_token_'. $type] = $res['access_token'];
                    $data['weixin_access_token_expires_in_'. $type] = time() + floor($res['expires_in']);
                    file_put_contents($_p, '<?php return ' . var_export($data, true) . ';?>');
                    return $res['access_token'];
                }
            }
        }
        return false;
    }

    public static function wxMsg($type = 'wxpub', $appid, $appsecret, $post, $is_retry = false) {
        $_p = dirname(__FILE__) . '/data.php';
        $access_token = self::accessToken($type, $appid, $appsecret);
        if ($type === 'wxpub') {
            $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send';
        } elseif ($type === 'wxamp') {
            $url = 'https://api.weixin.qq.com/cgi-bin/message/subscribe/send';
        }
        if ($access_token) {
            $res = self::curl($url . '?access_token=' . $access_token, $post);
            if (!empty($res['errcode'])) {
                if (in_array($res['errcode'], array('40001', '42001', 40001, 42001))) {
                    if (is_file($_p)) {
                        $data = include $_p;
                        if (!empty($data['weixin_access_token_'. $type]) && !empty($data['weixin_access_token_expires_in_'. $type])) {
                            unset($data['weixin_access_token_'. $type], $data['weixin_access_token_expires_in_'. $type]);
                            file_put_contents($_p, '<?php return ' . var_export($data, true) . ';?>');
                            if ($is_retry === false) {
                                wxMsg($type, $appid, $appsecret, $post, true);
                            }
                        }
                    }
                }
            }
        }
        return false;
    }
}
?>