<?php
namespace app\plugins\dataprettify\wga;
use app\service\PluginsService;
use app\plugins\dataprettify\service\Service;
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

        $ranges = array();
        $fields = array('sales_count' => array(), 'access_count' => array());
        
        if (self::check($params, 'fav_add_count_min') && self::check($params, 'fav_add_count_max')) {
            if ($params['fav_add_count_min'] <= $params['fav_add_count_max']) {
                $ranges['fav_add_count_min'] = $params['fav_add_count_min'];
                $ranges['fav_add_count_max'] = $params['fav_add_count_max'];
            }
        }
        if (self::check($params, 'fav_add_sales_count_min') && self::check($params, 'fav_add_sales_count_max')) {
            if ($params['fav_add_sales_count_min'] <= $params['fav_add_sales_count_max']) {
                $fields['sales_count']['fav_min'] = $params['fav_add_sales_count_min'];
                $fields['sales_count']['fav_max'] = $params['fav_add_sales_count_max'];
            }
        }
        if (self::check($params, 'fav_add_access_count_min') && self::check($params, 'fav_add_access_count_max')) {
            if ($params['fav_add_access_count_min'] <= $params['fav_add_access_count_max']) {
                $fields['access_count']['fav_min'] = $params['fav_add_access_count_min'];
                $fields['access_count']['fav_max'] = $params['fav_add_access_count_min'];
            }
        }

        if (self::check($params, 'sales_add_count_min') && self::check($params, 'sales_add_count_max')) {
            if ($params['sales_add_count_min'] <= $params['sales_add_count_max']) {
                $ranges['sales_add_count_min'] = $params['sales_add_count_min'];
                $ranges['sales_add_count_max'] = $params['sales_add_count_max'];
            }
        }
        if (self::check($params, 'sales_add_sales_count_min') && self::check($params, 'sales_add_sales_count_max')) {
            if ($params['sales_add_sales_count_min'] <= $params['sales_add_sales_count_max']) {
                $fields['sales_count']['sales_min'] = $params['sales_add_sales_count_min'];
                $fields['sales_count']['sales_max'] = $params['sales_add_sales_count_max'];
            }
        }
        if (self::check($params, 'sales_add_access_count_min') && self::check($params, 'sales_add_access_count_max')) {
            if ($params['sales_add_access_count_min'] <= $params['sales_add_access_count_max']) {
                $fields['sales_count']['sales_min'] = $params['sales_add_access_count_min'];
                $fields['sales_count']['sales_max'] = $params['sales_add_access_count_max'];
            }
        }

        if (self::check($params, 'access_add_count_min') && self::check($params, 'access_add_count_max')) {
            if ($params['access_add_count_min'] <= $params['access_add_count_max']) {
                $ranges['access_add_count_min'] = $params['access_add_count_min'];
                $ranges['access_add_count_max'] = $params['access_add_count_max'];
            }
        }
        if (self::check($params, 'access_add_sales_count_min') && self::check($params, 'access_add_sales_count_max')) {
            if ($params['access_add_sales_count_min'] <= $params['access_add_sales_count_max']) {
                $fields['sales_count']['access_min'] = $params['access_add_sales_count_min'];
                $fields['sales_count']['access_max'] = $params['access_add_sales_count_max'];
            }
        }
        if (self::check($params, 'access_add_access_count_min') && self::check($params, 'access_add_access_count_max')) {
            if ($params['sales_add_access_count_min'] <= $params['access_add_access_count_max']) {
                $fields['sales_count']['access_min'] = $params['access_add_access_count_min'];
                $fields['sales_count']['access_max'] = $params['access_add_access_count_max'];
            }
        }

        $data = array();
        foreach($params as $k => $v) {
            if (in_array($k, 
                array('available_fav', 'available_sales', 'available_access', 
                      'available_auto_fav', 'auto_fav_sales_count_every', 'auto_fav_access_count_every', 'auto_fav_add_time_count_every',
                      'available_auto_sales', 'auto_sales_sales_count_every', 'auto_sales_access_count_every', 'auto_sales_add_time_count_every', 'auto_sales_rate',
                      'available_auto_access', 'auto_access_sales_count_every', 'auto_access_access_count_every', 'auto_access_add_time_count_every', 'auto_access_rate',
                ))) {
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

        if (count($ranges) > 0 || count($fields['sales_count']) > 0 || count($fields['access_count']) > 0) {
            $res = Service::saveDataAll($ranges, $fields);
            if ($res) {
                $ret['msg'] .=  '<br>' . $res['goods_count'] . '个商品' .
                 ($res['goods_fav_count_sum'] ? ' 收藏加' . $res['goods_fav_count_sum'] : '') . 
                 ($res['goods_sales_count_sum'] ? ' 销量加' . $res['goods_sales_count_sum'] : '') . 
                 ($res['goods_access_count_sum'] ? ' 浏览次数加' . $res['goods_access_count_sum'] : '');
            }
        }

        return $ret;
    }
}
?>