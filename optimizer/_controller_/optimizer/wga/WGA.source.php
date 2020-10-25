<?php
namespace app\plugins\optimizer\wga;
use app\service\PluginsService;
class WGA
{
    private static function wga() {
        $root = dirname(__FILE__);
        $config = self::config();
        $_domain_ar = !empty($_SERVER['HTTP_HOST']) ? explode(':', $_SERVER['HTTP_HOST']) : array($_SERVER['SERVER_NAME']);
        $_domain = reset($_domain_ar);
        $r = @file_get_contents('https://api.os120.com/wga/verify?out_type=json&name=shopxoplugin_' . $config['base']['plugins'] . '&version=' . $config['base']['version'] . '&des=正版验证&domain=' . $_domain, false, stream_context_create(array('http' => array('method' => "GET",'timeout' => 5))));
        if ($r) {
            $r = json_decode($r, true);
            if (isset($r['code']) && $r['code'] === -1) {
                return $r['msg'];
            }
            if (isset($r['data'])) {
                if (!empty($r['data']['tip'])) {
                    file_put_contents($root . '/wga_tip.txt', $r['data']['tip']);
                }
                if (!empty($r['data']['data'])) {
                    file_put_contents($root . '/wga_data.php', '<?php return ' . var_export($r['data']['data'], true) . '; ?>');
                }
            } else if (file_exists($root . '/wga_tip.txt')){
                unlink($root. '/wga_tip.txt');
            }
        }
        return false;
    }
    public static function config() {
        return json_decode(file_get_contents(dirname(__FILE__) . '/../config.json'), true); 
    }
    public static function tip() {
        if (file_exists(dirname(__FILE__) . '/wga_tip.txt')){
            return '<div class="am-alert" data-am-alert><button type="button" class="am-close">&times;</button>' . file_get_contents(dirname(__FILE__) . '/wga_tip.txt') . '</div>';
        }
        return '';
    }
    public static function save($params = [])
    {
        $res = self::wga();
        if ($res) {
            return DataReturn($res, -1);
        }
        $config = self::config();
        return PluginsService::PluginsDataSave(['plugins'=>$config['base']['plugins'], 'data'=>$params]);
    }
}
?>