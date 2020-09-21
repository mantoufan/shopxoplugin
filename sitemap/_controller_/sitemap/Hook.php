<?php
namespace app\plugins\sitemap;
use think\Controller;
/**
 * Sitemap - 钩子入口
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2016-12-01T21:51:08+0800
 */
class Hook extends Controller
{
    // 应用响应入口
    public function run($params = [])
    {
        // 是否控制器钩子
        // is_backend 当前为后端业务处理
        // hook_name 钩子名称
        if(isset($params['is_backend']) && $params['is_backend'] === true && !empty($params['hook_name']))
        {
            // 参数一   描述
            // 参数二   0 为处理成功, 负数为失败
            // 参数三   返回数据
            // return DataReturn('返回描述', 0);
            switch($params['hook_name'])
            {
                case 'plugins_service_goods_save_handle' : // 新增、保存商品
                    $this->sitemap(array('goods'), 'single', array('id' => $params['goods_id'], 'is_enable' => @$params['params']['is_shelves']));
                    break;
                case 'plugins_service_article_save_handle' : // 新增、保存文章
                    $this->sitemap(array('article'), 'single', array('id' => $params['article_id'], 'is_enable' => @$params['params']['is_enable']));
                    break;
            }
        }
    }
    /**
     * 生成Sitemap
    */
    private function sitemap($tables, $type, $params)
    {
        $sitemap = new admin\sitemap();
        $sitemap -> generate($tables, $type, $params);
    }
}
?>