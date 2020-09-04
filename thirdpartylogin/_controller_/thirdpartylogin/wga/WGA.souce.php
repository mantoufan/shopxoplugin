<?php
namespace app\plugins\thirdpartylogin\wga;
use app\service\PluginsService;
use app\plugins\thirdpartylogin\service\Service;
class WGA
{
    private $_config = array();
    private static function wga() {
        $root = dirname(__FILE__);
        $config = self::config();
        $r = @file_get_contents('https://api.os120.com/wga/verify?out_type=json&name=shopxoplugin_' . $config['base']['plugins'] . '&version=' . $config['base']['version'] . '&des=正版验证&domain=' . $_SERVER['SERVER_NAME'], false, stream_context_create(array('http' => array('method' => "GET",'timeout' => 3))));
        if ($r) {
            $r = json_decode($r, true);
            if (isset($r['code']) && $r['code'] === -1) {
                return $r['msg'];
            }
            if (isset($r['data']) && isset($r['data']['tip']) && !empty($r['data']['tip'])) {
                file_put_contents($root . '/wga_tip.txt', $r['data']['tip']);
            } else if (file_exists($root . '/wga_tip.txt')){
                unlink($root. '/wga_tip.txt');
            }
        }
        return false;
    }
    public static function config() {
        if (!$this->_config) {
            $this->_config = json_decode(file_get_contents($root . '/../config.json'), true);
        }
        return $this->_config; 
    }
    public static function tip() {
        if (file_exists(dirname(__FILE__) . '/wga_tip.txt')){
            return '<div class="am-alert" data-am-alert><button type="button" class="am-close">&times;</button>' . file_get_contents(dirname(__FILE__) . '/wga_tip.txt') . '</div>';
        }
        return '';
    }
    public static function save($params = [])
    {
        $res = self::wga();
        if ($res) {
            return DataReturn($res, -1);
        }
        Service::savePayConfig(array('Alipay'=>array(
            'appid' => $params['alipay_appid'],
            'rsa_private' => $params['alipay_rsa_private'],
            'rsa_public' => $params['alipay_rsa_public'],
            'out_rsa_public' => $params['alipay_out_rsa_public'],
        )));
        unset($params['alipay_appid'], $params['alipay_rsa_private'], $params['alipay_rsa_public'], $params['alipay_out_rsa_public']);
        $config = self::config();
        return PluginsService::PluginsDataSave(['plugins'=>$config['base']['plugins'], 'data'=>$params]);
    }
}
?>