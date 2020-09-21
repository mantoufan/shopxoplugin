<?php
namespace app\plugins\sitemap\wga;
use app\service\PluginsService;
use think\Db;
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
    public static function getGenerateWhereByTable($table, $key)
    {
        if ($key === 0) {
            $res = self::wga();
            if ($res) {
                return DataReturn($res, -1);
            }
        }
        switch ($table) {
            case 'goods':
                $where = 'is_shelves = 1';
            break;
            case 'article':
                $where = 'is_enable = 1';
            break;
            default:
                $where = '1';
        }
        return Db::name($table) -> field('id, upd_time') -> where($where)-> order('upd_time DESC')  -> select();
    }
}
?>