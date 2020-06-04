<?php

// 公共助手函数

if (!function_exists('get_coordinate_address')) {

    /**
     * 经纬度->地址
     * 全球逆地理编码服务
     * @param float $lng 经度
     * @param float $lat 纬度
     * @return array
     */
    function get_coordinate_address($lng,$lat) {
        $ak = '9ZW1C601mHY3SAXNgcYZDRZheGku9QcX';
        $url = "http://api.map.baidu.com/reverse_geocoding/v3/?ak=".$ak."&output=json&coordtype=gcj02ll&ret_coordtype=gcj02ll&location=".$lat.",".$lng;

        $address_data = file_get_contents($url);
        $json_data = json_decode($address_data);

        return $json_data->result;
    }
}

if (!function_exists('get_address_coordinate')) {

    /**
     * 地址->经纬度
     * 地理编码服务
     * @param string $address 地址
     * @return array
     */
    function get_address_coordinate($address) {
        $ak = '9ZW1C601mHY3SAXNgcYZDRZheGku9QcX';
        $addressurl = urlencode($address);
        $url = "http://api.map.baidu.com/geocoding/v3/?address=".$addressurl."&ret_coordtype=gcj02ll&output=json&ak=".$ak;

        $address_data = file_get_contents($url);
        $json_data = json_decode($address_data);
        $data['lng'] = $json_data->result->location->lng;
        $data['lat'] = $json_data->result->location->lat;
        return $data;
    }
}

if (!function_exists('get_coordinate_distance')) {

    /**
     * 计算两点距离
     * 批量算路服务
     * @param string origins 起点坐标串
     * @param string destinations 终点坐标串
     * @return array
     */
    function get_coordinate_distance($lat1,$lng1,$lat2,$lng2) {
        $ak = '9ZW1C601mHY3SAXNgcYZDRZheGku9QcX';
        $url = "http://api.map.baidu.com/routematrix/v2/driving?output=json&origins=".$lat1.",".$lng1."&destinations=".$lat2.",".$lng2."&coord_type=gcj02&ak=".$ak;

        $data = file_get_contents($url);
        $json_data = json_decode($data,true);
        return $json_data['result'][0]['distance']['value'];
    }
}

if (!function_exists('get_nearby_location')) {

    /**
     * 圆形区域检索
     * @param string query 检索关键词
     * @param string location 检索中心点
     * @return array
     */
    function get_nearby_location($query,$lat,$lng) {
        $ak = '9ZW1C601mHY3SAXNgcYZDRZheGku9QcX';
        $url = "http://api.map.baidu.com/place/v2/search?query=".$query."&location=".$lat.",".$lng."&radius=2000&coord_type=2&ret_coordtype=gcj02ll&output=json&ak=".$ak;

        $data = file_get_contents($url);
        $json_data = json_decode($data);
        return $json_data;
    }
}

if (!function_exists('http_request')) {

    /**
     * CURL请求
     * @param $url 请求url地址
     * @param $method 请求方法 get post
     * @param null $postfields post数据数组
     * @param array $headers 请求header信息
     * @param bool|false $debug 调试开启 默认false
     * @return mixed
     */
    function http_request($url, $method = "GET", $postfields = null, $headers = array(), $debug = false)
    {
        $method = strtoupper($method);
        $ci = curl_init();
        /* Curl settings */
        curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ci, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.2; WOW64; rv:34.0) Gecko/20100101 Firefox/34.0");
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 60); /* 在发起连接前等待的时间，如果设置为0，则无限等待 */
        curl_setopt($ci, CURLOPT_TIMEOUT, 7); /* 设置cURL允许执行的最长秒数 */
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
        switch ($method) {
            case "POST":
                curl_setopt($ci, CURLOPT_POST, true);
                if (!empty($postfields)) {
                    $tmpdatastr = is_array($postfields) ? http_build_query($postfields) : $postfields;
                    curl_setopt($ci, CURLOPT_POSTFIELDS, $tmpdatastr);
                }
                break;
            default:
                curl_setopt($ci, CURLOPT_CUSTOMREQUEST, $method); /* //设置请求方式 */
                break;
        }
        $ssl = preg_match('/^https:\/\//i', $url) ? TRUE : FALSE;
        curl_setopt($ci, CURLOPT_URL, $url);
        if ($ssl) {
            curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
            curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, FALSE); // 不从证书中检查SSL加密算法是否存在
        }
        //curl_setopt($ci, CURLOPT_HEADER, true); /*启用时会将头文件的信息作为数据流输出*/
        curl_setopt($ci, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ci, CURLOPT_MAXREDIRS, 2);/*指定最多的HTTP重定向的数量，这个选项是和CURLOPT_FOLLOWLOCATION一起使用的*/
        curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ci, CURLINFO_HEADER_OUT, true);
        /*curl_setopt($ci, CURLOPT_COOKIE, $Cookiestr); * *COOKIE带过去** */
        $response = curl_exec($ci);
        $requestinfo = curl_getinfo($ci);
        $http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
        if ($debug) {
            echo "=====post data======\r\n";
            var_dump($postfields);
            echo "=====info===== \r\n";
            print_r($requestinfo);
            echo "=====response=====\r\n";
            print_r($response);
        }
        curl_close($ci);
        return $response;
    }
}

