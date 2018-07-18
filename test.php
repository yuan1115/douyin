<?php
/**
 * @Author: junmoxiao
 * @Date:   2018-07-09 14:20:20
 * @Last Modified by:   junmoxiao
 * @Last Modified time: 2018-07-09 17:36:24
 */
header("content-type:text/html;charset=utf-8");
function _GetContent($url){
	$cookie = 'KUXUABI=71d79e8c0c63f226bf6849ae96588ae091553666; NOVEL_LOGIN_COOKIE=94a8863a-4a34-4a9a-bb3e-bb35338b9427-1525068180270';
    $ch = curl_init();
    $ip = '220.181.108.91';  // 百度蜘蛛
    $timeout = 15;
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_TIMEOUT,0);
    curl_setopt($ch, CURLOPT_ENCODING, "");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
    //伪造百度蜘蛛IP
    curl_setopt($ch,CURLOPT_HTTPHEADER,array('X-FORWARDED-FOR:'.$ip.'','CLIENT-IP:'.$ip.''));
    //伪造百度蜘蛛头部
    curl_setopt($ch,CURLOPT_USERAGENT,"Safari/537.36 MicroMessenger/6.5.2.501 NetType/WIFI WindowsWechat QBCore/3.43.691.400 QQBrowser/9.0.2524.400");
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch,CURLOPT_HEADER,0);
    curl_setopt($ch,CURLOPT_COOKIE,$cookie);
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
    $content = curl_exec($ch);
    if($content === false)
    {//输出错误信息
        $no = curl_errno($ch);
        switch(trim($no))
        {
            case 28 : $error = '访问目标地址超时'; break;
            default : $error = curl_error($ch); break;
        }
        echo $error;
    }
    else
    {
        $succ = true;
        return $content;
    }
}
// 创建连接
$conn = new mysqli('127.0.0.1', 'root', 'root', 'douyin');
// 检测连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
} 
$listRow = 20;
$i = 0;
$info = file_get_contents("class.txt");
$infoArr = explode(PHP_EOL, $info);
foreach ($infoArr as $k => $v) {
    $lineInfo = explode(";", $v);
    $catename = $lineInfo[2];
    $chnid = $lineInfo[0];
    $chname = $lineInfo[1];
    do{
        echo iconv("utf-8", "gbk","开始采集数据第".$i."页数据").PHP_EOL;
        $firstRow = $i*$listRow;
        $url = "https://api.amemv.com/aweme/v1/challenge/fresh/aweme/?ch_id=".$chnid."&query_type=0&cursor=".$firstRow."&count=".$listRow."&type=5&retry_type=no_retry&iid=38045757473&device_id=50157301312&ac=wifi&channel=xiaomi&aid=1128&app_name=aweme&version_code=200&version_name=2.0.0&device_platform=android&ssmix=a&device_type=Mi+Note+3&device_brand=Xiaomi&language=zh&os_api=25&os_version=7.1.1&uuid=99001068638799&openudid=811b2878f3ea25b3&manifest_version_code=200&resolution=1080*1920&dpi=440&update_version_code=2002";
        $url_list = _GetContent($url);
        $res = json_decode($url_list,true);
        $i++;
        foreach ($res['aweme_list'] as $k => $v) {
            $sharetitle = $v["share_info"]['share_title'];
            $shareurl = $v["share_info"]['share_url'];
            $sharedesc = $v["share_info"]['share_desc'];
            $playurl = "https://aweme.snssdk.com/aweme/v1/play/?video_id=".$v["video"]['play_addr']['uri'];
            $cover = $v["video"]['cover']['url_list'][0];
            $gifcover = $v["video"]['dynamic_cover']['url_list'][0];
            $vdesc = $v["desc"];
            $aweme_id = $v['statistics']["aweme_id"];
            $uid = $v["author"]['uid'];
            $selectS = "SELECT * FROM douyin where aweme_id = '$aweme_id'";
            $result = $conn->query($selectS);
            if($result->num_rows > 0 ){
                $str = "视频"."$sharetitle"."已存在";
                echo  iconv("UTF-8", "GBK//IGNORE", "$str").PHP_EOL;
            }else{
                echo iconv("UTF-8", "GBK//IGNORE","开始写入数据".$sharetitle).PHP_EOL;
                $sql = "INSERT INTO douyin (sharetitle,shareurl,sharedesc,playurl,cover,gifcover,vdesc,aweme_id,uid,catename,chnid,chname) VALUES ('$sharetitle','$shareurl','$sharedesc','$playurl','$cover','$gifcover','$vdesc','$aweme_id','$uid','$catename','$chnid','$chname')";
                $result = $conn->query($sql);
            }   
        }
    }while($res['aweme_list']);
}
echo iconv("UTF-8", "GBK//IGNORE","采集完毕").PHP_EOL;
$conn->close();
?>