<?php
namespace app\plugins\dataprettify\service;

use think\Db;
use app\service\PluginsService;
use app\service\GoodsFavorService;

class Service
{
    public static function loadData($goods_id) {
        $plugins_dataprettify_fav_count = Db::name('goods_favor')->where(['goods_id'=>$goods_id])->count();
        $plugins_dataprettify_fav_count_fake = Db::name('goods_favor')->where(['goods_id'=>$goods_id, 'user_id'=>'0'])->count();
        $plugins_dataprettify_fav_count_min = $plugins_dataprettify_fav_count - $plugins_dataprettify_fav_count_fake;
        return array(
            'plugins_dataprettify_fav_count_min' => $plugins_dataprettify_fav_count_min,
            'plugins_dataprettify_fav_count' => $plugins_dataprettify_fav_count
        );
    }

    public static function saveData($goods_id, $fav_count) {
        $res = self::loadData($goods_id);
        if ($fav_count >= $res['plugins_dataprettify_fav_count_min'] && $fav_count !== $res['plugins_dataprettify_fav_count']) {
            if ($fav_count > $res['plugins_dataprettify_fav_count']) {
                return self::addData($goods_id, $fav_count - $res['plugins_dataprettify_fav_count']);
            } else {
                return self::removeData($goods_id, $res['plugins_dataprettify_fav_count'] - $fav_count);
            }
        }
        return false;
    }

    /**
     * 获取数据库配置
    */
    private static function getDbConfig()
    {
        if(!file_exists(ROOT.'config/database.php'))
        {
            return FALSE;
        }
        return $db_config = include ROOT.'config/database.php';
    }

    private static function isAvailable($type, $ranges, $fields = array(), $MAXCOUNT = PHP_INT_MAX) {
        if (isset($ranges[$type . '_add_count_min']) && isset($ranges[$type . '_add_count_max'])) {
            $min = $ranges[$type . '_add_count_min'];
            $max = $ranges[$type . '_add_count_max'];
            if (is_numeric($min) && is_numeric($max) && $min <= $max) {
                return array(min($min, $MAXCOUNT), min($max, $MAXCOUNT));
            }
        } else if (isset($fields['sales_count'][$type . '_min']) && isset($fields['sales_count'][$type . '_max'])
                || isset($fields['access_count'][$type . '_min']) && isset($fields['access_count'][$type . '_max'])) {
                return array(0, $MAXCOUNT);
        }
        return false;
    }
    private static function getRandNum($min, $max, $_min, $_max) {
        return rand(min(max($min, $_min), $_max), max($max ? min($max, $_max) : $_max, $_min));
    }