if (!function_exists('base_url')) {

    /**
     * 获取当前域名及根路径
     * @return string
     */
    function base_url()
    {
        $request = \think\Request::instance();
        $subDir = str_replace('\\', '/', dirname($request->server('PHP_SELF')));
        return $request->scheme() . '://' . $request->host() . $subDir . ($subDir === '/' ? '' : '/');
    }
}

if (!function_exists('write_log')) {

    /**
     * 写入日志
     * @param string|array $values
     * @param string $dir
     * @return bool|int
     */
    function write_log($values, $dir)
    {
        if (is_array($values))
            $values = print_r($values, true);
        // 日志内容
        $content = '[' . date('Y-m-d H:i:s') . ']' . PHP_EOL . $values . PHP_EOL . PHP_EOL;
        try {
            // 文件路径
            $filePath = $dir . '/logs/';
            // 路径不存在则创建
            !is_dir($filePath) && mkdir($filePath, 0755, true);
            // 写入文件
            return file_put_contents($filePath . date('Ymd') . '.log', $content, FILE_APPEND);
        } catch (\Exception $e) {
            return false;
        }
    }
}

if (!function_exists('upload_single')) {

    /**
     * 上传
     * @param $file
     * @return string
     */
    function upload_single($file)
    {
        if($file){
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info){
                $extension = $info->getExtension();
                if ($extension == 'jpg') {
                    $url = ROOT_PATH . 'public' . DS . "uploads\\" .$info->getSaveName();
                    $image = imagecreatefromstring(file_get_contents($url));
                    $exif = exif_read_data($url);

                    if (!empty($exif['Orientation'])) {
                        switch ($exif['Orientation']) {
                            case 8:
                                $image = imagerotate($image, 90, 0);
                                break;
                            case 3:
                                $image = imagerotate($image, 180, 0);
                                break;
                            case 6:
                                $image = imagerotate($image, -90, 0);
                                break;
                        }
                        imagejpeg($image, $url);
                        imagedestroy($image);
                    }
                }
                //$siteHost = 'http://'.$_SERVER['HTTP_HOST'].'/uploads/';
                $siteHost = '/uploads/';
                $path = str_replace("\\","/",$info->getSaveName());
                $fullPath = $siteHost.$path;
            }else{
                // 上传失败获取错误信息
                return $file->getError();
            }
        }
        return $fullPath;
    }
}


if (!function_exists('upload_customize')) {

    /**
     * 上传
     * @param $file
     * @return string
     */
    function upload_customize($file, $folder, $fileName)
    {
        if($file){
            $info = $file->move(ROOT_PATH . 'public' . DS . $folder, $fileName);
            if($info){
                $siteHost = '/uploads/';
                $path = str_replace("\\","/",$info->getSaveName());
                $fullPath = $siteHost.$path;
            }else{
                // 上传失败获取错误信息
                return $file->getError();
            }
        }
        return $fullPath;
    }
}

