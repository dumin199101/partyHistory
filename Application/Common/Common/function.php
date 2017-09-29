<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/4/25
 * Time: 11:00
 */
function getSearchwordNum($post){
    switch (floor((count($post)-3)/6)){
        case 1:
            $num=1;
            break;
        case 2:
            $num=2;
            break;
        case 3:
            $num=3;
            break;
    }
    return $num;
}



function get_search_arr($post){
    $num=getSearchwordNum($post);
    $searchArr=array();
    for($i=1;$i<=$num;$i++){
        if(isset($post['fieldRelation_'.$i])){
            $searchArr['title'][$i]['fieldRelation']=$post['fieldRelation_'.$i];
        }else{
            $searchArr['title'][$i]['fieldRelation']=null;
        }

        $searchArr['title'][$i]['searchFieldInput']=empty($post['searchFieldInput_'.$i])?'':$post['searchFieldInput_'.$i];

        if($post['wordRelation_'.$i]){
            $searchArr['title'][$i]['wordRelation']=$post['wordRelation_'.$i];
        }

        $searchArr['title'][$i]['attachFieldInput']=empty($post['attachFieldInput_'.$i])?'':$post['attachFieldInput_'.$i];

        if($post['isAccurate_'.$i]){
            $searchArr['title'][$i]['isAccurate']=$post['isAccurate_'.$i];
        }
        if($post['searchField_'.$i]){
            $searchArr['title'][$i]['searchField']=$post['searchField_'.$i];
        }

    }
    $searchArr['timeStart']=$post['timeStart'];
    $searchArr['timeEnd']=$post['timeEnd'];
    $searchArr['literatureCat']=$post['literatureCat'];
    $searchArr['adcencedMark']=$post['adcencedMark'];
    return $searchArr;
}

function dealKeywords($str){
    $array=array();
    $array=explode(',',$str);
    foreach ($array as $key=>$value){
        $array[$key]='<a href="javascript:void(0)" onclick="getSearch(this)" style="color:#25a67b">'.$value.'</a>';
    }
    $newStr=implode(' ',$array);
    return $newStr;
}


/***
 * @param $array 原数组
 * @param $num （检索添加的数量）
 * @return mixed
 */
function get_search_list($array,$num){
    $searchArr=array();
    $searchStr='';
    //主体
    for($i=1;$i<=$num;$i++){
            if(!empty($array['searchFieldInput_'.$i]) || !empty($array['attachFieldInput_'.$i])){
                if(isset($array['fieldRelation_'.$i])){
                    $searchStr.=' '.getWordRelation($array['fieldRelation_'.$i]).' ';
                    $searchArr[$i]['fieldRelation']=getChineseRelation($array['fieldRelation_'.$i]);
                }else{
                    $searchArr[$i]['fieldRelation']=null;
                }
                $searchArr[$i]['searchField']=getSearchChineseField($array['searchField_'.$i]);
                $searchStr.=getSearchField($array['searchField_'.$i]).':';
                if(!empty($array['searchFieldInput_'.$i]) && !empty($array['attachFieldInput_'.$i])){
                    $searchArr[$i]['wordStr']='<span style="color:red">'.$array["searchFieldInput_".$i].'</span> '.getChineseRelation($array['wordRelation_'.$i]).'<span style="color:red"> '.$array['attachFieldInput_'.$i].'</span>';
                    if($array['isAccurate_'.$i]==1){
                        $searchStr.='"'.$array['searchFieldInput_'.$i] .'" '.getWordRelation($array['wordRelation_'.$i]).' '.getSearchField($array['searchField_'.$i]).':'.'"'.$array['attachFieldInput_'.$i].'" ';

                    }else{
                        $searchStr.='('. $array['searchFieldInput_'.$i] .' '.getWordRelation($array['wordRelation_'.$i]).' '.$array['attachFieldInput_'.$i].')';
                    }
                }else{
                    $searchArr[$i]['wordStr']='<span style="color:red">'.$array['searchFieldInput_'.$i].'</span><span style="color:red">'.$array['attachFieldInput_'.$i].'</span>';
                    if($array['isAccurate_'.$i]==1){
                        if($array['searchFieldInput_'.$i]){
                            $searchStr.='"'.$array['searchFieldInput_'.$i].'"';
                        }
                        if($array['attachFieldInput_'.$i]){
                            $searchStr.='"'.$array['attachFieldInput_'.$i].'"';
                        }
                    }else{
                        if($array['searchFieldInput_'.$i]){
                            $searchStr.=$array['searchFieldInput_'.$i];
                        }
                        if($array['attachFieldInput_'.$i]){
                            $searchStr.=$array['attachFieldInput_'.$i];
                        }
                    }
                }
            }

    }
//    var_dump($searchStr);die;
    $data['searchStr']=$searchStr;
    $data['searchArr']=$searchArr;
    return $data;
}

