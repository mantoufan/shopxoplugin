<?php
namespace app\plugins\orderdeliverynotice\admin;

use think\Controller;
use app\service\PluginsService;
use app\plugins\orderdeliverynotice\wga\WGA;

/**
 * 国际手机号 - 后台管理
 * @author   小宇
 * @blog     https://www.madfan.cn/
 * @version  1.0.0
 * @datetime 2020-05-23T11:53:08+0800
 */
class Admin extends Controller
{
    private static $sms_bys =  array(
        array('value' => 'sms', 'name' => '手机'),
        array('value' => 'mail', 'name' => '邮箱'),
        array('value' => 'wxpub', 'name' => '微信公众号')
    );
    // 后台管理入口
    public function index($params = [])
    {
        $ret = PluginsService::PluginsData('orderdeliverynotice');
        if($ret['code'] == 0)
        {
            $this->assign('sms_bys',  self::$sms_bys);
            $this->assign('data', $ret['data']);
            return $this->fetch('../../../plugins/view/orderdeliverynotice/admin/admin/index');
        } else {
            return $ret['msg'];
        }
    }
    public function saveinfo($params = [])
    {
        $ret = PluginsService::PluginsData('orderdeliverynotice');
        if($ret['code'] == 0)
        {
            $this->assign('sms_bys',  self::$sms_bys);
            $this->assign('data', $ret['data']);
            return $this->fetch('../../../plugins/view/orderdeliverynotice/admin/admin/saveinfo');
        } else {
            return $ret['msg'];
        }
    }
    public function save($params = [])
    {
        $wga = new WGA();
        return $wga->save($params);
    }
}
?>