if (!function_exists('upload_multiple')) {

    /**
     * 批量上传
     * @param $files
     * @return array
     */
    function upload_multiple($files)
    {
        foreach($files as $file) {
            // 移动到框架应用根目录/public/uploads/ 目录下
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');

            if($info){
                $extension = $info->getExtension();
                if ($extension == 'jpg') {
                    $url = ROOT_PATH . 'public' . DS . "uploads\\" .$info->getSaveName();
                    $image = imagecreatefromstring(file_get_contents($url));
                    $exif = exif_read_data($url);

                    if (!empty($exif['Orientation'])) {
                        switch ($exif['Orientation']) {
                            case 8:
                                $image = imagerotate($image, 90, 0);
                                break;
                            case 3:
                                $image = imagerotate($image, 180, 0);
                                break;
                            case 6:
                                $image = imagerotate($image, -90, 0);
                                break;
                        }
                        imagejpeg($image, $url);
                        imagedestroy($image);
                    }
                }
                $siteHost = '/uploads/';
                $path = str_replace("\\","/",$info->getSaveName());
                $imgs[] = $siteHost.$path;
            }else{
                // 上传失败获取错误信息
                return $file->getError();
            }
        }
        return $imgs;
    }
}

if (!function_exists('delete_file')) {

    /**
     * 删除文件
     * @param $file
     * @return string
     */
    function delete_file($path)
    {
        if($path){
            $fullPath = ROOT_PATH . 'public' . DS . $path;
            if (file_exists($fullPath)) {
                unlink($fullPath); // 删除文件
            };
        }
    }
}

if (!function_exists('format_bytes')) {

    /**
     * 将字节转换为可读文本
     * @param int $size 大小
     * @param string $delimiter 分隔符
     * @return string
     */
    function format_bytes($size, $delimiter = '')
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
        for ($i = 0; $size >= 1024 && $i < 6; $i++)
            $size /= 1024;
        return round($size, 2) . $delimiter . $units[$i];
    }

}

if (!function_exists('datetime')) {

    /**
     * 将时间戳转换为日期时间
     * @param int $time 时间戳
     * @param string $format 日期时间格式
     * @return string
     */
    function datetime($time, $format = 'Y-m-d H:i:s')
    {
        $time = is_numeric($time) ? $time : strtotime($time);
        return date($format, $time);
    }

}

if (!function_exists('human_date')) {

    /**
     * 获取语义化时间
     * @param int $time 时间
     * @param int $local 本地时间
     * @return string
     */
    function human_date($time, $local = null)
    {
        return \mirse\Date::human($time, $local);
    }

}

if (!function_exists('cdnurl')) {

    /**
     * 获取上传资源的CDN的地址
     * @param string $url 资源相对地址
     * @param boolean $domain 是否显示域名 或者直接传入域名
     * @return string
     */
    function cdnurl($url, $domain = false)
    {
        $url = preg_match("/^https?:\/\/(.*)/i", $url) ? $url : \think\Config::get('upload.cdnurl') . $url;
        if ($domain && !preg_match("/^(http:\/\/|https:\/\/)/i", $url)) {
            if (is_bool($domain)) {
                $public = \think\Config::get('view_replace_str.__PUBLIC__');
                $url = rtrim($public, '/') . $url;
                if (!preg_match("/^(http:\/\/|https:\/\/)/i", $url)) {
                    $url = request()->domain() . $url;
                }
            } else {
                $url = $domain . $url;
            }
        }
        return $url;
    }

}


if (!function_exists('is_really_writable')) {

    /**
     * 判断文件或文件夹是否可写
     * @param    string $file 文件或目录
     * @return    bool
     */
    function is_really_writable($file)
    {
        if (DIRECTORY_SEPARATOR === '/') {
            return is_writable($file);
        }
        if (is_dir($file)) {
            $file = rtrim($file, '/') . '/' . md5(mt_rand());
            if (($fp = @fopen($file, 'ab')) === FALSE) {
                return FALSE;
            }
            fclose($fp);
            @chmod($file, 0777);
            @unlink($file);
            return TRUE;
        } elseif (!is_file($file) OR ($fp = @fopen($file, 'ab')) === FALSE) {
            return FALSE;
        }
        fclose($fp);
        return TRUE;
    }

}

if (!function_exists('rmdirs')) {

    /**
     * 删除文件夹
     * @param string $dirname 目录
     * @param bool $withself 是否删除自身
     * @return boolean
     */
    function rmdirs($dirname, $withself = true)
    {
        if (!is_dir($dirname))
            return false;
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dirname, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $fileinfo) {
            $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            $todo($fileinfo->getRealPath());
        }
        if ($withself) {
            @rmdir($dirname);
        }
        return true;
    }

}

