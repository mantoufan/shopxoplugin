<?php
namespace app\plugins\expressinwebfree\wga;

use think\Controller;
use app\service\PluginsService;

class WGA extends Controller
{
    private static function wga() {
        $root = dirname(__FILE__);
        $config = self::config();
        $_domain_ar = !empty($_SERVER['HTTP_HOST']) ? explode(':', $_SERVER['HTTP_HOST']) : array($_SERVER['SERVER_NAME']);
        $_domain = reset($_domain_ar);
        $r = @file_get_contents('https://api.os120.com/wga/verify?out_type=json&name=shopxoplugin_' . $config['base']['plugins'] . '&version=' . $config['base']['version'] . '&des=正版验证&domain=' . $_domain, false, stream_context_create(array('http' => array('method' => "GET",'timeout' => 3))));
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
        return json_decode(file_get_contents(dirname(__FILE__) . '/../config.json'), true); 
    }
    public static function tip() {
        if (file_exists(dirname(__FILE__) . '/wga_tip.txt')){
            return '<div class="am-alert" data-am-alert><button type="button" class="am-close">&times;</button>' . file_get_contents(dirname(__FILE__) . '/wga_tip.txt') . '</div>';
        }
        return '';
    }
    public function html($params = [])
    {
        $config = self::config();
        if (rand(0, 100) > 85) {
            $res = self::wga();
            if ($res) {
                return '<script defer>AMUI.dialog.alert({title: \'' . $config['base']['name'] . '\', content: \'' . $res . '\'})</script>';
            }
        }
        // 获取应用数据
        $ret = PluginsService::PluginsData($config['base']['plugins'], ['images']);
        if($ret['code'] == 0)
        {
            $this->assign('data', $ret['data']);
            return $this->fetch('../../../plugins/view/' . $config['base']['plugins'] . '/index/public/content');
        } else {
            return $ret['msg'];
        }
    }
    public function save($params = [])
    {
        $res = self::wga();
        if ($res) {
            return DataReturn($res, -1);
        }
        $config = self::config();
        return PluginsService::PluginsDataSave(['plugins'=>$config['base']['plugins'], 'data'=>$params]);
    }
}
?>