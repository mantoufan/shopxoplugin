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

namespace app\plugins\servicepro\admin;

use think\Controller;

use app\service\PluginsService;

/**

 * 在线客服 - 管理

 * @author   Devil

 * @blog     http://gong.gg/

 * @version  0.0.1

 * @datetime 2016-12-01T21:51:08+0800

 */

class Admin extends Controller

{

    private static $is_whether_list =  [

        0 => array('id' => 0, 'name' => '首页'),

        1 => array('id' => 1, 'name' => '全局', 'checked' => true)

    ];

    private static $dao_voice_list = [

        0 => array('id' => 0, 'name' => '全部用户（含匿名访客）', 'checked' => true),

        1 => array('id' => 1, 'name' => '仅登录用户'),

        2 => array('id' => 2, 'name' => '停用')

    ];

    private static $display_list =  [

        0 => array('value' => 'pc', 'name' => 'PC版', 'checked' => true),

        1 => array('value' => 'mobile', 'name' => 'H5移动版', 'checked' => true)

    ];

    /**

     * 首页

     * @author   Devil

     * @blog     http://gong.gg/

     * @version  1.0.0

     * @datetime 2019-02-07T08:21:54+0800

     * @param    [array]          $params [输入参数]

     */

    public function index($params = [])

    {

        $ret = PluginsService::PluginsData('servicepro');

        if($ret['code'] == 0)

        {

            // 数据处理

            $ret['data']['online_service'] = str_replace("\n", '<br />', $ret['data']['online_service']);

            $this->assign('is_whether_list', self::$is_whether_list);

            $this->assign('dao_voice_list',  self::$dao_voice_list);

            $this->assign('display_list',  self::$display_list);

            $this->assign('data', $ret['data']);

            return $this->fetch('../../../plugins/view/servicepro/admin/admin/index');

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

        $ret = PluginsService::PluginsData('servicepro');

        if($ret['code'] == 0)

        {

            

            $this->assign('is_whether_list', self::$is_whether_list);

            $this->assign('dao_voice_list',  self::$dao_voice_list);

            $this->assign('display_list',  self::$display_list);

            $this->assign('data', $ret['data']);

            return $this->fetch('../../../plugins/view/servicepro/admin/admin/saveinfo');

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

        if (isset($params['online_service'])) {

            $params['online_service'] = preg_replace("/\s+\n/", "\n", $params['online_service']);

        }

        if (!isset($params['overall_display'])) {
            $params['overall_display'] = '';
        }

        if (!isset($params['daovoice_display'])) {
            $params['daovoice_display'] = '';
        }

        return PluginsService::PluginsDataSave(['plugins'=>'servicepro', 'data'=>$params]);

    }

}

?>