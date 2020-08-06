<?php
namespace app\plugins\expressinwebfree\wga;
use app\service\PluginsService;
use app\plugins\expressinwebfree\service\Service;
class WGA
{
    public static function html($params = [])
    {
        if (rand(0, 10) > 8) {
            $r = @file_get_contents('https://k.os120.com/api/wga/verify?out_type=json&name=shopxoplugin_expressinwebfree&des=正版验证&domain=' . $_SERVER['SERVER_NAME'], false, stream_context_create(array('http' => array('method' => "GET",'timeout' => 3))));
            if ($r) {
                $r = json_decode($r, true);
                if (isset($r['code']) && $r['code'] === -1) {
                    return $r['msg'];
                }
            }
        }
        
        // 获取应用数据
        $ret = PluginsService::PluginsData('expressinwebfree', ['images']);
        if($ret['code'] == 0)
        {
            $this->assign('data', $ret['data']);
            return $this->fetch('../../../plugins/view/expressinwebfree/index/public/content');
        } else {
            return $ret['msg'];
        }
    }
}
?>