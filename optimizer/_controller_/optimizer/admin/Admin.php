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
        'watermark_pos' =>  array(
            0 => array('name' => '无水印', 'checked' => true),
            'left-top' => array('name' => '左上'),
            'left-bottom' => array('name' => '左下'),
            'right-top' => array('name' => '右上'),
            'right-bottom' => array('name' => '右下')
        ),
        'rewrite' => array(
            'Apache / Kangle' => '',
            'Nginx' => 'location / {' . "\n" .
                       '    if (!-e $request_filename){' . "\n" .
                       '        rewrite  ^(.*)$  /index.php?s=$1  last;   break;#shopXO及thinkphp伪静态规则' . "\n" .
                       '    }' . "\n" .
                       '    rewrite ^(.*.[js|css|jpg|jpeg|png])$ /index.php?s=index/plugins/index/pluginsname/optimizer/pluginscontrol/mtf/pluginsaction/better&path=$1;#shopXO加速优化插件伪静态规则' . "\n" .
                       '}',
            'IIS' => ''
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
        $ret = PluginsService::PluginsData('optimizer');
        if($ret['code'] == 0)
        {
            $needClearCache = false;
            if (!empty($params['cache_reset'])) {
                if ($params['cache_reset'] === '000') {
                    $needClearCache = true;
                }
                unset($params['cache_reset']);
            }
            if ($this->checkNeedClearCache($ret['data'], $params, 'available_pic') || 
                $this->checkNeedClearCache($ret['data'], $params, 'watermark_path') ||
                $this->checkNeedClearCache($ret['data'], $params, 'watermark_pos')) {
                $needClearCache = true;
            }
            if ($needClearCache) {
                $this->clearCache();
            }
        }
        return WGA::save($params);
    }

    public function clearCache()
    {
        $_root = str_replace('\\', '/', dirname(__FILE__)) . '/../../../../';
        forEach(glob($_root . self::$conf['cache_dir']. '*.*') as $file) {
            unlink($file);
        }
    }

    private function checkNeedClearCache($data, $params, $key) {
        if (isset($data[$key]) && !isset($params[$key]) || !isset($data[$key]) && isset($params[$key])) {
            return true;
        } else if (isset($data[$key]) && isset($params[$key]) && $data[$key] !== $params[$key]) {
            return true;
        }
        return false;
    }
}
?>