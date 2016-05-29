<?php
session_start();
date_default_timezone_set("Asia/Shanghai");
header('content-type:text/html;charset=utf-8;');
include('conn.php'); 
//include_once("analyticstracking.php");
$datatime = $_GET['key'];
$jdname = $_GET['id'];
$nowtime= date('Y-m-d H:i:s',time());
$address=$_SERVER["REMOTE_ADDR"];
$agent=$_SERVER['HTTP_USER_AGENT'];
$user_id=$_SESSION['username'];

$token=$_POST['token'];  //API Token
$ack=md5($_POST['ack']);  //API AccessKey

/////////// Access_Log // 默认不开启 ////////////
//$filename = "http://ip.taobao.com/service/getIpInfo.php?ip=".$address;
//json_log = json_decode(file_get_contents($filename));
//$location_add=$json_log->data->city.$json_log->data->isp;
//var_dump($location_add);
//$log_user="engine.php";
//$stmt_user_addresslog=$conn->prepare("INSERT INTO access_log VALUES (?,?,?,?,?)");
//$stmt_user_addresslog->bind_param("sssss", $address, $log_user, $location_add, $nowtime, $agent);
//$stmt_user_addresslog->execute();
/////////////////////////////////////

//////参数定义///////
$none="暂无人处理";
$run="处理中";
$wor_run="涉嫌违规.处理中";
$wor_done="订单违规.列入黑名单";
$wor_del="解除黑名单.违规订单作废";
$done="已完成";
$top_none="转交管理员处理.等待确认";
$top_run="管理员已受理订单";
$top_done="管理员已完成";

////////////////
//后台接单接口//
////////////////
if($_GET['action'] == "add"){
	$stmt_api_check_user=$conn->prepare("SELECT * from api_login where token=? and AccessKeyMD5=? limit 1");
	$stmt_api_check_user->bind_param("ss", $token,$ack);
	$stmt_api_check_user->execute();
	$result_api_check=$stmt_api_check_user->get_result();
	$row_api_check=$result_api_check->fetch_assoc();
	session_start(); 
		if(!isset($_SESSION['userid'])&&empty($row_api_check)){  
		    header("Location:page-login.html");  
		    exit;  
		}
	$stmt_add = $conn->prepare("UPDATE avada SET Personnel=? ,JDTIME=? ,NOW=? WHERE DATATIME=? and NOW=?");
	$stmt_add->bind_param("sssss", $add_jdname, $add_nowtime, $run, $add_datatime, $none);
	$add_jdname = $jdname;
	$add_nowtime = date('Y-m-d H:i:s',time());
	$add_datatime = $datatime;	
	$stmt_add->execute();
	if ($stmt_add->affected_rows) {
		if ($row_api_check) {
			echo "Add Successful";
			exit;
		}else{
			echo '<script language="JavaScript">;alert("接单成功");location.href="ui-table.php";</script>';
			exit;	
		}
	}else{
		if ($row_api_check) {
			echo "Add Failed";
			exit;
		}else{
			echo '<script language="JavaScript">;alert("接单失败");location.href="ui-table.php";</script>';
			exit;	
		}
	}
}

////////////////////
//后台完成订单接口//
////////////////////
if ($_GET['action'] == "done") {
	$stmt_api_check_user=$conn->prepare("SELECT * from api_login where token=? and AccessKeyMD5=? limit 1");
	$stmt_api_check_user->bind_param("ss", $token,$ack);
	$stmt_api_check_user->execute();
	$result_api_check=$stmt_api_check_user->get_result();
	$row_api_check=$result_api_check->fetch_assoc();
	session_start(); 
		if(!isset($_SESSION['userid'])&&!$row_api_check){  
		    header("Location:page-login.html");  
		    exit;  
		}
	$stmt_done = $conn->prepare("UPDATE avada SET DoneTIME=? ,NOW=? WHERE DATATIME=? and Personnel=? and NOW=? ");
	$stmt_done->bind_param("sssss", $nowtime, $done, $datatime, $jdname, $run);	
	$stmt_done->execute();
	if ($stmt_done->affected_rows) {
		if ($row_api_check) {
			echo "DONE Successful";
			exit;
		}else{
			echo '<script language="JavaScript">;alert("完成订单成功");location.href="ui-intable.php";</script>';
			exit;	
		}
	}else{
		if ($row_api_check) {
			echo "DONE Failed";
			exit;
		}else{
			echo '<script language="JavaScript">;alert("完成订单失败");location.href="ui-intable.php";</script>';
			exit;	
		}
	}	
}

