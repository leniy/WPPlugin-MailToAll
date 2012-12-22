<?php
/*
	Plugin Name:Mail To All
	Plugin URI: http://blog.leniy.info/mail-to-all.html
	Description: 方便给某篇文章的评论用户发送订阅、通知等邮件。
	Version: 0.0.4
	Author: leniy
	Author URI: http://blog.leniy.info/
*/

/*
	Copyright 2012 Leniy (m@leniy.info)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as 
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

register_activation_hook(__FILE__, 'qw_MTA_act');
register_deactivation_hook(__FILE__, 'qw_MTA_deact');

function qw_MTA_act() {
//	add_option("qw_MTA_post", "36");//给这篇文章的用户发送邮件
//	add_option("qw_MTA_notsentpost", "33");//只要用户在这篇文章中留言，则不再发送邮件至其邮箱
	add_option("qw_MTA_subject", "我启用Mail-To-All插件了");
	add_option("qw_MTA_mail", get_option('admin_email'));
	add_option("qw_MTA_list", "m@leniy.info");
	add_option("qw_MTA_content", "欢迎使用本插件");

	global $wpdb;
	//创建表
	$wpdb->query("
	CREATE TABLE IF NOT EXISTS mta_subscribe (
	id INT UNSIGNED NOT NULL DEFAULT NULL AUTO_INCREMENT,
	email TEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
	subscribe BOOLEAN,
	PRIMARY KEY (id)
	)
	");
	//初始化时将当前数据库的所有评论email导入，默认为不订阅0，需评论者手动勾选复选框，对应项目才改为1，即订阅
	$wpdb->query("
	INSERT INTO mta_subscribe (`email`)
	SELECT DISTINCT comment_author_email
	FROM $wpdb->comments
	");
	$wpdb->query("UPDATE mta_subscribe SET subscribe = 0");
}

function qw_MTA_deact() {
//	delete_option('qw_MTA_post');
//	delete_option('qw_MTA_notsentpost');
	delete_option('qw_MTA_subject');
	delete_option('qw_MTA_mail');
	delete_option('qw_MTA_list');
	delete_option('qw_MTA_content');

	global $wpdb;
	$wpdb->query("
	DROP TABLE mta_subscribe
	");
}

if (is_admin()) {
	add_action('admin_menu', 'qw_MTA_menu');
}

function qw_MTA_menu() {
	add_options_page('Mail To All', 'Mail To All', 'administrator', 'Mail-To-All.php', 'qw_MTA_page');
}

function baocuncaogao() {
//	update_option("qw_MTA_post", $_POST['qw_MTA_post']);
//	update_option("qw_MTA_notsentpost", $_POST['qw_MTA_notsentpost']);
	update_option("qw_MTA_subject", $_POST['qw_MTA_subject']);
	update_option("qw_MTA_mail", $_POST['qw_MTA_mail']);
	update_option("qw_MTA_list", $_POST['qw_MTA_list']);
	update_option("qw_MTA_content", $_POST['qw_MTA_content']);
//	echo get_option("qw_MTA_subject");
//	echo get_option("qw_MTA_mail");
//	echo get_option("qw_MTA_list");
//	echo get_option("qw_MTA_content");
	echo "<div id=\"message\" class=\"mtaupdate\"><p>保存草稿成功。</p></div>";
}

function chongxinhuoquyouxiangliebiao() {
	global $wpdb;
//	$query = "
//	SELECT DISTINCT `comment_author_email`
//	FROM $wpdb->comments
//	WHERE comment_post_ID = " . $_POST['qw_MTA_post'] . "
//	AND comment_author_email NOT 
//	IN (
//	SELECT DISTINCT  `comment_author_email` 
//	FROM $wpdb->comments
//	WHERE  `comment_post_ID` = " . $_POST['qw_MTA_notsentpost'] . "
//	)";
	$query = "
	SELECT DISTINCT email
	FROM mta_subscribe
	WHERE subscribe = 1
	";
//	echo $query;
	$queryemail = $wpdb->get_results($query);
	$output = "";
	foreach ($queryemail as $mail) {
		$output = $mail->email . ",\n" . $output;
	}
	update_option("qw_MTA_list", $output);
	echo "<div id=\"message\" class=\"mtaupdate\"><p>重新获取接收人邮件列表成功。</p></div>";
}

function fasong() {
	$from = $_POST['qw_MTA_mail'];
	$headers = "From: $from";
	$to = $_POST['qw_MTA_list'];
	$subject = $_POST['qw_MTA_subject'];
	$message = $_POST['qw_MTA_content'];
	$message .= "\n\n\n
---
This was sent by admin user at " . get_option('blogname') . " (" . get_option('siteurl') . ")
You receive this email because you checked the subscribe checkbox when you leaved a comment on the website.
To unsubscribe, just leave a comment <a href=\"" . get_option('siteurl') . "\">here</a>, with unselection of the subscribe checkbox";
	if($_POST['qw_MTA_confirm']=="YES") {
		mail($to,$subject,$message,$headers);
		echo "<div id=\"message\" class=\"mtaupdate\"><p>邮件发送成功。</p></div>";
	}
	else {echo "<div id=\"message\" class=\"mtaupdatefail\"><p>发送失败，您尚未确认发送</p><p>请填入“YES”确认发送。</p></div>";}
}

function showhtml() {
	?>
	<h2>Mail To All</h2>
	<div>
		<form method="post">
			<table class="form-table">
				<tbody>
<!--
					<tr>
						<th scope="row">输入一篇文章的ID，邮件将发给这篇文章的留言者</th>
						<td>
							<input name="qw_MTA_post" type="text" id="qw_MTA_post" value="<?php echo get_option('qw_MTA_post'); ?>" />
							<input type="submit" name="chongxinhuoquyouxiangliebiao" value="重新获取邮箱列表" class="button-primary" />
							<p><strong><b>同时位于此篇的留言者将不会收到邮件：</b></strong>
							<input name="qw_MTA_notsentpost" type="text" id="qw_MTA_notsentpost" value="<?php echo get_option('qw_MTA_notsentpost'); ?>" />(post_ID必填)</p>
						</td>
					</tr>
-->
					<tr>
						<th scope="row">标题</th>
						<td><input class="regular-text" name="qw_MTA_subject" type="text" id="qw_MTA_subject" value="<?php echo get_option('qw_MTA_subject'); ?>" /></td>
					</tr>
					<tr>
						<th scope="row">发件人</th>
						<td><input class="regular-text" name="qw_MTA_mail" type="text" id="qw_MTA_mail" value="<?php echo get_option('qw_MTA_mail'); ?>" /></td>
					</tr>
					<tr>
						<th scope="row">收件人</th>
						<td>
							<textarea name="qw_MTA_list" rows="5" cols="25" id="qw_MTA_list"><?php echo get_option('qw_MTA_list'); ?></textarea>
							<input type="submit" name="chongxinhuoquyouxiangliebiao" value="重新获取邮箱列表" class="button-primary" />
						</td>
					</tr>
					<tr>
						<th scope="row">正文</th>
						<td><textarea name="qw_MTA_content" rows="5" cols="50" id="qw_MTA_content" class="large-text code"><?php echo get_option('qw_MTA_content'); ?></textarea></td>
					</tr>
					<tr>
						<th scope="row">请输入“YES”确认发送这封邮件</th>
						<td><input name="qw_MTA_confirm" type="text" id="qw_MTA_confirm" value="No" /></td>
					</tr>
				</tbody>
			</table>
			<p><input type="submit" name="baocuncaogao" value="保存草稿" class="button-primary" />
			<input type="submit" name="fasong" value="发送" class="button-primary" /></p>
		</form>
	</div>
	<style type="text/css">
	.mtaupdate,.mtaupdatefail {
		background-color: #D6F8AB;
		border-color: #E6DB55;
		margin: 5px 0 15px;
		padding: 0 .6em;
		border-width: 1px;
		border-style: solid;
		font-size: 12px;
		line-height: 1.4em;
		border-radius: 5px;
		width: 96%;
	}
	.mtaupdatefail {
		background-color: #F8C1AB;
		border-color: #E66655;
	}
	.regular-text {
		width:25em;
	}
	</style>
	<?php
}

function qw_MTA_page() {
	if($_POST['baocuncaogao'] != "") {
		baocuncaogao();
	}
	if($_POST['chongxinhuoquyouxiangliebiao'] != "") {
		baocuncaogao();
		chongxinhuoquyouxiangliebiao();
	}
	if($_POST['fasong'] != "") {
		baocuncaogao();
		fasong();
	}
	showhtml();
}




/**********************************************************
下面的代码是面向评论者的
1.	显示是否订阅的复选框
2.	发送评论时，分别根据是否勾选复选框执行不同功能
**********************************************************/

