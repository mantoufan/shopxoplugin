<?php
namespace app\plugins\optimizer\index;
use think\Controller;
use app\service\PluginsService;
use app\plugins\optimizer\service\Service;

class Mtf extends Controller
{
    public function better($params = [])
    {
        if (!empty($_POST['paths'])) {
            $ret = PluginsService::PluginsData('optimizer');
            if($ret['code'] == 0)
            {
                $_paths = $_POST['paths'];
                if ($_paths) {
                    $paths = explode(',', $_paths);
                    if (count($paths) > 0) {
                        foreach ($paths as $k => $p) {
                            $paths[$k] = Service::root() . 'public/' . str_replace('/public/', '', $p);
                        }
                        Service::mtfBetter($ret['data'])->handler($paths);
                    }
                }
            }
        }
    }
}
?>