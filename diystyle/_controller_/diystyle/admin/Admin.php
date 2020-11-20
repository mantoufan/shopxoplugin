<?php
namespace app\plugins\diystyle\admin;

use think\Controller;
use app\service\PluginsService;
use app\plugins\diystyle\wga\WGA;
use app\plugins\diystyle\service\Service;

/**
 * 自定义风格 - 后台管理
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
        $ret = PluginsService::PluginsData('diystyle');
        if($ret['code'] == 0)
        {
            // 读取模版
            $ret['data']['tpl'] = Service::tpl();

            if (!empty($ret['data']['code'])) {
                $ret['data']['code'] = htmlspecialchars_decode($ret['data']['code']);
            }
            $this->assign('wga_tip', WGA::tip());
            $this->assign('data', $ret['data']);
            $this->assign('conf', Service::config());
            return $this->fetch('../../../plugins/view/diystyle/admin/admin/index');
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
        $ret = PluginsService::PluginsData('diystyle');
        if($ret['code'] == 0)
        {
            // 读取模版
            $ret['data']['tpl'] = Service::tpl();

            if (!empty($ret['data']['code'])) {
                $ret['data']['code'] = htmlspecialchars_decode($ret['data']['code']);
            }
            $this->assign('wga_tip', WGA::tip());
            $this->assign('data', $ret['data']);
            $this->assign('conf', Service::config());
            return $this->fetch('../../../plugins/view/diystyle/admin/admin/saveinfo');
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
        $ret = PluginsService::PluginsData('diystyle');
        if($ret['code'] == 0)
        {
            if (!empty($ret['data']['tpl_id'])) {
                if (!empty($params['tpl_id']) && $params['tpl_id'] !== $ret['data']['tpl_id'] || empty($params['tpl_id'])) {
                    Service::append($ret['data']['tpl_id'], 0);
                }
            }
        }
        return WGA::save($params);
    }
}
?>