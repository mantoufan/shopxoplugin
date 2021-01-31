<?php
namespace app\plugins\optimizer;
use think\Controller;
use app\service\PluginsService;
use app\service\UserService;
class Hook extends Controller
{
    public function run($params = [])
    {
        $ret = array();
        if(!PluginsService::PluginsStatus('optimizer'))
        {
            return '';
        }
        if(isset($params['is_backend']) && $params['is_backend'] === true && !empty($params['hook_name']))
        {
            return DataReturn('返回描述', 0);
        // 默认返回视图
        } else {
            if(!empty($params['hook_name']))
            {
                switch($params['hook_name'])
                {
                    case 'plugins_common_header':
                        $ret = $this->getDNS();
                        break;
                    case 'plugins_common_page_bottom' :
                        $ret = $this->getJS();
                        break;
                    default :
                        $ret = '';
                }
                return $ret;
            } else {
                return '';
            }
        }
    }
    private function getDNS() {
        $ret = PluginsService::PluginsData('optimizer');
        if($ret['code'] == 0)
        {
            if (!empty($ret['data']['available_dns'])) {
                $ret = '';$wga_data = array();$wga_path = dirname(__FILE__) . '/wga/wga_data.php';
                if(file_exists($wga_path)) {
                    $wga_data = include($wga_path);
                    if (!empty($wga_data['domains'])) {
                        foreach($wga_data['domains'] as $v) {
                            $ret .= '<link rel="dns-prefetch" href="//' . $v . '">';
                        }
                    }
                }
                return $ret;
            }
        }
        return '';
    }
    private function getJS() {
        $ret = PluginsService::PluginsData('optimizer');
        if($ret['code'] == 0)
        {
            $ar = array(
                __MY_ROOT_PUBLIC__.'static/plugins/js/optimizer/index/style.js',
            );
            $conf = array(
                'cacheVersion' => MyC('home_static_cache_version'),
                'availableSeoBaiduPush' => !empty($ret['data']['available_seo_baidu_push']),
                'pluginsHomeUrl' => PluginsHomeUrl('optimizer', 'mtf', 'better')
            );
            $html = '<script>var pluginOptimizerConf = ' . json_encode($conf) . '</script>';
            foreach($ar as $k => $v) {
                $html .= '<script type="text/javascript" src="' . $v . '?v=' . MyC('home_static_cache_version') . '"></script>';
            }
            return $html;
        }
        return '';
    }
}
?>