    public static function saveDataAll($ranges = array(), $fields = array('sales_count' => array(), 'access_count' => array())) {
        $db_config = self::getDbConfig();
        if ($db_config) {
            $prefix = $db_config['prefix'];
        } else {
            return false;
        }

        $MAXCOUNT = 100;
        $availables = array(
            'fav' => self::isAvailable('fav', $ranges, $fields, $MAXCOUNT),
            'sales' => self::isAvailable('sales', $ranges, $fields),
            'access' => self::isAvailable('access', $ranges, $fields),
            'inventory' => self::isAvailable('inventory', $ranges)
        );
        
        $sum = array('goods' => 0, 'spec' => 0, 'fav' => 0, 'sales' => 0, 'access' => 0, 'inventory' => 0);
        if ($availables['fav'] || $availables['sales'] || $availables['access']) {
            $datas = array();
            $tmp = array();
            $fields_str = '';

            foreach($fields as $field => $opt) {
                if (count($opt) > 0) {
                    if (!empty($opt['fav_min']) && !empty($opt['fav_max'])) {
                        $res = Db::query('SELECT max(' . $field . ') as maxcount FROM ' . $prefix . 'goods');
                        if ($res && !empty($res[0]) && $res[0]['maxcount'] > $MAXCOUNT) {
                            $_tmp = $res[0]['maxcount'] / $MAXCOUNT;
                            $fields[$field]['fav_max'] = $opt['fav_max'] / $_tmp;
                            $fields[$field]['fav_min'] = $opt['fav_min'] / $_tmp;
                        }
                    }
                }
                $fields_str .= ',' . $field;
            }
            $ret = Db::query('SELECT id' . $fields_str . ' FROM ' . $prefix . 'goods ORDER BY id ASC');
            $sum['goods'] = count($ret);
            if ($ret) {
                foreach ($ret as $k => $v) {
                    $goods_id = $v['id'];
                    $fav_add_count_min = 0;
                    $fav_add_count_max = 0;
                    $sales_add_count_min = 0;
                    $sales_add_count_max = 0;
                    $access_add_count_min = 0;
                    $access_add_count_max = 0;

                    foreach($fields as $field => $opt) {
                        if (count($opt) > 0) {
                            if ($availables['fav'] && isset($opt['fav_min']) && isset($opt['fav_max'])) {
                                $fav_add_count_min += $opt['fav_min'] * $v[$field] / 100;
                                $fav_add_count_max += $opt['fav_max'] * $v[$field] / 100;
                            }
                            if ($availables['sales'] && isset($opt['sales_min']) && isset($opt['sales_max'])) {
                                $sales_add_count_min += $opt['sales_min'] * $v[$field] / 100;
                                $sales_add_count_max += $opt['sales_max'] * $v[$field] / 100;
                            }
                            if ($availables['access'] && isset($opt['access_min']) && isset($opt['access_max'])) {
                                $access_add_count_min += $opt['access_min'] * $v[$field] / 100;
                                $access_add_count_max += $opt['access_max'] * $v[$field] / 100;
                            }
                        }
                    }
                    if ($availables['fav']) {
                        $num = self::getRandNum($fav_add_count_min, $fav_add_count_max, $availables['fav'][0], $availables['fav'][1]);
                        $sum['fav'] += $num;
                        for ($i = 0; $i < $num; $i++) {
                            array_push($tmp, '\'' .  $goods_id . '\', 0, \'' . time() . '\'');
                        }
                    }
                    if ($availables['sales']) {
                        $num = self::getRandNum($sales_add_count_min, $sales_add_count_max, $availables['sales'][0], $availables['sales'][1]);
                        $sum['sales'] += $num;
                        if (!isset($datas[$goods_id])) {$datas[$goods_id] = array();}
                        $datas[$goods_id]['sales_count'] = $num;
                    }
                    if ($availables['access']) {
                        $num = self::getRandNum($access_add_count_min, $access_add_count_max, $availables['access'][0], $availables['access'][1]);
                        $sum['access'] += $num;
                        if (!isset($datas[$goods_id])) {$datas[$goods_id] = array();}
                        $datas[$goods_id]['access_count'] = $num;
                    }
                }
                if (count($tmp) > 0) {
                    Db::execute('INSERT INTO ' . $prefix . 'goods_favor (goods_id, user_id, add_time) VALUES ' . '(' . implode('),(', $tmp) . ')');
                }
                if (count($datas) > 0) {
                    self::saveDataGoodsAll($datas);
                }   
            }
        }
        if ($availables['inventory']) {
            $datas = array();
            
            $ret = Db::query('SELECT id, inventory FROM ' . self::getGoodsSpecDbname($prefix) . ' ORDER BY id ASC');
            $sum['spec'] = count($ret);
            foreach ($ret as $k => $v) {
                $goods_spec_id = $v['id'];
                $goods_spec_inventory = $v['inventory'];
                $num = rand($availables['inventory'][0], $availables['inventory'][1]);
                if ($num < 0 && $goods_spec_inventory + $num < 0) {
                    $num = -$goods_spec_inventory;
                }
                $sum['inventory'] += $num;
                if (!isset($datas[$goods_spec_id])) {$datas[$goods_spec_id] = array();}
                $datas[$goods_spec_id]['inventory'] = $num;
            }
            if (count($datas) > 0) {
                self::saveDataGoodsSpecAll($datas);
            }
        }
        return array(
            'goods_sum' => $sum['goods'],
            'goods_fav_count_sum' => $sum['fav'],
            'goods_sales_count_sum' => $sum['sales'],
            'goods_access_count_sum' => $sum['access'],
            'goods_spec_sum' => $sum['spec'],
            'goods_inventory_count_sum' => $sum['inventory']
        );
    }

