<?php
ini_set("display_errors", "On");
error_reporting(E_ALL);
define('CONF', array(
    'js' => array(
        'amazeui.css' => 'https://cdn.jsdelivr.net/npm/amazeui@2.7.2/dist/css/amazeui.min.css',
        'jquery-2.1.0.js' => 'https://cdn.jsdelivr.net/combine/npm/jquery@2.1.0,npm/amazeui@2.7.2/dist/js/amazeui.min.js,npm/echarts@4.1.0/dist/echarts.min.js,npm/clipboard@2.0.4',
        'amazeui.min.js' => '',
        'echarts.min.js' => '',
        'clipboard.min.js' => ''
    )
));
$_root = dirname(__FILE__) . '/../../../../';
$_p = $_root . $_GET['p'];
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
                include_once('../vendor/autoload.php');
                $minifier = new marcocesarato\minifier\Minifier();
                $_c = file_get_contents($_p);
                if ($_i['extension'] === 'css') {
                    header('Content-type: text/css');
                    echo $minifier->minifyCSS($_c);
                } else if ($_i['extension'] === 'js') {
                    header('Content-type: text/javascript');
                    echo $minifier->minifyJS($_c);
                } else {
                    header('Content-type: text/html');
                    echo $minifier->minifyHTML($_c);
                }
            }
        break;
        default:
            echo file_get_contents($_p);
    }
}
?>