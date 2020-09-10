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
                    $ret = array(
                        __MY_ROOT_PUBLIC__.'static/plugins/js/picviewer/index/jquery.mtfpicviewer.js',
                        __MY_ROOT_PUBLIC__.'static/plugins/js/picviewer/index/rgbaster.min.js',
                        __MY_ROOT_PUBLIC__.'static/plugins/js/picviewer/index/style.js',
                    );
                    break;
                case 'plugins_common_header' :
                    $ret = $this->getStyle();
                    break;
                case 'plugins_common_page_bottom' :
                    $ret = $this->getScript();
                    break;
            }
        }
        return $ret;
    }
    // 获取样式
    private function getStyle()
    {
        $ret = PluginsService::PluginsData('picviewer');
        if($ret['code'] == 0)
        {
            if (isset($ret['data']['available']) && $ret['data']['available']) {
                $maxWidth = isset($ret['data']['max_width']) && !empty($ret['data']['max_width']) ? "max-width: " . $ret['data']['max_width'] . ";" : '';
                $backgroundColor = isset($ret['data']['background_color']) && !empty($ret['data']['background_color']) ? "background-color: " . $ret['data']['background_color'] . ";" : '';
                return '<style>.plugin-picviewer {' . $maxWidth . $backgroundColor . '}</style>';
            } else {
                return '';
            }           
        } else {
            return $ret['msg'];
        }
    }
    // 获取脚本
    private function getScript()
    {
        $ret = PluginsService::PluginsData('picviewer');
        if($ret['code'] == 0)
        {
            if (isset($ret['data']['available']) && $ret['data']['available']) { 
                $opt = "{selector: 'img', attrSelector: 'src', className: 'plugin-picviewer'";
                if (isset($ret['data']['background_color_auto']) && !empty($ret['data']['background_color_auto'])) {
                    $setBgWithDominantColor = '(curIndex) {pluginPicviewer.setBgWithDominantColor(curIndex, \'' . (isset($ret['data']['background_color']) && !empty($ret['data']['background_color']) ? $ret['data']['background_color'] : '') . '\')}';
                    $opt.= ', onChange' . $setBgWithDominantColor . ', onOpen' . $setBgWithDominantColor;
                }
                if (isset($ret['data']['scroll_auto']) && !empty($ret['data']['scroll_auto'])) {
                    $opt.= ', onClose(curIndex) {pluginPicviewer.scrollAuto(curIndex)}';
                }
                return "<script>$('.detail-content, .article-content, .customview-content').mtfpicviewer(" . $opt . "});$('.goods-comment-content').mtfpicviewer(" . $opt . ", parentSelector: '.comment-images'});</script>";
            } else {
                return '';
            }           
        } else {
            return $ret['msg'];
        }
    }
}
?>