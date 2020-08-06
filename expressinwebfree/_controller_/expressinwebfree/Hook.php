<?php
namespace app\plugins\expressinwebfree;

use think\Controller;
use app\service\PluginsService;
use app\plugins\expressinwebfree\wga\WGA;

/**
 * 快递鸟API接口 - 钩子入口
 * @author   GuoGuo
 * @blog     http://gadmin.cojz8.com/
 * @version  1.0.0
 * @datetime 2016-12-01T21:51:08+0800
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
                case 'plugins_view_admin_order_list_operate' :
                case 'plugins_view_admin_order_list_operation' :
                case 'plugins_view_index_order_list_operate' :
                case 'plugins_view_index_order_list_operation' :
                case 'plugins_view_index_order_detail_operate' :
                case 'plugins_view_index_order_detail_operation' :
                    $ret = $this->operation($params);
                    break;

        		// 弹窗代码
                case 'plugins_view_common_bottom' :
                case 'plugins_admin_view_common_bottom' :
                    $ret = $this->html($params);
                    break;

                // 页面底部
                case 'plugins_common_page_bottom' :
                case 'plugins_admin_common_page_bottom' :
                    $ret = $this->js($params);
                    break;

                // header代码
                case 'plugins_common_header' :
                case 'plugins_admin_common_header' :
                    $ret = $this->css($params);
                    break;
            }
        }
        return $ret;
    }

    /**
     * css
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  1.0.0
     * @datetime 2019-02-06T16:16:34+0800
     * @param    [array]          $params [输入参数]
     */
    public function css($params = [])
    {
        return '<style type="text/css">
                    #plugins-expressinwebfree-popup p { font-size: 16px; color: #FF9800; font-weight: 500; padding: 1rem; text-align: center; margin: 0; }
        			#plugins-expressinwebfree-popup .am-list-static > li { padding: .8rem 1rem; }
        			#plugins-expressinwebfree-popup .am-popup-bd { padding: 0; height: 100%; }
                    #plugins-expressinwebfree-popup .am-list > li { border: 1px dashed #dedede; border-width: 1px 0; }
                    #plugins-expressinwebfree-popup .am-popup-bd iframe { width: 100%; height: 100%; }
                    .order-base .operation .plugins-expressinwebfree-submit { display: inline-block; }
                </style>';
    }

    /**
     * js
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  1.0.0
     * @datetime 2019-02-06T16:16:34+0800
     * @param    [array]          $params [输入参数]
     */
    public function js($params = [])
    {
        return '<script type="text/javascript">
                $(function()
                {
                	$(".plugins-expressinwebfree-submit").on("click", function()
                	{
						$.ajax({
							url:"'.PluginsHomeUrl('expressinwebfree', 'index', 'getexpinfo').'",
							type:"POST",
							dataType:"json",
							data: {express_id: $(this).data("exp-id"), express_number: $(this).data("exp-num")},
							success:function(result)
							{
								$("#plugins-expressinwebfree-popup").modal();
								if(result.code == 0)
								{
									$("#plugins-expressinwebfree-popup .am-popup-bd").html(result.data);
								} else {
									$("#plugins-expressinwebfree-popup .am-popup-bd").html(result.data || result.msg);
								}
							}
						});
					});
                });
                </script>';
    }

    /**
     * 视图
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  1.0.0
     * @datetime 2019-02-06T16:16:34+0800
     * @param    [array]          $params [输入参数]
     */
    public function html($params = [])
    {
        $wga= new WGA();
        return $wga->html($params);
    }

    /**
     * 操作
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  1.0.0
     * @datetime 2019-02-06T16:16:34+0800
     * @param    [array]          $params [输入参数]
     */
    public function operation($params = [])
    {
    	if(!empty($params['data']) && isset($params['data']['status']) && in_array($params['data']['status'], [3,4]))
    	{
    		return '<button class="am-btn am-btn-warning am-btn-xs am-radius am-btn-block plugins-expressinwebfree-submit" data-exp-id="'.$params['data']['express_id'].'" data-exp-num="'.$params['data']['express_number'].'"><i class="am-icon-cube"></i> 物流</button>';
    	}
    	return '';
    }
}
?>