    private static function getGoodsSpecDbname($prefix) {
        return  $prefix . (Db::query('show tables like "' . $prefix . 'warehouse_goods_spec' . '"') ? 'warehouse_goods_spec' : 'goods_spec_base');
    }

    public static function saveDataGoods($goods_id, $datas, $raws = array()) {
        foreach ($raws as $field => $v) {
            $datas[$field] = Db::raw($field . ' + ' . $v);
        }
        return Db::name('Goods')->where('id', $goods_id)->update($datas);
    }

    public static function saveDataGoodsAll($datas) {
        $db_config = self::getDbConfig();
        if ($db_config) {
            $prefix = $db_config['prefix'];
        } else {
            return false;
        }
        $ids = array();
        $a = array();
        foreach($datas as $goods_id => $data) {
            array_push($ids, $goods_id);
            foreach($data  as $field => $v) {
                for($i = 0; $i < 2; $i++) {
                    if ($i === 1) {
                        $field = 'plugins_dataprettify_' . $field;
                    }
                    if (!isset($a[$field])) {
                        $a[$field] = $field . ' = '. $field .' + CASE id';
                    }
                    $a[$field] .= ' WHEN '. $goods_id . ' THEN '. $v;
                }
            }
        }
        if (count($a) > 0 && count($ids) > 0) {
            return Db::execute('UPDATE ' . $prefix . 'goods SET ' . implode(' END, ', $a) . ' END WHERE id IN(' . implode(',', $ids) . ');');
        }
        return false;
    }

    public static function saveDataGoodsSpecAll($datas) {
        $db_config = self::getDbConfig();
        if ($db_config) {
            $prefix = $db_config['prefix'];
        } else {
            return false;
        }
        $ids = array();
        $a = array();
        foreach($datas as $goods_spec_id => $data) {
            array_push($ids, $goods_spec_id);
            foreach($data  as $field => $v) {
                if (!isset($a[$field])) {
                    $a[$field] = $field . ' = '. $field .' + CASE id';
                }
                $a[$field] .= ' WHEN '. $goods_spec_id . ' THEN '. $v;
            }
        }
        if (count($a) > 0 && count($ids) > 0) {
            return Db::execute('UPDATE ' . self::getGoodsSpecDbname($prefix) . ' SET ' . implode(' END, ', $a) . ' END WHERE id IN(' . implode(',', $ids) . ');');
        }
    }

    public static function addData($goods_id, $fav_count) {
        $a = array();
        if (is_numeric($fav_count) && $fav_count > 0) {
            for ($i = 0; $i < $fav_count; $i++) {
                array_push($a, array(
                    'goods_id' => $goods_id,
                    'user_id' => '0',
                    'add_time' => time()
                ));
            }
            return Db::name('goods_favor')->insertAll($a);
        }
        return false;
    }

