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

    private function getJS()
    {
        $ret = PluginsService::PluginsData('optimizer');
        if($ret['code'] == 0)
        {
            $ar = array(
                __MY_ROOT_PUBLIC__.'static/plugins/js/optimizer/index/style.js',
            );
            $conf = array(
                'available_seo_baidu_push' => $ret['data']['available_seo_baidu_push']
            );
            $html = '<script>var plugin_optimizer_conf = ' . json_encode($conf) . '</script>';
            foreach($ar as $k => $v) {
                $html .= '<script type="text/javascript" src="' . $v . '"></script>';
            }
            return $html;
        }
    }
}
?>