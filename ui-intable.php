<?php
date_default_timezone_set("Asia/Shanghai");
session_start();  
  
//检测是否登录，若没登录则转向登录界面  
if(!isset($_SESSION['userid'])){  
    header("Location:page-login.html");  
    exit();  
}  
include('conn.php');
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
$local_user = $rows_user_check['devname'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta content="IE=edge" http-equiv="X-UA-Compatible">
	<meta content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no, width=device-width" name="viewport">
	<title>正在进行的订单 - 报修平台Ver3.0.3</title>

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
				<h1 class="heading">正在进行的订单</h1>
			</div>
		</div>
		<div class="container">
			<section class="content-inner margin-top-no">
				<div class="card">
					<div class="card-main">
						<div class="card-inner">
							<p>这里是你正在进行的订单</p>
							<p>请善用Ctrl+F搜索功能</p>
						</div>
					</div>
				</div>
				<?php
				$stmt_user_data=$conn->prepare("SELECT * from avada where Personnel=? and NOW='处理中' ORDER BY JDTIME DESC");
				$stmt_user_data->bind_param("s", $username);
				$stmt_user_data->execute();
				//$stmt_user_data->bind_result();
				$result_user_data=$stmt_user_data->get_result();
				while($row = mysqli_fetch_array($result_user_data))
				echo '
				<h2 class="content-sub-heading">'.$row[DATATIME].'</h2>
				<div class="table-responsive">
					<table class="table" title="A basic table">
						<thead>
							<tr>
								<th>姓名</th>
								<th>区域&房间</th>
								<th>电话</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>'.$row[NAME].'</td>
								<td>'.$row[AREA].$row[ROOM].'</td>
								<td>'.$row[PHONE].'</td>
							</tr>
						</tbody>
						<thead>
							<tr>
								<th>状态</th>
								<th>接单时间</th>
								<th>完成时间</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>'.$row[NOW].'</td>
								<td>'.$row[JDTIME].'</td>
								<td>'.$row[DoneTIME].'</td>
							</tr>
						</tbody>
						<thead>
							<tr>
							<th>类别与描述</th>
							<th></th>
							<th>
								<div class="card-action-btn pull-left" style="margin: 0;">
									<a class="btn btn-brand-accent btn-flat waves-attach waves-button waves-effect" style="display: initial;" data-backdrop="static" data-toggle="modal" href="#confirm_'.$row[PHONE].'">完成订单</a>
									<a class="btn btn-brand-accent btn-flat waves-attach waves-button waves-effect" style="display: initial;" data-backdrop="static" data-toggle="modal" href="#cancel_'.$row[PHONE].'">取消订单</a>
								</div>
							</th>
							</tr>
						</thead>
						<tbody>
							<tr>
							<td colspan="3">'.$row[TYPE].'&nbsp;'.$row[Description].'</td>
							</tr>
						</tbody>
					</table>
					</div>
					';
					?>
				<div class="card">
					<div class="card-main">
						<div class="card-inner">
							<p>木有订单了喔，过会刷新试试吧。</p>
						</div>
					</div>
				</div>
			</section>
		</div>
	</main>
	<footer class="footer">
		<div class="container">
			<p>Copyright © 2016 ByDisk Developers</p>
		</div>
	</footer>
	<?php
		$stmt_user_data->execute();
		//$stmt_user_data->bind_result();
		$result_user_data=$stmt_user_data->get_result();
		while($row2 = mysqli_fetch_array($result_user_data))
			echo '<div aria-hidden="true" class="modal fade" id="confirm_'.$row2[PHONE].'" role="dialog" tabindex="-1">
			    <div class="modal-dialog modal-xs">
			        <div class="modal-content">
			            <div class="modal-heading"> 完成订单 </div>
			            <div class="modal-inner"> ... </div>
			            <div class="modal-footer">
			            	<p class="text-right">
			            		<a class="btn btn-flat btn-brand waves-attach waves-button waves-effect" href="engine.php?action=done&key='.$row2[DATATIME].'&id='.$username.'">确认完成订单</a>
			            		<a class="btn btn-flat btn-brand waves-attach waves-button waves-effect" data-dismiss="modal">取消</a>
			            	</p>
			            </div>
			        </div>
			    </div>
				</div>
	<div aria-hidden="true" class="modal fade" id="cancel_'.$row2[PHONE].'" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            <div class="modal-heading"> 取消订单 </div>
            <div class="modal-inner"> 仅可取消接单起30分钟内的订单 </div>
            <div class="modal-footer"> 
            	<p class="text-right">
            		<a class="btn btn-flat btn-brand waves-attach waves-button waves-effect" href="engine.php?action=cancel&key='.$row2[DATATIME].'&id='.$username.'">确认取消订单</a>
            		<a class="btn btn-flat btn-brand waves-attach waves-button waves-effect" data-dismiss="modal">返回</a>
            	</p>
            </div>
        </div>
    </div>
	</div>
	';?>
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

	<!-- 谷歌统计 -->
	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	  ga('create', 'UA-74730081-2', 'auto');
	  ga('send', 'pageview');

	</script>

	<!-- js for doc -->
	<script src="../js/project.min.js"></script>
</body>
</html>