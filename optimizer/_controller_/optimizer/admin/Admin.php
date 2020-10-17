<?php
// +----------------------------------------------------------------------
// | ShopXO 国内领先企业级B2C免费开源电商系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011~2019 http://shopxo.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Devil
// +----------------------------------------------------------------------
namespace app\plugins\optimizer\admin;
use think\Controller;
use app\service\PluginsService;
use app\plugins\optimizer\wga\WGA;

class Admin extends Controller
{
    private static $conf = array(
        'pic_watermark_pos' =>  array(
            array('id' => 0, 'name' => '无水印', 'checked' => true),
            array('id' => 1, 'name' => '左上'),
            array('id' => 2, 'name' => '左下'),
            array('id' => 3, 'name' => '右上'),
            array('id' => 4, 'name' => '右下')
        )
    );

    public function index($params = [])
    {
        $ret = PluginsService::PluginsData('optimizer');
        if($ret['code'] == 0)
        {
            // 数据处理
            $this->assign('wga_tip', WGA::tip());
            $this->assign('conf', self::$conf);
            $this->assign('data', $ret['data']);
            return $this->fetch('../../../plugins/view/optimizer/admin/admin/index');
        } else {
            return $ret['msg'];
        }
    }

    public function saveinfo($params = [])
    {
        
        $ret = PluginsService::PluginsData('optimizer');
        if($ret['code'] == 0)
        {
            $this->assign('wga_tip', WGA::tip());
            $this->assign('conf', self::$conf);
            $this->assign('data', $ret['data']);
            return $this->fetch('../../../plugins/view/optimizer/admin/admin/saveinfo');
        } else {
            return $ret['msg'];
        }
    }

    public function save($params = [])
    {
        return WGA::save($params);
    }

    public function htcacess()
    {
        
    }
}
?>