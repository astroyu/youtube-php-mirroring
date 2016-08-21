<?php
$v=$_GET[v];
//判断设备
if(empty($v)){
$g_et='true';
}
function fcurl($url){
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);
$data = curl_exec($ch);
curl_close($ch);
return $data;
}
function isMobile(){
    $useragent=isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    $useragent_commentsblock=preg_match('|\(.*?\)|',$useragent,$matches)>0?$matches[0]:'';
    function CheckSubstrs($substrs,$text){
        foreach($substrs as $substr)
            if(false!==strpos($text,$substr)){
                return true;
            }
            return false;
    }
    $mobile_os_list=array('Google Wireless Transcoder','Windows CE','WindowsCE','Symbian','Android','armv6l','armv5','Mobile','CentOS','mowser','AvantGo','Opera Mobi','J2ME/MIDP','Smartphone','Go.Web','Palm','iPAQ');
    $mobile_token_list=array('Profile/MIDP','Configuration/CLDC-','160×160','176×220','240×240','240×320','320×240','UP.Browser','UP.Link','SymbianOS','PalmOS','PocketPC','SonyEricsson','Nokia','BlackBerry','Vodafone','BenQ','Novarra-Vision','Iris','NetFront','HTC_','Xda_','SAMSUNG-SGH','Wapaka','DoCoMo','iPhone','iPod');

    $found_mobile=CheckSubstrs($mobile_os_list,$useragent_commentsblock) ||
              CheckSubstrs($mobile_token_list,$useragent);

    if ($found_mobile){
        return true;
    }else{
        return false;
    }
}
if (isMobile()){}
else{
     //如果电脑访问
header("Location: video.php?v=$v");
//确保重定向后，后续代码不会被执行
exit;
}
//获取原始下载地址
//require 'inc/parser.php';
$parserurl='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];
$parserurl=dirname($parserurl);


$geturl= $parserurl.'/parser/index.php?videoid='."$v";

$w=fcurl($geturl);

$cv=json_decode($w);

//print_r($cv);

//echo $cv[Download][1][url];
function object_array($array)
{
   if(is_object($array))
   {
    $array = (array)$array;
   }
   if(is_array($array))
   {
    foreach($array as $key=>$value)
    {
     $array[$key] = object_array($value);
    }
   }
   return $array;
}
$rr=object_array($cv);
$aaaks=array_reverse(($rr[Download]));
$vname=$rr[title];//视频名称
$pagetitle=$vname;

//加密传输视频
// Declare the class

require 'pheader.php';
$API_key=$youtube_api;
$jsonurl='https://www.googleapis.com/youtube/v3/search?part=snippet&order=relevance&amp;regionCode=lk&key='.$API_key.'&part=snippet&maxResults=20&relatedToVideoId='.$v.'&type=video';
//To try without API key: $video_list = json_decode(file_get_contents(''));
$video_list = json_decode(fcurl($jsonurl));
$video_list1=object_array($video_list);
require 'header.php';
?>
<script src="js/jquery.js"></script>
<script src="//cdn.bootcss.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>

<div class="wrapper container">
<?php
if($g_et!=true){
echo <<<EOT
<div class="row">
        <div class="col-xs-12" id="video" style="z-index:-1000">
      <!--ckplayer配置开始-->
      <video controls="controls" poster="./thumbnail.php?vid=$v" autoplay="autoplay" width="100%" height="100%">
  <source src="./mpaly.php?id=$v" type="video/mp4" />
您的浏览器不支持HTML5播放MP4.
</video>

<!--ckplayer配置结束-->
</div>
<div class="col-xs-12">
<h3>$vname</h3>

EOT;
}else{
    echo '<div class="alert alert-danger">错误！非法请求。</div>';
}
?>
</div>
</div>
</div>
<?php require 'footer.php';?>
