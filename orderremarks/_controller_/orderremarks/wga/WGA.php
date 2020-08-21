<?php
namespace app\plugins\orderremarks\wga;

use think\Controller;
use app\service\PluginsService;

class WGA extends Controller
{
    private static function wga() {
        $r = @file_get_contents('https://api.os120.com/wga/verify?out_type=json&name=shopxoplugin_orderremarks&des=正版验证&domain=' . $_SERVER['SERVER_NAME'], false, stream_context_create(array('http' => array('method' => "GET",'timeout' => 3))));
        if ($r) {
            $r = json_decode($r, true);
            if (isset($r['code']) && $r['code'] === -1) {
                return $r['msg'];
            }
        }
        return false;
    }
    public function save($params = [])
    {
        $res = self::wga();
        if ($res) {
            return DataReturn($res, -1);
        }
        return PluginsService::PluginsDataSave(['plugins'=>'orderremarks', 'data'=>$params]);
    }
    public function getOrderUpdateData($ret = [], $params = [])
    {
        $res = self::wga();
        if ($res) {
            return DataReturn($res, -1);
        }
        return array(
            'order_no' => $ret['data']['order_no'],
            'admin_note' => $params['admin_note']
        );
    }
}
?>