function getChineseRelation($id){
    $wordRelation='';
    switch ($id) {
        case 1:
            $wordRelation='并含';
            break;
        case 2:
            $wordRelation='或者';
            break;
        case 3:
            $wordRelation='不含';
            break;

    }
    return $wordRelation;
}

function getWordRelation($id){
    $wordRelation='';
    switch ($id) {
        case 1:
            $wordRelation='AND ';
            break;
        case 2:
            $wordRelation='OR ';
            break;
        case 3:
            $wordRelation='NOT ';
            break;

    }

    return $wordRelation;
}
function getSearchField($num){
    $searchField='';
    switch ($num) {
        case 1:
            $searchField='v_title';
            break;
        case 2:
            $searchField='v_resource_content';
            break;
        case 3:
            $searchField='v_author';
            break;
    }
    return $searchField;
}

function getSearchChineseField($num){
    $searchField='';
    switch ($num) {
        case 1:
            $searchField='标题';
            break;
        case 2:
            $searchField='正文';
            break;
        case 3:
            $searchField='作者';
            break;
    }
    return $searchField;
}


/**
 * 二维数组根据字段进行排序
 * @params array $array 需要排序的数组
 * @params string $field 排序的字段
 * @params string $sort 排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
 */
function arraySequence($array, $field, $sort = 'SORT_DESC')
{
    $arrSort = array();
    foreach ($array as $uniqid => $row) {
        foreach ($row as $key => $value) {
            $arrSort[$key][$uniqid] = $value;
        }
    }
    array_multisort($arrSort[$field], constant($sort), $array);
    return $array;
}



/**
 *拼接xunsearch高级查询语句
 * @param $array 需要拼接的数组
 * @param $num 需要拼接的词语个数
 * @return string
 */
function get_search_str($array,$num){
    $searchStr='';
        foreach ($array as $key=>$val){

                switch($array["expert_relation_name_".($i+1)]){
                    case 1:
                        $searchStr.=' AND ';
                        break;
                    case 2:
                        $searchStr.=' OR ';
                        break;
                    case 3:
                        $searchStr.=' XOR ';
                        break;
                    case 4:
                        $searchStr.=' NOT ';
                        break;
                }

            }
    return $searchStr;
}



/*截取字符串构建图片路径*/
function get_resource_path($v_global_guid, $download = false)
{
    $prefix = substr($v_global_guid, 0, 2);
    switch ($prefix) {
        case 'JF':
            $path = $prefix . DIRECTORY_SEPARATOR . substr($v_global_guid, 2, 4) . DIRECTORY_SEPARATOR . substr($v_global_guid, 6) . DIRECTORY_SEPARATOR;
            break;
        case 'XH':
            $path = $prefix . DIRECTORY_SEPARATOR . substr($v_global_guid, 2, 4) . DIRECTORY_SEPARATOR . substr($v_global_guid, 6) . DIRECTORY_SEPARATOR;
            break;
        case 'BL':
            $path = $prefix . DIRECTORY_SEPARATOR . substr($v_global_guid, 2, 2) . DIRECTORY_SEPARATOR . substr($v_global_guid, 4) . DIRECTORY_SEPARATOR;
            break;
        case 'QZ':
            $path = $prefix . DIRECTORY_SEPARATOR . substr($v_global_guid, 2, 2) . DIRECTORY_SEPARATOR . substr($v_global_guid, 4) . DIRECTORY_SEPARATOR;
            break;
    }
    if ($download == true) {
        $pathDir = C("RESOURCE.DOWNLOAD");
    } else {
        $pathDir = C("RESOURCE.IMG");
    }
    return $pathDir . $path;

}



/**
 * 发送HTTP请求方法，目前只支持CURL发送请求
 * @param  string $url    请求URL
 * @param  array  $params 请求参数
 * @param  string $method 请求方法GET/POST
 * @return array  $data   响应数据
 */
