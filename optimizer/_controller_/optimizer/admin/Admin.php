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
        'cache_dir' => 'runtime/cache/optimizer/',
        'cache_time' => 60 * 60 * 6,
        'task_num' => 8,
        'watermark_pos' =>  array(
            array('id' => '', 'name' => '无水印', 'checked' => true),
            array('id' => 'left-top', 'name' => '左上'),
            array('id' => 'left-bottom', 'name' => '左下'),
            array('id' => 'right-top', 'name' => '右上'),
            array('id' => 'right-bottom', 'name' => '右下')
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
        $_root = str_replace('\\', '/', dirname(__FILE__)) . '/../../../../';
        $_p = $_root . '.htaccess';
        $_rules = array();
        $_querys = array();
        if (isset($params['available_static'])) {
            $_rules []= 'js|css';
        }
        if (isset($params['available_pic_wga']) || isset($params['anti_stealing_link_pic']) || isset($params['watermark_path'])) {
            $_rules []= 'jpg|jpeg|png';
            if (isset($params['anti_stealing_link_pic'])) {
                $_querys []= 'anti_stealing_link_pic=1';
            }
            if (!empty($params['watermark_path'])) {
                $_querys []= 'watermark_path=' . $params['watermark_path'];
            }
            if (!empty($params['watermark_pos'])) {
                $_querys []= 'watermark_pos=' . $params['watermark_pos'];
            }
        }

        $_querys []= 'cache_dir=' . $_root . self::$conf['cache_dir'];
        $_querys []= 'cache_time=' . (!empty($params['cache_time']) ? $params['cache_time'] : self::$conf['cache_time']);
        $_querys []= 'task_num=' . (!empty($params['task_num']) ? $params['task_num'] : self::$conf['task_num']);
        
        if (count($_rules)) {
            $_rule = 'RewriteRule ^(.*).(' . implode('|', $_rules) . ')$ /application/plugins/optimizer/index/mtfBetter/mtfBetter.php?' . implode('&', $_querys) . '&path=' . $_root . '\$1.\$2 [L]';
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