<?php
namespace app\plugins\orderdeliverynotice\wga;

use think\Controller;
use app\service\PluginsService;

class WGA extends Controller
{
    private static function wga() {
        $r = @file_get_contents('https://api.os120.com/wga/verify?out_type=json&name=shopxoplugin_orderdeliverynotice&des=正版验证&domain=' . $_SERVER['SERVER_NAME'], false, stream_context_create(array('http' => array('method' => "GET",'timeout' => 3))));
        if ($r) {
            $r = json_decode($r, true);
            if (isset($r['code']) && $r['code'] === -1) {
                return $r['msg'];
            }
        }
        return false;
    }
    public function hasAccess() {
        if (rand(0, 10) > 9) {
            $res = self::wga();
            if ($res) {
                return DataReturn('未授权', -1);
            }
        }
        $_key = 'plugin_orderdeliverynotice' . GetClientIP(true);
        if (isset($_SESSION[$_key])) {
            $_SESSION[$_key]++;
        } else {
            $_SESSION[$_key] = 1;
        }
        return $_SESSION[$_key] > 3 ? false : true;
    }
    public function save($params = [])
    {
        $res = self::wga();
        if ($res) {
            return DataReturn($res, -1);
        }
        return PluginsService::PluginsDataSave(['plugins'=>'orderdeliverynotice', 'data'=>$params]);
    }
}
?>