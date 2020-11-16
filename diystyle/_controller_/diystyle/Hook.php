<?php
namespace app\plugins\diystyle;

use think\Controller;
use app\service\PluginsService;

/**
 * 自定义风格 - 钩子入口
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
                // 操作按钮
                case 'plugins_common_header' :
                    $ret = $this->getCode($params);
                break;
            }
        }
        return $ret;
    }

    // 获取调用代码
    private function getCode()
    {
        $ret = PluginsService::PluginsData('diystyle');
        if($ret['code'] == 0)
        {
            if (!empty($ret['data']['code'])) {
                return '<style>div, span, applet, object, iframe, h1, h2, h3, h4, h5, h6, p, pre, a, abbr, acronym, address, big, cite, code, del, dfn, em, img, ins, kbd, q, s, samp, small, strike, strong, sub, sup, tt, var, b, u, i, center, dl, dt, dd, ol, ul, li, fieldset, form, legend, table, caption, tbody, tfoot, thead, tr, th, td, article, aside, canvas, details, embed, figure, figcaption, footer, header, hgroup, menu, nav, output, ruby, section, summary, time, mark, audio, video {font-family:inherit}</style>'.htmlspecialchars_decode($ret['data']['code']);
            } else {
                return '';
            }
        } else {
            return $ret['msg'];
        }
    }
}
?>