//在此篇post中的留言，将不会收到邮件，也就是取消订阅页面
//post_id定义于：get_option('qw_MTA_notsentpost');

//给评论者显示是否接收邮件通知的复选框
add_action('comment_form', 'show_checkbox_to_commenters');
function show_checkbox_to_commenters() {
	?>
	<p>
	<input type="checkbox" name="MTA_subscribe_checkbox" id="MTA_subscribe_checkbox" value="MTA_subscribe_checkbox" style="width: auto;" />
	<label for="MTA_subscribe_checkbox">订阅本站最新通知（已经订阅过，想取消？只需不选择复选框留个言就可以了）</label>
	</p>
	<?php
}

//依照复选框是否勾选执行动作
add_filter('preprocess_comment', 'ifischecked');
function ifischecked($incoming_comment) {
	global $wpdb;
	$temp = 1;
	if ($_POST['MTA_subscribe_checkbox'] != 'MTA_subscribe_checkbox') { //未被勾选，表示不订阅。
		$temp = 0;
	}
	else { //否则就是订阅。如果数据库“不订阅区”存在本邮箱的记录，则将其设为1
		$temp = 1;
	}

	$querytemp = "DELETE FROM `mta_subscribe` WHERE `email` LIKE \"" . $incoming_comment['comment_author_email'] . "\"";
	$wpdb->query($querytemp);

	$querytemp = "INSERT INTO mta_subscribe (`email` ,`subscribe`) VALUES (\"" . $incoming_comment['comment_author_email'] . "\" , \"" . $temp . "\")";
	$wpdb->query($querytemp);

	return( $incoming_comment );
}


?>
