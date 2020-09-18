<?php
namespace app\plugins\expressinwebfree\index;

use think\Controller;
use app\plugins\expressinwebfree\service\Service;

/**
 * 快递查询接口 - 后台管理
 * @author   Shon Ng
 * @blog     https://www.madfan.cn
 * @version  1.0.0
 * @datetime 2020-09-17T11:39:08+0800
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
        $params['check_available'] = true;
        $ret = Service::config($params);
        if ($ret['code'] == 0) {
            $apis = $ret['data']['apis'];
            $html = '<div class="expressinwebfree-tab' . (count($apis) === 1 ? ' expressinwebfree-tab-none' : '') . '">';
            foreach($apis as $k => $v) {
                $html.= '<a data-key="' . $k . '" class="am-btn" ' . (isset($v['href']) ? 'href="' . $v['u'] . '" target="_blank"': '') . '>' . $v['n'] . '查</a>';
            }
            $html.= '</div><iframe id="plugins-expressinwebfree-popup-iframe"></iframe><script>var expressinwebfree_apis = JSON.parse(\'' . json_encode($apis) . '\'.replace(/amp;/g, \'\'));$(\'.expressinwebfree-tab a\').on(\'click\', function(){var key=$(this).data(\'key\'),url=expressinwebfree_apis[key][\'u\'];$(this).addClass(\'expressinwebfree_a_active\').siblings().removeClass(\'expressinwebfree_a_active\');if(key===\'kuaidi100\'&&navigator.userAgent.indexOf(\'MicroMessenger\')>-1){top.location.href=url}else{$(\'#plugins-expressinwebfree-popup-iframe\').attr(\'src\',url)}});$(\'.expressinwebfree-tab a\')[0].click()</script>'; 
            return DataReturn('处理成功', 0, $html);
        } else {
            return DataReturn($ret['msg'], -100);
        }
    }
}
?>