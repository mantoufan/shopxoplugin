<?php
namespace app\plugins\optimizer\index;
use think\Controller;

class Handler extends Controller
{
    public function handler($params = []) {
        ini_set("display_errors", "On");
        error_reporting(E_ALL);
        define('CONF', array(
            'cache' => array(
                'dir' => dirname(__FILE__) . '/../cache/',
                'time' => 60 * 60 * 6 // 秒
            ),
            'js' => array(
                'amazeui.css' => 'https://cdn.jsdelivr.net/npm/amazeui@2.7.2/dist/css/amazeui.min.css',
                'jquery-2.1.0.js' => 'https://cdn.jsdelivr.net/combine/npm/jquery@2.1.0,npm/amazeui@2.7.2/dist/js/amazeui.min.js,npm/echarts@4.1.0/dist/echarts.min.js,npm/clipboard@2.0.4',
                'amazeui.min.js' => '',
                'echarts.min.js' => '',
                'clipboard.min.js' => ''
            )
        ));
        $_root = dirname(__FILE__) . '/../../../../';
        parse_str($_SERVER['QUERY_STRING'], $_req);
        $_ar = explode('?', $_req['p']);
        $_p = $_root . $_ar[0];
        $_pic_watermark = $_req['pic_watermark'];
        $_pic_watermark_pos = $_req['pic_watermark_pos'];
        $_available_pic_safe = $_req['available_pic_safe'];
        if (file_exists($_p)) {
            $_i = pathinfo($_p);
            switch ($_i['extension']) {
                case 'css':
                case 'js':
                case 'html':
                    if (isset(CONF['js'][$_i['basename']])) {
                        if (!empty(CONF['js'][$_i['basename']])) {
                            header('Location: ' . CONF['js'][$_i['basename']]);
                        }
                    } else {
                        include_once(dirname(__FILE__) . '/../vendor/autoload.php');
                        $minifier = new \marcocesarato\minifier\Minifier();
                        $_c = file_get_contents($_p);
                        if ($_i['extension'] === 'css') {
                            header('Content-type: text/css');
                            die($minifier->minifyCSS($_c));
                        } else if ($_i['extension'] === 'js') {
                            header('Content-type: text/javascript');
                            die($minifier->minifyJS($_c));
                        } else {
                            header('Content-type: text/html');
                            die($minifier->minifyHTML($_c));
                        }
                    }
                break;
                case 'jpeg':
                case 'jpg':
                    $this->webp(imagecreatefromstring(file_get_contents($_p)), md5($_i['basename']), 'jpeg', 'image/jpeg');
                    break;
                case 'png':
                    $image = imagecreatefromstring(file_get_contents($_p));
                    imagepalettetotruecolor($image);
                    imagealphablending($image, true);
                    imagesavealpha($image, true);
                    $this->webp($image, md5($_i['basename']), 'png', 'image/png');
                    imagedestroy($image);
                    break;
                case 'gif':
                    $this->webp(imagecreatefromstring(file_get_contents($_p)), md5($_i['basename']), 'gif', 'image/gif');
                    break;
                default:
                    die(file_get_contents($_p));
            }
        }
    }
    private function webp($image, $filename, $extension, $mimetype) {
        if (!is_dir(CONF['cache']['dir'])) {
            mkdir(CONF['cache']['dir']);
        }
        if (rand(0, 10) > 5) {
            $this->cacheClear();
        }
        if(strpos( $_SERVER['HTTP_ACCEPT'], 'image/webp' ) !== false) {
            $_b = true;
            $_p = CONF['cache']['dir']. $filename .'.webp';
            if (!file_exists($_p)) {
                try {
                    imagepalettetotruecolor($image);
                    $_b  = imagewebp($image, $_p, 75);
                } catch (\Exception $e) {
                    $_b  = false;
                }
            }
            if ($_b) {
                header('Content-type: image/webp');
                die(file_get_contents($_p));
            }
        }
        echo $_p = CONF['cache']['dir']. $filename . '.' . $extension;
        header('Content-type: ' . $mimetype);
        if (!file_exists($_p)) {
            $quality = $extension === 'png' ? 7 : 75;
            $im = 'image' . $extension;
            $im($image, $_p, $quality);
        }
        die(file_get_contents($_p));
    }
    private function cacheClear() {
        if (is_dir(CONF['cache']['dir'])) {
            forEach(glob(CONF['cache']['dir'] . '*.*') as $file) {
                if (time() - filectime($file) > CONF['cache']['time']) {
                    unlink($file);
                }
            }
        }
    }
}
?>