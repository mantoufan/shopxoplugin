<?php
namespace app\plugins\diystyle\wga;
use app\service\PluginsService;
use app\plugins\diystyle\service\Service;

class WGA
{
    private static function wga() {
        $root = dirname(__FILE__);
        $config = self::config();
        $_domain_ar = !empty($_SERVER['HTTP_HOST']) ? explode(':', $_SERVER['HTTP_HOST']) : array($_SERVER['SERVER_NAME']);
        $_domain = reset($_domain_ar);
        $r = @file_get_contents('https://api.os120.com/wga/verify?out_type=json&name=shopxoplugin_' . $config['base']['plugins'] . '&version=' . $config['base']['version'] . '&des=正版验证&domain=' . $_domain, false, stream_context_create(array('http' => array('method' => "GET",'timeout' => 6))));
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
    public static function save($params = [])
    {
        $res = self::wga();
        if ($res) {
            return DataReturn($res, -1);
        }
        if (!empty($params['tpl_id'])) {
            Service::append($params['tpl_id'], !empty($params['hue']) ? $params['hue'] : 0);
        }
        $conf =  Service::config();
        if (!empty($params['color_amp'])) {
            if ($params['color_amp'] === $conf['color_amp']) {
                Service::replace('');
            } else {
                Service::replace($params['color_amp']);
            }
        }
        return PluginsService::PluginsDataSave(array('plugins'=>'diystyle', 'data'=>$params));
    }
}
?>