<?php
namespace app\plugins\servicepro\wga;
use app\service\PluginsService;
class WGA
{
    private static function wga() {
        $config = json_decode(file_get_contents('../config.json'), true); 
        $r = @file_get_contents('https://api.os120.com/wga/verify?out_type=json&name=shopxoplugin_' . $config['base']['plugins'] . '&version=' . $config['base']['version'] . '&des=正版验证&domain=' . $_SERVER['SERVER_NAME'], false, stream_context_create(array('http' => array('method' => "GET",'timeout' => 3))));
        if ($r) {
            $r = json_decode($r, true);
            if (isset($r['code']) && $r['code'] === -1) {
                return DataReturn($r['msg'], -1);
            }
            if (isset($r['tip']) && !empty($r['tip'])) {
                file_put_contents(dirname(__FILE__) . '/wga_tip.txt', $r['tip']);
            } else if (file_exists(dirname(__FILE__) . '/wga_tip.txt')){
                unlink(dirname(__FILE__) . '/wga_tip.txt');
            }
        }
        return false;
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
        if (isset($params['online_service'])) {
            $params['online_service'] = preg_replace("/\s+\n/", "\n", $params['online_service']);
        }
        return PluginsService::PluginsDataSave(['plugins'=>'servicepro', 'data'=>$params]);
    }
}
?>