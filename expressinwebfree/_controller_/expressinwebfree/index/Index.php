<?php
namespace app\plugins\expressinwebfree\index;

use think\Controller;
use app\service\PluginsService;

/**
 * 快递鸟API接口 - 前端
 * @author   GuoGuo
 * @blog     http://gadmin.cojz8.com/
 * @version  1.0.0
 * @datetime 2016-12-01T21:51:08+0800
 */
class Index extends Controller
{
    /**
     * 获取物流信息
     * @author   Devil
     * @blog    http://gong.gg/
     * @version 1.0.0
     * @date    2019-03-12
     * @desc    description
     * @param   [array]          $params [输入参数]
     * @return  [type]                   [description]
     */
    public function getexpinfo($params = [])
    {
        // html
        $html = '';

        // 请求参数
        $p = [
            [
                'checked_type'      => 'empty',
                'key_name'          => 'express_id',
                'error_msg'         => '快递id有误',
            ],
            [
                'checked_type'      => 'empty',
                'key_name'          => 'express_number',
                'error_msg'         => '快递单号有误',
            ],
        ];
        $ret = ParamsChecked($params, $p);
        if($ret !== true)
        {
            return DataReturn($ret, -1, '<p>'.$ret.'</p>');
        }

        // 获取配置数据
        $ret = PluginsService::PluginsData('expressinwebfree');
        if($ret['code'] == 0)
        {
            // 是否配置物流代码
            if(empty($ret['data']['express_ids'][$params['express_id']]))
            {
                $com = '';
            } else {
                $com = $ret['data']['express_ids'][$params['express_id']];
            }

            $url = 'https://m.kuaidi100.com/app/query/?coname=' . substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'),0,5) . '&com=' . $com . '&nu=' . trim($params['express_number']) . '&callbackurl=' . $_SERVER['HTTP_REFERER'];

            $html = '<script>var expressinwebfree_url = "' . $url . '".replace(/amp;/g, \'\');';
            if (stripos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
                $html.= 'window.location.href = expressinwebfree_url;</script>';
            } else {
                $html.= '</script><iframe id="plugins-expressinwebfree-popup-iframe"></iframe><script>document.getElementById(\'plugins-expressinwebfree-popup-iframe\').src = expressinwebfree_url;</script>';
            }
            
            return DataReturn('处理成功', 0, $html);
        } else {
            return DataReturn($ret['msg'], -100);
        }
    }
}
?>