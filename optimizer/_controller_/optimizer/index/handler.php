<?php
namespace app\plugins\optimizer\index;
use think\Controller;

class Handler extends Controller
{
    public function handler($params = []) {
        ini_set("display_errors", "On");
        error_reporting(E_ALL);
        $CONF = array(
            'cache' => array(
                'dir' => dirname(__FILE__) . '/../cache/'
            ),
            'js' => array(
                'amazeui.css' => 'https://cdn.jsdelivr.net/npm/amazeui@2.7.2/dist/css/amazeui.min.css',
                'jquery-2.1.0.js' => 'https://cdn.jsdelivr.net/combine/npm/jquery@2.1.0,npm/amazeui@2.7.2/dist/js/amazeui.min.js,npm/echarts@4.1.0/dist/echarts.min.js,npm/clipboard@2.0.4',
                'amazeui.min.js' => '',
                'echarts.min.js' => '',
                'clipboard.min.js' => ''
            ),
            'req' => array(
                'cache_time' => 60 * 60 * 6, // 秒
                'task_num' => 8,
                'pic_watermark' => '',
                'pic_watermark_pos' => '',
                'available_pic_safe' => false
            )
        );
        $_root = dirname(__FILE__) . '/../../../../';
        $_data = $_root . 'data.php';
        parse_str($_SERVER['QUERY_STRING'], $_req);
        $_ar = explode('?', $_req['p']);
        $_p = $_root . $_ar[0];

        foreach(array('pic_watermark', 'pic_watermark_pos', 'available_pic_safe', 'task_num', 'cache_time') as $v) {
            if (!empty($_req[$v])) {
                $CONF['req'][$v] = $_req[$v];
            }
        }
        
        if (file_exists($_p)) {
            $_i = pathinfo($_p);
            switch ($_i['extension']) {
                case 'css':
                case 'js':
                case 'html':
                    if (isset($CONF['js'][$_i['basename']])) {
                        if (!empty($CONF['js'][$_i['basename']])) {
                            header('Location: ' . CONF['js'][$_i['basename']]);
                        }
                    } else {
                        $_p_cache = $CONF['cache']['dir']. md5($_i['dirname'] . '/' . $_i['basename']) . '.' . $_i['extension'];
                        if (file_exists($_p_cache)) {
                            $_c = file_get_contents($_p_cache);
                            $this->cacheClear(10);
                        } else {
                            $this->$taskManager(1, $_p, $_i);
                            include_once(dirname(__FILE__) . '/../vendor/autoload.php');
                            $minifier = new \marcocesarato\minifier\Minifier();
                            $_c = file_get_contents($_p);
                            if ($_i['extension'] === 'css') {
                                $_c = $minifier->minifyCSS($_c);
                            } else if ($_i['extension'] === 'js') {
                                $_c = $minifier->minifyJS($_c);
                            } else {
                                $_c = $minifier->minifyHTML($_c);
                            }
                            file_put_contents($_p_cache, $_c);
                            $this->$taskManager(0);
                        }
                        contentType($_i['extension']);
                        die($_c);
                    }
                break;
                case 'jpeg':
                case 'jpg':
                    $this->webp(imagecreatefromstring(file_get_contents($_p)), md5($_i['basename']), 'jpeg');
                    break;
                case 'png':
                    $image = imagecreatefromstring(file_get_contents($_p));
                    imagepalettetotruecolor($image);
                    imagealphablending($image, true);
                    imagesavealpha($image, true);
                    $this->webp($image, md5($_i['basename']), 'png');
                    imagedestroy($image);
                    break;
                case 'gif':
                    $this->webp(imagecreatefromstring(file_get_contents($_p)), md5($_i['basename']), 'gif');
                    break;
                default:
                    die(file_get_contents($_p));
            }
        }
    }
    private function webp($image, $filename, $extension) {
        if (!is_dir($CONF['cache']['dir'])) {
            mkdir($CONF['cache']['dir']);
        }
        $this->cacheClear(5);
        if(strpos( $_SERVER['HTTP_ACCEPT'], 'image/webp' ) !== false) {
            $_b = true;
            $_p = $CONF['cache']['dir']. $filename .'.webp';
            if (!file_exists($_p)) {
                try {
                    imagepalettetotruecolor($image);
                    $_b  = imagewebp($image, $_p, 75);
                } catch (\Exception $e) {
                    $_b  = false;
                }
            }
            if ($_b) {
                contentType('webp');
                die(file_get_contents($_p));
            }
        }
        contentType($extension);
        if (!file_exists($_p)) {
            $quality = $extension === 'png' ? 7 : 75;
            $im = 'image' . $extension;
            $im($image, $_p, $quality);
        }
        die(file_get_contents($_p));
    }
    private function cacheClear($rand) {
        if (rand(0, 100) > $rand) return;
        if (is_dir($CONF['cache']['dir'])) {
            forEach(glob($CONF['cache']['dir'] . '*.*') as $file) {
                if (time() - filectime($file) > CONF['req']['cache_time']) {
                    unlink($file);
                }
            }
        }
    }
    private function data($key, $val) {
        $_p = $CONF['cache']['dir'] . 'data.php';
        if (file_exists($_p)) {
            $data = include $_p;
        } else {
            $data = array();
        }
        if (isset($val)) {
            $data[$key] = $val;
        }
        file_put_contents($_p, $data);
        return $data[$key];
    }
    private function contentType($ext) {
        switch ($ext) {
            case 'css':
                header('Content-type: text/css');
            break;
            case 'js':
                header('Content-type: text/javascript');
            break;
            case 'html':
                header('Content-type: text/html');
            break;
            case 'jpg':
            case 'jpeg':
                header('Content-type: image/jpeg');
            break;
            case 'png':
                header('Content-type: image/png');
            break;
            case 'gif':
                header('Content-type: image/gif');
            break;
            case 'webp':
                header('Content-type: image/webp'); 
            break;
        }
    }
    private function taskManager($add, $_p, $_i) {
        $_task_num = $this->data('task_num');
        if ($_task_num > $CONF['req']['task_num']) {
            contentType($_i['extension']);
            die($file_get_contents($_p));
        } else {
            $this->data('task_num', $add ? ++$_task_num : --$_task_num);
        }
    }
}
?>