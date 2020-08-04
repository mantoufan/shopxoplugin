<?php
namespace app\plugins\sitemap\admin;
use app\service\PluginsAdminService;
use app\service\PluginsService;
use think\Controller;
/**
 * Sitemap - 后台管理
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2016-12-01T21:51:08+0800
 */
class Admin extends Controller
{
    // 后台管理入口
    public function index($params = [])
    {
        $sitemap = new sitemap();
        $data_list = $sitemap -> getSitemaps($sitemap->sitemapDir);
        $is_enable = PluginsService::PluginsStatus('sitemap');
        // 数组组装
        $this->assign('data_list', $data_list['data']);
        $this->assign('page_size', $sitemap->pageSize);
        $this->assign('is_enable', $is_enable);
        return $this->fetch('../../../plugins/view/sitemap/admin/admin/index');
    }
    // 开启插件
    public function open()
    {
        if (PluginsService::PluginsStatus('sitemap'))
        {
            return DataReturn('插件已启动', 0);
        }
        $params = ['id' => 'sitemap', 'state' => 1];
        return PluginsAdminService::PluginsStatusUpdate($params);
    }
    // 生成Sitemap
    public function sitemap()
    {
        $sitemap = new sitemap();
        return $this -> tip($sitemap -> generate(array('goods', 'article')), 'all');
    }
   
    // 设置pagesize
    public function set_pagesize($params = [])
    {
        $sitemap = new sitemap();
        return $this -> tip($sitemap -> setPagesize($params), 'page');
    }
    // 删除sitemap.xml文件
    public function del_file($params = [])
    {
        $sitemap = new sitemap();
        return $this -> tip($sitemap -> delFile($params));
    }
    // 下载文件
    public function down_file($params = [])
    {
        $sitemap = new sitemap();
        return $this -> tip($sitemap -> downFile($params));
    }
    /**
     * 提示
     * @param type ajax|page 原页面提示/新页面提示
     */
    private function tip($ret, $type = 'ajax') {
        if ($ret['error']) {
            return $type === 'ajax' ? DataReturn($ret['error'], -100) : $this -> error($ret['error'], null, '', 2);
        } else {
            return $type === 'ajax' ? DataReturn('操作成功', 0) : $this -> success('操作成功', null, '', 2);
        }
    }
}
?>