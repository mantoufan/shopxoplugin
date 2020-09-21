<?php
namespace app\plugins\goodslocation\admin;

use think\Controller;
use app\service\PluginsService;
use app\plugins\goodslocation\service\Service;
use app\plugins\goodslocation\wga\WGA;

class Admin extends Controller
{
    // 后台管理入口
    public function index($params = [])
    {             
        $this->assign('wga_tip', WGA::tip());
        $this->assign('data', self::loadConfig());
        return $this->fetch('../../../plugins/view/goodslocation/admin/admin/index');
    }
    
    public function saveinfo($params = [])
    {
        $this->assign('wga_tip', WGA::tip());
        $this->assign('data', self::loadConfig());
        return $this->fetch('../../../plugins/view/goodslocation/admin/admin/saveinfo');
    }

    public function save($params = [])
    {
        return WGA::save($params);
    }

    private static function loadConfig() {
        $common_baidu_map_ak = Service::loadConfig('common_baidu_map_ak');
        return array(
            'common_baidu_map_ak' => $common_baidu_map_ak
        );
    }
}
?>