function http($url, $params, $method = 'GET', $header = array(), $multi = false){
    $opts = array(
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_HTTPHEADER     => $header
    );

    /* 根据请求类型设置特定参数 */
    switch(strtoupper($method)){
        case 'GET':
            $opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
            break;
        case 'POST':
            //判断是否传输文件
            $params = $multi ? $params : http_build_query($params);
            $opts[CURLOPT_URL] = $url;
            $opts[CURLOPT_POST] = 1;
            $opts[CURLOPT_POSTFIELDS] = $params;
            break;
        default:
            throw new Exception('不支持的请求方式！');
    }

    /* 初始化并执行curl请求 */
    $ch = curl_init();
    curl_setopt_array($ch, $opts);
    $data  = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);
    if($error) throw new Exception('请求发生错误：' . $error);
    return  $data;
}



function SimplifiedToTraditional($word){
    if(function_exists('opencc_open')){
        $od = opencc_open("s2twp.json"); //传入配置文件名
        $result=opencc_convert($word, $od);
        opencc_close($od);
    }else{
        $params['word']=$word;
        $result= http(C('SIMPLE_TO_TRADITON_URL'),$params,'POST');

    }


    return $result;
}


/**
 * +----------------------------------------------------------
 * 加密密码
 * +----------------------------------------------------------
 * @param string $data 待加密字符串
 * +----------------------------------------------------------
 * @return string 返回加密后的字符串
 */
function encrypt($data)
{

    return md5(C("AUTH_CODE") . md5($data));
}


/*
 * 功能：判断客户端IP是否是在IP允许范围内
 * 在登录验证之前进行IP验证时会用到
 * 获得数据库中的所有IP，并判断客户端IP是否是在IP允许范围内
 * 1.区间：判断是否在区间范围内 2.单个必须与客户端IP一致
 * @return bool
 */

function isInIpRange()
{

    date_default_timezone_set("PRC");
    $riqi = date("Y-m-d");

    $ipModel = M('IpArea');
    $IP = get_client_ip();



    $result = $ipModel->field('v_ip,n_ip_id')->where("n_status=1 AND `d_date`>='$riqi'")->select();


    if (!$result) {
        return false;
    }


    foreach ($result as $row) {
        $v_ip = $row['v_ip'];
        $ips = explode("-", $v_ip);


        if (count($ips) == 2) {

            $from = explode(".", $ips[0]);
            $to = explode(".", $ips[1]);
            $p = explode(".", $IP);


            $longfrom = $from[0] * pow(256, 3) + $from[1] * pow(256, 2) + $from[2] * 256 + $from[3];
            $longto = $to[0] * pow(256, 3) + $to[1] * pow(256, 2) + $to[2] * 256 + $to[3];
            $longp = $p[0] * pow(256, 3) + $p[1] * pow(256, 2) + $p[2] * 256 + $p[3];


            if ($longp >= $longfrom) {
                if ($longp <= $longto) {
                    return true;
                }
            }
        }


        if (count($ips) == 1) {
            if ($IP == $ips[0]) {
                return true;
            }
        }
    }

    return false;
}


/*
 * 自定义记录登录日志的函数，写到数据库中
  write_loginlog(用户名,密码,登录成功?,是特权帐户?,是特殊IP?,IP错误?,用户错误?,用户ID[失败-1]);
 */

function write_loginlog($user, $passwd, $login_success, $isvip, $isnouserip, $iperror, $usererror, $uid)
{

    $IP = get_client_ip();
    $login_date = date("Y-m-d H:i:s");
    $loginLogModel = M('LoginLog');


    $sql = "INSERT INTO `tb_login_log` (`id`,`login_time`,`u_name`,`u_pwd`,`u_ip`,`login_success`,`isvip`,`isnouserip`,`ip_error`,`user_error`,`n_user_id`) VALUES (NULL,'" . $login_date . "','" . $user . "','" . $passwd . "','" . $IP . "','" . $login_success . "','" . $isvip . "','" . $isnouserip . "','" . $iperror . "','" . $usererror . "','" . $uid . "')";
    $loginLogModel->execute($sql);
}

/*
 * 自定义web日志的函数，写到数据库中
  write_weblog($url,$uid);
  @params:$url:获取当前页面的url String
  @params:$uid:获取session中存取的用户id int
 */
