<?php
namespace app\plugins\optimizer\service;
use app\service\PluginsService;

class Service
{
    public static function config() {
        return array(
            'cache_dir' => 'public/cache/optimizer/',
            'cache_time' => 60 * 60 * 6,
            'task_num' => 3,
            'watermark_pos' =>  array(
                0 => array('name' => '无水印', 'checked' => true),
                'left-top' => array('name' => '左上'),
                'left-bottom' => array('name' => '左下'),
                'right-top' => array('name' => '右上'),
                'right-bottom' => array('name' => '右下')
            ),
            'rules' => array(// 文件替换引擎
                'application/index/view/*/public/header.html' => array(
                    '{{$public_host}}static/common/lib/assets/css/amazeui.css' => 'https://cdn.jsdelivr.net/npm/amazeui@2.7.2/dist/css/amazeui.min.css', // 302跳转
                    '{{$public_host}}static/common/lib/amazeui-switch/amazeui.switch.css' => 'https://cdn.jsdelivr.net/combine/npm/amazeui-switch@3.3.3/amazeui.switch.min.css,npm/amazeui-chosen@1.3.0/amazeui.chosen.min.css,npm/cropper@0.9.2/dist/cropper.min.css,npm/amazeui-tagsinput@0.5.2/amazeui.tagsinput.min.css',
                    '{{$public_host}}static/common/lib/amazeui-chosen/amazeui.chosen.css?v={{:MyC(\'home_static_cache_version\')}}"' => '',
                    '{{$public_host}}static/common/lib/cropper/cropper.min.css?v={{:MyC(\'home_static_cache_version\')}}' => '',
                    '{{$public_host}}static/common/lib/amazeui-tagsinput/amazeui.tagsinput.css?v={{:MyC(\'home_static_cache_version\')}}' => '',
                ),
                'application/index/view/*/public/footer.html' => array(
                    '{{$public_host}}static/common/lib/jquery/jquery-2.1.0.js' => 'https://cdn.jsdelivr.net/combine/npm/jquery@2.1.0,npm/amazeui@2.7.2/dist/js/amazeui.min.js,npm/echarts@4.1.0/dist/echarts.min.js,npm/echarts@4.1.0/theme/macarons.min.js,npm/amazeui-switch@3.3.3,npm/amazeui-chosen@1.3.0,npm/amazeui-dialog@0.0.2/dist/amazeui.dialog.min.js,npm/amazeui-tagsinput@0.5.2,npm/cropper@0.9.2,npm/clipboard@2.0.4',
                    '{{$public_host}}static/common/lib/assets/js/amazeui.min.js?v={{:MyC(\'home_static_cache_version\')}}' => '', // 返回空
                    '{{$public_host}}static/common/lib/echarts/echarts.min.js?v={{:MyC(\'home_static_cache_version\')}}' => '',
                    '{{$public_host}}static/common/lib/echarts/macarons.js?v={{:MyC(\'home_static_cache_version\')}}'=> '',
                    '{{$public_host}}static/common/lib/amazeui-switch/amazeui.switch.min.js?v={{:MyC(\'home_static_cache_version\')}}'=> '',
                    '{{$public_host}}static/common/lib/amazeui-chosen/amazeui.chosen.min.js?v={{:MyC(\'home_static_cache_version\')}}'=> '',
                    '{{$public_host}}static/common/lib/amazeui-dialog/amazeui.dialog.js?v={{:MyC(\'home_static_cache_version\')}}'=> '',
                    '{{$public_host}}static/common/lib/amazeui-tagsinput/amazeui.tagsinput.min.js?v={{:MyC(\'home_static_cache_version\')}}'=> '',
                    '{{$public_host}}static/common/lib/cropper/cropper.min.js?v={{:MyC(\'home_static_cache_version\')}}'=> '',
                    '{{$public_host}}static/common/lib/clipboard/clipboard.min.js?v={{:MyC(\'home_static_cache_version\')}}'=> '',
                    '{{$public_host}}static/common/lib/My97DatePicker/WdatePicker.js'=> 'https://cdn.jsdelivr.net/npm/my97datepicker@4.8.0/WdatePicker.min.js',
                )
            ),
            'rewrite' => array(
                'Apache / Kangle' => '<IfModule mod_rewrite.c>' . "\n" .
                                    '  RewriteEngine on' . "\n" .
                                    '  RewriteBase /' . "\n" .
                                    '  # ShopXO及ThinkPHP伪静态规则' . "\n" .
                                    '  RewriteCond %{REQUEST_FILENAME} !-d' . "\n" .
                                    '  RewriteCond %{REQUEST_FILENAME} !-f' . "\n" .
                                    '  RewriteRule ^(.*)$ index.php?s=/$1 [QSA,PT,L]' . "\n" .
                                    '  # 加速优化插件伪静态规则' . "\n" .
                                    '  RewriteRule ^(.*.(js|css|jpg|jpeg|png))$ /index.php?s=index/plugins/index/pluginsname/optimizer/pluginscontrol/mtf/pluginsaction/better&path=$1' . "\n" .
                                    '</IfModule>',
                'Nginx' => 'if (!-e $request_filename){' . "\n" .
                        '    rewrite  ^(.*)$  /index.php?s=$1  last;   break; # ShopXO及ThinkPHP伪静态规则' . "\n" .
                        '}' . "\n" .
                        'rewrite ^(.*.(js|css|jpg|jpeg|png))$ /index.php?s=index/plugins/index/pluginsname/optimizer/pluginscontrol/mtf/pluginsaction/better&path=$1; # 加速优化插件伪静态规则',
                'IIS' =>   '<?xml version="1.0" ?>' . "\n" .
                        '<rules>' . "\n" .
                        '<!-- ShopXO及ThinkPHP伪静态规则 -->' . "\n" .
                        '    <rule name="OrgPage_rewrite" stopProcessing="true">' . "\n" .
                        '       <match url="^(.*)$"/>' . "\n" .
                        '       <conditions logicalGrouping="MatchAll">' . "\n" .
                        '		<add input="{HTTP_HOST}" pattern="^(.*)$"/>' . "\n" .
                        '		<add input="{REQUEST_FILENAME}" matchType="IsFile" negate="true"/>' . "\n" .
                        '		<add input="{REQUEST_FILENAME}" matchType="IsDirectory" negate="true"/>' . "\n" .
                        '        </conditions>' . "\n" .
                        '        <action type="Rewrite" url="index.php/{R:1}"/>' . "\n" .
                        '    </rule>' . "\n" .
                        '<!-- 加速优化插件伪静态规则 -->' . "\n" .
                        '    <rule name="Optimizer_rewrite" stopProcessing="true">' . "\n" .
                        '		<match url="^(.*.(js|css|jpg|jpeg|png))$"/>' . "\n" .
                        '        <action type="Rewrite" url="/index.php?s=index/plugins/index/pluginsname/optimizer/pluginscontrol/mtf/pluginsaction/better&amp;path={R:1}"/>' . "\n" .
                        '    </rule>' . "\n" .
                        '</rules>'
            )
        );
    }

    public static function root() {
        return str_replace('\\', '/', dirname(__FILE__)) . '/../../../../';
    }

    public static function mtfBetter($data = array()) {
        include(dirname(__FILE__) . '/mtfBetter/mtfBetter.class.php');
        $conf = self::config();
        $conf['cache_dir'] = self::root() . $conf['cache_dir'];
        unset($conf['file_rules'], $conf['rewrite']);
        return new \mtfBetter(array_merge($conf, $data));
    }
}
?>