<?php
namespace app\plugins\sitemap\admin;
use think\Db;
use app\service\PluginsService;
use app\plugins\sitemap\wga\WGA;

class sitemap {
    var $caches;
    var $cacheFile;
    public $pageSize;
    public $sitemapDir;
    public function __construct()
    {
        // 读取分页数
        $ret = PluginsService::PluginsData('sitemap'); 
        $this->pageSize = $ret['data'] ? ($ret['data']['pageSize'] ? $ret['data']['pageSize'] : 10000) : 10000; 
        // Sitemap存储目录
        $downloadDir = ROOT . 'public' . DS . 'download';
        $this->sitemapDir = $downloadDir . DS . 'sitemap';
        if (!is_dir($this->sitemapDir)) {
            if (!is_dir($downloadDir)) {
                mkdir($downloadDir);
            }
            mkdir($this->sitemapDir);
        }
        $this->sitemapDir =  $this->sitemapDir . DS;
        // 读取缓存
        $this->cacheFile = dirname(__FILE__) . DS . 'cache.php';
    }
    /**
     * 获取数据库配置
    */
    private function getDbConfig()
    {
        if(!file_exists(ROOT.'config/database.php'))
        {
            return FALSE;
        }
        return $db_config = include ROOT.'config/database.php';
    }
    /**
     * 生成Sitemap
     * @param type: all|single
    */
    public function generate($tables = array('goods', 'article'), $type = 'all', $params = array())
    {
        $code = 200;
        $msg = 'success';
        if ($type === 'all') {
            $this->caches = array();
        } else {
            // 初始化缓存
            $caches = array();
            if (file_exists($this -> cacheFile)) {
                include($this -> cacheFile);
            }
            $this->caches = $caches;
            // 读取数据库前缀
            $db_config = $this -> getDbConfig();
            if ($db_config) {
                $prefix = $db_config['prefix'];
            } else {
                $code = -1;
                $msg = '数据库配置未找到';
            }
        }
        if ($code === 200) {
            foreach($tables as $key => $table) {
                $datas = array();
                if ($type === 'all') {
                    $result = WGA::getGenerateWhereByTable($table, $key);
                    if (isset($result['msg'])) {
                        return array('error' => $result['msg']);
                    }
                    foreach($result as $key => $row) {
                        $row['url'] = $this -> getUrl($table, $row['id']);
                        if ($row['upd_time'] === 0) {
                            $row['upd_time'] = $row['add_time'];
                        }
                        $datas[$row['id']] = $row;
                    }
                    $this->caches[$table] = $datas;
                } else {
                    if (!$params['is_enable']) {
                        return false;
                    }
                    if ($params['id']) {// 保存
                        $id = $params['id']; 
                    } else {// 新增
                        $row = Db::table('information_schema.tables') -> where('table_name=\'' . $prefix . $table . '\'') -> find();
                        $id = $row['auto_increment'];
                    }
                    $row['id']  = $id;
                    $row['url'] = $this -> getUrl($table, $id);
                    $row['upd_time'] = time();
                    if (!$this->caches[$table]) {
                        $this->caches[$table] = array();
                    }
                    $this->caches[$table][$id] = $row;
                }
            }
            $urlset = array();
            foreach($this->caches as $key => $urls) {
                $urlset = array_merge($urlset, array_values($urls));
            }
            file_put_contents($this->cacheFile, '<?php $caches='.var_export($this->caches, true) .';?>');
            $urlset = $this->arraySort($urlset, 'upd_time', SORT_DESC);
            $sitemap_header = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
            $sitemap_footer = '</urlset>';
            $urlsets = array_chunk($urlset, $this->pageSize);
            foreach($urlsets as $key => $urlset) {
                $sitemap = '';
                foreach($urlset as $k => $url) {
                    $updDateTime = new \DateTime(date('Y-m-d H:i:s', $url['upd_time']));
                    $upd_time = $updDateTime -> format('c');
                    $sitemap.= '<url><loc>' . $url['url'] . '</loc><lastmod>' . $upd_time. '</lastmod></url>';
                }
                file_put_contents($this->sitemapDir . 'sitemap' . ($key > 0 ? $key : '') . '.xml', $sitemap_header . $sitemap . $sitemap_footer);
            }
        }
        return array(
            'code' => $code,
            'msg' => $msg
        );
    }
     /**
     * 获取sitemap.xml
     * @param $dir string 查看的路径
     * @return array
     */
    public function getSitemaps($dir)
    {
        $code = 200;
        $msg = 'success';
        $data = array();
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                $i = 0;
                while (($file = readdir($dh)) !== false) {
                    if ($file != "." && $file != ".." && !is_dir($file) && substr($file, 0, 7) === 'sitemap') {
                        $files[$i]["name"] = $file;//获取文件名称
                        $files[$i]["size"] = round((filesize($dir.$file)/1024),2).' Kb';//获取文件大小
                        $files[$i]["time"] = date("Y-m-d H:i:s",filemtime($dir.$file));//获取文件最近修改日期
                        $i++;
                    }
                }
            }
            closedir($dh);
            if (empty($files)) {
                $code = -1;
                $msg = '暂无数据';
            } else {
                foreach($files as $k=>$v){
                    $size[$k] = $v['size'];
                    $time[$k] = $v['time'];
                    $name[$k] = $v['name'];
                    $order_num = preg_replace('/(\D+)/', '', $v['name']);
                    $order[$k] = ($order_num ? $order_num : 0);
                }
                array_multisort($order, SORT_ASC, SORT_NUMERIC, $files);//按名称正序
                $data = $files;
            }
        }
        return array(
            'code' => $code,
            'msg' => $msg,
            'data' => $data
        );
    }
    // 设置pagesize
    public function setPagesize($params = [])
    {
        $code = 200;
        $msg = 'success';
        if (empty($params['pagesize']))
        {
            $code = -1;
            $msg = '不能为空';
        }
        if (!is_numeric($params['pagesize']))
        {
            $code = -1;
            $msg = '必须为数字';
        }
        if ($params['pagesize'] <= 0)
        {
            $code = -1;
            $msg = '必须为正数';
        }
        if ($code === 200) {
            PluginsService::PluginsDataSave(['plugins'=>'sitemap', 'data'=>array(
                'pageSize' => $params['pagesize']
           )]);
        }
        return array(
            'code' => $code,
            'msg' => $msg
        );
    }
    // 删除sitemap.xml文件
    public function delFile($params = [])
    {
        $code = 200;
        $msg = 'success';
        if (empty($params['id']))
        {
            $code = -1;
            $msg = '数据配置异常';
        }
        if (!file_exists($this->sitemapDir . $params['id']))
        {
            $code = -1;
            $msg = '已删除';
        }
        if (!unlink($this->sitemapDir . $params['id']))
        {
            $code = -1;
            $msg = '删除失败，请确保有权限';
        }
        return array(
            'code' => $code,
            'msg' => $msg
        );
    }
    // 下载文件
    public function downFile($params = [])
    {
        $code = 200;
        $msg = 'success';
        if (empty($params['id']))
        {
            $code = -1;
            $msg = '数据配置异常';
        }
        if (!file_exists($this->sitemapDir . $params['id']))
        {
            $code = -1;
            $msg = 'Sitemap.xml已删除';
        }
        if ($code === 200) {
            $file = $this->sitemapDir . $params['id'];
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.basename($file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            ob_clean();
            flush();
            readfile($file);
            exit;
        } else {
            return array(
                'code' => $code,
                'msg' => $msg
            );
        }
    }
    /**
     * 二维数组根据某个字段排序
     * @param array $array 要排序的数组
     * @param string $keys   要排序的键字段
     * @param string $sort  排序类型  SORT_ASC     SORT_DESC 
     * @return array 排序后的数组
    */
    private function arraySort($array, $keys, $sort = SORT_DESC) {
        $keysValue = [];
        foreach ($array as $k => $v) {
            $keysValue[$k] = $v[$keys];
        }
        array_multisort($keysValue, $sort, $array);
        return $array;
    }
    /**
     * 获取Http协议头
    */
    private function getHttpType() {
        return ((isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) === 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https')) ? 'https:' : 'http:'; 
    }
    /**
     * 生成Url
     */
    private function getUrl($table, $id) {
        switch ($table) {
            case 'goods':
                $url = str_replace('index.php?s=/', '', MyUrl('index/goods/index', ['id' => $id]));
            break;
            case 'article':
                $url = str_replace('index.php?s=/', '', MyUrl('index/article/index', ['id'=> $id]));
            break;
            default:
                $url = '';
        }
        return $url;
    }
}
?>