function write_weblog($url, $uid,$query_str=array())
{
    if(empty($uid)){
        return;
    }
    if(!empty($query_str)){
        $query_str=json_encode($query_str);
    }else{
        $query_str=NULL;
    }
    $IP = get_client_ip();
    $login_date = date("Y-m-d H:i:s");
    $webLogModel = M('WebLog');

    $sql = "INSERT INTO `tb_web_log`(`n_id`,`v_ip`,`d_date`,`v_web`,`n_uid`,`bz`) VALUES (NULL,'" . $IP . "','" . $login_date . "','" . $url . "','" . $uid . "','".$query_str."')";
    $webLogModel->execute($sql);
}

/*
 * 自定义搜索函数，写到数据库中
  write_weblog($url,$uid,$keyword);
  @params:$url:获取当前页面的url String
  @params:$uid:获取session中存取的用户id int
  @params:$keyword:获取用户查询字符串 $keyword	 String
 */

function write_keylog($url, $uid, $keyword)
{

    $IP = get_client_ip();
    $login_date = date("Y-m-d H:i:s");
    $keyLogModel = M('KeyLog');


    $sql = "INSERT INTO `tb_key_log`(`n_id`,`v_ip`,`d_date`,`v_web`,`n_uid`,`v_key`,`bz`) VALUES (NULL,'" . $IP . "','" . $login_date . "','" . $url . "','" . $uid . "','" . $keyword . "',NULL)";


    $keyLogModel->execute($sql);
}

/*
 * 自定义详细信息函数，写到数据库中
  write_detaillog($url,$uid,$v_hash_guid);
  @params:$url:获取当前页面的url String
  @params:$uid:获取session中存取的用户id int
  @params:$v_hash_guid:获取当前图书ID $v_hash_guid	 String
 */

function write_detaillog($url, $uid, $v_hash_guid)
{

    $IP = get_client_ip();
    $login_date = date("Y-m-d H:i:s");
    $detailLogModel = M('DetailLog');


    $sql = "INSERT INTO `tb_detail_log`(`n_id`,`v_ip`,`d_date`,`v_web`,`n_uid`,`v_hashid`,`bz`) VALUES (NULL,'" . $IP . "','" . $login_date . "','" . $url . "','" . $uid . "','" . $v_hash_guid . "',NULL)";
    $detailLogModel->execute($sql);
}

/*
 * 根据获得的IP地址字符串形式转换为数字
 */
function Ipstr2Number($ip_str)
{

    date_default_timezone_set("PRC");
    $riqi = date("Y-m-d");

    $ip_zone = M('ipArea');
    $result = $ip_zone->field('v_ip')->where("`n_status`=1 AND `d_date`>='$riqi'")->select();


    foreach ($result as $row) {

        $v_ip = $row['v_ip'];
        $ips = explode("-", $v_ip);


        if (count($ips) == 2) {

            $from = explode(".", $ips[0]);
            $to = explode(".", $ips[1]);
            $p = explode(".", $ip_str);


            $longfrom = $from[0] * pow(256, 3) + $from[1] * pow(256, 2) + $from[2] * 256 + $from[3];
            $longto = $to[0] * pow(256, 3) + $to[1] * pow(256, 2) + $to[2] * 256 + $to[3];
            $longp = $p[0] * pow(256, 3) + $p[1] * pow(256, 2) + $p[2] * 256 + $p[3];


            if ($longp >= $longfrom) {
                if ($longp <= $longto) {
                    return $row;
                }
            }
        }


        if (count($ips) == 1) {
            if ($ip_str == $ips[0]) {
                return $row;
            }
        }
    }


}


/*
 * 当没有输入用户名跟密码，登录成功，用于获取登录用户的信息在session中存储用户信息,说明IP验证肯定通过了，我通过IP来获取用户的信息，IP地址区间一般不会重复。。。。
 * return $user_company
 * @params:$client_ip客户端的IP地址
 * */

function get_user_info()
{


    //获取客户端IP
    $client_ip = get_client_ip();
    //$client_ip = '61.138.177.30';

    $ip_str = Ipstr2Number($client_ip);


    $ip = $ip_str['v_ip'];

    //将客户端IP同IP范围比较，确定用户
    $ip_zone = M('IpArea');
    //根据获得的IP取得相应的用户信息
    $v_user_info = $ip_zone->alias('a')->field('a.v_company,a.n_user_id')->where("a.v_ip='$ip'")->find();

    //return $v_user_info['v_user_nick'];
    return $v_user_info;
}

function get_uid(){
   return $_SESSION['valid_user_id'];
}

function get_current_url(){
    return 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
}
