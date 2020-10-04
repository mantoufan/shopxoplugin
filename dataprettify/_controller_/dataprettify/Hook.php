<?php
namespace app\plugins\dataprettify;

use think\Controller;
use think\Db;
use app\service\PluginsService;
use app\plugins\dataprettify\service\Service;
use app\plugins\dataprettify\wga\WGA;

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
        $ret = PluginsService::PluginsData('dataprettify');$h = '';
        if($ret['code'] == 0)
        {
            $availables = array(
                'fav' => false,
                'sales' => false,
                'access' => false
            );
            foreach ($availables as $k => $v) {
                if (isset($ret['data']['available_' . $k]) && $ret['data']['available_' . $k] === '1') {
                    $availables[$k] = true;
                }
            }
            if ($availables['fav']) {
                $ret = array(
                    'plugins_dataprettify_fav_count_min' => 0,
                    'plugins_dataprettify_fav_count' => 0,
                );
                if (isset($params['data']['id'])) {
                    $ret = Service::loadData($params['data']['id']);
                }
                $h .= '<div class="am-form-group am-form-success">
                            <label>收藏数量修改</label>
                            <input type="text" name="plugins_dataprettify_fav_count" placeholder="请输入收藏数量，数量不能小于真实收藏数量（' . $ret['plugins_dataprettify_fav_count_min'] . '），不修改留空即可" min="' . $ret['plugins_dataprettify_fav_count_min'] . '" class="am-radius am-field-valid" value="' . $ret['plugins_dataprettify_fav_count'] . '">
                            <input type="hidden" name="plugins_dataprettify_fav_count_source" value="' . $ret['plugins_dataprettify_fav_count'] . '">
                        </div>';
            }
            if ($availables['sales']) {
                $h .= '<div class="am-form-group am-form-success">
                            <label>销量修改</label>
                            <input type="text" name="plugins_dataprettify_sales_count" placeholder="请输入销量，不修改留空即可" class="am-radius am-field-valid" value="' . $ret['plugins_dataprettify_sales_count'] . '">
                        </div>';
            }
            if ($availables['access']) {
                $h .= '<div class="am-form-group am-form-success">
                            <label>浏览次数修改</label>
                            <input type="text" name="plugins_dataprettify_access_count" placeholder="请输入浏览次数，不修改留空即可" class="am-radius am-field-valid" value="' . $ret['plugins_dataprettify_access_count'] . '">
                        </div>';
            }
        }
        return $h;
    }

    private function save($params) {
        if (!empty($params['params']['plugins_dataprettify_fav_count']) && isset($params['params']['plugins_dataprettify_fav_count_source']) && $params['params']['plugins_dataprettify_fav_count'] !== $params['params']['plugins_dataprettify_fav_count_source']) {
            Service::saveData($params['params']['id'], $params['params']['plugins_dataprettify_fav_count']);
        }
        $datas = array();
        if (!empty($params['params']['plugins_dataprettify_sales_count'])) {
            $datas['sales_count'] = $params['params']['plugins_dataprettify_sales_count'];
        }
        if (!empty($params['params']['plugins_dataprettify_access_count'])) {
            $datas['access_count'] = $params['params']['plugins_dataprettify_access_count'];
        }
        if (count($datas) > 0) {
            Service::saveDataGoods($params['params']['id'], $datas);
        }
    }

    private function auto($params) {
        if (!empty($params['goods_id'])) {
            $ret = PluginsService::PluginsData('dataprettify');
            if($ret['code'] == 0)
            {
                if (!empty($ret['data']['available_auto_fav']) || !empty($ret['data']['available_auto_sales']) || !empty($ret['data']['available_auto_access'])) {
                    return '<script src=\'' . Service::PluginsHomeUrl(PluginsHomeUrl('dataprettify', 'auto', 'add', array('goods_id' => $params['goods_id'])), $params['goods_id'], 'admin') . '\'></script>';
                }
            }
        }
    }
}
?>