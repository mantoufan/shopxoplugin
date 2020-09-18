<?php
namespace app\plugins\expressinwebfree\service;
use app\service\PluginsService;
class Service {
    public static function config($params = array())
    {
        // 获取配置数据
        $ret = PluginsService::PluginsData('expressinwebfree');
        if($ret['code'] == 0)
        {
            $com = '';
            $express_number = '';
            if(isset($params['express_id']) && !empty($ret['data']['express_ids'][$params['express_id']])) {
                $com = $ret['data']['express_ids'][$params['express_id']];
            }
            if (isset($params['express_number'])) {
                $express_number = trim($params['express_number']);
            }
            $apis = array(
                    'kuaidi100' => array(
                        'n' => '快递100',
                        'u' => 'https://m.kuaidi100.com/app/query/?coname=' . substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'),0,5) . '&com=' . $com . '&nu=' . $express_number . '&callbackurl=' . $_SERVER['HTTP_REFERER'],
                    ),
                    'ickd' => array(
                        'n' => '爱查快递',
                        'u' => 'https://m.ickd.cn/result.html#no=' . $express_number . '&com=' . ($com ? $com : 'auto')
                    ),
                    'baidu' => array(
                        'n' => '百度',
                        'u' => 'https://m.baidu.com/from=1012852q/ssid=0/s?word=%E5%BF%AB%E9%80%92%E6%9F%A5%E8%AF%A2%20' .$express_number,
                        'href' => true
                    )
            );
            $available = array();$apis_order = array();
            if (isset($params['check_available']) && isset($ret['data']['available'])) {
                $available = explode(',', $ret['data']['available']);
            }
            foreach($apis as $key => $v) {
                if (empty($available) || in_array($key, $available)) {
                    $apis_order[] = isset($ret['data']['order_' . $key]) ? $ret['data']['order_' . $key] : 0;
                } else {
                    unset($apis[$key]);
                }
            }
            array_multisort($apis_order, SORT_DESC, SORT_NUMERIC, $apis);
            if (stripos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
                $apis = array_reverse($apis);
            }
            
            return array(
                'code' => 0,
                'data' => array(
                    'apis' => $apis
                )
            );
        } else {
            return $ret;
        }
    }
}
?>