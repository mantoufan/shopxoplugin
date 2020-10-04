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

    public static function saveDataAll($ranges = array(), $fields = array()) {
        $MAXCOUNT = 100;

        $availables = array(
            'fav' => false,
            'sales' => false,
            'access' => false
        );

        $fav_count_min = $ranges['fav_count_min'];
        $fav_count_max = $ranges['fav_count_max'];
        if (is_numeric($fav_count_min) && is_numeric($fav_count_max) && $fav_count_min <= $fav_count_max) {
            $availables['fav'] = array(min($fav_count_min, $MAXCOUNT), min($fav_count_max, $MAXCOUNT));
        }

        $sales_add_count_min = $ranges['sales_add_count_min'];
        $sales_add_count_max = $ranges['sales_add_count_max'];
        if (is_numeric($sales_add_count_min) && is_numeric($sales_add_count_max) && $sales_add_count_min <= $sales_add_count_max) {
            $availables['sales'] = array($sales_add_count_min, $sales_add_count_max);
        }

        $access_add_count_min = $ranges['access_add_count_min'];
        $access_add_count_max = $ranges['access_add_count_max'];
        if (is_numeric($access_add_count_min) && is_numeric($access_add_count_max) && $access_add_count_min <= $access_add_count_max) {
            $availables['access'] = array($access_add_count_min, $access_add_count_max);
        }
        
        $tmp = array();
        $sum = array('fav' => 0, 'sales' => 0, 'access' => 0);
        $fields_str = '';

        $db_config = self::getDbConfig();
        if ($db_config) {
            $prefix = $db_config['prefix'];
        } else {
            return false;
        }

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
                $fields_str .= ',' . $field;
            }
        }

        $ret = Db::query('SELECT id' . $fields_str . ' FROM ' . $prefix . 'goods ORDER BY id ASC');
        if ($ret) {
            foreach ($ret as $k => $v) {
                $fav_count_min = 0;
                $fav_count_max = 0;
                $sales_add_count_min = 0;
                $sales_add_count_max = 0;
                $access_add_count_min = 0;
                $access_add_count_max = 0;
                foreach($fields as $field => $opt) {
                    if (count($opt) > 0) {
                        if ($availables['fav']) {
                            $fav_count_min += min($availables['fav'][1], max($availables['fav'][0], $opt['fav_min'] * $v[$field] / 100));
                            $fav_count_max += max($availables['fav'][0], min($availables['fav'][1], $opt['fav_max'] * $v[$field] / 100));
                        }
                        if ($availables['sales']) {
                            $sales_add_count_min += min($availables['sales'][1], max($availables['sales'][0], $opt['fav_min'] * $v[$field] / 100));
                            $sales_add_count_max += max($availables['sales'][0], min($availables['sales'][1], $opt['fav_max'] * $v[$field] / 100));
                        }
                        if ($availables['access']) {
                            $access_add_count_min += min($availables['access'][1], max($availables['access'][0], $opt['fav_min'] * $v[$field] / 100));
                            $access_add_count_max += max($availables['access'][0], min($availables['access'][1], $opt['fav_max'] * $v[$field] / 100));
                        }
                    }
                }
                if ($availables['fav']) {
                    $_insert_pre = 'INSERT INTO ' . $prefix . 'goods_favor (goods_id, user_id, add_time) VALUES ';
                    $num = rand($fav_count_min, $fav_count_max);
                    $sum['fav'] += $num;
                    for ($i = 0; $i < $num; $i++) {
                        array_push($tmp, '\'' . $v['id'] . '\', 0, \'' . time() . '\'');
                        if (($i + 1) % 500 === 0) {
                            Db::execute($_insert_pre . '(' . implode('),(', $tmp) . ')');
                            $tmp = array();
                        }
                    }
                    if (count($a) > 1) {
                        Db::execute($_insert_pre . '(' . implode('),(', $tmp) . ')');
                        $tmp = array();
                    }
                }
                if ($availables['sales']) {
                    $num = rand($sales_add_count_min, $sales_add_count_max);
                    $sum['sales'] += $num;
                    
                }
                if ($availables['access']) {
                    $num = rand($access_add_count_min, $access_add_count_max);
                    $sum['access'] += $num;
                }
            }
            return array(
                'goods_count' => count($ret),
                'goods_fav_count_sum' => $sum['fav'],
                'goods_sales_count_sum' => $sum['sales'],
                'goods_access_count_sum' => $sum['access'],
            );
        }
        return false;
    }

    public static function saveDataGoods($goods_id, $datas) {
        return Db::name('goods_favor')->where(array('id' => $goods_id))->update($datas);
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
                $datas = array();
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
                            }
                        }
                    }
                }
                if (count($datas) > 0) {
                    $flag = Service::saveDataGoods($goods_id, $datas);
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