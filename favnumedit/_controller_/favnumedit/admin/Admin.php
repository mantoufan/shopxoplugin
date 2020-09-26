<?php
namespace app\plugins\favnumedit\admin;

use think\Controller;

/**
 * 收藏数量修改 - 后台管理
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2016-12-01T21:51:08+0800
 */
class Admin extends Controller
{
    // 构造方法
    public function __construct($params = [])
    {
        parent::__construct();
    }

    // 后台管理入口
    public function index($params = [])
    {
        // 数组组装
        $this->assign('data', ['hello', 'world!']);
        $this->assign('msg', 'hello world! admin');
        return $this->fetch('../../../plugins/view/favnumedit/admin/admin/index');
    }
}
?>