<?php
namespace app\plugins\expressinwebfree\admin;

use think\Controller;
use app\service\PluginsService;
use app\service\ExpressService;
use app\plugins\expressinwebfree\wga\WGA;
use app\plugins\expressinwebfree\service\Service;

/**
 * 快递查询接口 - 后台管理
 * @author   Shon Ng
 * @blog     https://www.madfan.cn
 * @version  1.0.0
 * @datetime 2020-09-17T11:39:08+0800
 */
class Admin extends Controller
{
    private static function getConf() {
        $available = array();
        
        $ret = Service::config();
        if ($ret['code'] == 0) {
            $apis = $ret['data']['apis'];
            foreach($apis as $key => $v) {
                $available[$key]= array('value' => 'pc', 'name' => $v['n'], 'checked' => true);
            }
        }
        return array(
            'available' => $available
        );
    }
    // 后台管理入口
    public function index($params = [])
    {
        $ret = PluginsService::PluginsData('expressinwebfree');
        if($ret['code'] == 0)
        {
			$this->assign('express_list', ExpressService::ExpressList());
            $this->assign('data', $ret['data']);
            $this->assign('conf', self::getConf($ret['data']));
            $this->assign('wga_tip', WGA::tip());
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
            $this->assign('conf', self::getConf($ret['data']));
            $this->assign('wga_tip', WGA::tip());
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
        if (!isset($params['available'])) {
            return DataReturn('至少选择一个渠道', -1);
        }
        $wga = new WGA();
        return $wga->save($params);
    }
}
?>