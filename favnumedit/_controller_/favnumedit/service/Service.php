<?php
namespace app\plugins\favnumedit\service;

use think\Db;
use app\service\PluginsService;
use app\service\GoodsFavorService;

class Service
{
    public static function loadData($goods_id) {
        $plugins_favnumedit_fav_count = Db::name('goods_favor')->where(['goods_id'=>$goods_id])->count();
        $plugins_favnumedit_fav_count_fake = Db::name('goods_favor')->where(['goods_id'=>$goods_id, 'user_id'=>'0'])->count();
        $plugins_favnumedit_fav_count_min = $plugins_favnumedit_fav_count - $plugins_favnumedit_fav_count_fake;
        return array(
            'plugins_favnumedit_fav_count_min' => $plugins_favnumedit_fav_count_min,
            'plugins_favnumedit_fav_count' => $plugins_favnumedit_fav_count
        );
    }

    public static function saveData($goods_id, $fav_count) {
        $res = self::loadData($goods_id);
        if ($fav_count >= $res['plugins_favnumedit_fav_count_min'] && $fav_count !== $res['plugins_favnumedit_fav_count']) {
            if ($fav_count > $res['plugins_favnumedit_fav_count']) {
                return self::addData($goods_id, $fav_count - $res['plugins_favnumedit_fav_count']);
            } else {
                return self::removeData($goods_id, $res['plugins_favnumedit_fav_count'] - $fav_count);
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

    public static function saveDataAll($fav_count_min, $fav_count_max, $fields = array()) {
        if (is_numeric($fav_count_min) && is_numeric($fav_count_max) && $fav_count_min <= $fav_count_max) {
            $MAXCOUNT = 100;
            $a = array();
            $sum = array();
            $fields_str = '';
            $fav_count_ar = array(min($fav_count_min, $MAXCOUNT), min($fav_count_max, $MAXCOUNT));

            $db_config = self::getDbConfig();
            if ($db_config) {
                $prefix = $db_config['prefix'];
            } else {
                return false;
            }

            if (count($fields) > 0) {
                foreach($fields as $k => $field) {
                    $res = Db::query('SELECT max(' . $field['field'] . ') as maxcount FROM ' . $prefix . 'goods');
                    if ($res && !empty($res[0]) && $res[0]['maxcount'] > $MAXCOUNT) {
                        $_tmp = $res[0]['maxcount'] / $MAXCOUNT;
                        $fields[$k]['max'] = $field['max'] / $_tmp;
                        $fields[$k]['min'] = $field['min'] / $_tmp;
                    }
                    $fields_str .= ',' . $field['field'];
                }
            }

            $ret = Db::query('SELECT id' . $fields_str . ' FROM ' . $prefix . 'goods ORDER BY id ASC');
            if ($ret) {
                $_insert_pre = 'INSERT INTO ' . $prefix . 'goods_favor (goods_id, user_id, add_time) VALUES ';
                foreach ($ret as $k => $v) {
                    if (count($fields) > 0) {
                        $fav_count_min = 0;
                        $fav_count_max = 0;
                        foreach($fields as $field) {
                            $fav_count_min += min($fav_count_ar[1], max($fav_count_ar[0], $field['min'] * $v[$field['field']] / 100));
                            $fav_count_max += max($fav_count_ar[0], min($fav_count_ar[1], $field['max'] * $v[$field['field']] / 100));
                        }
                    }
                    array_push($sum, rand($fav_count_min, $fav_count_max));
                    for ($i = 0; $i < end($sum); $i++) {
                        array_push($a, '\'' . $v['id'] . '\', 0, \'' . time() . '\'');
                        if (($i + 1) % 500 === 0) {
                            Db::execute($_insert_pre . '(' . implode('),(', $a) . ')');
                            $a = array();
                        }
                    }
                    if (count($a) > 1) {
                        Db::execute($_insert_pre . '(' . implode('),(', $a) . ')');
                        $a = array();
                    }
                }
                return array(
                    'goods_count' => count($ret),
                    'goods_fav_count_sum' => array_sum($sum) 
                );
            }
        }
        return false;
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
        if ($goods_id) {
            $ret = PluginsService::PluginsData('favnumedit');
            if($ret['code'] == 0)
            {
                if (!empty($ret['data']['available_auto'])) {

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

                        $count = 0;

                        if (!empty($ret['data']['auto_sales_count_every'])) {
                            $count += $sales_count / $ret['data']['auto_sales_count_every'];
                        }

                        if (!empty($ret['data']['auto_access_count_every'])) {
                            $count += $access_count / $ret['data']['auto_access_count_every'];
                        }
                        
                        if (!empty($ret['data']['auto_add_time_count_every'])) {
                            $count += (time() - $add_time) / 86400 / $ret['data']['auto_add_time_count_every'];
                        }

                        if ($count) {
                            $fav_count = GoodsFavorService::GoodsFavorTotal(array('goods_id' => $goods_id));

                            if ($count > $fav_count) {
                                return self::addData($goods_id, floor($count) - $fav_count);
                            }
                        }
                    }
                }
                
            }
        }
        return false;
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