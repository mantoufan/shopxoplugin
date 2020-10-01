<?php
namespace app\plugins\favnumedit;

use think\Controller;
use think\Db;
use app\service\PluginsService;
use app\plugins\favnumedit\service\Service;
use app\plugins\favnumedit\wga\WGA;

class Hook extends Controller
{
    // 应用响应入口
    public function run($params = [])
    {
        if(!empty($params['hook_name']))
        {
            switch($params['hook_name'])
            {
                // 后台商品编辑页显示地址
                case 'plugins_view_admin_goods_save' : 
                    $ret = $this->edit($params);
                break;
                // 后台商品保存位置地址
                case 'plugins_service_goods_save_handle' :                    
                    $ret = $this->save($params);
                break;
                // 商品底部
                case 'plugins_view_goods_detail_tabs_bottom' :                    
                    $ret = $this->auto($params);
                break;
                default :
                    $ret = '';
            }
            return $ret;
        }
    }
    
    private function edit($params) {
        $ret = PluginsService::PluginsData('favnumedit');
        if($ret['code'] == 0)
        {
            if (isset($ret['data']['available']) && $ret['data']['available'] === '1') {
                $ret = array(
                    'plugins_favnumedit_fav_count_min' => 0,
                    'plugins_favnumedit_fav_count' => 0,
                );
                if (isset($params['data']['id'])) {
                    $ret = Service::loadData($params['data']['id']);
                }
                return '<div class="am-form-group am-form-success">
                            <label>收藏数量</label>
                            <input type="text" name="plugins_favnumedit_fav_count" placeholder="请输入收藏数量，数量不能小于真实收藏数量（' . $ret['plugins_favnumedit_fav_count_min'] . '），不修改留空即可" min="' . $ret['plugins_favnumedit_fav_count_min'] . '" class="am-radius am-field-valid" value="' . $ret['plugins_favnumedit_fav_count'] . '">
                            <input type="hidden" name="plugins_favnumedit_fav_count_source" value="' . $ret['plugins_favnumedit_fav_count'] . '">
                        </div>';
            }
        }
        return '';
    }

    private function save($params) {
        if (!empty($params['params']['plugins_favnumedit_fav_count']) && isset($params['params']['plugins_favnumedit_fav_count_source']) && $params['params']['plugins_favnumedit_fav_count'] !== $params['params']['plugins_favnumedit_fav_count_source']) {
            $ret = Service::saveData($params['params']['id'], $params['params']['plugins_favnumedit_fav_count']);
        }
    }

    private function auto($params) {
        if (!empty($params['goods_id'])) {
            $ret = PluginsService::PluginsData('favnumedit');
            if($ret['code'] == 0)
            {
                if (!empty($ret['data']['available_auto'])) {
                    return '<script src=\'' . Service::PluginsHomeUrl(PluginsHomeUrl('favnumedit', 'auto', 'add', array('goods_id' => $params['goods_id'])), $params['goods_id'], 'admin') . '\'></script>';
                }
            }
        }
    }
}
?>