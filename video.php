<?php
$v=$_GET[v];
//判断设备
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
if (isMobile()){
  //如果手机访问
header("Location: m_video.php?v=$v");
//确保重定向后，后续代码不会被执行
exit;
}
else{
}
//获取原始下载地址
require 'inc/parser.php';

$geturl= $ym.$parser.'/index.php?videoid='."$v";
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

$vname=$rr[title];//视频名称
$furl=$rr[Download][2][url];//flv视频地址
$murl=$rr[Download][1][url];//mp4视频地址
$pagetitle=$vname;

//加密传输视频
// Declare the class
class GoogleUrlApi {

	// Constructor
	function GoogleURLAPI($key,$apiURL = 'https://www.googleapis.com/urlshortener/v1/url') {
		// Keep the API Url
		$this->apiURL = $apiURL.'?key='.$key;
	}

	// Shorten a URL
	function shorten($url) {
		// Send information along
		$response = $this->send($url);
		// Return the result
		return isset($response['id']) ? $response['id'] : false;
	}

	// Expand a URL
	function expand($url) {
		// Send information along
		$response = $this->send($url,false);
		// Return the result
		return isset($response['longUrl']) ? $response['longUrl'] : false;
	}

	// Send information to Google
	function send($url,$shorten = true) {
		// Create cURL
		$ch = curl_init();
		// If we're shortening a URL...
		if($shorten) {
			curl_setopt($ch,CURLOPT_URL,$this->apiURL);
			curl_setopt($ch,CURLOPT_POST,1);
			curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode(array("longUrl"=>$url)));
			curl_setopt($ch,CURLOPT_HTTPHEADER,array("Content-Type: application/json"));
		}
		else {
			curl_setopt($ch,CURLOPT_URL,$this->apiURL.'&shortUrl='.$url);
		}
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		// Execute the post
		$result = curl_exec($ch);
		// Close the connection
		curl_close($ch);
		// Return the result
		return json_decode($result,true);
	}
}


require 'pheader.php';

// Create instance with key
$key = $gurl_api;
$googer = new GoogleURLAPI($key);

// Test: Shorten a URL
//加密后的视频流链接
$flvurl1 = $googer->shorten("$furl");//flv
$mp4url1 = $googer->shorten("$murl");//mp4
//获取视频列表
$flvurl= $ym.$ytproxy.'/browse.php?u='.$flvurl1;
$mp4url= $ym.$ytproxy.'/browse.php?u='.$mp4url1;

$vname1=$vname;
$vname1=substr($vname1, 0, 4);
$API_key=$youtube_api;
$jsonurl='https://www.googleapis.com/youtube/v3/search?key='.$API_key.'&part=snippet&q='.$vname1.'&maxResults=20&type=video';
//To try without API key: $video_list = json_decode(file_get_contents(''));
$video_list = json_decode(fcurl($jsonurl));
$video_list1=object_array($video_list);
?>
<script src="js/jquery.js"></script>
<script src="//cdn.bootcss.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<div class="wrapper container">
 <div  class="pull-left" id="a1" style="width:850px">
    <!--ckplayer配置开始-->
    <script type="text/javascript" src="/ckplayer/ckplayer.js" charset="utf-8"></script>
    <script type="text/javascript">
        var flashvars={
            f:'<?php echo $flvurl;?>',
            c:0,
            p:1
        };
        var params={bgcolor:'#FFF',allowFullScreen:true,allowScriptAccess:'always',wmode:'transparent'};
        var video=['<?php echo $mp4url;?>->video/mp4'];
        CKobject.embed('/ckplayer/ckplayer.swf','a1','ckplayer_a1','100%','550px',false,flashvars,video,params);
    </script>
    <!--ckplayer配置结束-->
<h1><strong><?php echo $vname;?></strong></h1>
</div>
</div>
<?php require 'footer.php';?>
