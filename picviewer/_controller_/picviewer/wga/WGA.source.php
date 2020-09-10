<?php
namespace app\plugins\picviewer\wga;
use app\service\PluginsService;
class WGA
{
    private static function wga() {
        $root = dirname(__FILE__);
        $config = self::config();
        $r = @file_get_contents('https://api.os120.com/wga/verify?out_type=json&name=shopxoplugin_' . $config['base']['plugins'] . '&version=' . $config['base']['version'] . '&des=正版验证&domain=' . $_SERVER['SERVER_NAME'], false, stream_context_create(array('http' => array('method' => "GET",'timeout' => 3))));
        if ($r) {
            $r = json_decode($r, true);
            if (isset($r['code']) && $r['code'] === -1) {
                return $r['msg'];
            }
            if (isset($r['data']) && isset($r['data']['tip']) && !empty($r['data']['tip'])) {
                file_put_contents($root . '/wga_tip.txt', $r['data']['tip']);
            } else if (file_exists($root . '/wga_tip.txt')){
                unlink($root. '/wga_tip.txt');
            }
        }
        return false;
    }
    public static function config() {
        return json_decode(file_get_contents(dirname(__FILE__) . '/../config.json'), true); 
    }
    public static function tip() {
        if (file_exists(dirname(__FILE__) . '/wga_tip.txt')){
            return '<div class="am-alert" data-am-alert><button type="button" class="am-close">&times;</button>' . file_get_contents(dirname(__FILE__) . '/wga_tip.txt') . '</div>';
        }
        return '';
    }
    public static function save($params = [])
    {
        $root = dirname(__FILE__);
        $res = self::wga();
        if ($res) {
            return DataReturn($res, -1);
        }
        if (is_dir($root . '/../mod/mtfPicViewer')) {
            self::move(array(
                array(
                    'from' => $root  . '/../mod/mtfPicViewer/jquery.mtfpicviewer.css',
                    'to' => $root . '/../../../..' . __MY_ROOT_PUBLIC__ . 'static/plugins/css/picviewer/index/jquery.mtfpicviewer.css'
                ),
                array(
                    'from' => $root  . '/../mod/mtfPicViewer/jquery.mtfpicviewer.js',
                    'to' => $root . '/../../../..' . __MY_ROOT_PUBLIC__ . 'static/plugins/js/picviewer/index/jquery.mtfpicviewer.js'
                ),
            ));
            self::deldir(array(
                $root . '/../mod'
            ));
        }
        $config = self::config();
        return PluginsService::PluginsDataSave(['plugins'=>$config['base']['plugins'], 'data'=>$params]);
    }
    public static function move($files) {
        foreach($files as $k => $v) {
            if (!file_exists($v['to'])) {
                rename($v['from'], $v['to']);
            }
        }
    }
    public static function deldir($dirs) {
        foreach($dirs as $k => $dir) {
            if (is_dir($dir)) {
                $dh = opendir($dir);
                while ($file = readdir($dh)) {
                    if($file != '.' && $file != '..') {
                        $fullpath=$dir . '/' . $file;
                        if(!is_dir($fullpath)) {
                            unlink($fullpath);
                        } else {
                            self::deldir($fullpath);
                        }
                    }
                }  
                closedir($dh);
                if(rmdir($dir)) {
                    return true;
                } else {
                    return false;
                }
            }
        }
    }
}
?>