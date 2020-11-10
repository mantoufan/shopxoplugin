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
        if (!empty($params['data']['id'])) {
            $goods_id = $params['data']['id'];
            $sales_count = $params['data']['sales_count'];
            $access_count = $params['data']['access_count'];
            $plugins_dataprettify_sales_count = !empty($params['data']['plugins_dataprettify_sales_count']) ? $params['data']['plugins_dataprettify_sales_count'] : 0;
            $plugins_dataprettify_access_count = !empty($params['data']['plugins_dataprettify_access_count']) ? $params['data']['plugins_dataprettify_access_count'] : 0;
        } else {
            return '';
        }
        $ret = PluginsService::PluginsData('dataprettify');
        $h = '';
        if($ret['code'] == 0)
        {
            $availables = array(
                'fav' => false,
                'sales' => false,
                'access' => false
            );
            $flag = false;
            foreach ($availables as $k => $v) {
                if (isset($ret['data']['available_' . $k]) && $ret['data']['available_' . $k] === '1') {
                    $availables[$k] = true;
                    $flag = true;
                }
            }
            if ($flag) {
                $h = '<div class="am-form-group am-form-success">';
            }
            
            if ($availables['fav']) {
                $ret = Service::loadData($goods_id);
                $h .= '<label>收藏数量修改<span class="am-form-group-label-tips">真实收藏数量：' . $ret['plugins_dataprettify_fav_count_min'] . '</span></label>
                       <input type="text" name="plugins_dataprettify_fav_count" placeholder="请输入收藏数量，数量不能小于真实收藏数量（' . $ret['plugins_dataprettify_fav_count_min'] . '），不修改留空即可" min="' . $ret['plugins_dataprettify_fav_count_min'] . '" class="am-radius am-field-valid" value="' . $ret['plugins_dataprettify_fav_count'] . '">
                       <input type="hidden" name="plugins_dataprettify_fav_count_source" value="' . $ret['plugins_dataprettify_fav_count'] . '">';
            }
            if ($availables['sales']) {
                $h .= '<label>销量修改<span class="am-form-group-label-tips">真实销量：' . ($sales_count - $plugins_dataprettify_sales_count) . '</span></label>
                       <input type="text" name="plugins_dataprettify_sales_count" placeholder="请输入销量，不修改留空即可" min="0" max="9999999999" class="am-radius am-field-valid" value="' . $sales_count . '">
                       <input type="hidden" name="sales_count" value="' . $sales_count . '">';
            }
            if ($availables['access']) {
                $h .= '<label>浏览次数修改<span class="am-form-group-label-tips">真实浏览次数：' . ($access_count - $plugins_dataprettify_sales_count) . '</span></label>
                       <input type="text" name="plugins_dataprettify_access_count" placeholder="请输入浏览次数，不修改留空即可" min="0" max="9999999999" class="am-radius am-field-valid" value="' . $access_count . '">
                       <input type="hidden" name="access_count" value="' . $access_count . '">';
            }
            if ($flag) {
                $h .= '</div>';
            }
            
        }
        return $h;
    }

    private function save($params) {
        if (!empty($params['params']['id'])) {
            $goods_id = $params['params']['id'];
            $sales_count = $params['params']['sales_count'];
            $access_count = $params['params']['access_count'];
        } else {
            return '';
        }
        if (!empty($params['params']['plugins_dataprettify_fav_count']) && isset($params['params']['plugins_dataprettify_fav_count_source']) && $params['params']['plugins_dataprettify_fav_count'] !== $params['params']['plugins_dataprettify_fav_count_source']) {
            Service::saveData($goods_id, $params['params']['plugins_dataprettify_fav_count']);
        }
        $datas = array();$raws = array();
        if (!empty($params['params']['plugins_dataprettify_sales_count']) && $sales_count !== $params['params']['plugins_dataprettify_sales_count']) {
            $datas['sales_count'] = $params['params']['plugins_dataprettify_sales_count'];
            $raws['plugins_dataprettify_sales_count'] = $datas['sales_count'] - $sales_count;
        }
        if (!empty($params['params']['plugins_dataprettify_access_count']) && $access_count !== $params['params']['plugins_dataprettify_access_count']) {
            $datas['access_count'] = $params['params']['plugins_dataprettify_access_count'];
            $raws['plugins_dataprettify_access_count'] = $datas['access_count'] - $access_count;
        }
        if (count($datas) > 0) {
            Service::saveDataGoods($goods_id, $datas, $raws);
        }
    }

    private function auto($params) {
        if (!empty($params['goods_id'])) {
            $goods_id = $params['goods_id'];
        } else {
            return '';
        }
        $ret = PluginsService::PluginsData('dataprettify');
        if($ret['code'] == 0)
        {
            if (!empty($ret['data']['available_auto_fav']) || !empty($ret['data']['available_auto_sales']) || !empty($ret['data']['available_auto_access'])) {
                return '<script src=\'' . Service::PluginsHomeUrl(PluginsHomeUrl('dataprettify', 'auto', 'add', array('goods_id' => $goods_id)), $goods_id, 'admin') . '\'></script>';
            }
        }
    }
}
?>