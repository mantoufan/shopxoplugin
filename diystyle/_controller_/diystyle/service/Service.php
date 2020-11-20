<?php
namespace app\plugins\diystyle\service;
use app\service\PluginsService;

class Service           
{
	public static function config()
	{
		return array(
			'hsl' => '352,63%,52%',
			'color_amp' => '#d2364c',
			'color_amp_second' => '#ff6a80'
		);
	}
	public static function root() {
		return str_replace('\\', '/', dirname(__FILE__)) . '/../../../../';
	}
	public static function tpl() {
		$root = self::root();
		$tpl_path = $root . 'application/index/view';
		$a = array();
		foreach(glob($tpl_path . '/*') as $v) {
			if (is_dir($v)) {
				$info = pathinfo($v);
				if (is_file($v . '/config.json')) {
					$config = json_decode(file_get_contents($v . '/config.json'), true);
					array_push($a, array(
						'id' => $info['filename'],
						'name' => $config['name']
					));
				} else {
					array_push($a, array(
						'id' => $info['filename'],
						'name' => $info['filename']
					));
				}
			}
		}
		return $a;
	}
	public static function append($tpl, $hue) {
		$root = self::root();
		$tpl_path_css_common = $root . 'public/static/index/' . $tpl . '/css/common.css';
		if (is_file($tpl_path_css_common)) {
			include_once dirname(__FILE__) . '/mtfReplace.php';
			$mtfReplace = new \mtfReplace();
			$mtfReplace->append(array(
				$tpl_path_css_common => array(
					'.last{border-right:none !important;}' => $hue ? '.mobile-navigation, .search-bar, .am-topbar, .am-btn, .tb-detail-hd, .tm-ind-panel, .iteminfo_parameter, .am-sticky-placeholder, .buy-nav, .title, .am-pagination > .am-active > a, .am-pagination > .am-active > a:hover, .goods-category-s, .am-btn-primary, .banner-mixed, .plugins-footercustomerservice-customer-service, .am-footer-default, .various .am-panel-default > .am-panel-hd i, .user-sidebar, .user-base, .select, .search-list .items {filter:hue-rotate(' . $hue . 'deg);}.user-base img, .am-footer-default img, .goods-category-s img, .search-list .items img, .am-topbar img, .banner-mixed img {filter:hue-rotate(-' . $hue . 'deg);}.search-bar .am-btn, .am-topbar .am-btn {filter:hue-rotate(0deg);}' : '',
				),
			));
		}
	}
	public static function replace($color_amp) {
		include_once dirname(__FILE__) . '/mtfReplace.php';
		$conf = self::config();
		$root = self::root();
		$mtfReplace = new \mtfReplace();
		$mtfReplace->replace(array(
			$root . 'sourcecode/weixin/app.wxss' => array(
				$conf['color_amp'] => $color_amp ? $color_amp : '',
			),
			$root . 'sourcecode/weixin/pages/index/index.wxss' => array(
				$conf['color_amp'] => $color_amp ? $color_amp : '',
			),
			$root . 'sourcecode/weixin/components/quick-nav/quick-nav.wxss' => array(
				'rgb(210 54 76 / 80%)' => $color_amp ? $color_amp . ';opacity: .5' : '',
			),
			$root . 'sourcecode/weixin/app.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp']) . '$3',
				'/("selectedColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp']) . '$3',
			),
			$root . 'sourcecode/weixin/pages/index/index.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp']) . '$3',
			),
			$root . 'sourcecode/weixin/pages/user/user.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp']) . '$3',
			),
			$root . 'sourcecode/weixin/pages/plugins/distribution/extraction/extraction.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/weixin/pages/plugins/distribution/extraction-apply/extraction-apply.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/weixin/pages/plugins/distribution/extraction-apply/extraction-apply.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/weixin/pages/plugins/distribution/extraction-order/extraction-order.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/weixin/pages/plugins/distribution/introduce/introduce.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/weixin/pages/plugins/distribution/order/order.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/weixin/pages/plugins/distribution/order-detail/order-detail.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/weixin/pages/plugins/distribution/poster/poster.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/weixin/pages/plugins/distribution/profit/profit.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/weixin/pages/plugins/distribution/order-detail/order-detail.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/weixin/pages/plugins/distribution/profit-detail/profit-detail.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/weixin/pages/plugins/distribution/statistics/statistics.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/weixin/pages/plugins/distribution/order-detail/order-detail.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/weixin/pages/plugins/distribution/team/team.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/weixin/pages/plugins/distribution/user/user.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/weixin/pages/plugins/distribution/order-detail/order-detail.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/weixin/pages/plugins/distribution/order-detail/order-detail.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			
			$root . 'sourcecode/toutiao/app.ttss' => array(
				$conf['color_amp'] => $color_amp ? $color_amp : '',
			),
			$root . 'sourcecode/toutiao/pages/index/index.ttss' => array(
				$conf['color_amp'] => $color_amp ? $color_amp : '',
			),
			$root . 'sourcecode/toutiao/components/quick-nav/quick-nav.ttss' => array(
				'rgb(210 54 76 / 80%)' => $color_amp ? $color_amp . ';opacity: .5' : '',
			),
			$root . 'sourcecode/toutiao/app.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp']) . '$3',
				'/("selectedColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp']) . '$3',
			),
			$root . 'sourcecode/toutiao/pages/index/index.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp']) . '$3',
			),
			$root . 'sourcecode/toutiao/pages/user/user.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp']) . '$3',
			),
			$root . 'sourcecode/toutiao/pages/plugins/distribution/extraction/extraction.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/toutiao/pages/plugins/distribution/extraction-apply/extraction-apply.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/toutiao/pages/plugins/distribution/extraction-apply/extraction-apply.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/toutiao/pages/plugins/distribution/extraction-order/extraction-order.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/toutiao/pages/plugins/distribution/introduce/introduce.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/toutiao/pages/plugins/distribution/order/order.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/toutiao/pages/plugins/distribution/order-detail/order-detail.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/toutiao/pages/plugins/distribution/poster/poster.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/toutiao/pages/plugins/distribution/profit/profit.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/toutiao/pages/plugins/distribution/order-detail/order-detail.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/toutiao/pages/plugins/distribution/profit-detail/profit-detail.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/toutiao/pages/plugins/distribution/statistics/statistics.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/toutiao/pages/plugins/distribution/order-detail/order-detail.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/toutiao/pages/plugins/distribution/team/team.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/toutiao/pages/plugins/distribution/user/user.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/toutiao/pages/plugins/distribution/order-detail/order-detail.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/toutiao/pages/plugins/distribution/order-detail/order-detail.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			
			$root . 'sourcecode/qq/app.qss' => array(
				$conf['color_amp'] => $color_amp ? $color_amp : '',
			),
			$root . 'sourcecode/qq/pages/index/index.qss' => array(
				$conf['color_amp'] => $color_amp ? $color_amp : '',
			),
			$root . 'sourcecode/qq/components/quick-nav/quick-nav.qss' => array(
				'rgb(210 54 76 / 80%)' => $color_amp ? $color_amp . ';opacity: .5' : '',
			),
			$root . 'sourcecode/qq/app.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp']) . '$3',
				'/("selectedColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp']) . '$3',
			),
			$root . 'sourcecode/qq/pages/index/index.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp']) . '$3',
			),
			$root . 'sourcecode/qq/pages/user/user.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp']) . '$3',
			),
			$root . 'sourcecode/qq/pages/plugins/distribution/extraction/extraction.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/qq/pages/plugins/distribution/extraction-apply/extraction-apply.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/qq/pages/plugins/distribution/extraction-apply/extraction-apply.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/qq/pages/plugins/distribution/extraction-order/extraction-order.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/qq/pages/plugins/distribution/introduce/introduce.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/qq/pages/plugins/distribution/order/order.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/qq/pages/plugins/distribution/order-detail/order-detail.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/qq/pages/plugins/distribution/poster/poster.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/qq/pages/plugins/distribution/profit/profit.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/qq/pages/plugins/distribution/order-detail/order-detail.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/qq/pages/plugins/distribution/profit-detail/profit-detail.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/qq/pages/plugins/distribution/statistics/statistics.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/qq/pages/plugins/distribution/order-detail/order-detail.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/qq/pages/plugins/distribution/team/team.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/qq/pages/plugins/distribution/user/user.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/qq/pages/plugins/distribution/order-detail/order-detail.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/qq/pages/plugins/distribution/order-detail/order-detail.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			
			$root . 'sourcecode/baidu/app.css' => array(
				$conf['color_amp'] => $color_amp ? $color_amp : '',
			),
			$root . 'sourcecode/baidu/pages/index/index.css' => array(
				$conf['color_amp'] => $color_amp ? $color_amp : '',
			),
			$root . 'sourcecode/baidu/components/quick-nav/quick-nav.css' => array(
				'rgb(210 54 76 / 80%)' => $color_amp ? $color_amp . ';opacity: .5' : '',
			),
			$root . 'sourcecode/baidu/app.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp']) . '$3',
				'/("selectedColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp']) . '$3',
			),
			$root . 'sourcecode/baidu/pages/index/index.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp']) . '$3',
			),
			$root . 'sourcecode/baidu/pages/user/user.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp']) . '$3',
			),
			$root . 'sourcecode/baidu/pages/plugins/distribution/extraction/extraction.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/baidu/pages/plugins/distribution/extraction-apply/extraction-apply.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/baidu/pages/plugins/distribution/extraction-apply/extraction-apply.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/baidu/pages/plugins/distribution/extraction-order/extraction-order.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/baidu/pages/plugins/distribution/introduce/introduce.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/baidu/pages/plugins/distribution/order/order.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/baidu/pages/plugins/distribution/order-detail/order-detail.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/baidu/pages/plugins/distribution/poster/poster.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/baidu/pages/plugins/distribution/profit/profit.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/baidu/pages/plugins/distribution/order-detail/order-detail.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/baidu/pages/plugins/distribution/profit-detail/profit-detail.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/baidu/pages/plugins/distribution/statistics/statistics.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/baidu/pages/plugins/distribution/order-detail/order-detail.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/baidu/pages/plugins/distribution/team/team.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/baidu/pages/plugins/distribution/user/user.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/baidu/pages/plugins/distribution/order-detail/order-detail.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/baidu/pages/plugins/distribution/order-detail/order-detail.json' => array(
				'/("navigationBarBackgroundColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
				'/("backgroundColorTop":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			
			$root . 'sourcecode/alipay/app.acss' => array(
				$conf['color_amp'] => $color_amp ? $color_amp : '',
			),
			$root . 'sourcecode/alipay/pages/index/index.acss' => array(
				$conf['color_amp'] => $color_amp ? $color_amp : '',
			),
			$root . 'sourcecode/alipay/components/quick-nav/quick-nav.acss' => array(
				'rgb(210 54 76 / 80%)' => $color_amp ? $color_amp . ';opacity: .5' : '',
			),
			$root . 'sourcecode/alipay/app.json' => array(
				'/("titleBarColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp']) . '$3',
				'/("selectedColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp']) . '$3',
			),
			$root . 'sourcecode/alipay/pages/index/index.json' => array(
				'/("titleBarColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp']) . '$3',
			),
			$root . 'sourcecode/alipay/pages/user/user.json' => array(
				'/("titleBarColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp']) . '$3',
			),
			$root . 'sourcecode/alipay/pages/plugins/distribution/extraction/extraction.json' => array(
				'/("titleBarColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/alipay/pages/plugins/distribution/extraction-apply/extraction-apply.json' => array(
				'/("titleBarColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/alipay/pages/plugins/distribution/extraction-apply/extraction-apply.json' => array(
				'/("titleBarColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/alipay/pages/plugins/distribution/extraction-order/extraction-order.json' => array(
				'/("titleBarColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/alipay/pages/plugins/distribution/introduce/introduce.json' => array(
				'/("titleBarColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/alipay/pages/plugins/distribution/order/order.json' => array(
				'/("titleBarColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/alipay/pages/plugins/distribution/order-detail/order-detail.json' => array(
				'/("titleBarColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/alipay/pages/plugins/distribution/poster/poster.json' => array(
				'/("titleBarColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/alipay/pages/plugins/distribution/profit/profit.json' => array(
				'/("titleBarColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/alipay/pages/plugins/distribution/order-detail/order-detail.json' => array(
				'/("titleBarColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/alipay/pages/plugins/distribution/profit-detail/profit-detail.json' => array(
				'/("titleBarColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/alipay/pages/plugins/distribution/statistics/statistics.json' => array(
				'/("titleBarColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/alipay/pages/plugins/distribution/order-detail/order-detail.json' => array(
				'/("titleBarColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/alipay/pages/plugins/distribution/team/team.json' => array(
				'/("titleBarColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/alipay/pages/plugins/distribution/user/user.json' => array(
				'/("titleBarColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/alipay/pages/plugins/distribution/order-detail/order-detail.json' => array(
				'/("titleBarColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
			$root . 'sourcecode/alipay/pages/plugins/distribution/order-detail/order-detail.json' => array(
				'/("titleBarColor":.*?")(.*?)(")/' => '$1' . ($color_amp ? $color_amp : $conf['color_amp_second']) . '$3',
			),
		));
	}
}
?>