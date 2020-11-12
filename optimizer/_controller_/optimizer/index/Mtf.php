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
                if (!isset($ret['data']['available_pic']) && empty($ret['data']['watermark_pos'])) return;
                $_paths = $_POST['paths'];
                if ($_paths) {
                    $paths = explode(',', $_paths);
                    if (count($paths) > 0) {
                        foreach ($paths as $k => $p) {
                            $paths[$k] = array(
                                'input' => Service::root() . 'public/' . str_replace('/public/', '', $p),
                                'output' => $p
                            );
                        }
                        $ret['data']['watermark_path'] = Service::root() . 'public/' . str_replace('/public/', '', preg_replace('/http[s]*:\/\/.*?\//', '', $ret['data']['watermark_path'][0]));
                        Service::mtfBetter($ret['data'])->handler($paths);
                    }
                }
            }
        }
    }
}
?>