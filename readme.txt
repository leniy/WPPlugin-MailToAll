=== Mail To All ===
Contributors: leniy
Tags: comments,email,subscribe,notification,newsletter
Requires at least: 3.0
Tested up to: 3.5
Stable tag: trunk
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl.html

You can easily send subscription,notification,newsletter,etc by email to your comments users under one post.

== Description ==

You can easily send subscription,notification,newsletter,etc by email to your comments users under one post.
方便给某篇文章的评论用户发送订阅、通知等邮件。

== Installation ==

1. Copy Mail-To-All.zip to your Wordpress plugins directory, usually `wp-content/plugins/` and unzip the file.  It will create a `wp-content/plugins/Mail-To-All/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Follow the Mail-To-All page under options.
4. Then, just follow the Mail-To-All page under options.

== Screenshots ==

1. MTA-screenshot1.png
2. MTA-screenshot2.png

== Changelog ==

= 1.0 =

* 增加插件初始化功能，允许插件用户设置选项。可高度定制
* 插件将一个设置页面增加为3个页面，各司其职，使插件更易用
* 重写退订功能，减少数据库资源消耗

= 0.0.4 =

* 2012.12.21
* 增加面向评论者的复选框，手动确认是否接收订阅邮件
* 收件人邮箱不再由wp_comments中获取，改为新建mta_subscribe表，存储数据
* 取消订阅页面,不再单独存在，评论者在任何页面留言时不选择新增的复选框，即为取消订阅

= 0.0.3 =

* 2012.12.17
* Allow comment users to confirm whether accept email or not.
* They can unsubscribe easily:
	1. click the unsubscribe link in the email they received.
	2. Just leave the email which don't want received again, and comment it.
	3. No email will be sent again.

= 0.0.2 =

* 2012.12.15
* To avoid spam,version 0.0.2 added post select and confirm input,administrator must input the post id and "YES" to send email,this can avoid the plugin being used as a spam sent tool.

= 0.0.1 =

* 2012.12.14
* First version released.

== Frequently Asked Questions ==

Just a plugin,easy to use.

== Upgrade Notice ==

Nothing is worth to notice.