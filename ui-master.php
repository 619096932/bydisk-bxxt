<?php
date_default_timezone_set("Asia/Shanghai");
session_start(); 
include('conn.php');

//检测是否登录，若没登录则转向登录界面  
if(!isset($_SESSION['userid'])){  
    header("Location:page-login.html");  
    exit();  
}

$username = $_SESSION['userid'];
$stmt_user_check=$conn->prepare("SELECT * from login where username=? and NOW='RUN' limit 1");
$stmt_user_check->bind_param("s", $username);
$stmt_user_check->execute();
//$stmt_user_check->bind_result();
$result_user_check=$stmt_user_check->get_result();
$rows_user_check=$result_user_check->fetch_assoc();
if ($rows_user_check['NOW'] == "OFF") {
	echo '<script language="JavaScript">;alert("拒绝停用账户访问");location.href="page-login.html";</script>;';
   exit;
}

if ($rows_user_check['TYPE'] != "SUPER") {
	echo '<script language="JavaScript">;alert("您没有这个页面的权限");location.href="ui-table.php";</script>';
	exit;
}
$local_user = $rows_user_check['devname'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta content="IE=edge" http-equiv="X-UA-Compatible">
	<meta content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no, width=device-width" name="viewport">
	<title>注册/举报/黑名单处理 - 报修平台Ver3.0.3</title>

	<!-- css -->
	<link href="../css/base.css" rel="stylesheet">

	<!-- css for doc -->
	<link href="../css/project.min.css" rel="stylesheet">
	
	<!-- Loading -->
	<script src="../js/jquery.js"></script>
	<link rel="stylesheet" href="../xLoader/css/xloader.css">
	<script type="text/javascript">
		$("#xLoader").fadeIn(300);
	   window.onload=function(){  
        $("#xLoader").fadeOut(300);
		};
  	</script>
	<style>
		
		#xLoader{
			padding: 20px;
		    min-width: 450px;
		    min-height: 180px;
		    background-color: #eee;
		    position: fixed;
		}
	</style>
	<!-- ... -->
</head>
<div id="xLoader">
	<div class="google-spin-wrapper">
		<div class="google-spin">
		</div>
	</div>
