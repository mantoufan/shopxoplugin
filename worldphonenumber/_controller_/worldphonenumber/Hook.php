<?php
namespace app\plugins\worldphonenumber;
use think\Db;
use think\Controller;
use app\service\PluginsService;
/**
 * 国际手机号 - 钩子入口
 * @author   小宇
 * @blog     https://www.madfan.cn
 * @version  1.0.0
 * @datetime 2020-05-23T21:51:08+0800
 */
class Hook extends Controller
{
    private $codes = array();
    // 应用响应入口
    public function run($params = [])
    {
        // 是否控制器钩子
        // is_backend 当前为后端业务处理
        // hook_name 钩子名称
        if(isset($params['is_backend']) && $params['is_backend'] === true && !empty($params['hook_name']))
        {
            switch($params['hook_name'])
            {
                case 'plugins_service_user_register_end':
                case 'plugins_service_user_accounts_update':
                    $ret = $this->reg($params);
                break;
                case 'plugins_service_order_status_change_history_success_handle':
                    if ($params['data']['new_status'] === 2){ // 新订单提醒
                        $ret = $this->notify_neworder($params);
                    } elseif ($params['data']['new_status'] === 3){ // 发货通知
                        $ret = $this->notify_asn($params);
                    } else {
                        $ret ='';
                    }
                break;
                default :
                    $ret = '';
            }
            // 参数一   描述
            // 参数二   0 为处理成功, 负数为失败
            // 参数三   返回数据
            return DataReturn('操作成功', 0);
        // 默认返回视图
        } else {
            if(!empty($params['hook_name']))
            {
                switch($params['hook_name'])
                {
                    case 'plugins_css' :
                        $ret = 'static/plugins/css/worldphonenumber/index/style.css';
                        break;
                    case 'plugins_js' :
                        $ret = 'static/plugins/js/worldphonenumber/index/style.js';
                        break;
                    case 'plugins_view_common_bottom':
                        $ret = $this->html($params);
                        break;
                    default :
                        $ret = '';
                }
                return $ret;
            }
        }
    }
    public function html($params = [])
    {
        $ret = PluginsService::PluginsData('worldphonenumber');
        if($ret['code'] == 0)
        {  
            $html = '';
            switch($params['hook_name'])
            {
                case 'plugins_view_common_bottom':
                    if (!empty($ret['data'])) {
                        $html = '<script>worldphonenumber_preferredCountries = "'.$ret['data']['preferredCountries'].'";worldphonenumber_excludeCountries = "'.$ret['data']['excludeCountries'].'";worldphonenumber_onlyCountries = "'.$ret['data']['onlyCountries'].'";</script>';
                    }
                break;
                default :
                    $html = '';
            }
                return $html;
        } else {
                return $ret['msg'];
        }
    }
    public function reg($params = [])
    {
        if (isset($params['params']['worldphonenumber_phone_code']) && !empty($params['params']['worldphonenumber_phone_code']) && !empty($params['user_id'])) {
            Db::name('User')->where(['id'=>$params['user_id']])->update(array(
                'plugins_worldphonenumber_phone_code' => $params['params']['worldphonenumber_phone_code']
            ));
        }
    }
    public function get_codes($order_id) {
        $order =  Db::name('order')->where('id', $order_id)->find();
        $user = Db::name('user')->where('id', $order['user_id'])->find();
        $order_address = Db::name('order_address')->where('order_id', $order_id)->find();
        if (!empty($order['express_id'])) {
            $express = Db::name('express')->where('id', $order['express_id'])->find();
        } else {
            $express = array('name'=>'');
        }

        $goods_name_ar = array();
        $order_detail = Db::name('order_detail')->where('order_id', $order_id)->select();
        for ($v = 0; $v < count($order_detail); $v++) {
            array_push($goods_name_ar, $order_detail[$v]['title']);
        }

        return array(
            'sitename' => MyC('home_site_name'),
            'goodsname' => implode(',', $goods_name_ar),
            'expname' => $express['name'],
            'expnum' => $order['express_number'],
            'account' => $order_address['name'] ? $order_address['name'] : ($user['nickname'] ? $user['nickname'] : $user['username']),
            'phone' => $order_address['tel'] ? $order_address['tel'] : $user['mobile'],
            'mail' => $user['email'],
            'ordernum' => $order['order_no']
        );
    }
    public function notify_neworder($params) {
        $ret = PluginsService::PluginsData('worldphonenumber');
        if($ret['code'] == 0) {
            $neworder_by_sms = FALSE;
            $neworder_by_mail = FALSE;
            if (isset($ret['data']['neworder_by']) && !empty($ret['data']['neworder_by'])) {
                $a = explode(',', $ret['data']['neworder_by']);
                if (in_array('sms', $a )) {
                    $neworder_by_sms = TRUE;
                }
                if (in_array('mail', $a )) {
                    $neworder_by_mail = TRUE;
                }
            }
            $this->codes = $this->get_codes($params['order_id']); 
            if ($neworder_by_sms && $ret['data']['neworder_sms_tpl'] && $ret['data']['neworder_sms_signname'] && $ret['data']['neworder_sms_phone']) {
                $obj = new \base\Sms(); 
                $obj->SendCode($ret['data']['neworder_sms_phone'], $this->limit_codes_length($this->codes), $ret['data']['neworder_sms_tpl'], $ret['data']['neworder_sms_signname']);
            }
            if ($neworder_by_mail && $ret['data']['neworder_mail_tpl_title'] && $ret['data']['neworder_mail_tpl_content']  && $ret['data']['neworder_mail_address']) { 
                $title = preg_replace_callback('/\$\{(.*?)\}/', function($m){return $this->codes[$m[1]];}, $ret['data']['neworder_mail_tpl_title']);
                $content = preg_replace_callback('/\$\{(.*?)\}/', function($m){return $this->codes[$m[1]];}, $ret['data']['neworder_mail_tpl_content']);
                $obj = new \base\Email();
                $obj->SendHTML(array(
                    'email' => $ret['data']['neworder_mail_address'],
                    'title' => $title,
                    'content' => $content,
                    'username' => 'Admin'
                ));
            }
        }
        return '';
    }
    public function notify_asn($params) {
        $ret = PluginsService::PluginsData('worldphonenumber');
        if($ret['code'] == 0) {
            $asn_by_sms = FALSE;
            $asn_by_mail = FALSE;
            if (isset($ret['data']['asn_by']) && !empty($ret['data']['asn_by'])) {
                $a = explode(',', $ret['data']['asn_by']);
                if (in_array('sms', $a )) {
                    $asn_by_sms = TRUE;
                }
                if (in_array('mail', $a )) {
                    $asn_by_mail = TRUE;
                }
            }
            $this->codes = $this->get_codes($params['order_id']);
            if ($asn_by_sms && $ret['data']['asn_sms_tpl'] && $ret['data']['asn_sms_signname'] && $this->codes['phone']) {
                $obj = new \base\Sms();
                $obj->SendCode($this->codes['phone'], $this->limit_codes_length($this->codes), $ret['data']['asn_sms_tpl'], $ret['data']['asn_sms_signname']);
            }
            if ($asn_by_mail && $ret['data']['asn_mail_tpl_title'] && $ret['data']['asn_mail_tpl_content']  && $this->codes['mail']) {
                $title = preg_replace_callback('/\$\{(.*?)\}/', function($m){return $this->codes[$m[1]];}, $ret['data']['asn_mail_tpl_title']);
                $content = preg_replace_callback('/\$\{(.*?)\}/', function($m){return $this->codes[$m[1]];}, $ret['data']['asn_mail_tpl_content']);
                $obj = new \base\Email();
                $obj->SendHTML(array(
                    'email' => $this->codes['mail'],
                    'title' => $title,
                    'content' => $content,
                    'username' => $this->codes['account']
                ));
            }
        }
        return '';
    }
    
    public function limit_codes_length($codes=array()) {
        foreach($codes as $k=>$v) {
            $codes[$k] = mb_substr($v, 0, 20);
        }
        return $codes;
    }
}
?>