if (!function_exists('copydirs')) {

    /**
     * 复制文件夹
     * @param string $source 源文件夹
     * @param string $dest 目标文件夹
     */
    function copydirs($source, $dest)
    {
        if (!is_dir($dest)) {
            mkdir($dest, 0755, true);
        }
        foreach (
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST) as $item
        ) {
            if ($item->isDir()) {
                $sontDir = $dest . DS . $iterator->getSubPathName();
                if (!is_dir($sontDir)) {
                    mkdir($sontDir, 0755, true);
                }
            } else {
                copy($item, $dest . DS . $iterator->getSubPathName());
            }
        }
    }

}

if (!function_exists('mb_ucfirst')) {

    function mb_ucfirst($string)
    {
        return mb_strtoupper(mb_substr($string, 0, 1)) . mb_strtolower(mb_substr($string, 1));
    }

}

if (!function_exists('addtion')) {

    /**
     * 附加关联字段数据
     * @param array $items 数据列表
     * @param mixed $fields 渲染的来源字段
     * @return array
     */
    function addtion($items, $fields)
    {
        if (!$items || !$fields)
            return $items;
        $fieldsArr = [];
        if (!is_array($fields)) {
            $arr = explode(',', $fields);
            foreach ($arr as $k => $v) {
                $fieldsArr[$v] = ['field' => $v];
            }
        } else {
            foreach ($fields as $k => $v) {
                if (is_array($v)) {
                    $v['field'] = isset($v['field']) ? $v['field'] : $k;
                } else {
                    $v = ['field' => $v];
                }
                $fieldsArr[$v['field']] = $v;
            }
        }
        foreach ($fieldsArr as $k => &$v) {
            $v = is_array($v) ? $v : ['field' => $v];
            $v['display'] = isset($v['display']) ? $v['display'] : str_replace(['_ids', '_id'], ['_names', '_name'], $v['field']);
            $v['primary'] = isset($v['primary']) ? $v['primary'] : '';
            $v['column'] = isset($v['column']) ? $v['column'] : 'name';
            $v['model'] = isset($v['model']) ? $v['model'] : '';
            $v['table'] = isset($v['table']) ? $v['table'] : '';
            $v['name'] = isset($v['name']) ? $v['name'] : str_replace(['_ids', '_id'], '', $v['field']);
        }
        unset($v);
        $ids = [];
        $fields = array_keys($fieldsArr);
        foreach ($items as $k => $v) {
            foreach ($fields as $m => $n) {
                if (isset($v[$n])) {
                    $ids[$n] = array_merge(isset($ids[$n]) && is_array($ids[$n]) ? $ids[$n] : [], explode(',', $v[$n]));
                }
            }
        }
        $result = [];
        foreach ($fieldsArr as $k => $v) {
            if ($v['model']) {
                $model = new $v['model'];
            } else {
                $model = $v['name'] ? \think\Db::name($v['name']) : \think\Db::table($v['table']);
            }
            $primary = $v['primary'] ? $v['primary'] : $model->getPk();
            $result[$v['field']] = $model->where($primary, 'in', $ids[$v['field']])->column("{$primary},{$v['column']}");
        }

        foreach ($items as $k => &$v) {
            foreach ($fields as $m => $n) {
                if (isset($v[$n])) {
                    $curr = array_flip(explode(',', $v[$n]));

                    $v[$fieldsArr[$n]['display']] = implode(',', array_intersect_key($result[$n], $curr));
                }
            }
        }
        return $items;
    }

}

if (!function_exists('var_export_short')) {

    /**
     * 返回打印数组结构
     * @param string $var 数组
     * @param string $indent 缩进字符
     * @return string
     */
    function var_export_short($var, $indent = "")
    {
        switch (gettype($var)) {
            case "string":
                return '"' . addcslashes($var, "\\\$\"\r\n\t\v\f") . '"';
            case "array":
                $indexed = array_keys($var) === range(0, count($var) - 1);
                $r = [];
                foreach ($var as $key => $value) {
                    $r[] = "$indent    "
                        . ($indexed ? "" : var_export_short($key) . " => ")
                        . var_export_short($value, "$indent    ");
                }
                return "[\n" . implode(",\n", $r) . "\n" . $indent . "]";
            case "boolean":
                return $var ? "TRUE" : "FALSE";
            default:
                return var_export($var, TRUE);
        }
    }

}