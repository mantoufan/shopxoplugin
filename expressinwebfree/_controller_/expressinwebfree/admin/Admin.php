<?php
namespace app\plugins\expressinwebfree\admin;

use think\Controller;
use app\service\PluginsService;
use app\service\ExpressService;
use app\plugins\expressinwebfree\wga\WGA;

/**
 * 快递鸟API接口 - 后台管理
 * @author   GuoGuo
 * @blog     http://gadmin.cojz8.com/
 * @version  1.0.0
 * @datetime 2016-12-01T21:51:08+0800
 */
class Admin extends Controller
{
    // 后台管理入口
    public function index($params = [])
    {
        $ret = PluginsService::PluginsData('expressinwebfree');
        if($ret['code'] == 0)
        {
			$this->assign('express_list', ExpressService::ExpressList());
            $this->assign('data', $ret['data']);
            return $this->fetch('../../../plugins/view/expressinwebfree/admin/admin/index');
        } else {
            return $ret['msg'];
        }
    }

	/**
     * 编辑页面
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  1.0.0
     * @datetime 2019-02-07T08:21:54+0800
     * @param    [array]          $params [输入参数]
     */
    public function saveinfo($params = [])
    {
        $ret = PluginsService::PluginsData('expressinwebfree');
        if($ret['code'] == 0)
        {
			$this->assign('express_list', ExpressService::ExpressList());
            $this->assign('data', $ret['data']);
            return $this->fetch('../../../plugins/view/expressinwebfree/admin/admin/saveinfo');
        } else {
            return $ret['msg'];
        }
    }
	    /**
     * 数据保存
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  1.0.0
     * @datetime 2019-02-07T08:21:54+0800
     * @param    [array]          $params [输入参数]
     */
    public function save($params = [])
    {
        $wga = new WGA();
        return $wga->save($params);
    }
}
?>