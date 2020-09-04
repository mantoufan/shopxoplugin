<?php
namespace app\plugins\thirdpartylogin\admin;

use think\Controller;
use app\service\PluginsService;
use app\plugins\thirdpartylogin\service\Service;
use app\plugins\thirdpartylogin\wga\WGA;

class Admin extends Controller
{
    public function index($params = [])
    {

        $data = Service::loadData();
        if ($data)
        {
            $this->assign('data', $data);
            $this->assign('config', Service::config());
            $this->assign('wga_tip', WGA::tip());
            return $this->fetch('../../../plugins/view/thirdpartylogin/admin/admin/index');
        } else {
            return $ret['msg'];
        }
    }

    public function saveinfo($params = [])
    {
        $data = Service::loadData();
        if ($data)
        {
            $this->assign('data', $data);
            $config = Service::config();
            foreach ($config as $party => $v) {
                $config[$party]['callback'] = Service::PluginsHomeUrl(PluginsHomeUrl('thirdpartylogin', 'auth', 'callback', array('party' => $party)), $party, 'admin');
            }
            $this->assign('config', $config);
            $this->assign('wga_tip', WGA::tip());
            return $this->fetch('../../../plugins/view/thirdpartylogin/admin/admin/saveinfo');
        } else {
            return $ret['msg'];
        }
    }

    public function save($params = [])
    {
        return WGA::save($params);
    }
}
?>