////////////////////
//后台取消订单接口//
////////////////////

if ($_GET['action'] == "cancel") {

	$stmt_api_check_user=$conn->prepare("SELECT * from api_login where token=? and AccessKeyMD5=? limit 1");
	$stmt_api_check_user->bind_param("ss", $token,$ack);
	$stmt_api_check_user->execute();
	$result_api_check=$stmt_api_check_user->get_result();
	$row_api_check=$result_api_check->fetch_assoc();
		if(!isset($_SESSION['userid'])&&empty($row_api_check)){  
		    header("Location:page-login.html");  
		    exit();  
		}
	$stmt_cancel = $conn->prepare("SELECT * from avada where DATATIME=? limit 1");
	$stmt_cancel->bind_param("s", $datatime);
	$stmt_cancel->execute()or die(取消订单失败请检查是否非法);
	$result = $stmt_cancel->get_result();
	$rows= $result->fetch_assoc();
		if ($rows['Personnel']!=$jdname) {
			if (!empty($row_api_check)) {
				echo "Cancel Failed:ID does not match";
				exit;
			}else{
			echo '<script language="JavaScript">;alert("取消订单失败请检查是否非法");location.href="ui-intable.php";</script>';
			exit;	
			}	
		}
	$data1 = strtotime($nowtime);
	$data2 = strtotime($rows['JDTIME']);
		if ($data1-$data2 <= 1800) {
			$stmt_cancel_pass = $conn->prepare("UPDATE avada SET Personnel = '暂无人接单',NOW=?,JDTIME = null WHERE DATATIME =? and Personnel =?");
			$stmt_cancel_pass->bind_param("sss", $none, $datatime, $jdname);
			$stmt_cancel_pass->execute()or die(取消订单失败请检查是否非法);
				if (!empty($row_api_check)) {
					echo "Cancel successful";
					exit;
				}else{
					echo '<script language="JavaScript">;alert("取消成功！");location.href="ui-intable.php";</script>';
					exit;	
				}
		}else{
			if (!empty($row_api_check)) {
				echo "Cancel Failed:overtime";
				exit;
			}else{
				echo '<script language="JavaScript">;alert("取消失败！该订单接单已超过30分钟！");location.href="ui-intable.php";</script>';
				exit;	
			}	
		}
}

////////////////////
//后台举报订单接口//
////////////////////

if ($_GET['action'] == "wor-add") {
	//session_start(); 
		if(!isset($_SESSION['userid'])){  
		    header("Location:page-login.html");  
		    exit();  
		}
	$stmt_wor_add = $conn->prepare("SELECT * from avada where DATATIME=? limit 1");
	$stmt_wor_add->bind_param("s", $datatime);
	$stmt_wor_add->execute()or die(举报订单失败请检查是否非法);
	$result = $stmt_wor_add->get_result();
	$rows= $result->fetch_assoc();
	if ($rows['NOW'] == $none) {
		$stmt_wor_add_pass= $conn->prepare("UPDATE avada SET Personnel =?,JDTIME =?,NOW=? WHERE DATATIME = ?");
		$stmt_wor_add_pass->bind_param("ssss", $jdname, $nowtime, $wor_run, $datatime);
		$stmt_wor_add_pass->execute()or die(哎呀发生错误啦);
		echo '<script language="JavaScript">;alert("举报订单成功");location.href="ui-table.php";</script>';
		exit;
	}else{
		echo '<script language="JavaScript">;alert("举报订单失败！不符合举报条件！");location.href="ui-table.php";</script>';
		exit;
	}
}

