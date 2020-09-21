<?php
namespace app\plugins\goodslocation\service;

use think\Db;

class Service
{
    private static $x_pi = 3.14159265358979324 * 3000.0 / 180.0;

    public static function loadConfig($only_tag) {
        $value = '';
        $ret = Db::name('config')->where(['only_tag'=>$only_tag])->find();
        if (isset($ret['value'])) {
            $value = $ret['value'];
        }
        return $value;
    }

    public static function saveConfig($only_tag, $value) {
        return Db::name('config')->where(['only_tag'=>$only_tag])->update(['value'=>$value]);
    }

    public static function gg2bd($gg_lat, $gg_lon) {  
        $x = (float)$gg_lon;
        $y = (float)$gg_lat;  
        $z = sqrt($x * $x + $y * $y) + 0.00002 * sin($y * self::$x_pi);  
        $theta = atan2($y, $x) + 0.000003 * cos($x * self::$x_pi);  
        $bd_lon = $z * cos($theta) + 0.0065;  
        $bd_lat = $z * sin($theta) + 0.006;  
        return array(
            'lat' => $bd_lat, 
            'lon' => $bd_lon
        );
    }

    public static function bd2gg($bd_lat, $bd_lon) {  
        $x = (float)$bd_lon - 0.0065;
        $y = (float)$bd_lat - 0.006;  
        $z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y *  self::$x_pi);  
        $theta = atan2($y, $x) - 0.000003 * cos($x * self::$x_pi);  
        $gg_lon = $z * cos($theta);  
        $gg_lat = $z * sin($theta);  
        return array(
            'lat' => $gg_lat,
            'lon' => $gg_lon
        );
    }
    
    public static function loadData($good_id) {
        $res = array(
            'location'=>'',
            'lon'=>'',
            'lat'=>''
        );
        if (isset($good_id)) {// 保存
            $ret = Db::name('plugins_goodslocation')->where(['good_id'=>$good_id])->find();
            if (isset($ret['id'])) {
                $res = $ret;
            }
        }
        return $res;
    }

}
?>