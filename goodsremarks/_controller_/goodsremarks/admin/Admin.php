<?php
namespace app\plugins\goodsremarks\admin;

use think\Controller;
use app\plugins\goodsremarks\service\BaseService;
use app\plugins\goodsremarks\wga\WGA;

class Admin extends Controller
{
    public function index($params = [])
    {
        $ret = BaseService::BaseConfig();
        if($ret['code'] == 0)
        {
            $this->assign('data', $ret['data']);
            $this->assign('wga_tip', WGA::tip());
            return $this->fetch('../../../plugins/view/goodsremarks/admin/admin/index');
        } else {
            return $ret['msg'];
        }
    }

    public function saveinfo($params = [])
    {
        $ret = BaseService::BaseConfig();
        if($ret['code'] == 0)
        {
            $this->assign('data', $ret['data']);
            $this->assign('wga_tip', WGA::tip());
            return $this->fetch('../../../plugins/view/goodsremarks/admin/admin/saveinfo');
        } else {
            return $ret['msg'];
        }
    }

    public function save($params = [])
    {
        return BaseService::BaseConfigSave($params);
    }
}
?>