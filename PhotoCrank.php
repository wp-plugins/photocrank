<?php
/*
Plugin Name: PhotoCrank
Plugin URI:
Description: Adds PhotoCrank functionality to all images on the blog.
Version: 2.0.0
Author: PhotoCrank.com
Author URI: http://www.photocrank.com
*/

/*  Copyright YEAR  PLUGIN_AUTHOR_NAME  (email : support@photocrank.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

//version
add_option("photocrank_version", "2.0.0");

//setup the DB
require_once(ABSPATH . '/wp-admin/upgrade-functions.php');
$wpdb->hide_errors();
$wpdb->photocrank_settings = $table_prefix . 'photocrank_settings';
$installed = $wpdb->get_results("SELECT value FROM $wpdb->photocrank_settings");

if (mysql_errno() == 1146) 
{
	$sql = "CREATE TABLE " . $wpdb->photocrank_settings . " (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			setting VARCHAR(128) NOT NULL,
			value VARCHAR(128) NOT NULL,
			UNIQUE KEY id (id), UNIQUE KEY `setting` (`setting`)
			);";
	$wpdb->query($sql);
}

global $wpdb, $pc_memberid;

//add js to top of page
function pc_js_head()
{
	global $wpdb, $pc_memberid;
	if(!$pc_memberid)	
	{
		$sql = "SELECT value FROM $wpdb->photocrank_settings WHERE setting = 'memberid' LIMIT 1";
		$pc_memberid = $wpdb->get_var($sql);
	}
?>
<script type="text/javascript">_pcWidgetId = <?=$pc_memberid?>;</script>
<script language="javascript" id="photocrank_widget_script" type="text/javascript" src="http://www.photocrank.com/crankbar/widget/"></script>
<?php
}


//setup and function for admin
function pc_add_pages() 
{
	add_options_page('PhotoCrank', 'PhotoCrank', 8, 'photocrank', 'pc_photocrank');
}

function pc_photocrank()
{
	global $wpdb, $table_prefix, $pc_memberid;
	
	//are updating the member id?
	$pc_memberid = $_REQUEST['memberid'];
	if($pc_memberid)
	{
		$sql = "INSERT INTO $wpdb->photocrank_settings (setting, value) VALUES ('memberid', '$pc_memberid')";
		$wpdb->hide_errors();
		$result = $wpdb->query($sql);
		if($result)
		{
		?>
			<div id="message" class="updated fade"><p>Member ID updated successfully.</p></div>
		<?php
		}
		else
		{
			//try an update
			$sql = "UPDATE $wpdb->photocrank_settings SET value = '$pc_memberid' WHERE setting = 'memberid' LIMIT 1";
			$result = $wpdb->query($sql);
			if($result)
			{
		?>
			<div id="message" class="updated fade"><p>Member ID updated successfully.</p></div>
		<?php
			}
			else
			{
		?>
			<div id="message" class="error"><p>Error updating Member ID.</p></div>
		<?php
			}
		}
	}
	
	//get the member id from the DB?
	if(!$pc_memberid)	
	{
		$sql = "SELECT value FROM $wpdb->photocrank_settings WHERE setting = 'memberid' LIMIT 1";
		$pc_memberid = $wpdb->get_var($sql);
	}
	
	?>
	<div class="wrap">		
			
		<h2>PhotoCrank Settings</h2>
		
		<form action="options-general.php?page=photocrank" method="post">
			<br/><br/>Member ID: (If you still need a Member ID <a href="http://www.photocrank.com/wpmemberid.aspx" target="_blank">Click Here</a>)<br />
			<input type="text" name="memberid" value="<?=$pc_memberid?>" size="45" /><br /><br />
			<input type="submit" name="pcsubmit" value="Update" />
		</form>
	
    <br/><br/>FAQs <a href="http://www.photocrank.com/widget/quickcrank/faq.aspx" target="_blank">Click Here</a>)<br />
<?php
if($pc_memberid)	
	{
	?>
<br/><br/>
Manage and customize all your PhotoCrank settings <a href="http://www.photocrank.com/widget/quickcrank/customize.aspx?wid=<?=$pc_memberid?>" target="_blank">here</a>

	<?php
	}
	
?>
	</div>
	<?php
}

add_action('admin_menu', 'pc_add_pages');
add_filter('wp_head', 'pc_js_head');
//add_action('wp_footer', 'pc_js_footer');	
?>