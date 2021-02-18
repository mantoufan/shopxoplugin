<?php
namespace app\plugins\picviewer;
use think\Controller;
use app\service\PluginsService;
use app\plugins\picviewer\wga\WGA;
/**
 * 钩子入口
 * @author   小宇
 * @blog   https://www.madfan.cn
 * @version  1.0.0
 * @datetime 2019-04-29T12:51:08+0800
 */
class Hook extends Controller
{
  // 应用响应入口
  public function run($params = [])
  {
    $ret = '';
    if(!empty($params['hook_name']))
    {
      switch($params['hook_name'])
      {
        case 'plugins_css' :
          $ret = $this->getCSS('array');
          break;
        case 'plugins_js' :
          $ret = $this->getJS('array');
          break;
        case 'plugins_admin_common_header' :
          $ret = $this->getCSS('html');
        case 'plugins_common_header' :
          $ret .= $this->getStyle();
          break;
        case 'plugins_admin_common_page_bottom' :
          $ret = $this->getJS('html');
        case 'plugins_common_page_bottom' :
          $ret .= $this->getScript($params);
          break;
      }
    }
    return $ret;
  }
	private function public_url($url) 
	{
		return config('shopxo.public_host') . $url;
	}
  private function getCSS($type = 'array')
  {
    $ar = array(
			'static/plugins/css/picviewer/index/jquery.mtfpicviewer.css',
		);
    if ($type === 'html') {
      $html = '';
      foreach($ar as $k => $v) {
        $html .= '<link rel="stylesheet" type="text/css" href="' . $this->public_url($v) . '" />';
      }
      return $html;
    }
    return $ar;
  }
  private function getJS($type = 'array')
  {
    $ar = array(
      'static/plugins/js/picviewer/index/jquery.mtfpicviewer.js',
      'static/plugins/js/picviewer/index/rgbaster.min.js',
      'static/plugins/js/picviewer/index/style.js',
    );
    if ($type === 'html') {
      $html = '';
      foreach($ar as $k => $v) {
        $html .= '<script type="text/javascript" src="' . $this->public_url($v) . '"></script>';
      }
      return $html;
    }
    return $ar;
  }
  // 获取样式
  private function getStyle()
  {
    $ret = PluginsService::PluginsData('picviewer');
    if($ret['code'] == 0)
    {
      if (isset($ret['data']['available']) && $ret['data']['available']) {
        $maxWidth = isset($ret['data']['max_width']) && !empty($ret['data']['max_width']) ? "max-width: " . $ret['data']['max_width'] . ";" : '';
        $backgroundColor = isset($ret['data']['background_color']) && !empty($ret['data']['background_color']) ? "background-color: " . $ret['data']['background_color'] . ";" : '';
        return '<style>.plugin-picviewer {' . $maxWidth . $backgroundColor . '}</style>';
      } else {
        return '';
      }       
    } else {
      return $ret['msg'];
    }
  }
  // 获取脚本
  private function getScript($params)
  {
    $ret = PluginsService::PluginsData('picviewer');
    if($ret['code'] == 0)
    {
      if (isset($ret['data']['available']) && $ret['data']['available']) { 
        $opt = "{selector: 'img', attrSelector: 'src', className: 'plugin-picviewer'";
        if (isset($ret['data']['background_color_auto']) && !empty($ret['data']['background_color_auto'])) {
          $setBgWithDominantColor = '(curIndex) {pluginPicviewer.setBgWithDominantColor(curIndex, \'' . (isset($ret['data']['background_color']) && !empty($ret['data']['background_color']) ? $ret['data']['background_color'] : '') . '\')}';
          $opt.= ', onChange' . $setBgWithDominantColor . ', onOpen' . $setBgWithDominantColor;
        }
        
        $selectors = Array();
        $script = Array();
        if ($params['hook_name'] === 'plugins_admin_common_page_bottom') {
          // 适配后台页面
          $q = isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : $_SERVER['REQUEST_URI'];
          if (stripos($q, '/slide/') || stripos($q, '/brand/') || stripos($q, '/apphomenav/') || stripos($q, '/appcenternav/')) { // 首页轮播 品牌管理 首页导航 用户中心导航
            $selectors []= '.am-table';
            if (stripos($q, '/slide/')) {
              $script []= "$('.am-table img').css({'width':'100%','height':'auto','padding':'5px 10px','vertical-align':'middle'})";
            } 
          } elseif (stripos($q, '/goods/detail/id')) { // 商品详情
            $selectors []= '.am-panel-bd';
          }
        } elseif (isset($ret['data']['scroll_auto']) && !empty($ret['data']['scroll_auto'])) {
          $opt.= ', onClose(curIndex) {pluginPicviewer.scrollAuto(curIndex)}';
        }
        
        return "<script>$('.detail-content, .article-content, .customview-content, .plug-file-upload-view" . (!empty($selectors) ? ', ' . implode(', ', $selectors) : '') . "').mtfpicviewer(" . $opt . "});$('.goods-comment-content').mtfpicviewer(" . $opt . ", parentSelector: '.comment-images'});" . implode(';', $script) . "</script>";
      } else {
        return '';
      }       
    } else {
      return $ret['msg'];
    }
  }
}
?>