////////////////////////////
//后台订单提交上级处理接口//
////////////////////////////
if ($_GET['action'] == "regtop") {
	if(!isset($_SESSION['userid'])){  
	    header("Location:page-login.html");  
	    exit();  
	}
	$stmt_user_regtop=$conn->prepare("SELECT NOW from avada where DATATIME=? limit 1");
	$stmt_user_regtop->bind_param("s", $datatime);
	$stmt_user_regtop->execute();
	$result=$stmt_user_regtop->get_result();
	$rows=$result->fetch_assoc();
	if ($rows['NOW'] == $none) {
		$stmt_user_regtop_pass=$conn->prepare("UPDATE avada SET NOW=? , REG_TOPID=? where DATATIME=? limit 1 ");
		$stmt_user_regtop_pass->bind_param("ss", $top_none, $user_id, $datatime);
		$stmt_user_regtop_pass->execute();
		echo '<script language="JavaScript">;alert("订单提交上级成功");location.href="ui-table.php";</script>';
		exit;
	}else{
		echo '<script language="JavaScript">;alert("订单提交上级失败");location.href="ui-table.php";</script>';
		exit;
	}
}

////////////////////
//上级选择处理接口//
////////////////////
if ($_GET['action'] == "regrun") {
	if(!isset($_SESSION['userid'])){  
	    header("Location:page-login.html");  
	    exit();  
	}
	$stmt_user_regrun=$conn->prepare("SELECT NOW from avada where DATATIME=? limit 1");
	$stmt_user_regrun->bind_param("s", $datatime);
	$stmt_user_regrun->execute();
	$result=$stmt_user_regrun->get_result();
	$rows=$result->fetch_assoc();
	if ($rows['NOW'] == $top_none) {
		$stmt_user_regrun_pass=$conn->prepare("UPDATE avada SET NOW=?,Personnel=?,JDTIME=? where DATATIME=? limit 1");
		$stmt_user_regrun_pass->bind_param("ssss", $top_run, $jdname, $nowtime, $datatime);
		$stmt_user_regrun_pass->execute();
		echo '<script language="JavaScript">;alert("管理员处理上交订单成功");location.href="ui-mtable.php";</script>';
		exit;
	}else{
		echo '<script language="JavaScript">;alert("管理员处理上交订单失败");location.href="ui-mtable.php";</script>';
		exit;
	}
}

////////////////////
//上级完成处理接口//
////////////////////
if ($_GET['action'] == "regdone") {
	if(!isset($_SESSION['userid'])){  
	    header("Location:page-login.html");  
	    exit();  
	}
	$stmt_user_regdone=$conn->prepare("SELECT NOW from avada where DATATIME=? limit 1");
	$stmt_user_regdone->bind_param("s", $datatime);
	$stmt_user_regdone->execute();
	$result=$stmt_user_regdone->get_result();
	$rows=$result->fetch_assoc();
	if ($rows['NOW'] == $top_run) {
		$stmt_user_regdone_pass=$conn->prepare("UPDATE avada SET NOW=?,DoneTIME=? where DATATIME=? limit 1");
		$stmt_user_regdone_pass->bind_param("sss", $top_done, $nowtime, $datatime);
		$stmt_user_regdone_pass->execute();
		echo '<script language="JavaScript">;alert("管理员完成上交订单成功");location.href="ui-mtable.php";</script>';
		exit;
	}else{
		echo '<script language="JavaScript">;alert("管理员完成上交订单失败");location.href="ui-mtable.php";</script>';
		exit;
	}
}

//////////////////////////////////////
//超级管理员完成举报审核接口->黑名单//
//////////////////////////////////////

