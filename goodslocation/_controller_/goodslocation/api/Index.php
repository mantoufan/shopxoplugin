<?php
namespace app\plugins\goodslocation\api;

use think\Controller;
use app\plugins\goodslocation\service\Service;

class Index extends Controller
{
    public function __construct()
    {
        // 调用父类前置方法
        parent::__construct();
    }

    public function Goods($params = [])
    {
        // 参数
        if(empty($params['goods_id']))
        {
            return DataReturn('商品id为空', -1);
        }

        // 调用服务层
        $ret = Service::loadData(intval($params['goods_id']));
        if (!empty($ret['location'])) {
            $res = Service::bd2gg($ret['lat'], $ret['lon']);
            return array(
                'code' => 0,
                'data' => array(
                    'lon' => $res['lon'],
                    'lat' => $res['lat'],
                    'location' => $ret['location']
                )
            );
        } else {
            return array(
                'code' => -1
            );
        }
    }
}
?>