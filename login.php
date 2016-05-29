<?php 
date_default_timezone_set("Asia/Shanghai");
echo "<head><meta http-equiv='Content-Type' content='text/html; charset=utf-8'/></head>";
$nowtime= date('Y-m-d H:i:s',time());
$address=$_SERVER["REMOTE_ADDR"];
$agent=$_SERVER['HTTP_USER_AGENT'];
include('conn.php'); 

//记录Access
//$filename = "http://ip.taobao.com/service/getIpInfo.php?ip=".$address;
//$json = json_decode(file_get_contents($filename));
//$location_add=$json->data->city.$json->data->isp;
//var_dump($location_add);
//$log_user="login.php";
//$stmt_user_addresslog=$conn->prepare("INSERT INTO access_log VALUES (?,?,?,?,?)");
//$stmt_user_addresslog->bind_param("sssss", $address, $log_user, $location_add, $nowtime, $agent);
//$stmt_user_addresslog->execute();


//注销登录  
if($_GET['action'] == "logout"){
    session_start();
    unset($_SESSION['userid']);  
    unset($_SESSION['username']);
    session_destroy();  
    echo '<script language="JavaScript">;alert("注销成功");location.href="page-login.html";</script>;';
	exit;  
}
 
//检测用户名及密码是否正确  
if ($_GET['action'] == "login") {
    $username = htmlspecialchars(strval($_POST['username']));  
    $password = md5(htmlspecialchars(strval($_POST['password'])));
    $stmt_user_login=$conn->prepare("SELECT * from login where username=? and password=? limit 1");
    $stmt_user_login->bind_param("ss", $username, $password);
    $stmt_user_login->execute()or die(哎呀出错啦登陆失败);
    //$stmt_user_login->bind_result();
    $result=$stmt_user_login->get_result();
    $rows=$result->fetch_assoc();
    $rely_username = $rows['username'];
    $dev_name = $rows['devname'];
        if (!$rows) {
            echo '<script language="JavaScript">;alert("登陆失败！请检查用户名密码或联系管理员！");location.href="page-login.html";</script>;';
            exit;
        }
        if ($rows['NOW'] == "WAIT") {
            echo '<script language="JavaScript">;alert("登陆失败！你的账号还在审核中呢");location.href="page-login.html";</script>;';
            exit;
        }
        if ($rows['NOW'] == "OFF") {
            echo '<script language="JavaScript">;alert("登陆失败！你的账号已停用");location.href="page-login.html";</script>;';
            exit;
        }
        if ($rows['NOW'] == "RUN"&&$rows['TYPE'] == "USER") { 
            session_start(); 
            $_SESSION['username'] = $username;  
            $_SESSION['userid'] = $rely_username;
            $_SESSION['devname'] = $devname;
            $stmt_user_address=$conn->prepare("UPDATE login SET Last_Address=? ,Last_Time=? WHERE username=? limit 1");
            $stmt_user_address->bind_param("sss", $address, $nowtime, $username);
            $stmt_user_address->execute();
            echo '<script language="JavaScript">;alert("登陆成功");location.href="ui-table.php";</script>;';
            exit;
        }
        if ($rows['NOW'] == "RUN"&&$rows['TYPE'] == "SUPER") { 
            session_start(); 
            $_SESSION['username'] = $username;  
            $_SESSION['userid'] = $rely_username;
            $_SESSION['devname']=$devname;
            $stmt_user_address=$conn->prepare("UPDATE login SET Last_Address=? ,Last_Time=? WHERE username=? limit 1");
            $stmt_user_address->bind_param("sss", $address, $nowtime, $username);
            $stmt_user_address->execute();
            echo '<script language="JavaScript">;alert("登陆成功");location.href="ui-mtable.php";</script>;';
            exit;
        }
}
echo '<script language="JavaScript">;alert("拒绝无效访问");location.href="page-login.html";</script>;';
exit; 
?>  