if ($_GET['action'] == "wor-done") {
	//session_start(); 
		if(!isset($_SESSION['userid'])){  
		    header("Location:page-login.html");  
		    exit();  
		}
	$stmt_wor_done = $conn->prepare("SELECT TYPE from login where username=? limit 1");
	$stmt_wor_done->bind_param("s", $user_id);
	$stmt_wor_done->execute()or die(哎呀发生错误啦);
	$result = $stmt_wor_done->get_result();
	$rows_super = $result->fetch_assoc(); 
		if ($rows_super['TYPE'] == 'SUPER') {
			$stmt_wor_done_pass = $conn->prepare("UPDATE avada SET NOW=?,DoneTIME=? WHERE DATATIME =?");
			$stmt_wor_done_pass->bind_param("sss", $wor_done, $nowtime, $datatime);
			$stmt_wor_done_pass->execute()or die(哎呀发生错误啦);
			echo '<script language="JavaScript">;alert("加入黑名单成功");location.href="ui-master.php";</script>';
			exit;
		}else{
			echo '<script language="JavaScript">;alert("加入黑名单失败！不符合条件！");location.href="ui-master.php";</script>';
			exit;
		}
}


////////////////////////////////////
//超级管理员完成举报审核接口->解除//
////////////////////////////////////

if ($_GET['action'] == "wor-cancel") {
	//session_start(); 
		if(!isset($_SESSION['userid'])){  
		    header("Location:page-login.html");  
		    exit();  
		}
	$stmt_wor_cancel = $conn->prepare("SELECT TYPE from login where username=? limit 1");
	$stmt_wor_cancel->bind_param("s", $user_id);
	$stmt_wor_cancel->execute()or die(哎呀发生错误啦);
	$result = $stmt_wor_cancel->get_result();
	$rows_super = $result->fetch_assoc();
		if ($rows_super['TYPE'] == 'SUPER') {
			$stmt_wor_done_pass = $conn->prepare("UPDATE avada SET NOW=?,JDTIME=null ,DoneTIME=null WHERE DATATIME =?");
			$stmt_wor_done_pass->bind_param("ss", $wor_del, $datatime);
			$stmt_wor_done_pass->execute()or die(哎呀发生错误啦);
			echo '<script language="JavaScript">;alert("解除封禁成功");location.href="ui-master.php";</script>';
			exit;
		}else{
			echo '<script language="JavaScript">;alert("解除封禁失败！不符合条件！");location.href="ui-master.php";</script>';
			exit;
		}
}

////////////////////////
//公众用户提交订单接口//
////////////////////////

