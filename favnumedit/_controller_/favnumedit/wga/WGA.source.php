<?php
namespace app\plugins\favnumedit\wga;
use app\service\PluginsService;
use app\plugins\favnumedit\service\Service;
class WGA
{
    private static function wga() {
        $root = dirname(__FILE__);
        $config = self::config();
        $_domain_ar = !empty($_SERVER['HTTP_HOST']) ? explode(':', $_SERVER['HTTP_HOST']) : array($_SERVER['SERVER_NAME']);
        $_domain = reset($_domain_ar);
        $r = @file_get_contents('https://api.os120.com/wga/verify?out_type=json&name=shopxoplugin_' . $config['base']['plugins'] . '&version=' . $config['base']['version'] . '&des=正版验证&domain=' . $_domain, false, stream_context_create(array('http' => array('method' => "GET",'timeout' => 3))));
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
    public static function check($a, $b) {
        return isset($a[$b]) && is_numeric($a[$b]);
    }
    public static function save($params = [])
    {
        $root = dirname(__FILE__);
        $res = self::wga();
        if ($res) {
            return DataReturn($res, -1);
        }

        $fields = array();
        $fav_add_count_min = 0;
        $fav_add_count_max = PHP_INT_MAX;
        if (self::check($params, 'fav_add_count_min') && self::check($params, 'fav_add_count_max')) {
            if ($params['fav_add_count_min'] <= $params['fav_add_count_max']) {
                $fav_add_count_min = $params['fav_add_count_min'];
                $fav_add_count_max = $params['fav_add_count_max'];
            }
        }
        if (self::check($params, 'fav_add_sales_count_min') && self::check($params, 'fav_add_sales_count_max')) {
            if ($params['fav_add_sales_count_min'] <= $params['fav_add_sales_count_max']) {
                array_push($fields, array(
                    'field' => 'sales_count',
                    'min' => $params['fav_add_sales_count_min'],
                    'max' => $params['fav_add_sales_count_max']
                ));
            }
        }
        if (self::check($params, 'fav_add_access_count_min') && self::check($params, 'fav_add_access_count_max')) {
            if ($params['fav_add_access_count_min'] <= $params['fav_add_access_count_max']) {
                array_push($fields, array(
                    'field' => 'access_count',
                    'min' => $params['fav_add_access_count_min'],
                    'max' => $params['fav_add_access_count_max']
                ));
            }
        }

        $data = array();
        foreach($params as $k => $v) {
            if (in_array($k, array('available', 'available_auto', 'auto_sales_count_every', 'auto_access_count_every', 'auto_add_time_count_every'))) {
                $data[$k] = $params[$k];
            }
        }
        $config = self::config();
        $ret = PluginsService::PluginsDataSave(['plugins'=>$config['base']['plugins'], 'data'=>$data]);

        if(self::check($params, 'fav_reset')) {
            if ($params['fav_reset'] === '000') {
                $res = Service::resetDataAll();
                if ($res) {
                    $ret['msg'] .=  '<br>所有收藏数回到真实';
                }
            }
        }

        if ($fav_add_count_max !== PHP_INT_MAX || count($fields) > 0) {
            $res = Service::saveDataAll($fav_add_count_min, $fav_add_count_max, $fields);
            if ($res) {
                $ret['msg'] .=  '<br>' . $res['goods_count'] . '个商品加了' . $res['goods_fav_count_sum'] . '收藏';
            }
        }

        return $ret;
    }
}
?>