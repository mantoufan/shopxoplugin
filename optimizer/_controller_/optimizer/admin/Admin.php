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
        $this->htaccess($params);
        return WGA::save($params);
    }

    public function htaccess($params)
    {
        $_root = dirname(__FILE__) . '/../../../../';
        $_p = $_root . '.htaccess';
        $_rules = array();
        $_querys = array('');
        if (isset($params['available_static'])) {
            $_rules []= 'js|css';
        }
        if (isset($params['available_pic_wga']) || isset($params['available_pic_safe']) || isset($params['pic_watermark'])) {
            $_rules []= 'jpg|jpeg|pngj';
            if (isset($params['available_pic_safe'])) {
                $_querys []= 'available_pic_safe=1';
            }
            if (!empty($params['pic_watermark'])) {
                $_querys []= 'pic_watermark=' . $params['pic_watermark'];
            }
            if (!empty($params['pic_watermark_pos'])) {
                $_querys []= 'pic_watermark_pos=' . $params['pic_watermark_pos'];
            }
        }
        if (count($_rules)) {
            $_rule = 'RewriteRule ^(.*).(' . implode('|', $_rules) . ')$ index.php?s=/index/plugins/index/pluginsname/optimizer/pluginscontrol/handler/pluginsaction/handler' . implode('&', $_querys) . '&p=\$1.\$2 [L]';
        } else {
            $_rule = '';
        }
        $_c = '<IfModule mod_rewrite.c>\n  RewriteEngine On' . "\n" . '  ' . $_rule . "\n" . '</IfModule>';
        if (file_exists($_p)) {
            $_c = file_get_contents($_p);
            if (stripos($_c, '/optimizer/') === false) {
                $_c = preg_replace('/(RewriteRule \^\(\.\*\)\$.*?\n)/', '$1'. $_rule ."\n", $_c);
            } else {
                $_c = preg_replace('/RewriteRule \^\(\.\*\)\.\(.*?\n/', $_rule ? $_rule ."\n" : '', $_c);
            }
        }
        file_put_contents($_p, $_c);
    }
}
?>