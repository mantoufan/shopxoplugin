<?php
namespace app\plugins\notice;
use think\Db;
use think\Controller;
use app\plugins\notice\service\Service;
use app\service\PluginsService;
use app\plugins\notice\wga\WGA;
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
        if(!empty($params['hook_name']))
        {
            $ret = '';
            switch($params['hook_name'])
            {
                case 'plugins_service_buy_order_insert_success':// 新订单提醒
                    $this->notify_neworder($params);
                break;
                case 'plugins_service_order_status_change_history_success_handle':
                    if ($params['data']['new_status'] === 3){ // 发货通知
                        $this->notify_asn($params);
                    }
                break;
                case 'plugins_view_common_bottom':
                    $ret = $this->dataJS($params);
                break;
                case 'plugins_js' :
                    $ret = 'static/plugins/js/notice/index/style.js';
                break; 
            }
            if (!empty($params['is_backend'])) {
                return DataReturn('操作成功', 0);
            } else {
                return $ret;
            }
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
            'account' => $order_address && $order_address['name'] ? $order_address['name'] : ($user['nickname'] ? $user['nickname'] : $user['username']),
            'phone' => $order_address && $order_address['tel'] ? $order_address['tel'] : $user['mobile'],
            'mail' => $user['email'],
            'ordernum' => $order['order_no'],
            'price' => $order['total_price'],
            'address' => $order_address['province_name'] . $order_address['city_name'] . $order_address['county_name'] . $order_address['address'],
            'weixin_openid' => !empty($user['weixin_openid']) ? $user['weixin_openid'] : '',
            'weixin_web_openid' => !empty($user['weixin_web_openid']) ? $user['weixin_web_openid'] : ''
        );
    }
    public function notify_neworder($params) {
        $ret = PluginsService::PluginsData('notice');
        if($ret['code'] == 0) {
            if (isset($params['order_ids'])) {
                $params['order_id'] = $params['order_ids'][0];
            }
            $neworder_by_sms = FALSE;
            $neworder_by_mail = FALSE;
            $neworder_by_wxpub = FALSE;
            $neworder_by_wxamp = FALSE;
            if (!empty($ret['data']['neworder_by'])) {
                $a = explode(',', $ret['data']['neworder_by']);
                if (in_array('sms', $a )) {
                    $neworder_by_sms = TRUE;
                }
                if (in_array('mail', $a )) {
                    $neworder_by_mail = TRUE;
                }
                if (in_array('wxpub', $a )) {
                    $neworder_by_wxpub = TRUE;
                }
                if (in_array('wxamp', $a )) {
                    $neworder_by_wxamp = TRUE;
                }
            }
            $this->codes = $this->get_codes($params['order_id']);
            if ($neworder_by_sms && !empty($ret['data']['neworder_sms_tpl']) && !empty($ret['data']['neworder_sms_signname']) && !empty($ret['data']['neworder_sms_phone'])) {
                $obj = new \base\Sms();
                $neworder_sms_phone_ar = explode(',', $ret['data']['neworder_sms_phone']);
                foreach ($neworder_sms_phone_ar as $k => $neworder_sms_phone) {
                    $obj->SendCode($neworder_sms_phone, $this->limit_codes_length($this->codes), $ret['data']['neworder_sms_tpl'], $ret['data']['neworder_sms_signname']);
                }
            }
            if ($neworder_by_mail && !empty($ret['data']['neworder_mail_tpl_title']) && !empty($ret['data']['neworder_mail_tpl_content']) && !empty($ret['data']['neworder_mail_address'])) {
                $title = preg_replace_callback('/\$\{(.*?)\}/', function($m){return $this->codes[$m[1]];}, $ret['data']['neworder_mail_tpl_title']);
                $content = preg_replace_callback('/\$\{(.*?)\}/', function($m){return $this->codes[$m[1]];}, $ret['data']['neworder_mail_tpl_content']);
                $obj = new \base\Email();
                $neworder_mail_address_ar = explode(',', $ret['data']['neworder_mail_address']);
                foreach ($neworder_mail_address_ar as $k => $neworder_mail_address) {
                    $obj->SendHTML(array(
                        'email' => $neworder_mail_address,
                        'title' => $title,
                        'content' => $content,
                        'username' => 'Admin'
                    ));
                } 
            }
            if ($neworder_by_wxpub && !empty($ret['data']['wxpub_appid']) && !empty($ret['data']['wxpub_appsecret']) && !empty($ret['data']['neworder_wxpub_openid']) && !empty($ret['data']['neworder_wxpub_tpl'])) {
                $neworder_wxpub_openid_ar = explode(',', $ret['data']['neworder_wxpub_openid']);
                foreach ($neworder_wxpub_openid_ar as $k => $neworder_wxpub_openid) {
                    Service::wxpubTplMsg($ret['data']['wxpub_appid'], $ret['data']['wxpub_appsecret'], array(
                        'touser' => $neworder_wxpub_openid,
                        'template_id' => $ret['data']['neworder_wxpub_tpl'],
                        'url' => MyUrl('index/order/index'),
                        'data' => array(
                            'first' => array(
                                'value' => '您有新订单'
                            ),
                            'keyword1' => array(
                                'value' => $this->codes['ordernum']
                            ),
                            'keyword2' => array(
                                'value' => date('Y-m-d H：i', time())
                            ),
                            'keyword3' => array(
                                'value' => mb_substr($this->codes['goodsname'], 0, 20)
                            ),
                            'keyword4' => array(
                                'value' => mb_substr($this->codes['address'], 0, 20)
                            ),
                            'remark' => array(
                                'value' => '订单金额：' . $this->codes['price']
                            )
                        )
                    ));
                }
            }
        }
        return '';
    }
    public function notify_asn($params) {
        $ret = PluginsService::PluginsData('notice');
        if($ret['code'] == 0) {
            $asn_by_sms = FALSE;
            $asn_by_mail = FALSE;
            $asn_by_wxpub = FALSE;
            $asn_by_wxamp = FALSE;
            if (!empty($ret['data']['asn_by'])) {
                $a = explode(',', $ret['data']['asn_by']);
                if (in_array('sms', $a )) {
                    $asn_by_sms = TRUE;
                }
                if (in_array('mail', $a )) {
                    $asn_by_mail = TRUE;
                }
                if (in_array('wxpub', $a )) {
                    $asn_by_wxpub = TRUE;
                }
                if (in_array('wxamp', $a )) {
                    $asn_by_wxamp = TRUE;
                }
            }
            $this->codes = $this->get_codes($params['order_id']);
            if ($asn_by_sms && !empty($ret['data']['asn_sms_tpl']) && !empty($ret['data']['asn_sms_signname']) && $this->codes['phone']) {
                $obj = new \base\Sms();
                $obj->SendCode($this->codes['phone'], $this->limit_codes_length($this->codes), $ret['data']['asn_sms_tpl'], $ret['data']['asn_sms_signname']);
            }
            if ($asn_by_mail && !empty($ret['data']['asn_mail_tpl_title']) && !empty($ret['data']['asn_mail_tpl_content'])  && $this->codes['mail']) {
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
            if ($asn_by_wxpub && !empty($ret['data']['wxpub_appid']) && !empty($ret['data']['wxpub_appsecret']) && !empty($ret['data']['asn_wxpub_tpl']) && !empty($this->codes['weixin_openid'])) {
                Service::wxpubTplMsg($ret['data']['wxpub_appid'], $ret['data']['wxpub_appsecret'], array(
                    'touser' => $this->codes['weixin_openid'],
                    'template_id' => $ret['data']['asn_wxpub_tpl'],
                    'url' => MyUrl('index/order/index'),
                    'data' => array(
                        'first' => array(
                            'value' => '您的订单已发货'
                        ),
                        'keyword1' => array(
                            'value' => mb_substr($this->codes['goodsname'], 0, 20)
                        ),
                        'keyword2' => array(
                            'value' => '已发货'
                        ),
                        'keyword3' => array(
                            'value' => $this->codes['expname']
                        ),
                        'keyword4' => array(
                            'value' => $this->codes['expnum']
                        ),
                        'remark' => array(
                            'value' => date('Y年m月d日 H:i', time())
                        )
                    )
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
    public function dataJS() {
        return Service::dataJS();
    }
}
?>