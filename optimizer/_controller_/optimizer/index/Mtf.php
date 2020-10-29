<?php
namespace app\plugins\optimizer\index;
use think\Controller;
use app\service\PluginsService;

class Mtf extends Controller
{
    private static $conf = array(
        'cache_dir' => 'runtime/cache/optimizer/',
        'cache_time' => 60 * 60 * 6,
        'task_num' => 3
    );
    public function better($params = [])
    {
        $_root = str_replace('\\', '/', dirname(__FILE__)) . '/../../../../';
        $ret = PluginsService::PluginsData('optimizer');
        if($ret['code'] == 0)
        {
            $data = $ret['data'];
            $_querys = array();
            if (isset($data['available_static'])) {
                $_querys []= 'available_static=1';
            }
            if (isset($data['available_pic'])) {
                $_querys []= 'available_pic=1';
            }
            if (isset($data['anti_stealing_link_pic'])) {
                $_querys []= 'anti_stealing_link_pic=1';
            }
            if (!empty($data['watermark_path'])) {
                $_querys []= 'watermark_path=' . $_root . 'public/' . preg_replace(array('/http.*?\/\/.*?\//', '/public\//'), '', $data['watermark_path'][0]);
            }
            if (!empty($data['watermark_pos'])) {
                $_querys []= 'watermark_pos=' . $data['watermark_pos'];
            }
            $_querys []= 'cache_dir=' . $_root . self::$conf['cache_dir'];
            $_querys []= 'cache_time=' . (!empty($data['cache_time']) ? $data['cache_time'] : self::$conf['cache_time']);
            $_querys []= 'task_num=' . (!empty($data['task_num']) ? $data['task_num'] : self::$conf['task_num']);
            $_SERVER['QUERY_STRING'] = implode('&', $_querys) . '&path=' . $_root . 'public/' . str_replace('/public/', '', $params['path']);
            include('mtfBetter/mtfBetter.php');
        } else {
            return $ret['msg'];
        }
    }
}
?>