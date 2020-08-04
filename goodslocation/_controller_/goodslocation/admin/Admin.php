<?php

namespace app\plugins\goodslocation\admin;



use think\Controller;

use app\service\PluginsService;

use app\plugins\goodslocation\service\Service;



class Admin extends Controller

{

    // 后台管理入口

    public function index($params = [])

    {             

        $this->assign('data', self::loadConfig());

        return $this->fetch('../../../plugins/view/goodslocation/admin/admin/index');

    }

    

    public function saveinfo($params = [])

    {

        $this->assign('data', self::loadConfig());

        return $this->fetch('../../../plugins/view/goodslocation/admin/admin/saveinfo');

    }



    public function save($params = [])

    {

        $ret = Service::saveConfig('common_baidu_map_ak', $params['common_baidu_map_ak']);

        return $ret !== FALSE ? DataReturn('保存成功', 0) : DataReturn('保存失败，请重试', -100);

    }



    private static function loadConfig() {

        $common_baidu_map_ak = Service::loadConfig('common_baidu_map_ak');

        return array(

            'common_baidu_map_ak' => $common_baidu_map_ak

        );

    }

}

?>