if ($_GET['action'] == "register") {
 	$reg_name = $_POST['name'];
 	$reg_RGID = $_POST['RGID'];
 	$reg_area = $_POST['area'];
 	$reg_room = $_POST['room'];
 	$reg_type = $_POST['type'];
 	$reg_phone = $_POST['phone'];
 	$reg_Des = $_POST['Description'];
  	if (empty($reg_name)||empty($reg_RGID)||empty($reg_area)||empty($reg_room)||empty($reg_type)||empty($reg_phone)||empty($reg_Des)) {
 		echo '<script language="JavaScript">;alert("还有些没填写呢");location.href="index.html#doc_tab_example_1";</script>';
 		exit;
 	}	
 	if (!preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $reg_phone)) {
		echo '<script language="JavaScript">;alert("不认识这个手机号");location.href="index.html#doc_tab_example_1";</script>';
 		exit;
	}
 	$stmt_reg_add_check=$conn->prepare("SELECT NOW from avada where RGID=? Order by DATATIME DESC limit 1");
 	$stmt_reg_add_check->bind_param("s", $reg_RGID);
 	$stmt_reg_add_check->execute();
 	$result=$stmt_reg_add_check->get_result();
 	$rows = $result->fetch_assoc();
 	if (!$rows) {
 		$stmt_reg_add_none_pass=$conn->prepare("INSERT INTO avada (DATATIME,NAME,RGID,AREA,ROOM,TYPE,PHONE,Description,Reg_Address,NOW) VALUES(?,?,?,?,?,?,?,?,?,?)");
 		$stmt_reg_add_none_pass->bind_param("ssssssssss", $nowtime, $reg_name, $reg_RGID, $reg_area, $reg_room, $reg_type, $reg_phone, $reg_Des, $address, $none);
 		$stmt_reg_add_none_pass->execute()or die(提交新订单失败请稍后再试);
		echo '<script language="JavaScript">;alert("新用户提交成功！");location.href="index.html#doc_tab_example_1";</script>';
		exit;
 	}
 	if ($rows['NOW'] == $none) {
 		echo '<script language="JavaScript">;alert("提交失败！您已提交过订单了！");location.href="index.html#doc_tab_example_1";</script>';
		exit;
 	}
 	if ($rows['NOW'] == $run) {
 		echo '<script language="JavaScript">;alert("提交失败！您已提交过订单了且正在维修！");location.href="index.html#doc_tab_example_1";</script>';
		exit;
 	}
 	if ($rows['NOW'] == $wor_run) {
 		echo '<script language="JavaScript">;alert("提交失败！您的订单已被举报，您短期内暂时无法提交订单！");location.href="index.html#doc_tab_example_1";</script>';
		exit;
 	}
 	if ($rows['NOW'] == $wor_done) {
 		echo '<script language="JavaScript">;alert("提交失败！您已被列入黑名单，您无法提交订单！");location.href="index.html#doc_tab_example_1";</script>';
		exit;
 	}
	if ($rows['NOW'] == $done||$rows['NOW'] == $top_done||$rows['NOW'] == $wor_del) {
	$stmt_reg_add_done_pass=$conn->prepare("INSERT INTO avada (DATATIME,NAME,RGID,AREA,ROOM,TYPE,PHONE,Description,Reg_Address,NOW) VALUES(?,?,?,?,?,?,?,?,?,?)");
		$stmt_reg_add_done_pass->bind_param("ssssssssss", $nowtime, $reg_name, $reg_RGID, $reg_area, $reg_room, $reg_type, $reg_phone, $reg_Des, $address, $none);
		$stmt_reg_add_done_pass->execute()or die(提交订单失败请稍后再试);
	echo '<script language="JavaScript">;alert("提交成功！");location.href="index.html#doc_tab_example_1";</script>';
	exit;
	}
	if ($rows['NOW'] == $top_none) {
 		echo '<script language="JavaScript">;alert("提交失败！您已提交过订单了！");location.href="index.html#doc_tab_example_1";</script>';
		exit;
 	}
 	if ($rows['NOW'] == $top_run) {
 		echo '<script language="JavaScript">;alert("提交失败！您已提交过订单了且正在维修！");location.href="index.html#doc_tab_example_1";</script>';
		exit;
 	}
}

////////////////////////
//公众用户查询订单接口//
////////////////////////

