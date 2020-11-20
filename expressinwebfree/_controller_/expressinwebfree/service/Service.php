<?php
namespace app\plugins\expressinwebfree\service;
use app\service\PluginsService;
class Service {
    public static function root() {
		return str_replace('\\', '/', dirname(__FILE__)) . '/../../../../';
	}
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
                    'ickd' => array(
                        'n' => '爱查快递',
                        'u' => 'https://m.ickd.cn/result.html#no=' . $express_number . '&com=' . ($com ? $com : 'auto')
                    ),
                    'kuaidi100' => array(
                        'n' => '快递100',
                        'u' => 'https://m.kuaidi100.com/app/query/?coname=' . substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'),0,5) . '&com=' . $com . '&nu=' . $express_number . '&callbackurl=' . $_SERVER['HTTP_REFERER'],
                        'href' => true
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

    public static function append() {
        include_once dirname(__FILE__) . '/mtfReplace.php';
        $root = self::root();
        $mtfReplace = new \mtfReplace();
        $mtfReplace->append(array(
			$root . 'sourcecode/weixin/pages/user-order/user-order.wxml' => array(
				'hover-class="none">催催</button>' => '<navigator wx:if="{{item.status == 3}}" target="miniProgram" path="pages/result/result?nu={{item.express_number}}&com={{item.express_id_expressinwebfree}}&querysource=third_xcx" app-id="wx6885acbedba59c14" style="display:inline-block"><button size="mini" class="br">物流</button></navigator>',
            ),
            $root . 'sourcecode/weixin/app.json' => array(
                '/("debug": true,\n)  "navigateToMiniProgramAppIdList": \[".*?"\],\n/' => '$1',
				'/("debug": true,\n)/' => '$1  "navigateToMiniProgramAppIdList": ["wx6885acbedba59c14"],' . "\n",
            ),
            $root . 'application/api/controller/Order.php' => array(
                'PaymentService::BuyPaymentList([\'is_enable\'=>1, \'is_open_user\'=>1]);' => '// 快递查询多渠道版' . "\n" .
                'if (!empty($data[\'data\'])) {' . "\n" .
                '    $ret = \app\service\PluginsService::PluginsData(\'expressinwebfree\');' . "\n" .
                '    if($ret[\'code\'] === 0)' . "\n" .
                '    {' . "\n" .
                '        $express_ids = $ret[\'data\'][\'express_ids\'];' . "\n" .
                '    }' . "\n" .
                '    foreach ($data[\'data\'] as $k => $v) {' . "\n" .
                '        if (!empty($v[\'express_id\'])) {' . "\n" .
                '            $data[\'data\'][$k][\'express_id_expressinwebfree\'] = !empty($express_ids[$v[\'express_id\']]) ? $express_ids[$v[\'express_id\']] : \'\';' . "\n" .
                '        }' . "\n" .
                '    }' . "\n" .
                '}'
            )
        ));
    }
}
?>