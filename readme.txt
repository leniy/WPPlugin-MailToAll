=== Mail To All ===
Contributors: leniy
Donate link: http://blog.leniy.info/
Tags: comments,email,subscribe,notification,newsletter,mass
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

1. screenshot-1.png
2. screenshot-2.png

== Changelog ==

= 1.3 =
* 2013.02.07
* Fix one bug：因为后台“阅读设置”中“博客页面至多显示”的参数，使得本插件不能显示全部含评论的文章
* 感谢用户reizhi的提醒

= 1.2 =
* 2013.01.26
* Use wp_mail to send Emails.In case some plugins had changed the SMTP setting.

= 1.1 =
* 2012.12.31
* 收件人邮箱改为私密信件，防止收件人相互看到别人的邮箱地址，保护隐私
* 允许发送包含HTML元素的邮件
* 修正readme.txt文件错误，恢复screenshot的显示
* 收件人编辑框直接使用换行切换收件人邮箱，换行结尾不再需要用逗号标记

= 1.0 =
* 2012.12.24
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