if ($_GET['action'] == "check") {
	$check_name = $_POST['name'];
	$check_phone = $_POST['phone'];
	if (empty($check_name)||empty($check_phone)) {
		echo '<script language="JavaScript">;alert("还有些没填写呢");location.href="index.html#doc_tab_example_1";</script>';
 		exit;
	}
	if (!preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $check_phone)) {
		echo '<script language="JavaScript">;alert("不认识这个手机号");location.href="index.html#doc_tab_example_1";</script>';
 		exit;
	}
	$stmt_check = $conn->prepare("SELECT * from avada where name=? and PHONE=? Order by DATATIME DESC limit 1");
	$stmt_check->bind_param("ss", $check_name, $check_phone);
	$stmt_check->execute()or die(查询订单失败请稍后再试);
	$result = $stmt_check->get_result();
	$rows = $result->fetch_assoc();
		if (!$rows) {
			echo '<script language="JavaScript">;alert("查不到您的相关信息喔");location.href="index.html#doc_tab_example_2";</script>';
			exit;
		}
		if ($rows['PHONE'] == $check_phone && $rows['NAME'] == $check_name && $rows['NOW'] == $none) {
			echo '<script language="JavaScript">;alert("您的订单还没有工程师处理呢，请耐心等候。");location.href="index.html#doc_tab_example_2";</script>';
			exit;
		}
		if ($rows['PHONE'] == $check_phone && $rows['NAME'] == $check_name && $rows['NOW'] == $top_none) {
			echo '<script language="JavaScript">;alert("您的订单还没有工程师处理呢，请耐心等候。");location.href="index.html#doc_tab_example_2";</script>';
			exit;
		}
		if ($rows['PHONE'] == $check_phone && $rows['NAME'] == $check_name && $rows['NOW'] == $run) {
			$outside = "您的订单已于".$rows['JDTIME']."受理\\n 由工程师".$rows['Personnel']."受理";
			echo "<script language='JavaScript'>;alert('".$outside."');location.href='index.html#doc_tab_example_2';</script>";
			exit;
		}
		if ($rows['PHONE'] == $check_phone && $rows['NAME'] == $check_name && $rows['NOW'] == $top_run) {
			$outside = "您的订单已于".$rows['JDTIME']."受理\\n 由工程师".$rows['Personnel']."受理";
			echo "<script language='JavaScript'>;alert('".$outside."');location.href='index.html#doc_tab_example_2';</script>";
			exit;
		}
		if ($rows['PHONE'] == $check_phone && $rows['NAME'] == $check_name && $rows['NOW'] == $done) {
			$outside = "您的订单已于".$rows['JDTIME']."完成\\n 由工程师".$rows['Personnel']."受理";
			echo "<script language='JavaScript'>;alert('".$outside."');location.href='index.html#doc_tab_example_2';</script>";
			exit;
		}
		if ($rows['PHONE'] == $check_phone && $rows['NAME'] == $check_name && $rows['NOW'] == $top_done) {
			$outside = "您的订单已于".$rows['JDTIME']."完成\\n 由工程师".$rows['Personnel']."受理";
			echo "<script language='JavaScript'>;alert('".$outside."');location.href='index.html#doc_tab_example_2';</script>";
			exit;
		}
		if ($rows['PHONE'] == $check_phone && $rows['NAME'] == $check_name && $rows['NOW'] == $wor_run) {
			$outside = "您的订单已被举报.请与工作人员联系！";
			echo "<script language='JavaScript'>;alert('".$outside."');location.href='index.html#doc_tab_example_2';</script>";
			exit;
		}
		if ($rows['PHONE'] == $check_phone && $rows['NAME'] == $check_name && $rows['NOW'] == $wor_done) {
			$outside = "您已被列入黑名单\\n 请与工作人员联系！";
			echo "<script language='JavaScript'>;alert('".$outside."');location.href='index.html#doc_tab_example_2';</script>";
			exit;
		}
}

