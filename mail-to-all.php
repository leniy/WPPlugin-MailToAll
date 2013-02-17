<?php
/*
	Plugin Name:Mail To All
	Plugin URI: http://blog.leniy.info/mail-to-all.html
	Description: 方便给某篇文章的评论用户发送订阅、通知等邮件。
	Version: 1.4
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
	add_option("qw_MTA_default_sub", "T");//T(rue)表示留言框中是否订阅的复选框被勾选，即默认订阅；如为F(alse)，则不勾选，即默认不订阅
//	add_option("qw_MTA_post", "0");//给这篇文章的用户发送邮件，数字0表示站点全部文章；数字如为正整数，则单指Post_ID为这个数字的文章
//	add_option("qw_MTA_notsentpost", "33");//只要用户在这篇文章中留言，则不再发送邮件至其邮箱
	add_option("qw_MTA_subject", "我启用Mail-To-All插件了");
	add_option("qw_MTA_mail", get_option('admin_email'));
	add_option("qw_MTA_list", "m@leniy.info");
	add_option("qw_MTA_content", "欢迎使用本插件");
}

function qw_MTA_deact() {
	delete_option('qw_MTA_default_sub');
//	delete_option('qw_MTA_post');
//	delete_option('qw_MTA_notsentpost');
	delete_option('qw_MTA_subject');
	delete_option('qw_MTA_mail');
	delete_option('qw_MTA_list');
	delete_option('qw_MTA_content');
}

if (is_admin()) {
	add_action('admin_menu', 'qw_MTA_menu');
}

function qw_MTA_menu() {
	add_menu_page('Mail To All', 'Mail To All', 'administrator', 'Mail-To-All', 'MTA_about_page', plugins_url('mail-to-all-comment/icon.png'), 99.1);

	add_submenu_page( 'Mail-To-All', '关于', '关于', 'administrator', 'Mail-To-All', 'MTA_about_page');
	add_submenu_page( 'Mail-To-All', '群发邮件', '群发邮件', 'administrator', 'Mail-To-All/mail.php', 'qw_MTA_page');
	add_submenu_page( 'Mail-To-All', '初始化', '初始化', 'administrator', 'Mail-To-All/init.php', 'MTA_init_page');
}

/**********************************************************************/
/**********************   下面是初始化设置页面   **********************/
/**********************************************************************/
function MTA_init_page() {

	if($_POST['MTA_init_btn']=="初始化") {

		//删除旧表
		global $wpdb;
		$wpdb->query("DROP TABLE mta_subscribe");

		//创建新表，位于此表的邮箱拒绝接收邮件
		$wpdb->query("
		CREATE TABLE IF NOT EXISTS mta_subscribe (
			id INT UNSIGNED NOT NULL DEFAULT NULL AUTO_INCREMENT,
			email TEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
			PRIMARY KEY (id)
		)
		");

		//下拉框选F，则将当前数据库的所有评论email导入,代表其不想订阅；下拉框选T，代表订阅，则不导入，代表想订阅
		if ($_POST['MTA_init_select']=="F") {
			$wpdb->query("
				INSERT INTO mta_subscribe (`email`)
				SELECT DISTINCT comment_author_email
				FROM $wpdb->comments
			");
			echo "<div id=\"message\" class=\"mtainit\"><p>已有邮箱已导入，默认为不订阅</p></div>";
		}
		else {
			echo "<div id=\"message\" class=\"mtainit\"><p>已有邮箱已导入，默认为订阅</p></div>";
		}
		
		//后续新增评论者，留言区的订阅复选框是否默认勾选
		update_option("qw_MTA_default_sub", $_POST['MTA_default_sub']);
		echo "<div id=\"message\" class=\"mtainit\"><p>复选框是否默认勾选为" . $_POST['MTA_default_sub'] . "</p></div>";
	}
	?>
	<h2>Mail To All插件初始化</h2>

	<div><form method="post">
		请注意，一旦初始化，之前站点评论者提交的是否接收邮件的设置将会丢失。
		评论者的设置将会依照本页面的设置批量更改
		<table class="form-table">
			<tbody>
					<tr>
						<th scope="row">导入站点已有评论者信息</th>
						<td>
							<select name="MTA_init_select" id="MTA_init_select">
								<option value="T">默认订阅</option>
								<option value="F">不订阅</option>
							</select>
						<p>导入时，设置已有评论者允许接收邮件还是不允许接收邮件？</p>
						</td>
					</tr>
					<tr>
						<th scope="row">留言复选框设置</th>
						<td>
							<select name="MTA_default_sub" id="MTA_default_sub">
								<option value="T">默认勾选，允许订阅</option>
								<option value="F">默认不勾选，拒绝订阅</option>
							</select>
						<p>评论者留言时是否订阅邮件复选框是否默认勾选</p>
						</td>
					</tr>
			</tbody>
		</table>
		<input type="submit" name="MTA_init_btn" value="初始化" class="button-primary" />
	</form></div>
	<style type="text/css">
	.mtainit {
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
	</style>


	<?php
}

/**********************************************************************/
/***********************   下面是邮件发送页面   ***********************/
/**********************************************************************/
function qw_MTA_page() {
	echo "<h2>Mail To All</h2>";

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

	?>
	<div>
		<form method="post">
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">选择一篇文章的ID，邮件将发给这篇文章的留言者</th>
						<td>
							<select name="qw_MTA_post" id="qw_MTA_post">
							<?php
								$args = array(
									'post_type' => 'post',
									'post_status' => 'publish',
									'order' => 'DESC',
									'orderby' => 'comment_count',
									'posts_per_page' => '-1',
								);
								$query = new WP_Query();
								$posts = $query->query( $args );
								echo "<option value=\"0\">All-Post</option>";
								foreach ($posts as $post) {
									//只显示有评论的
									if ( $post->comment_count != 0 ) {
										printf( '<option value="%d">%s (%d评论)</option>', $post->ID, esc_attr( $post->post_title ), $post->comment_count );
									}
								}
							?>
							</select>
							（只显示有评论的文章）
<!--
							<input name="qw_MTA_post" type="text" id="qw_MTA_post" value="<?php echo get_option('qw_MTA_post'); ?>" />
							<input type="submit" name="chongxinhuoquyouxiangliebiao" value="重新获取邮箱列表" class="button-primary" />
							<p><strong><b>同时位于此篇的留言者将不会收到邮件：</b></strong>
							<input name="qw_MTA_notsentpost" type="text" id="qw_MTA_notsentpost" value="<?php echo get_option('qw_MTA_notsentpost'); ?>" />(post_ID必填)</p>
-->
						</td>
					</tr>

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

/**********************************************************************/
/*************************   下面是关于页面   *************************/
/**********************************************************************/
function MTA_about_page() {
?>
<h2>关于</h2>
第一次使用此插件，请首先<a href="?page=Mail-To-All/init.php">初始化</a>
使用过程中如有疑问，请<a href="http://blog.leniy.info/mail-to-all.html">到这儿留言提问</a>，我会尽快解答
<h2>插件相关</h2>
<iframe frameborder="0" src="http://blog.leniy.info/mail-to-all.html" scrolling="auto" noresize="" width="100%" height="500px"></iframe>
<?php
}

/**********************************************************************/
/**********************   下面是几个按钮的函数   **********************/
/**********************************************************************/
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
	$query = "";
	if ($_POST['qw_MTA_post'] == '0') {
		//qw_MTA_post为零，即发给全部评论者
		$query = "
			SELECT DISTINCT `comment_author_email`
			FROM $wpdb->comments
			WHERE comment_author_email NOT
			IN (
				SELECT DISTINCT email
				FROM mta_subscribe
				)
			";
	}
	else {
		//否则就按照qw_MTA_post所代表的post_id查询
		$query = "
			SELECT DISTINCT `comment_author_email`
			FROM $wpdb->comments
			WHERE comment_post_ID = " . $_POST['qw_MTA_post'] . "
			AND comment_author_email NOT 
			IN (
				SELECT DISTINCT email
				FROM mta_subscribe
				)
			";
	}
/*
	$query = "
	SELECT DISTINCT `comment_author_email`
	FROM $wpdb->comments
	WHERE comment_post_ID = " . $_POST['qw_MTA_post'] . "
	AND comment_author_email NOT 
	IN (
	SELECT DISTINCT  `comment_author_email` 
	FROM $wpdb->comments
	WHERE  `comment_post_ID` = " . $_POST['qw_MTA_notsentpost'] . "
	)";
	$query = "
	SELECT DISTINCT email
	FROM mta_subscribe
	WHERE subscribe = 1
	";
*/
//	echo $query;
	$queryemail = $wpdb->get_results($query);
	$output = "";
	foreach ($queryemail as $mail) {
		$output = $mail->comment_author_email . "\n" . $output;
//		echo $mail->comment_author_email;
	}
	update_option("qw_MTA_list", $output);
	echo "<div id=\"message\" class=\"mtaupdate\"><p>重新获取接收人邮件列表成功。</p></div>";
}

function fasong() {
	$from = $_POST['qw_MTA_mail'];
	$bcc = str_replace("\n", ",", $_POST['qw_MTA_list']);

	$headers  = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type: text/html; charset=utf-8" . "\r\n";
	$headers .= "From: " . $from . "\r\n";
//    $headers .= "X-Sender: " . $from . "\r\n";
//    $headers .= "Return-Path: " . $from . "\r\n";
	$headers .= "Bcc: " . $bcc . "\r\n";

	$subject = $_POST['qw_MTA_subject'];
	$message = $_POST['qw_MTA_content'];
	$message .= "\n\n\n
---
This was sent by admin user at <a href=\"" . get_option('siteurl') . "\">" . get_option('blogname') . "</a>
You receive this email because you checked the subscribe checkbox when you leaved a comment on the website.
To unsubscribe, just leave a comment <a href=\"" . get_option('siteurl') . "\">here</a>, with unselection of the subscribe checkbox
";
	if($_POST['qw_MTA_confirm']=="YES") {
		wp_mail("",$subject,$message,$headers);
		echo "<div id=\"message\" class=\"mtaupdate\"><p>邮件发送成功。</p></div>";
	}
	else {echo "<div id=\"message\" class=\"mtaupdatefail\"><p>发送失败，您尚未确认发送</p><p>请填入“YES”确认发送。</p></div>";}
}

/**********************************************************
下面的代码是面向评论者的
1.	显示是否订阅的复选框
2.	发送评论时，分别根据是否勾选复选框执行不同功能
**********************************************************/

//给评论者显示是否接收邮件通知的复选框
add_action('comment_form', 'show_checkbox_to_commenters');
function show_checkbox_to_commenters() {
	echo "<p>";
	if (get_option('qw_MTA_default_sub') == "F") {
		echo "<input type=\"checkbox\" name=\"MTA_subscribe_checkbox\" id=\"MTA_subscribe_checkbox\" value=\"MTA_subscribe_checkbox\" style=\"width: auto;\" />";
	}
	else {
		echo "<input type=\"checkbox\" name=\"MTA_subscribe_checkbox\" id=\"MTA_subscribe_checkbox\" value=\"MTA_subscribe_checkbox\" style=\"width: auto;\" checked=\"checked\"/>";
	}
	echo "<label for=\"MTA_subscribe_checkbox\">订阅本站最新通知（已经订阅过，想取消？只需不选择复选框留个言就可以了）</label>";
	echo "</p>";
}

//依照复选框是否勾选执行动作
add_filter('preprocess_comment', 'ifischecked');
function ifischecked($incoming_comment) {
	global $wpdb;
/*
	$temp = 1;
	if ($_POST['MTA_subscribe_checkbox'] != 'MTA_subscribe_checkbox') { //未被勾选，表示不订阅。
		$temp = 0;
	}
	else { //否则就是订阅。如果数据库“不订阅区”存在本邮箱的记录，则将其设为1
		$temp = 1;
	}
*/
	if ($_POST['MTA_subscribe_checkbox'] != 'MTA_subscribe_checkbox') { //未被勾选，表示不订阅。则邮箱放入mta_subscribe表中
		$querytemp = "INSERT INTO mta_subscribe (`email`) VALUES (\"" . $incoming_comment['comment_author_email'] . "\")";
		$wpdb->query($querytemp);
	}
	else { //否则就是订阅。则数据库“不订阅区”（即表mta_subscribe）不能存在本邮箱的记录，则将其删除
		$querytemp = "DELETE FROM `mta_subscribe` WHERE `email` LIKE \"" . $incoming_comment['comment_author_email'] . "\"";
		$wpdb->query($querytemp);
	}
	return( $incoming_comment );
}

?>
