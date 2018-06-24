<?php
class weixin{
public function index()
{ 
//微信
$url = $GLOBALS['request']['urll'];//获取当前页面的url，接收请求参数
$root['url'] = $url;
//获取access_token，并缓存
$file = 'access_token';//缓存文件名access_token
$expires = 3600;//缓存时间1个小时
if(file_exists($file)) {
$time = filemtime($file);
if(time() - $time > $expires) {
$token = null;
}else {
$token = file_get_contents($file);
}
}else{
fopen("$file", "w+");
$token = null;
}
if (!$token || strlen($token) < 6) {
$res = file_get_contents("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='wx477a6132a43b3bf0'&secret='bbb84e1e770104fb19679603d02d7955'");//自己的appid,通过微信公众平台查看appid和AppSecret
$res = json_decode($res, true);
$token = $res['access_token'];
// write('access_token', $token, 3600);
@file_put_contents($file, $token);
}

//获取jsapi_ticket，并缓存
$file1 = 'jsapi_ticket';
if(file_exists($file1)) {
$time = filemtime($file1);
if(time() - $time > $expires) {
$jsapi_ticket = null;
}else {
$jsapi_ticket = file_get_contents($file1);
}
}else{
fopen("$file1", "w+");
$jsapi_ticket = null;
}
if (!$jsapi_ticket || strlen($jsapi_ticket) < 6) {
$ur = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=$token&type=jsapi";
$res = file_get_contents($ur);
$res = json_decode($res, true);
$jsapi_ticket = $res['ticket'];
@file_put_contents($file1, $jsapi_ticket);
}

$timestamp = time();//生成签名的时间戳
$metas = range(0, 9);
$metas = array_merge($metas, range('A', 'Z'));
$metas = array_merge($metas, range('a', 'z'));
$nonceStr = '';
for ($i=0; $i < 16; $i++) {
$nonceStr .= $metas[rand(0, count($metas)-1)];//生成签名的随机串
}

$string1="jsapi_ticket=".$jsapi_ticket."&noncestr=$nonceStr"."&timestamp=$timestamp"."&url=$url";
$signature=sha1($string1);
$root['appid'] = $appid;
$root['nonceStr'] = $nonceStr;
$root['timestamp'] = $timestamp;
$root['signature'] = $signature;


output($root);
}
}
?>