////////////////
//注册提交接口//
////////////////
if ($_GET['action'] == "register_user") {
	$reg_user_name = $_POST['us'];
	$reg_user_pass = md5($_POST['pw']);
	$reg_user_devname = $_POST['devname'];
	$reg_user_phone = $_POST['phone'];
	$reg_user_email = $_POST['em'];
	$reg_user_jc = $_POST['jc'];
	$reg_user_card = $_POST['card'];
	$reg_user_type = "WAIT";
	if (empty($reg_user_name)||empty($reg_user_pass)||empty($reg_user_devname)||empty($reg_user_phone)||empty($reg_user_email)||empty($reg_user_jc)||empty($reg_user_card)) {
		echo '<script language="JavaScript">;alert("还有些没写呢");location.href="register.html";</script>';
		exit;
 	}
	$pattern_email = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
	if (!preg_match($pattern_email, $reg_user_email)) {
		echo '<script language="JavaScript">;alert("不认识这个邮箱地址");location.href="register.html";</script>';
 		exit;
	}
	if (!preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $reg_user_phone)) {
		echo '<script language="JavaScript">;alert("不认识这个手机号");location.href="register.html";</script>';
 		exit;
	}
	if (IS_POST) {
		$stmt_reg_user_check=$conn->prepare("SELECT * from login where username=? limit 1");
		$stmt_reg_user_check->bind_param("s", $reg_user_name);
		$stmt_reg_user_check->execute();
		$result=$stmt_reg_user_check->get_result();
		$rows=$result->fetch_assoc();
		if ($rows['NOW'] == "WAIT") {
			$outside = "您的账户正在被审核\\n 如有异议请与工作人员联系！";
			echo "<script language='JavaScript'>;alert('".$outside."');location.href='register.html';</script>";
			exit;
		}
		if ($rows['NOW'] == "RUN") {
			$outside = "提交失败\\n 账户名已被占用！";
			echo "<script language='JavaScript'>;alert('".$outside."');location.href='register.html';</script>";
			exit;
		}
		if ($rows['NOW'] == "BLOCK") {
			$outside = "提交失败被封禁\\n 请与工作人员联系！";
			echo "<script language='JavaScript'>;alert('".$outside."');location.href='register.html';</script>";
			exit;
		}
		if (!$rows['username']) {
			$stmt_reg_user_into=$conn->prepare("INSERT INTO login (username,password,devname,NOW,Phone,Email,CardNumber,JIESHAO,Last_Address,Last_Time) VALUES(?,?,?,?,?,?,?,?,?,?)");
			$stmt_reg_user_into->bind_param("ssssssssss", $reg_user_name, $reg_user_pass, $reg_user_devname, $reg_user_type, $reg_user_phone, $reg_user_email, $reg_user_card, $reg_user_jc, $address, $nowtime);
			$stmt_reg_user_into->execute();
			echo '<script language="JavaScript">;alert("提交成功！工作人员将尽快审核您的申请\\n 审核通过后将会发送邮件提醒您");location.href="register.html";</script>';
			exit;
		}
	}
    exit;
}

////////////////////////////////
//超级管理员审核用户接口->通过//
////////////////////////////////
if ($_GET['action'] == "user_check_pass") {
	$stmt_user_check_pass_supercheck=$conn->prepare("SELECT TYPE from login where username=? limit 1");
	$stmt_user_check_pass_supercheck->bind_param("s", $user_id);
	$stmt_user_check_pass_supercheck->execute()or die(请稍后再试);
	$result = $stmt_user_check_pass_supercheck->get_result();
	$rows_super = $result->fetch_assoc();
	if ($rows_super['TYPE'] == 'SUPER') {
		$stmt_user_check_pass_supercheck=$conn->prepare("UPDATE login SET NOW = 'RUN' WHERE username =?");
		$stmt_user_check_pass_supercheck->bind_param("s", $datatime);
		$stmt_user_check_pass_supercheck->execute()or die(请稍后再试);
		echo '<script language="JavaScript">;alert("审核通过完成");location.href="ui-master.php";</script>';
		exit;
	}else{
		echo '<script language="JavaScript">;alert("审核通过失败！不符合条件！");location.href="ui-master.php";</script>';
		exit;
	}
}


////////////////////////////////
//超级管理员审核用户接口->删除//
////////////////////////////////
if ($_GET['action'] == "user_check_cancel") {
	$stmt_user_check_cancel_supercheck=$conn->prepare("SELECT TYPE from login where username=? limit 1");
	$stmt_user_check_cancel_supercheck->bind_param("s", $user_id);
	$stmt_user_check_cancel_supercheck->execute();
	$result=$stmt_user_check_cancel_supercheck->get_result();
	$rows_super=$result->fetch_assoc();
	if ($rows_super['TYPE'] == 'SUPER') {
		$stmt_user_check_cancel_pass=$conn->prepare("DELETE FROM login WHERE username =?");
		$stmt_user_check_cancel_pass->bind_param("s", $datatime);
		$stmt_user_check_cancel_pass->execute()or die(请稍后再试);
		echo '<script language="JavaScript">;alert("删除条目完成");location.href="ui-master.php";</script>';
		exit;
	}else{
		echo '<script language="JavaScript">;alert("删除条目失败！不符合条件！");location.href="ui-master.php";</script>';
		exit;
	}
}

