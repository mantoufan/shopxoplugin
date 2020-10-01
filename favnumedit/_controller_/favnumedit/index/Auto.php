<?php
namespace app\plugins\favnumedit\index;

use think\Controller;
use app\plugins\favnumedit\service\Service;

class Auto extends Controller
{
    private $config = array();
    public function add($params = [])
    {
        if (!empty($params['goods_id'])) {
            Service::addDataAuto($params['goods_id']);
        }
        return '';
    }
}
?>