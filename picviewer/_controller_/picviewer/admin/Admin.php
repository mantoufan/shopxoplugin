<?php
namespace app\plugins\picviewer\admin;
use think\Controller;
use app\service\PluginsService;
use app\plugins\picviewer\wga\WGA;
/**
 * 后台管理
 * @author   小宇
 * @blog     https://www.madfan.cn
 * @version  1.0.0
 * @datetime 2019-04-29T12:53:09+0800
 */
class Admin extends Controller
{
    // 后台管理入口
    public function index($params = [])
    {
        $ret = PluginsService::PluginsData('picviewer');
        if($ret['code'] == 0)
        {
            $this->assign('wga_tip', WGA::tip());
            $this->assign('data', $ret['data']);
            return $this->fetch('../../../plugins/view/picviewer/admin/admin/index');
        } else {
            return $ret['msg'];
        }
    }

	/**
     * 编辑页面
     * @author   小宇
     * @blog     https://www.madfan.cn
     * @version  1.0.0
     * @datetime 2019-04-29T12:54:01+0800
     * @param    [array]          $params [输入参数]
     */
    public function saveinfo($params = [])
    {
        $ret = PluginsService::PluginsData('picviewer');
        if($ret['code'] == 0)
        {
            $this->assign('wga_tip', WGA::tip());
            $this->assign('data', $ret['data']);
            return $this->fetch('../../../plugins/view/picviewer/admin/admin/saveinfo');
        } else {
            return $ret['msg'];
        }
    }
	/**
     * 数据保存
     * @author   小宇
     * @blog     https://www.madfan.cn
     * @version  1.0.0
     * @datetime 2019-04-29T12:54:08+0800
     * @param    [array]          $params [输入参数]
     */
    public function save($params = [])
    {
        return WGA::save($params);
    }
}
?>