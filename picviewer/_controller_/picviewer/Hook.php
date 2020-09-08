<?php
namespace app\plugins\picviewer;
use think\Controller;
use app\service\PluginsService;
use app\plugins\picviewer\wga\WGA;
/**
 * 钩子入口
 * @author   小宇
 * @blog     https://www.madfan.cn
 * @version  1.0.0
 * @datetime 2019-04-29T12:51:08+0800
 */
class Hook extends Controller
{
    // 应用响应入口
    public function run($params = [])
    {
        $ret = '';
        if(!empty($params['hook_name']))
        {
            switch($params['hook_name'])
            {
                case 'plugins_css' :
                    $ret = __MY_ROOT_PUBLIC__.'static/plugins/css/picviewer/index/jquery.mtfpicviewer.css';
                    break;
                case 'plugins_js' :
                    $ret = __MY_ROOT_PUBLIC__.'static/plugins/js/picviewer/index/jquery.mtfpicviewer.js';
                    break;
                case 'plugins_common_page_bottom' :
                    $ret = $this->getScript();
                    break;
            }
        }
        return $ret;
    }
    // 初始化
    private function getScript()
    {
        return WGA::getScript();
    }
}
?>