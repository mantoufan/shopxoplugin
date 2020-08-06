<?php
namespace app\plugins\expressinwebfree\wga;

use think\Controller;
use app\service\PluginsService;
use app\plugins\expressinwebfree\service\Service;

class WGA extends Controller
{
    private static function wga() {
        $r = @file_get_contents('https://k.os120.com/api/wga/verify?out_type=json&name=shopxoplugin_expressinwebfree&des=正版验证&domain=' . $_SERVER['SERVER_NAME'], false, stream_context_create(array('http' => array('method' => "GET",'timeout' => 3))));
        if ($r) {
            $r = json_decode($r, true);
            if (isset($r['code']) && $r['code'] === -1) {
                return $r['msg'];
            }
        }
        return false;
    }
    public function html($params = [])
    {
        if (rand(0, 100) > 85) {
            $res = self::wga();
            if ($res) {
                return '<script defer>AMUI.dialog.alert({title: \'快递查询插件\', content: \'' . $res . '\'})</script>';
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
    public function save($params = [])
    {
        $res = self::wga();
        if ($res) {
            return DataReturn($res, -1);
        }
        return PluginsService::PluginsDataSave(['plugins'=>'expressinwebfree', 'data'=>$params]);
    }
}
?>