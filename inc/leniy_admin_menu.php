<?php
/*  Copyright 2012-2013 Leniy (m@leniy.org)

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

if (!function_exists('register_leniy_plugins_admin_menu_page')) {
	function register_leniy_plugins_admin_menu_page(){
		add_menu_page( 'Leniy Plugins', 'Leniy Plugins', 'manage_options', 'leniy-plugins', 'leniy_plugins_admin_menu_page', plugins_url( 'leniy-ico.png' , __FILE__ ), 100 );
		add_submenu_page( 'leniy-plugins', 'About', 'About', 'manage_options', 'leniy-plugins', 'leniy_plugins_admin_menu_page'	);
	}
	add_action( 'admin_menu', 'register_leniy_plugins_admin_menu_page' );
}

if (!function_exists('leniy_plugins_admin_menu_page')) {
	function leniy_plugins_admin_menu_page(){
	?>
	<div class="wrap">
		<div id="icon-leniy" class="icon32" style="background-image: url('<?php echo plugins_url( 'leniy.png' , __FILE__ ); ?>');background-repeat: no-repeat;"><br></div>
		<h2>Leniy Plugins</h2>
			<h3>About Me</h3>
				<ul>
					<li><a href="http://blog.leniy.org">My blog</a></li>
					<li><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=HAENMLDR2UMFJ&lc=US&item_name=Leniy%20Plugins%20Donation&item_number=plugin%2ddonate&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donate_SM%2egif%3aNonHosted">Donate link</a></li>
				</ul>
			<h3>Leave a Comment</h3>
			还没加上
			<h3>About This Plugin</h3>
				<iframe frameborder="0" src="http://blog.leniy.org/project/wordpress" scrolling="auto" noresize="" width="100%" height="400px"></iframe>
	</div>
	<?php
	}
}
?>
