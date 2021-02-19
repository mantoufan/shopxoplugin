<?php
namespace app\plugins\orderremarks\service;
use think\Db;
use app\service\PluginsService;
use app\service\OrderService;
use app\service\MessageService;
use app\plugins\orderremarks\wga\WGA;

class BaseService
{
    public static function BaseConfigSave($params = array())
    {
        $wga= new WGA();
        return $wga->save($params);
    }

    public static function BaseConfig($is_cache = true)
    {
        return PluginsService::PluginsData('orderremarks', null, $is_cache);
    }
    public static function OrderDetail($params = [])
    {
        // 请求参数
        $p = [
            [
                'checked_type'      => 'empty',
                'key_name'          => 'id',
                'error_msg'         => '订单id不能为空',
            ]
        ];
        $ret = ParamsChecked($params, $p);
        if($ret !== true)
        {
            return DataReturn($ret, -1);
        }
        // 条件
        $where = OrderService::OrderListWhere($params);
        // 获取列表
        $data_params = array(
            'm'         => 0,
            'n'         => 1,
            'where'     => $where,
            'is_items'  => 0,
        );
        $ret = OrderService::OrderList($data_params);
        if($ret['code'] == 0 && !empty($ret['data'][0]))
        {
            $res = Db::name('plugins_orderremarks_notes')->where(['order_no'=>$ret['data'][0]['order_no']])->find();
            if ($res) {
                $ret['data'][0]['admin_note'] = $res['admin_note'];
                $ret['data'][0]['admin_note_upd_time'] = $res['upd_time'];
            }
            return DataReturn('处理成功', 0, $ret['data'][0]);
        }
        return DataReturn('没相关订单数据', -1);
    }
    public static function OrderUpdate($params = [])
    {
        $success = false;
        // 请求参数
        $p = [
            [
                'checked_type'      => 'empty',
                'key_name'          => 'id',
                'error_msg'         => '订单id不能为空',
            ]
        ];
        $ret = ParamsChecked($params, $p);
        if($ret !== true)
        {
            return DataReturn($ret, -1);
        }
        // 获取订单信息
        $ret = self::OrderDetail($params);
        if($ret['code'] != 0)
        {
            return $ret;
        }
        // 更新数据
        $wga= new WGA();
        $data = $wga->getOrderUpdateData($ret, $params);
        if (isset($data['msg'])) {
            return $data;
        }
        if (isset($ret['data']['admin_note'])) {
            if(Db::name('plugins_orderremarks_notes')->where(['order_no'=>$ret['data']['order_no']])->update($data))
            {
                $success = true;
            }
        } else {
            if(Db::name('plugins_orderremarks_notes')->insert($data))
            {
                $success = true;
            }
        }
        
        return DataReturn('备注未修改或备注失败', -100);
    }
  
    public static function OrderAdminNote($order_no)
    {
        // 基础配置
        $base = self::BaseConfig();
        if(!empty($base['data']))
        {
            // 列表页查看订单备注
            if(isset($base['data']['is_dispaly_on_list']) && $base['data']['is_dispaly_on_list'] == 1)
            {
                if ($order_no) {
                    $ret = Db::name('plugins_orderremarks_notes')->where(['order_no'=>$order_no])->find();
                    if ($ret) {
                        return array('admin_note'=>$ret['admin_note'], 'upd_time'=>$ret['upd_time']);
                    }
                }
                
            }
        }
        return FALSE;
    }
}
?>