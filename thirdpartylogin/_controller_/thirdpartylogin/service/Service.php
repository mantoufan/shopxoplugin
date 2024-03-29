<?php
namespace app\plugins\thirdpartylogin\service;
use think\Db;
use app\service\UserService;
use app\service\PluginsService;
class Service
{
  public static $last_url = 'plugins_thirdpartylogin_last_url';
  public static function config() {
    return array(
      'weixin' => array(
        'n' => '微信',
        'db' => array('plugin_thirdpartylogin_weixin_web_openid', 'plugin_thirdpartylogin_weixin_unionid'),
        'scope' => 'snsapi_userinfo',
        'mua' => array('MicroMessenger'),
        'form' => array(
          array('id' => 'enable_weixin'),
          array('id' => 'weixin_appid_web', 'n' => 'AppId（网站应用）', 'url' => 'https://open.weixin.qq.com/cgi-bin/frame?t=home/web_tmpl&lang=zh_CN'),
          array('id' => 'weixin_secret_web', 'n' => 'AppSecret（网站应用）'),
          array('id' => 'weixin_appid_m', 'n' => 'AppId（公众号）', 'url' => 'https://mp.weixin.qq.com/advanced/advanced?action=dev&t=advanced/dev&lang=zh_CN'),
          array('id' => 'weixin_secret_m', 'n' => 'AppSecret（公众号）')
        ),
        'help' => array(
          'PC版的微信登录，请在微信开发者平台（需完成开发者认证），创建网站应用，填写网站应用的AppId和AppSecert，用户通过微信扫码登录。',
          'PC版的回调地址，填写回调域名，不含http、https和//。',
          'H5移动版的微信登录，请在微信公众号平台（建议完成微信公众号认证），开发，基本设置里，获取AppId和AppSecert，用户在微信中打开网站，点微信登录，授权后登录。微信不支持其他手机浏览器登录。',
          'H5移动版的回调地址，也填写回调域名，在微信公众平台，设置，公众号设置，功能设置，网页授权域名里，添加。',
          '微信小程序的登录，shopXO自带，创建网站应用后，请在微信开发者平台，绑定你的微信公众号，微信小程序，插件会自动将使用同一微信，分别进行PC扫码，微信公众号授权和小程序登录的用户，识别为同一用户。'
        )
      ),
      'qq' => array(
        'n' => 'QQ',
        'db' => array('plugin_thirdpartylogin_qq_openid', 'plugin_thirdpartylogin_qq_unionid'),
        'scope' => 'get_user_info',
        'form' => array(
          array('id' => 'enable_qq'),
          array('id' => 'qq_appid', 'n' => 'AppId', 'url' => 'https://connect.qq.com/manage.html#/appcreate/web'),
          array('id' => 'qq_secret', 'n' => 'AppSecret'),
        ),
        'help' => array(
          'QQ小程序的登录，shopXO自带，创建网站应用后，请在QQ互联平台，绑定你的QQ小程序，插件会自动将使用同一QQ号，分别进行PC扫码，手机QQ一键登录，QQ小程序登录的用户，识别为同一用户。'
        )
      ),
      'weibo'=> array(
        'n' => '微博',
        'db' => array('plugin_thirdpartylogin_weibo_openid'),
        'scope' => 'all',
        'form' => array(
          array('id' => 'enable_weibo'),
          array('id' => 'weibo_appid', 'n' => 'AppId', 'url' => 'https://open.weibo.com/apps/new?sort=web'),
          array('id' => 'weibo_secret', 'n' => 'AppSecret')
        )
      ),
      'alipay'=> array(
        'n' => '支付宝',
        'db' => array('plugin_thirdpartylogin_alipay_openid'),
        'scope' => 'auth_user',
        'mua' => array('AlipayClient'),
        'form' => array(
          array('id' => 'enable_alipay'),
          array('id' => 'alipay_appid', 'n' => 'AppId', 'url' => 'https://app.alipay.com/abilityprod/detail?abilityCode=SM010000000000001010'),
          array('id' => 'alipay_rsa_private', 'n' => '应用私钥'),
          array('id' => 'alipay_rsa_public', 'n' => '应用公钥'),
          array('id' => 'alipay_out_rsa_public', 'n' => '支付宝公钥')
        )
      ),
      'line'=> array(
        'n' => 'Line',
        'db' => array('plugin_thirdpartylogin_line_openid'),
        'scope' => 'profile',
        'form' => array(
          array('id' => 'enable_line'),
          array('id' => 'line_appid', 'n' => 'Channel ID', 'url' => 'https://developers.line.biz/console/channel/new?type=line-login'),
          array('id' => 'line_secret', 'n' => 'Channel Secret')
        )
      ),
      'google'=> array(
        'n' => 'Google',
        'db' => array('plugin_thirdpartylogin_google_openid'),
        'scope' => 'https://www.googleapis.com/auth/userinfo.profile',
        'form' => array(
          array('id' => 'enable_google'),
          array('id' => 'google_appid', 'n' => 'AppId', 'url' => 'https://developers.google.com/identity/sign-in/web/sign-in#before_you_begin'),
          array('id' => 'google_secret', 'n' => 'AppSecret')
        )
      ),
    );
  }
  public static function root() {
    return dirname(__FILE__) . '/';
  }
  public static function pem($file) {
    return self::root() . 'pem/' . $file . '.pem';
  }
  public static function loadData() {
    $ret = PluginsService::PluginsData('thirdpartylogin');
    if($ret['code'] == 0)
    {
      $payConfig = Service::loadPayConfig(array('Alipay'));
      $isMobile = isMobile();
      $ret['data'] = array_merge($ret['data'] ? $ret['data'] : array(), array(
        'alipay_appid' => $payConfig['Alipay']['appid'],
        'alipay_rsa_private' => $payConfig['Alipay']['rsa_private'],
        'alipay_rsa_public' => $payConfig['Alipay']['rsa_public'],
        'alipay_out_rsa_public' => $payConfig['Alipay']['out_rsa_public'],
        'alipay_pem_private' => $payConfig['Alipay']['pem_private'],
        'alipay_pem_public' => $payConfig['Alipay']['pem_public']
      ));
      if ($isMobile) {
        $ret['data']['weixin_appid'] = isset($ret['data']['weixin_appid_m']) ? $ret['data']['weixin_appid_m'] : '';
        $ret['data']['weixin_secret'] = isset($ret['data']['weixin_secret_m']) ? $ret['data']['weixin_secret_m'] : '';
      } else {
        $ret['data']['weixin_appid'] = isset($ret['data']['weixin_appid_web']) ? $ret['data']['weixin_appid_web'] : '';
        $ret['data']['weixin_secret'] = isset($ret['data']['weixin_secret_web']) ? $ret['data']['weixin_secret_web'] : '';
      }
      return $ret['data'];
    }
    return false;
  }
  public static function loadPayConfig($payments=array()) {
    $_configs = array();
    foreach($payments as $k => $payment) {
      if ($payment === 'Alipay') {
        $_config = array(
          'appid' => '',
          'out_rsa_public' => '',
          'rsa_private' => '',
          'rsa_public' => '',
          'pem_private' => '',
          'pem_public' => ''
        );
      } else {
        $_config = array(
          'appid' => ''
        ); 
      }
      $ret = Db::name('payment')->where(['payment	'=>$payment])->find();
      if ($ret !== false) {
        $_config_ar = json_decode($ret['config'], true);
        $_config = array_merge($_config, $_config_ar ? $_config_ar : array());
      }
      $_configs[$payment] = $_config;
    }
    return $_configs;
  }
  public static function savePayConfig($payments=array()) {
    foreach($payments as $payment => $config) {
      $_config = self::loadPayConfig(array($payment));
      $_config[$payment] = array_merge($_config[$payment], $config);
      if ($payment === 'Alipay') {
        $_root_pem = self::root() . 'pem/';
        if ($_config[$payment]['rsa_private']) {
          $_config[$payment]['pem_private'] = self::pem('alipay_private');
          file_put_contents($_config[$payment]['pem_private'], $_config[$payment]['rsa_private']);
        } else {
          if (file_exists($_config[$payment]['pem_private'])) {
            unlink($_config[$payment]['pem_private']);
          }
          $_config[$payment]['pem_private'] = '';
        }
        if ($_config[$payment]['rsa_public']) {
          $_config[$payment]['pem_public'] = self::pem('alipay_public');
          file_put_contents($_config[$payment]['pem_public'], $_config[$payment]['rsa_public']);
        } else {
          if (file_exists($_config[$payment]['pem_public'])) {
            unlink($_config[$payment]['pem_public']);
          }
          $_config[$payment]['pem_public'] = '';
        }
      }
      Db::name('payment')->where(['payment'=>$payment])->update(array('config'=> json_encode($_config[$payment])));
    }
  }
  // 频道名 → 字段
  private static function channel2n($arv = array('channel' => '', 'openid' => '', 'unionid' => '')) {
    $config = self::config();
    $n = false;
    switch ($arv['channel']) {
      case 'qq':
        if (!empty($arv['unionid'])) {
          $n = $config[$arv['channel']]['db'][1];
        } else {
          $n = $config[$arv['channel']]['db'][0];
        }
      break;
      case 'weixin':
        if (!empty($arv['unionid'])) {
          $n = $config[$arv['channel']]['db'][1];
        } else {
          $n = $config[$arv['channel']]['db'][0];
        }
      break;
      default:
        $n = isset($config[$arv['channel']]) ? $config[$arv['channel']]['db'][0] : false;
    }
    return $n;
  }
  // 绑定 解绑 查找
  public static function todo($action, $arv = array()) {
    $config = self::config();
    $n = isset($arv['db']) ? $arv['db'] : self::channel2n($arv);
    $openid = (isset($arv['unionid']) && !empty($arv['unionid'])) ? $arv['unionid'] : (isset($arv['openid']) ? $arv['openid'] : '');
    if ($n) {
      switch ($action) {
        case 'bind':
          $user = self::todo('find', $arv);
          $data = array(
            $n => $openid,
            'upd_time' => time()
          );

          // 有unionid时，也存openid
          if (!empty($arv['unionid']) && !empty($arv['openid'])) {
            $data[$config[$arv['channel']]['db'][0]] = $arv['openid'];
          }

          if (empty($user['nickname']) || stripos($user['nickname'], '游客-') !== FALSE) {
            $data['nickname'] = $arv['nick'];
          }
          if (empty($user['gender'])) {
            $data['gender'] = $arv['gender'] === 'm' ? 2 : ($arv['gender'] === 'f' ? 1 : 0);
          }
          if (empty($user['avatar'])) {
            $data['avatar'] = str_replace('http://', 'https://', $arv['avatar']);
          }
          Db::name('User')->where(['id'=>$arv['id']])->update($data);
          UserService::UserLoginHandle($arv['id'], array());
          if ($user && isset($user['id']) && $user['id'] !== $arv['id']) {
            $arv['id'] = $user['id'];
            $arv['NoUserLoginHandle'] = true;
            self::todo('unbind', $arv);
          }
          return DataReturn('绑定成功', 0);
        break;
        case 'unbind':
          $data = array('upd_time' => time());

          // 删unionid，也删openid
          foreach ($config[$arv['channel']]['db'] as $db) {
            $data[$db] = '';
          }

          Db::name('User')->where(['id'=>$arv['id']])->update($data);
          if (!isset($arv['NoUserLoginHandle'])) {
            UserService::UserLoginHandle($arv['id'], array());
          }
          return DataReturn('解绑成功', 0);
        break;
        case 'find':
          if (!empty($arv['unionid'])) {
            $unionid = $arv['unionid'];
            $user = UserService::UserInfo($n, $unionid);
            if(!empty($user))
            {
              // 只有unionid，存openid
              if (empty($user[$config[$arv['channel']]['db'][0]]) && !empty($arv['openid'])) {
                $data[$config[$arv['channel']]['db'][0]] = $arv['openid'];
                Db::name('User')->where(['id'=>$user['id']])->update($data);
              }

              UserService::UserLoginHandle($user['id'], array());
              return $user;
            }
          } 
          if (!empty($arv['openid'])) {
            $openid = $arv['openid'];

            // 用openid
            $n = $config[$arv['channel']]['db'][0];

            $user = UserService::UserInfo($n, $openid);
            if(!empty($user) && isset($user['id']))
            {
              // 只有openid，存unionid
              if (!empty($arv['unionid']) && !empty($config[$arv['channel']]['db'][1]) && empty($user[$config[$arv['channel']]['db'][1]])) {
                $data[$config[$arv['channel']]['db'][1]] = $arv['unionid'];
                Db::name('User')->where(['id'=>$user['id']])->update($data);
              }

              UserService::UserLoginHandle($user['id'], array());
              return $user;
            }
          }
          return false;
        break;
      }
    }
  }
  public static function PluginsHomeUrl($url, $party = '', $case = '') {
    $ar = explode('/', $url);
    $http = $ar[0];
    if ($case === 'admin') {
      if(in_array($party, array('line'))) {
        $url = $http .'//'. $ar[2] . '/';
      } else if (in_array($party, array('weixin'))) {
        $url = $ar[2];
      }
    }
    if (stripos($url, '?') === FALSE) {
      $url = str_replace('index/plugins/index/pluginsname/', '?s=/index/plugins/index/pluginsname/', $url);
    } else {
      $url = str_replace('index.php?s=', '?s=', $url);
    }
    return $url;
  }
}
?>