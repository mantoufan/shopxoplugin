<?php
namespace app\plugins\favnumedit\service;

use think\Db;

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

    public static function saveDataAll($fav_count_min, $fav_count_max, $fields = array()) {
        if (is_numeric($fav_count_min) && is_numeric($fav_count_max) && $fav_count_min <= $fav_count_max) {
            $a = array();
            $sum = array();
            $fields_str = '';
            $fav_count_ar = array($fav_count_min, $fav_count_max);
            if (count($fields) > 0) {
                foreach($fields as $field) {
                    $fields_str .= ',' . $field['field'];
                }
            }
            $ret = Db::name('goods')->field('id' . $fields_str)->select();
            if ($ret) {
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
                        array_push($a, array(
                            'goods_id' => $v['id'],
                            'user_id' => '0',
                            'add_time' => time()
                        ));
                    } 
                }
                return Db::name('goods_favor')->insertAll($a) ? array(
                    'goods_count' => count($ret),
                    'goods_fav_count_sum' => array_sum($sum) 
                ) : false;
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
}
?>