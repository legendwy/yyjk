<?php

/**
 * 返回可读性更好的文件尺寸
 * @param $bytes
 * @param int $decimals
 * @return string
 */
function human_filesize($bytes, $decimals = 2)
{
    $size = ['B', 'kB', 'MB', 'GB', 'TB', 'PB'];
    $factor = floor((strlen($bytes) - 1) / 3);

    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
}

/**
 * 判断文件的MIME类型是否为图片
 */
function is_image($mimeType)
{
    return starts_with($mimeType, 'image/');
}

/**
 * 文件上传
 * @param $file_request
 * @param $path
 * @param string $type image =>图片  file => 文件
 * @return array
 * @author: simayubo
 */
function upload_file($file_request, $path, $type = 'file')
{
    $file = $file_request;
    if ($file->isValid()) {
        $file_type = 0;
        //判断文件类型
        $mimeTye = $file->getMimeType();
        if ($type == 'image') {
            $file_type = 1;
            $mime = ['image/bmp', 'image/cis-cod', 'image/gif', 'image/ief', 'image/jpeg', 'image/pipeg', 'image/png', 'image/svg+xml', 'image/tiff', 'image/x-cmu-raster', 'image/x-cmx', 'image/x-icon',];
            if (!in_array($mimeTye, $mime)) {
                return ['status' => false, 'error' => '图片格式不允许'];
            }
        } elseif ($type == 'file') {
            $file_type = 2;
            $mime = ['application/zip', 'video/mpeg', 'video/quicktime', 'video/x-msvideo', 'video/x-sgi-movie'];
            if (!in_array($mimeTye, $mime)) {
                return ['status' => false, 'error' => '文件格式不允许'];
            }
        } else {
            return ['status' => false, 'error' => '系统参数错误'];
        }
        //判断文件大小
        if ($file->getSize() > 20480000) {
            return ['status' => false, 'error' => '文件太大，限制20M'];
        }

        $entension = $file->getClientOriginalExtension(); //上传文件的后缀.
        $file_path = "/uploads/" . $path . "/" . date('Y-m-d', time()) . '/';
        $file_name = time() . '_' . str_random(5) . '.' . $entension;
        $path = $file->move(public_path() . $file_path, $file_name);
        if ($path) {
            \DB::table('files')->insert(['path' => $file_path . $file_name, 'type' => $file_type]);
            return ['status' => true, 'path' => $file_path . $file_name];
        }
    } else {
        return ['status' => false, 'error' => '文件错误'];
    }
}

/**
 * 批量修改附件状态
 * @param array $path_array ['/uploads/asdasd.png', '.....'] 数组
 * @param int $status
 * @return bool
 * @author: simayubo
 */
function set_file_status(array $path_array, $status = 1)
{
    $db_status = 1;
    foreach ($path_array as $value) {
        $res = DB::table('files')->where('path', $value)->update(['status' => $status]);
        if ($res < 0) $db_status = 0;
    }
    if ($db_status == 1) {
        return true;
    } else {
        return false;
    }
}

/**
 * 错误日志
 * @param $arr
 * @Author wangyan
 */
function write_error_logs($arr)
{

    $data = array(
        'target' => $arr[0],
        'text' => $arr[1],
        'time' => \Carbon\Carbon::now(),
    );
    DB::table('error_logs')->insert($data);
}

function getUserInfoById($id){
    $user_info = DB::table('users')->where(['id' => $id])->first();
    return $user_info;
}

/**
 * 通过权限前缀获取权限名
 * @param $name
 * @return mixed
 * @author: simayubo
 */
function permission_config($name)
{
    return config('permission.message.' . $name);
}

/**
 * 通过id获取城市名称
 * @param $id
 * @return mixed
 * @author: simayubo
 */
function get_city_name($id)
{
    return DB::table('region')->where('region_id', $id)->value('REGION_NAME');
}

/**
 * 用户下级
 * User: lf
 * @param $array
 * @param $id
 * @return array
 */
function getChildsList($array, $id)
{
    $tree = array();
    foreach ($array as $k => $v) {
        $v = get_object_vars($v);
        if ($v['pid'] == $id) {
            $v['level'] = 1;
            foreach($array as $kk => $vv){
                $vv = get_object_vars($vv);
                if($vv['pid'] == $v['id']){
                    $vv['level'] = 2;
                    $v['child'] = $vv;
                }
            }
            $tree[] = $v;
        }
    }
    return $tree;
}

/**
 * 通过物流单号查询物流信息
 * @param $kd 物流单号
 * @param $kdId 快递公司
 * @return bool|mixed
 * @author: simayubo
 */
function get_wuliu_traces($kd, $kdId)
{
    $requestData = "{'OrderCode':'','ShipperCode':'" . $kd . "','LogisticCode':'" . $kdId . "'}";
    $datas = array(
        'EBusinessID' => config('app.wuliu_config.EBusinessID'),
        'RequestType' => '1002',
        'RequestData' => urlencode($requestData),
        'DataType' => '2',
    );
//    dd(config('app.wuliu_config.AppKey'));
    $datas['DataSign'] = wuliu_encrypt($requestData, config('app.wuliu_config.AppKey'));
    $result = send_post(config('app.wuliu_config.ReqURL'), $datas);
    //根据公司业务处理返回的信息......
    if ($result) {
        $re = json_decode($result, true);
        if ($re) {
            return $re;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

/**
 * post提交数据
 * @param $url
 * @param $datas
 * @return string
 * @author: simayubo
 */
function send_post($url, $datas)
{
    $temps = array();
    foreach ($datas as $key => $value) {
        $temps[] = sprintf('%s=%s', $key, $value);
    }
    $post_data = implode('&', $temps);
    $url_info = parse_url($url);
    if (empty($url_info['port'])) {
        $url_info['port'] = 80;
    }
//    echo $url_info['port'];
    $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
    $httpheader .= "Host:" . $url_info['host'] . "\r\n";
    $httpheader .= "Content-Type:application/x-www-form-urlencoded\r\n";
    $httpheader .= "Content-Length:" . strlen($post_data) . "\r\n";
    $httpheader .= "Connection:close\r\n\r\n";
    $httpheader .= $post_data;
    $fd = fsockopen($url_info['host'], $url_info['port']);
    fwrite($fd, $httpheader);
    $gets = "";
    $headerFlag = true;
    while (!feof($fd)) {
        if (($header = @fgets($fd)) && ($header == "\r\n" || $header == "\n")) {
            break;
        }
    }
    while (!feof($fd)) {
        $gets .= fread($fd, 128);
    }
    fclose($fd);

    return $gets;
}

/**
 * Sign签名生成
 * @param $data
 * @param $appkey
 * @return string
 * @author: simayubo
 */
function wuliu_encrypt($data, $appkey)
{
    return urlencode(base64_encode(md5($data . $appkey)));
}