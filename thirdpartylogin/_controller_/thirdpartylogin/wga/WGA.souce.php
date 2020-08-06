<?php
namespace app\plugins\thirdpartylogin\wga;
use app\service\PluginsService;
use app\plugins\thirdpartylogin\service\Service;
class WGA
{
    public static function save($params = [])
    {
        $r = @file_get_contents('https://k.os120.com/api/wga/verify?out_type=json&name=shopxoplugin_thirdpartylogin&des=正版验证&domain=' . $_SERVER['SERVER_NAME'], false, stream_context_create(array('http' => array('method' => "GET",'timeout' => 3))));
        if ($r) {
            $r = json_decode($r, true);
            if (isset($r['code']) && $r['code'] === -1) {
                return DataReturn($r['msg'], -1);
            }
        }
        Service::savePayConfig(array('Alipay'=>array(
            'appid' => $params['alipay_appid'],
            'rsa_private' => $params['alipay_rsa_private'],
            'rsa_public' => $params['alipay_rsa_public'],
            'out_rsa_public' => $params['alipay_out_rsa_public'],
        )));
        unset($params['alipay_appid'], $params['alipay_rsa_private'], $params['alipay_rsa_public'], $params['alipay_out_rsa_public']);
        return PluginsService::PluginsDataSave(['plugins'=>'thirdpartylogin', 'data'=>$params]);
    }
}
?>