</div>
<body class="page-brand">
	<header class="header header-transparent header-waterfall">
		<ul class="nav nav-list pull-left">
			<li>
				<a data-toggle="menu" href="#doc_menu">
					<span class="icon icon-lg">menu</span>
				</a>
			</li>
		</ul>
		<a class="header-affix-hide header-logo margin-left-no margin-right-no" data-offset-top="213" data-spy="affix">报修平台<small>Ver3.0.3</small></a>
		<a class="header-affix header-logo margin-left-no margin-right-no" data-offset-top="213" data-spy="affix"><small>侧边菜单</small></a>
		<ul class="nav nav-list pull-right">
			<li class="dropdown margin-right">
				<a class="dropdown-toggle padding-left-no padding-right-no" data-toggle="dropdown">
					<span class="access-hide"></span>
					<span class="avatar avatar-sm"><img alt="alt text for John Smith avatar" src="../images/users/avatar-001.jpg"></span>
				</a>
				<ul class="dropdown-menu">
					<li>
						<a class="padding-right-lg waves-attach" href="javascript:void(0)"><span class="icon icon-lg margin-right">account_box</span><?php echo ($local_user);?></a>
					</li>
					<li>
						<a class="padding-right-lg waves-attach" href="login.php?action=logout"><span class="icon icon-lg margin-right">exit_to_app</span>Logout</a>
					</li>
				</ul>
			</li>
		</ul>
	</header>
	<nav aria-hidden="true" class="menu menu-left nav-drawer nav-drawer-md" id="doc_menu" tabindex="-1">
		<div class="menu-scroll">
			<div class="menu-content">
				<a class="menu-logo">报修平台<small>Ver3.0.3</small></a>
				<ul class="nav">
					<li>
						<a class="waves-attach waves-effect" href="ui-table.php">等待处理订单</a>
					</li>
					<li>
						<a class="waves-attach waves-effect" href="ui-intable.php">正在进行的订单</a>
					</li>
					<li>
						<a class="waves-attach waves-effect" href="ui-donetable.php">您完成的订单</a>
					</li>
					<li>
						<a class="waves-attach waves-effect" href="ui-wortable.php">全局违规订单</a>
					</li>
					<li>
						<a class="waves-attach waves-effect" href="ui-master.php">注册/举报/黑名单处理</a>
					</li>
					<li>
						<a class="waves-attach waves-effect" href="ui-mtable.php">管理员订单处理</a>
					</li>
					<li>
						<a class="waves-attach waves-effect" href="#">数据库管理员</a>
					</li>
					<li>
						<a class="waves-attach waves-effect" href="ui-gpa.php">全局报表</a>
					</li>
					<li>
						<a class="waves-attach waves-effect" href="ui-all.php">全部订单</a>
					</li>
					<li>
						<a class="waves-attach waves-effect" href="#">Android APP</a>
					</li>
					<li>
						<a class="waves-attach waves-effect" href="#">iOS APP</a>
					</li>
				</ul>
			</div>
		</div>
	</nav>
	<main class="content">
		<div class="content-heading">
			<div class="container">
				<div class="row">
					<div class="col-lg-6 col-lg-offset-3 col-md-8 col-md-offset-2">
						<h1 class="heading">注册/举报/黑名单处理</h1>
					</div>
				</div>
			</div>
		</div>
		<div class="container">
			<div class="row">
				<div class="col-lg-6 col-lg-offset-3 col-md-8 col-md-offset-2">
					<section class="content-inner margin-top-no">
						<div class="card">
							<div class="card-main">
								<div class="card-inner">
									<p>这里注册/举报/黑名单处理页面</p>
									<p>请善用Ctrl+F搜索功能</p>
								</div>
							</div>
						</div>
						<h2 class="content-sub-heading"></h2>
							<div class="card">
								<div class="card-main">
									<nav class="margin-top-no tab-nav">
										<ul class="nav nav-justified">
											<li class="active">
												<a class="waves-attach waves-effect" data-toggle="tab" href="#doc_tab_example_1" aria-expanded="true">待审核的注册用户</a>
											</li>
											<li class="">
												<a class="waves-attach waves-effect" data-toggle="tab" href="#doc_tab_example_2" aria-expanded="false">待审核的举报订单</a>
											</li>
											<li class="">
												<a class="waves-attach waves-effect" data-toggle="tab" href="#doc_tab_example_3" aria-expanded="false">黑名单</a>
											</li>
										</ul>
									<div class="tab-nav-indicator" style="left: 424px; right: 0px;"></div></nav>
									<div class="card-inner">
										<div class="tab-content">
											<div class="tab-pane fade active in" id="doc_tab_example_1">
											<?php
											$stmt_user=$conn->prepare("SELECT * from login where NOW='WAIT'");
											$stmt_user->bind_param();
											$stmt_user->execute();
											//$stmt_user->bind_result();
											$result_user=$stmt_user->get_result();
											while($rows_user = mysqli_fetch_array($result_user))
											echo '
												<div class="card">
													<div class="card-main">
														<div class="card-inner">
															<p class="card-heading">'.$rows_user['Last_Time'].'</p>
															<p>
																姓名:'.$rows_user['devname'].'<br>
																账号：'.$rows_user['username'].'<br>
																手机：'.$rows_user['Phone'].'<br>
																邮箱：'.$rows_user['Email'].'<br>
																锐捷：'.$rows_user['CardNumber'].'<br>
																个人介绍：'.$rows_user['JIESHAO'].'<br>
															</p>
														</div>
														<div class="card-action">
															<div class="card-action-btn pull-left">
																<a class="btn btn-flat waves-attach waves-button waves-effect" href="engine.php?action=user_check_pass&key='.$rows_user[username].'&id='.$username.'"><span class="icon">check</span>&nbsp;YES</a>
																<a class="btn btn-flat waves-attach waves-button waves-effect" href="engine.php?action=user_check_cancel&key='.$rows_user[username].'&id='.$username.'"><span class="icon">clear</span>&nbsp;NO</a>
															</div>
														</div>
													</div>
												</div>
											';?>
											<div class="card">
													<div class="card-main">
														<div class="card-inner">
															<p>下面木有了喔，过会刷新试试吧。</p>
														</div>
													</div>
												</div>
											</div>
											<div class="tab-pane fade" id="doc_tab_example_2">
											<?php
											$stmt_wor=$conn->prepare("SELECT * from avada where NOW='涉嫌违规.处理中' ORDER BY JDTIME DESC");
											$stmt_wor->bind_param();
											$stmt_wor->execute();
											//$stmt_wor->bind_result();
											$result_wor=$stmt_wor->get_result();
											while($rows_wor = mysqli_fetch_array($result_wor))
											echo '
											
												<div class="card">
													<div class="card-main">
														<div class="card-inner">
															<p class="card-heading">'.$rows_wor['DATATIME'].'</p>
															<p>
																姓名：'.$rows_wor['NAME'].'<br>
																锐捷：'.$rows_wor['RGID'].'<br>
																地址：'.$rows_wor['AREA'].$rows_wor['ROOM'].'<br>
																电话：'.$rows_wor['PHONE'].'<br>
																举报人：'.$rows_wor['Personnel'].'<br>
																类型：'.$rows_wor['TYPE'].'<br>
																描述'.$rows_wor['Description'].'<br>
															</p>
														</div>
														<div class="card-action">
															<div class="card-action-btn pull-left">
																<a class="btn btn-flat waves-attach waves-button waves-effect" href="engine.php?action=wor-done&key='.$rows_wor[DATATIME].'&id='.$username.'"><span class="icon">check</span>&nbsp;YES</a>
																<a class="btn btn-flat waves-attach waves-button waves-effect" href="engine.php?action=wor-cancel&key='.$rows_wor[DATATIME].'&id='.$username.'"><span class="icon">clear</span>&nbsp;NO</a>
															</div>
														</div>
													</div>
												</div>
											 ';
											?>
											<div class="card">
													<div class="card-main">
														<div class="card-inner">
															<p>下面木有了喔，过会刷新试试吧。</p>
														</div>
													</div>
												</div>
											</div>
											<div class="tab-pane fade" id="doc_tab_example_3">
											<?php
											$stmt_black=$conn->prepare("SELECT * from avada where NOW='订单违规.列入黑名单' ORDER BY JDTIME DESC");
											$stmt_black->bind_param();
											$stmt_black->execute();
											//$stmt_black->bind_result();
											$result_black=$stmt_black->get_result();
											while($rows_black = mysqli_fetch_array($result_black))
											echo '
												<div class="card">
													<div class="card-main">
														<div class="card-inner">
															<p class="card-heading">'.$rows_black['DATATIME'].'</p>
															<p>
																姓名：'.$rows_black['NAME'].'<br>
																锐捷：'.$rows_wor['RGID'].'<br>
																地址：'.$rows_black['AREA'].$rows_black['ROOM'].'<br>
																电话：'.$rows_black['PHONE'].'<br>
																举报人：'.$rows_black['Personnel'].'<br>
																类型：'.$rows_black['TYPE'].'<br>
																描述'.$rows_black['Description'].'<br>
																列入黑名单时间'.$rows_black['DoneTIME'].'<br>
															</p>
														</div>
														<div class="card-action">
															<div class="card-action-btn pull-left">
																<a class="btn btn-flat waves-attach waves-button waves-effect" href="engine.php?action=wor-cancel&key='.$rows_black[DATATIME].'&id='.$username.'"><span class="icon">check</span>&nbsp;解除封禁</a>
															</div>
														</div>
													</div>
												</div>
											 ';?>
											    <div class="card">
													<div class="card-main">
														<div class="card-inner">
															<p>下面木有了喔，过会刷新试试吧。</p>
														</div>
													</div>
												</div>
											 </div>
										</div>
									</div>
								</div>
							</div>
					</section>
				</div>
			</div>
		</div>
	</main>
	<footer class="footer">
		<div class="container">
			<p>Copyright © 2016 ByDisk Developers</p>
		</div>
	</footer>
	<div class="fbtn-container">
		<div class="fbtn-inner">
			<a class="fbtn fbtn-brand-accent fbtn-lg" data-toggle="dropdown"><span class="fbtn-text">Links</span><span class="fbtn-ori icon">apps</span><span class="fbtn-sub icon">close</span></a>
			<div class="fbtn-dropdown">
				<a class="fbtn" href="https://developers.bydisk.com/" target="_blank"><span class="fbtn-text">开发者社区主页</span><span class="icon">home</span></a>
				<a class="fbtn fbtn-brand" href="http://weibo.com/347898945" target="_blank"><span class="fbtn-text">关注站长微博</span><span class="icon">share</span></a>
			</div>
		</div>
	</div>

	<!-- js -->
	<script src="../js/base.min.js"></script>

 
	<!-- js for doc -->
	<script src="../js/project.min.js"></script>
</body>
</html>