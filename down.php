<?php
/**
 * @Author: junmoxiao
 * @Date:   2018-07-09 17:37:30
 * @Last Modified by:   junmoxiao
 * @Last Modified time: 2018-07-09 20:52:50
 */
header("content-type:text/html;charset=utf-8");
function _GetContent($url,$type=0){
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
    curl_setopt($ch, CURLOPT_NOBODY, 1);
	// 返回最后的Location
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    $content = curl_exec($ch);
    $info = curl_getinfo($ch,CURLINFO_EFFECTIVE_URL);
    if($type==1){
    	return $info;
    }else{
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
}
$info = file_get_contents("down.txt");
$infoArr = explode(PHP_EOL, $info);
// 创建连接
$conn = new mysqli('127.0.0.1', 'root', 'root', 'douyin');
// 检测连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
} 
foreach ($infoArr as $k => $v) {
	$lineInfo = explode(";", $v);
	$cateName = $lineInfo[0];
	if(!is_dir(iconv("UTF-8","GBK//IGNORE","./v/".$cateName))){
		mkdir(iconv("UTF-8","GBK//IGNORE","./v/".$cateName));
	}
	if(!is_dir(iconv("UTF-8","GBK//IGNORE","./img/".$cateName))){
		mkdir(iconv("UTF-8","GBK//IGNORE","./img/".$cateName));
	}
	$length = isset($lineInfo[1])?$lineInfo[1]:0;
	$is_down_cover = isset($lineInfo[2])?$lineInfo[2]:0;
	$limit = $length?" limit $length":'';
	// echo $limit;die;
	$selectS = "SELECT * FROM douyin where isdown = 0 and catename like '%$cateName%' order by id desc".$limit;
    $result = $conn->query($selectS);
    if($result->num_rows > 0 ){
        while ($row = $result->fetch_assoc()) {
        	if(!$row['vdesc']&&!$row['sharedesc']){
        		$title = $row['sharetitle'];
        	}else{
        		$title = $row['vdesc']?$row['vdesc']:$row['sharedesc'];
        	}
        	if($is_down_cover == 1 ){
        		echo iconv("UTF-8","GBK//IGNORE","开始下载图片".$row['aweme_id'].'->'.$title).PHP_EOL;
        		$vurl = _GetContent($row['cover']);
	        	$url = iconv("UTF-8", "GBK//IGNORE", "./img/".$lineInfo[0]."/".$title.$row['aweme_id'].".jpg");
	        	file_put_contents($url ,$vurl);
        	}
        	echo iconv("UTF-8","GBK//IGNORE","开始下载视频".$row['aweme_id'].'->'.$title).PHP_EOL;
        	$vurl = _GetContent($row['playurl'],1);
        	$vurl = file_get_contents($vurl);
        	$url = iconv("UTF-8", "GBK//IGNORE", "./v/".$lineInfo[0].'/'.$title.$row['aweme_id'].".mp4");
        	file_put_contents($url ,$vurl);
            $id = $row['id'];
            $sql = "UPDATE douyin SET isdown = 1 where id = $id";
            $conn->query($sql);
        }
    }else{
    	echo iconv("UTF-8", "GBK//IGNORE", $cateName."没有可以下载的视频").PHP_EOL;
    }
}