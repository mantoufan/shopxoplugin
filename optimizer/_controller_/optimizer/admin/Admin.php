<?php
namespace app\plugins\optimizer\admin;
use think\Controller;
use app\service\PluginsService;
use app\plugins\optimizer\wga\WGA;
use app\plugins\optimizer\service\Service;

class Admin extends Controller
{
    public function index($params = [])
    {
        $ret = PluginsService::PluginsData('optimizer');
        if($ret['code'] == 0)
        {
            // 数据处理
            $this->assign('wga_tip', WGA::tip());
            $this->assign('conf', Service::config());
            $this->assign('data', $ret['data']);
            return $this->fetch('../../../plugins/view/optimizer/admin/admin/index');
        } else {
            return $ret['msg'];
        }
    }

    public function saveinfo($params = [])
    {
        
        $ret = PluginsService::PluginsData('optimizer');
        if($ret['code'] == 0)
        {
            $this->assign('wga_tip', WGA::tip());
            $this->assign('conf', Service::config());
            $this->assign('data', $ret['data']);
            return $this->fetch('../../../plugins/view/optimizer/admin/admin/saveinfo');
        } else {
            return $ret['msg'];
        }
    }

    public function save($params = [])
    {
        $ret = PluginsService::PluginsData('optimizer');
        if($ret['code'] == 0)
        {
            $metBetter = Service::mtfBetter($params);
            $needClearCache = false;
            if (!empty($params['cache_reset'])) {
                if ($params['cache_reset'] === '000') $needClearCache = true;
                unset($params['cache_reset']);
            }
            if ($this->checkNeedClearCache($ret['data'], $params, 'available_pic') || 
                $this->checkNeedClearCache($ret['data'], $params, 'watermark_path') ||
                $this->checkNeedClearCache($ret['data'], $params, 'watermark_pos')) {
                $needClearCache = true;
            }
            $conf = Service::config();
            if ($needClearCache) {
                $metBetter->removeDir(Service::root() . $conf['cache_dir']);
            }
            if (isset($conf['rules'])) {
                $rules = $conf['rules'];
                foreach ($rules as $path => $rule) {
                    $rules[Service::root() . $path] = $rule;
                    unset($rules[$path]);
                }
            }
            if (!empty($params['available_static'])) {
                $metBetter->restore($rules);
            } else {
                $metBetter->restore($rules);
            }
           
        }
        return WGA::save($params);
    }

    private function checkNeedClearCache($data, $params, $key) {
        return isset($data[$key]) && !isset($params[$key]) || !isset($data[$key]) && isset($params[$key]) || isset($data[$key]) && isset($params[$key]) && $data[$key] !== $params[$key];
    }
}
?>