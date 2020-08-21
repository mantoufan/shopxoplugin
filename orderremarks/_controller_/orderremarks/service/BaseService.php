<?php
// +----------------------------------------------------------------------
// | ShopXO 国内领先企业级B2C免费开源电商系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011~2019 http://shopxo.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Devil
// +----------------------------------------------------------------------
namespace app\plugins\orderremarks\service;
use think\Db;
use app\service\PluginsService;
use app\service\OrderService;
use app\service\MessageService;
use app\plugins\orderremarks\wga\WGA;
/**
 * 基础服务层
 * @author  Devil
 * @blog    http://gong.gg/
 * @version 1.0.0
 * @date    2019-09-24
 * @desc    description
 */
class BaseService
{
    // 可操作的订单状态
    public static $operate_order_status = array(0, 1, 2, 3, 4, 5, 6);
    
    /**
     * 基础配置信息保存
     * @author  Devil
     * @blog    http://gong.gg/
     * @version 1.0.0
     * @date    2019-12-24
     * @desc    description
     * @param   [array]          $params [输入参数]
     */
    public static function BaseConfigSave($params = array())
    {
        $wga= new WGA();
        return $wga->save($params);
    }
    /**
     * 基础配置信息获取
     * @author  Devil
     * @blog    http://gong.gg/
     * @version 1.0.0
     * @date    2019-12-24
     * @desc    description
     * @param   [boolean]          $is_cache [是否缓存中读取]
     */
    public static function BaseConfig($is_cache = true)
    {
        return PluginsService::PluginsData('orderremarks', null, $is_cache);
    }
    /**
     * 获取订单详情信息
     * @author  Devil
     * @blog    http://gong.gg/
     * @version 1.0.0
     * @date    2020-02-15
     * @desc    description
     * @param   [array]          $params [输入参数]
     */
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
            // 状态判断
            if(!in_array($ret['data'][0]['status'], self::$operate_order_status))
            {
                $order_status_list = lang('common_order_user_status');
                return DataReturn('订单状态不可操作['.$order_status_list[$ret['data'][0]['status']]['name'].']', -1);
            }
            $res = Db::name('plugins_orderremarks_notes')->where(['order_no'=>$ret['data'][0]['order_no']])->find();
            if ($res) {
                $ret['data'][0]['admin_note'] = $res['admin_note'];
                $ret['data'][0]['admin_note_upd_time'] = $res['upd_time'];
            }
            return DataReturn('处理成功', 0, $ret['data'][0]);
        }
        return DataReturn('没相关订单数据', -1);
    }
    /**
     * 订单价格修改
     * @author  Devil
     * @blog    http://gong.gg/
     * @version 1.0.0
     * @date    2020-02-15
     * @desc    description
     * @param   [array]          $params [输入参数]
     */
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
        
        if ($success) {
            // 消息通知
            self::UserMessageNotice($ret['data']['user_id'], $ret['data']['id'], $ret['data']['order_no'], $data['admin_note']);
    
            return DataReturn('备注成功', 0);
        }
        return DataReturn('备注失败', -100);
    }
    /**
     * 消息通知
     * @author  Devil
     * @blog    http://gong.gg/
     * @version 1.0.0
     * @date    2020-02-15
     * @desc    description
     * @param   [int]        $user_id       [用户id]
     * @param   [int]        $order_id      [订单id]
     * @param   [int]        $order_no      [订单号]
     * @param   [float]      $total_price   [金额]
     */
    private static function UserMessageNotice($user_id, $order_id, $order_no, $admin_note)
    {
        // 基础配置
        $base = self::BaseConfig();
        if(!empty($base['data']))
        {
            // 是否发送站内信
            if(isset($base['data']['is_site_inside_notice']) && $base['data']['is_site_inside_notice'] == 1)
            {
                $msg = '管理员备注订单：'.$admin_note;
                MessageService::MessageAdd($user_id, '订单备注', $msg, 1, $order_id);
            }
        }
    }
    /**
     * 获得订单备注
     */
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