    public static function addDataAuto($goods_id)
    {
        $flag = false;
        if ($goods_id) {
            $ret = PluginsService::PluginsData('dataprettify');
            if($ret['code'] == 0)
            {
                if (!empty($ret['data']['available_auto_fav']) || !empty($ret['data']['available_auto_sales']) || !empty($ret['data']['available_auto_access'])) {
                    $db_config = self::getDbConfig();
                    if ($db_config) {
                        $prefix = $db_config['prefix'];
                    } else {
                        return false;
                    }
                    $res = Db::query('SELECT sales_count, access_count, add_time  FROM ' . $prefix . 'goods WHERE id = \'' . $goods_id . '\'');
                    if ($res && $res[0]) {
                        $sales_count = $res[0]['sales_count'];
                        $access_count = $res[0]['access_count'];
                        $add_time = $res[0]['add_time'];
                    }
                }
                if (!empty($ret['data']['available_auto_fav'])) {
                    if ($res && $res[0]) {
                        $count = 0;

                        if (!empty($ret['data']['auto_fav_sales_count_every'])) {
                            $count += $sales_count / $ret['data']['auto_fav_sales_count_every'];
                        }

                        if (!empty($ret['data']['auto_fav_access_count_every'])) {
                            $count += $access_count / $ret['data']['auto_fav_access_count_every'];
                        }
                        
                        if (!empty($ret['data']['auto_fav_add_time_count_every'])) {
                            $count += (time() - $add_time) / 86400 / $ret['data']['auto_fav_add_time_count_every'];
                        }

                        if ($count) {
                            $fav_count = GoodsFavorService::GoodsFavorTotal(array('goods_id' => $goods_id));

                            if ($count > $fav_count) {
                                $flag = self::addData($goods_id, floor($count) - $fav_count);
                            }
                        }
                    }
                }
                $datas = array();$raws = array();
                if (!empty($ret['data']['available_auto_sales'])) {
                    if ($res && $res[0]) {
                        if (!empty($ret['data']['auto_sales_rate'])) {
                            if (rand(0, 100) > $ret['data']['auto_sales_rate']) {
                                return false;
                            }
                        }

                        $count = 0;

                        if (!empty($ret['data']['auto_sales_sales_count_every'])) {
                            $count += $sales_count / $ret['data']['auto_sales_sales_count_every'];
                        }

                        if (!empty($ret['data']['auto_sales_access_count_every'])) {
                            $count += $access_count / $ret['data']['auto_sales_access_count_every'];
                        }
                        
                        if (!empty($ret['data']['auto_sales_add_time_count_every'])) {
                            $count += (time() - $add_time) / 86400 / $ret['data']['auto_sales_add_time_count_every'];
                        }

                        if ($count) {
                            if ($count > $sales_count) {
                                $datas['sales_count'] = $count;
                                $raws['plugins_dataprettify_sales_count'] = $count - $sales_count;
                            }
                        }
                    }
                }
                if (!empty($ret['data']['available_auto_access'])) {
                    if ($res && $res[0]) {
                        if (!empty($ret['data']['auto_access_rate'])) {
                            if (rand(0, 100) > $ret['data']['auto_access_rate']) {
                                return false;
                            }
                        }

                        $count = 0;

                        if (!empty($ret['data']['auto_access_sales_count_every'])) {
                            $count += $sales_count / $ret['data']['auto_access_sales_count_every'];
                        }
                        
                        if (!empty($ret['data']['auto_access_access_count_every'])) {
                            $count += $access_count / $ret['data']['auto_access_access_count_every'];
                        }
                        
                        if (!empty($ret['data']['auto_access_add_time_count_every'])) {
                            $count += (time() - $add_time) / 86400 / $ret['data']['auto_access_add_time_count_every'];
                        }

                        if ($count) {
                            if ($count > $access_count) {
                                $datas['access_count'] = $count;
                                $raws['plugins_dataprettify_access_count'] = $count - $access_count;
                            }
                        }
                    }
                }
                if (count($datas) > 0) {
                    $flag = Service::saveDataGoods($goods_id, $datas, $raws);
                }
            }
        }
        return $flag;
    }
    
    public static function removeData($goods_id, $fav_count) {
        $a = array();
        if (is_numeric($fav_count) && $fav_count > 0) {
            $ret = Db::name('goods_favor')->where(['goods_id'=>$goods_id, 'user_id'=>'0'])->field('id')->order('id desc')->limit($fav_count - 1, 1)->select();
            if ($ret) {
                return Db::name('goods_favor')->where('id', '>=', $ret[0]['id'])->delete();
            }
        }
        return false;
    }

    public static function resetData($goods_id) {
        return Db::name('goods_favor')->where(['goods_id'=>$goods_id, 'user_id'=>'0'])->delete();
    }

    public static function resetDataAll() {
        return Db::name('goods_favor')->where(['user_id'=>'0'])->delete();
    }

    public static function resetDataGoodsAll($field) {
        return Db::name('goods')->where('id', '>', 0)->update([$field=>Db::raw($field . ' - plugins_dataprettify_' . $field), 'plugins_dataprettify_' . $field=>0]);
    }

    public static function PluginsHomeUrl($url) {
        if (stripos($url, '?') === FALSE) {
            $url = str_replace('/index/plugins/index/pluginsname/', '/?s=/index/plugins/index/pluginsname/', $url);
        } else {
            $url = str_replace('index.php?s=', '?s=', $url);
        }
        return $url;
    }
}
?>