<?php
namespace app\plugins\servicepro\wga;
use app\service\PluginsService;
class WGA
{
    public static function save($params = [])
    {
        $r = @file_get_contents('https://k.os120.com/api/wga/verify?out_type=json&name=shopxoplugin_servicepro&des=正版验证&domain=' . $_SERVER['SERVER_NAME'], false, stream_context_create(array('http' => array('method' => "GET",'timeout' => 3))));
        if ($r) {
            $r = json_decode($r, true);
            if (isset($r['code']) && $r['code'] === -1) {
                return DataReturn($r['msg'], -1);
            }
        }
        if (isset($params['online_service'])) {
            $params['online_service'] = preg_replace("/\s+\n/", "\n", $params['online_service']);
        }
        if (!isset($params['overall_display'])) {
            $params['overall_display'] = '';
        }
        if (!isset($params['daovoice_display'])) {
            $params['daovoice_display'] = '';
        }
        return PluginsService::PluginsDataSave(['plugins'=>'servicepro', 'data'=>$params]);
    }
}
?>