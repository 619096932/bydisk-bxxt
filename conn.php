<?php
if(is_file($_SERVER['DOCUMENT_ROOT'].'/360safe/360webscan.php')){
    require_once($_SERVER['DOCUMENT_ROOT'].'/360safe/360webscan.php');
} // 注意文件路径
date_default_timezone_set("Asia/Shanghai");
 $conn = new mysqli('127.0.0.1:3306','root','tset.','bxxt') or die("哎呀出错了啦");  
 $conn->query("SET NAMES utf8");
?>  