/////// API 接口 ////////
if ($_GET['action']=="def_table") {
	$stmt_def_table_user=$conn->prepare("SELECT * from api_login where token=? and AccessKeyMD5=? limit 1");
	$stmt_def_table_user->bind_param("ss", $token,$ack);
	$stmt_def_table_user->execute();
	$result=$stmt_def_table_user->get_result();
	$row=$result->fetch_assoc();
	if (!empty($row)) {
		$stmt_user_data=$conn->prepare("SELECT * from avada where NOW=? ORDER BY DATATIME DESC");
		$stmt_user_data->bind_param("s", $none);
		$stmt_user_data->execute();
		$result_user_data=$stmt_user_data->get_result();
		while (!!$rows = mysqli_fetch_array($result_user_data)) {
			foreach ($rows as $keys => $value) {
				$rows[$keys]=urlencode(str_replace("\n", "", "$value"));
			}
		$json.=urldecode(json_encode($rows)).',';
		}
		echo '{'.substr($json,0, strlen($json)-1).'}';
		$stmt_api_addresslog=$conn->prepare("UPDATE api_login SET LastAddress=? WHERE token=? limit 1");
		$stmt_api_addresslog->bind_param("ss", $address, $token);
		$stmt_api_addresslog->execute();
		exit;
	}else{
		exit;
	}
}

if ($_GET['action']=="def_intable") {
	$stmt_def_intable_user=$conn->prepare("SELECT * from api_login where token=? and AccessKeyMD5=? limit 1");
	$stmt_def_intable_user->bind_param("ss", $token,$ack);
	$stmt_def_intable_user->execute();
	$result=$stmt_def_intable_user->get_result();
	$row=$result->fetch_assoc();
	if (!empty($row)) {
		$stmt_user_data=$conn->prepare("SELECT * from avada where NOW=? ORDER BY JDTIME DESC");
		$stmt_user_data->bind_param("s", $run);
		$stmt_user_data->execute();
		$result_user_data=$stmt_user_data->get_result();
		while (!!$rows = mysqli_fetch_array($result_user_data)) {
			foreach ($rows as $keys => $value) {
				$rows[$keys]=urlencode(str_replace("\n", "", "$value"));
			}
		$json.=urldecode(json_encode($rows)).',';
		}
		echo '{'.substr($json,0, strlen($json)-1).'}';
		$stmt_api_addresslog=$conn->prepare("UPDATE api_login SET LastAddress=? WHERE token=? limit 1");
		$stmt_api_addresslog->bind_param("ss", $address, $token);
		$stmt_api_addresslog->execute();
		exit;
	}else{
		exit;
	}
}

if ($_GET['action']=="def_donetable") {
	$stmt_def_donetable_user=$conn->prepare("SELECT * from api_login where token=? and AccessKeyMD5=? limit 1");
	$stmt_def_donetable_user->bind_param("ss", $token,$ack);
	$stmt_def_donetable_user->execute();
	$result=$stmt_def_donetable_user->get_result();
	$row=$result->fetch_assoc();
	if (!empty($row)) {
		$stmt_user_data=$conn->prepare("SELECT * from avada where NOW=? ORDER BY DoneTIME DESC");
		$stmt_user_data->bind_param("s", $done);
		$stmt_user_data->execute();
		$result_user_data=$stmt_user_data->get_result();
		while (!!$rows = mysqli_fetch_array($result_user_data)) {
			foreach ($rows as $keys => $value) {
				$rows[$keys]=urlencode(str_replace("\n", "", "$value"));
			}
		$json.=urldecode(json_encode($rows)).',';
		}
		echo '{'.substr($json,0, strlen($json)-1).'}';
		$stmt_api_addresslog=$conn->prepare("UPDATE api_login SET LastAddress=? WHERE token=? limit 1");
		$stmt_api_addresslog->bind_param("ss", $address, $token);
		$stmt_api_addresslog->execute();
		exit;
	}else{
		exit;
	}
}
exit(什么都木有);
?>
