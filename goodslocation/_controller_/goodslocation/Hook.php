<?php
namespace app\plugins\goodslocation;

use think\Controller;
use think\Db;
use app\service\PluginsService;
use app\plugins\goodslocation\service\Service;

class Hook extends Controller
{
    // 应用响应入口
    public function run($params = [])
    {
        if(!empty($params['hook_name']))
        {
            switch($params['hook_name'])
            {
                //  js
                case 'plugins_js':
                    $ret = __MY_ROOT_PUBLIC__.'static/plugins/js/goodslocation/index/common.js';
                break;   
                // 后台商品编辑页显示地址
                case 'plugins_view_admin_goods_save' : 
                    $ret = $this->edit($params);
                break;
                // 后台商品保存位置地址
                case 'plugins_service_goods_save_handle' :                    
                    $ret = $this->save($params);
                break;
                // 前台显示位置地址
                case "plugins_view_goods_detail_title" :
                    $ret = $this->show($params);
                break;
                default :
                    $ret = '';
            }
            return $ret;
        }
    }

    private function getDbConfig() {
        if(!file_exists(ROOT.'config/database.php'))
        {
            return FALSE;
        }
        return $db_config = include ROOT.'config/database.php';
    }
    
    private function edit($params) {
        $ret = array(
            'location' => '',
            'lon' => '',
            'lat' => ''
        );
        if (isset($params['data']['id'])) {
            $ret = Service::loadData($params['data']['id']);
        }
        $ak = Service::loadConfig('common_baidu_map_ak');
        return '<div class="am-form-group am-form-success">
            <label>地址<a class="am-form-group-label-tips" data-am-modal="{target: \'#js_plugins_goodslocation_popup\', dimmer: false}" id="js_plugins_goodslocation_bt">（点此自动录入地址、经度和纬度）</a></label>
            <input type="text" name="plugins_goodslocation_location" placeholder="请输入位置地址" class="am-radius am-field-valid" value="' . $ret['location'] . '">
            <label>经度（BD-09）</label>
            <input type="text" name="plugins_goodslocation_lon" placeholder="请输入经度" class="am-radius am-field-valid" value="' . $ret['lon'] . '">
            <label>纬度（BD-09）</label>
            <input type="text" name="plugins_goodslocation_lat" placeholder="请输入纬度" class="am-radius am-field-valid" value="' . $ret['lat'] . '">
            <div class="am-popup" id="js_plugins_goodslocation_popup"><div class="am-popup-inner"><div class="am-popup-hd"><h4 class="am-popup-title">自动录入</h4><span data-am-modal-close class="am-close">&times;</span></div><div class="am-popup-bd"><div class="plugins_goodslocation_menu"><input type="text" id="js_plugins_goodslocation_input" placeholder="请输入位置地址" class="am-radius am-field-valid" value="' . $ret['location'] . '"></div><div class="plugins_goodslocation_map" id="js_plugins_goodslocation_map"></div></div></div><input type="hidden" name="js_plugins_goodslocation_is_admin" value="1"><input type="hidden" id="js_plugins_goodslocation_ak" value="' . $ak . '"></div>
        </div><script type="text/javascript" src="' . config('shopxo.attachment_host') . '/static/plugins/js/goodslocation/index/common.js" defer></script>';
    }

    private function save($params) {
        $db_config = $this->getDbConfig();
        if ($db_config) {
            $prefix = $db_config['prefix'];
        } else {
            return array('error' => '数据库配置未找到');
        }
        if (empty($params['params']['id'])) {// 新增
            $row = Db::table('information_schema.tables') -> where('table_name=\'' . $prefix . 'goods\'') -> find();
            $id = $row['auto_increment'];
        } else {// 保存
            $id = $params['params']['id'];
        }
        $ret = Db::name('plugins_goodslocation')->where(['good_id'=>$id])->find();
        // 更新数据
        $data = [
            'good_id' => $id,
            'lon' => $params['params']['plugins_goodslocation_lon'],
            'lat' => $params['params']['plugins_goodslocation_lat'],
            'location' => $params['params']['plugins_goodslocation_location']
        ];
        if (isset($ret['id'])) {
            if(Db::name('plugins_goodslocation')->where(['good_id'=>$id])->update($data))
            {
                $success = true;
            }
        } else {
            if(Db::name('plugins_goodslocation')->insert($data))
            {
                $success = true;
            }
        }
    }
    
    private function show($params) {
        $location = '';
        $ret = Service::loadData($params['goods']['id']);
        $ak = Service::loadConfig('common_baidu_map_ak');
        $gg = Service::bd2gg($ret['lat'], $ret['lon']);
        if ($ret) {
            $location = '<div id="js_plugins_goodslocation_bt" data-am-modal="{target: \'#js_plugins_goodslocation_popup\', dimmer: false}"><small class="am-icon-location-arrow"> ' . $ret['location'] . '</small></div><div class="am-popup" id="js_plugins_goodslocation_popup"><div class="am-popup-inner"><div class="am-popup-hd"><h4 class="am-popup-title">' . $ret['location'] . '</h4><span data-am-modal-close class="am-close">&times;</span></div><div class="am-popup-bd"><div class="plugins_goodslocation_menu"><a class="am-btn" style="width:50%" href="https://api.map.baidu.com/marker?location='. $ret['lat'] .','. $ret['lon'] .'&title='. $ret['location'] .'&output=html" target="_blank"><img src="/static/upload/images/plugins_goodslocation/icon/icon_baidumap.png" width="25px" height="25px" alt="百度地图图标" />百度导航</a><a class="am-btn" style="width:50%" href="https://uri.amap.com/marker?position='. $gg['lon'] .','. $gg['lat'] .'&name='. $ret['location'] .'" target="_blank"><img src="/static/upload/images/plugins_goodslocation/icon/icon_gaodemap.png" width="25px" height="25px" alt="高德地图图标" />高德导航</a></div><div class="plugins_goodslocation_map" id="js_plugins_goodslocation_map"></div></div></div><input type="hidden" id="js_plugins_goodslocation_ak" value="' . $ak . '"><input type="hidden" name="plugins_goodslocation_location" value="' . $ret['location'] . '"><input type="hidden" name="plugins_goodslocation_lon" value="' . $ret['lon'] . '"><input type="hidden" name="plugins_goodslocation_lat" value="' . $ret['lat'] . '">/div>';
        }
        return $location;
    }
}
?>