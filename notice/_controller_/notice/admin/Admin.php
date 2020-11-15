<?php
namespace app\plugins\notice\admin;

use think\Controller;
use app\service\PluginsService;
use app\plugins\notice\wga\WGA;
use app\plugins\notice\service\Service;

/**
 * 通知 - 后台管理
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
        array('value' => 'wxpub', 'name' => '微信公众号'),
        array('value' => 'wxamp', 'name' => '微信小程序')
    );
    // 后台管理入口
    public function index($params = [])
    {
        $ret = PluginsService::PluginsData('notice');
        if($ret['code'] == 0)
        {
            $this->assign('sms_bys',  self::$sms_bys);
            $this->assign('wga_tip', WGA::tip());
            $this->assign('data', $ret['data']);
            return $this->fetch('../../../plugins/view/notice/admin/admin/index');
        } else {
            return $ret['msg'];
        }
    }
    public function saveinfo($params = [])
    {
        $ret = PluginsService::PluginsData('notice');
        if($ret['code'] == 0)
        {
            $this->assign('sms_bys',  self::$sms_bys);
            $this->assign('wga_tip', WGA::tip());
            $this->assign('data', $ret['data']);
            return $this->fetch('../../../plugins/view/notice/admin/admin/saveinfo');
        } else {
            return $ret['msg'];
        }
    }
    public function save($params = [])
    {
        $wga = new WGA();
        $rules = array(
            Service::root() . 'sourcecode/weixin/pages/buy/buy.js' => array(
                'wx.showLoading({title: \'提交中...\'});' => !empty($params['asn_wxamp_tpl']) ? 'wx.requestSubscribeMessage({tmplIds: [\'' . $params['asn_wxamp_tpl'] . '\']})' : ''
            )
        );
        include dirname(__FILE__) . '/mtfReplace.php';
        $mtfReplace = new \mtfReplace();
        $mtfReplace->append($rules);
        return $wga->save($params);
    }
}
?>