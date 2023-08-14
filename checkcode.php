<?php
//checkcode.php
//开启session
session_start();

//宽
$w = 60;
//高
$h = 40;

//新建一个真彩色图像
$image = imagecreatetruecolor($w, $h);
//设置验证码颜色
$bgcolor = imagecolorallocate($image,255,255,255);
//填充背景色
imagefill($image, 0, 0, $bgcolor);
//10>设置变量
$captcha_code = "";

//随机种子
$char_str = '23456789';
$char_str_len = strlen($char_str)-1;

$checkcode = $code = '';

//生成随机码
for($i=0;$i<2;$i++){
    //设置字体大小
    $fontsize = 5;
    //设置字体颜色，随机颜色
    $fontcolor = imagecolorallocate($image, rand(0,120),rand(0,120), rand(0,120));
    //设置数字
    $code = substr($char_str,rand(0,$char_str_len),1);
    //拼接验证码
    $checkcode .= $code;
    //随机码宽度
    $x = ($i*$w/2)+rand(5,10);
    //随机码高度
    $y = rand(5,$h/2);
    imagestring($image,$fontsize,$x,$y,$code,$fontcolor);
}

//保存code到session
$_SESSION['checkcode'] = $checkcode;

//设置雪花点
for($i=0;$i<100;$i++){
    //设置点的颜色
    $pointcolor = imagecolorallocate($image,rand(50,200), rand(50,200), rand(50,200));
    //imagesetpixel画一个单一像素
    imagesetpixel($image, rand(0,$w), rand(0,$h), $pointcolor);
}

//增加干扰元素
for($i=0;$i<0;$i++){
    //设置线的颜色
    $linecolor = imagecolorallocate($image,rand(80,220), rand(80,220),rand(80,220));
    //设置线，两点一线
    imageline($image,rand(1,$w-1), rand(1,$h-1),rand(1,$w-1), rand(1,$h-1),$linecolor);
}

//设置图片头部
header('Content-Type: image/png');
//生成png图片
imagepng($image);
//销毁$image
imagedestroy($image);
?>