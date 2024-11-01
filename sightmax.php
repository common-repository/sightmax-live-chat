<?php

/*
 Plugin Name: SightMax Live Chat
 Plugin URI: http://wordpress.org/extend/plugins/sightmax-live-chat/
 Description: Allow users to use SightMax live chat.
 Version: 1.0.1
 Author: SightMax
 Author URI: http://sightmax.com
 */

/*  Copyright 2010 SightMax.com - E-Mail: sales@sightmax.com

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
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define(sightmax_SERVICE_URL_DEFAULT, 'https://lc4.sightmaxondemand.com/');

// variables for the field and option names
define(sightmax_ENABLE, 'sightmax_enable');
define(sightmax_ACCOUNT_ID, 'sightmax_account_id');
define(sightmax_SERVICE_URL, 'sightmax_service_url');
define(sightmax_QUEUE_ID,  'sightmax_queue_id');
define(sightmax_SITE_ID, 'sightmax_site_id');
define(sightmax_USE_MONITOR, 'sightmax_useMonitor');
define(sightmax_USE_JQUERY, 'sightmax_useJQuery');

// Hook for adding admin menus
//add_action('init', 'sightmax_init');
add_action('admin_init', 'sightmax_admin_init');
add_action('admin_menu', 'sightmax_add_pages');

function sightmax_init(){
	
}

function sightmax_admin_init()
    {
        /* Register our stylesheet. */
        wp_register_style('SightmaxPluginStylesheet', WP_PLUGIN_URL . '/sightmax/css/sightmax-plugin.css');
        $opt["width"] = 300;
		wp_register_widget_control(('sightmax_widget'), 'SightMAx Live Chat', 'sightmax_widget_inline_admin', $opt);
	
    }

// action function for above hook
function sightmax_add_pages() {
	$page = add_options_page('SightMax', 'SightMax', 'administrator', 'sightmax_wp', 'sightmax_options_page');
	
	/* Register our plugin page */
//        $page = add_submenu_page( 'sightmax.php', 
//                                  __('SightMax Live Chat', 'SightMax'), 
//                                  __('SightMax Live Chat', 'SightMax'), 'administrator',  __FILE__, 
//                                  'sightmax_options_page');
   
        /* Using registered $page handle to hook stylesheet loading */
        add_action('admin_print_styles-' . $page, 'sightmax_admin_styles');
	
}

function sightmax_admin_styles()
    {
        /*
         * It will be called only on your plugin admin page, enqueue our stylesheet here
         */
        wp_enqueue_style('SightmaxPluginStylesheet');
    }

function sightmax_options_page(){
	if (!current_user_can('administrator'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}

	$options = $newoptions = get_option('sightmax_widget');
	if( $options == false ||  $options[sightmax_SERVICE_URL] == '') {
		$options[sightmax_SERVICE_URL] = sightmax_SERVICE_URL_DEFAULT;
	}
	if ( $_POST["sightmax_widget-submit"] ) {
		$newoptions[sightmax_ENABLE] = strip_tags(stripslashes($_POST[sightmax_ENABLE]));
		$newoptions[sightmax_SERVICE_URL] = esc_url($_POST[sightmax_SERVICE_URL]);
		$newoptions[sightmax_ACCOUNT_ID ] = strip_tags(stripslashes($_POST[sightmax_ACCOUNT_ID]));
		$newoptions[sightmax_SITE_ID ] = strip_tags(stripslashes($_POST[sightmax_SITE_ID]));
		$newoptions[sightmax_QUEUE_ID ] = strip_tags(stripslashes($_POST[sightmax_QUEUE_ID]));
		$newoptions[sightmax_USE_MONITOR ] = strip_tags(stripslashes($_POST[sightmax_USE_MONITOR]));
		//$newoptions[sightmax_USE_JQUERY ] = strip_tags(stripslashes($_POST[sightmax_USE_JQUERY]));

		if ( $options != $newoptions ) {
			$options = $newoptions;
			update_option('sightmax_widget', $options);
		}
	}

	?>
<div class="wrap">
<h2>SightMax Live Chat Widget</h2>
<p>Here is where the form would go if I actually had options.</p>
<form method="post" action=""><input type="hidden"
	id="sightmax_widget-submit" name="sightmax_widget-submit" value="1" />
<table class="system-tooltip-wrap">
	<tr>
		<!--		onmouseover='jQuery("#row_sightmax_sightmax_general_enabled_comment").show();'-->
		<!--		onmouseout='jQuery("#row_sightmax_sightmax_general_enabled_comment").hide();' >-->
		<td><label for="<?php echo esc_attr(sightmax_ENABLE);?>">Enable
		SightMax</label></td>
		<td><select id="<?php echo esc_attr(sightmax_ENABLE);?>"
			name=<?php echo sightmax_ENABLE;?> " class=" select">
			<option value="1" <?php if ($options[sightmax_ENABLE]) { ?>
				selected="selected" <?php }?>>Yes</option>
			<option value="0" <?php if (!$options[sightmax_ENABLE]) { ?>
				selected="selected" <?php }?>>No</option>
		</select></td>
	</tr>
	<tr>
		<td><label for="<?php echo esc_attr(sightmax_ACCOUNT_ID);?>">SightMax
		Account ID</label></td>
		<td><input style="width: 350px;"
			id="<?php echo esc_attr(sightmax_ACCOUNT_ID);?>"
			name="<?php echo esc_attr(sightmax_ACCOUNT_ID); ?>" type="text"
			value="<?php echo esc_html($options[sightmax_ACCOUNT_ID]); ?>" /></td>
	</tr>
	<tr>
		<td><label for="<?php echo esc_attr(sightmax_QUEUE_ID);?>">SightMax
		Queue ID</label></td>
		<td><input style="width: 350px;"
			id="<?php echo esc_attr(sightmax_QUEUE_ID);?>"
			name="<?php echo esc_attr(sightmax_QUEUE_ID); ?>" type="text"
			value="<?php echo esc_html($options[sightmax_QUEUE_ID]); ?>" /></td>
	</tr>
	<tr>
		<td><label for="<?php echo esc_attr(sightmax_SITE_ID);?>">SightMax
		Site ID</label></td>
		<td><input style="width: 350px;"
			id="<?php echo esc_attr(sightmax_SITE_ID);?>"
			name="<?php echo esc_attr(sightmax_SITE_ID); ?>" type="text"
			value="<?php echo esc_html($options[sightmax_SITE_ID]); ?>" /></td>
	</tr>
	<tr>
		<td><label for="sightmax_service_url">Webservice URL</label></td>
		<td><input style="width: 350px;"
			id="<?php echo esc_attr(sightmax_SERVICE_URL);?>"
			name="<?php echo esc_attr(sightmax_SERVICE_URL);?>" type="text"
			value="<?php echo esc_html($options[sightmax_SERVICE_URL]); ?>" /></td>
	</tr>
	<tr>
		<td><label for="<?php echo sightmax_USE_MONITOR;?>">Include SightMax
		monitor Tag</label></td>
		<td><select id="<?php echo sightmax_USE_MONITOR;?>"
			name=<?php echo sightmax_USE_MONITOR;?> " class=" select">
			<option value="1" <?php if ($options[sightmax_USE_MONITOR]) { ?>
				selected="selected" <?php }?>>Yes</option>
			<option value="0" <?php if (!$options[sightmax_USE_MONITOR]) { ?>
				selected="selected" <?php }?>>No</option>
		</select></td>
	</tr>
	<tr>
		<td colspan="2">
		<p class="submit"><input type="submit" name="Submit"
			class="button-primary" value="Save Changes"></p>
		</td>
	</tr>
</table>

</form>


<div id="row_sightmax_sightmax_general_enabled_comment"
	class="system-tooltip-box"><a
	href="http://www.sightmax.com/store/installable.aspx" target="_blank">&nbsp;</a>
<p><strong>SightMaxOnDemand (SaaS)</strong>. Up and Running in
Minutes... Month to Month SightMaxOnDemand Hosted Software as a Service
(SaaS). No server required, insert two simple tags into your web pages
and begin using SightMax Live Chat today.</p>
<p><strong>SightMax.</strong> You buy it, you own it. Yep, you heard it
right. No recurring fees, no license renewals - it's all yours.</p>
</div>
</div>
	<?php
}

function sightmax_widget_inline_admin(){
$options = $newoptions = get_option('sightmax_widget');
	if( $options == false ||  $options[sightmax_SERVICE_URL] == '') {
		$options[sightmax_SERVICE_URL] = sightmax_SERVICE_URL_DEFAULT;
	}
	if ( $_POST["sightmax_widget-submit"] ) {
		$newoptions[sightmax_ENABLE] = strip_tags(stripslashes($_POST[sightmax_ENABLE]));
		$newoptions[sightmax_SERVICE_URL] = esc_url($_POST[sightmax_SERVICE_URL]);
		$newoptions[sightmax_ACCOUNT_ID ] = strip_tags(stripslashes($_POST[sightmax_ACCOUNT_ID]));
		$newoptions[sightmax_SITE_ID ] = strip_tags(stripslashes($_POST[sightmax_SITE_ID]));
		$newoptions[sightmax_QUEUE_ID ] = strip_tags(stripslashes($_POST[sightmax_QUEUE_ID]));
		$newoptions[sightmax_USE_MONITOR ] = strip_tags(stripslashes($_POST[sightmax_USE_MONITOR]));
		$newoptions[sightmax_USE_JQUERY ] = strip_tags(stripslashes($_POST[sightmax_USE_JQUERY]));

		if ( $options != $newoptions ) {
			$options = $newoptions;
			update_option('sightmax_widget', $options);
		}
	}
	?><form method="post" action="">	
	<ul >
	<li><label for="<?php echo esc_attr(sightmax_ENABLE);?>">Enable SightMax:</label>
		<br/>
	<select id="<?php echo esc_attr(sightmax_ENABLE);?>" 
			name=<?php echo sightmax_ENABLE;?> " class=" select">
			<option value="1" <?php if ($options[sightmax_ENABLE]) { ?>
				selected="selected" <?php }?>>Yes</option>
			<option value="0" <?php if (!$options[sightmax_ENABLE]) { ?>
				selected="selected" <?php }?>>No</option>
		</select></li>
	<li><label for="<?php echo esc_attr(sightmax_ACCOUNT_ID);?>">SightMax Account ID:</label><br/>
		<input style="width: 70px;"
			id="<?php echo esc_attr(sightmax_ACCOUNT_ID);?>"
			name="<?php echo esc_attr(sightmax_ACCOUNT_ID); ?>" type="text"
			value="<?php echo esc_html($options[sightmax_ACCOUNT_ID]); ?>" /></li>
	<li><label for="<?php echo esc_attr(sightmax_QUEUE_ID);?>">SightMax Queue ID:</label><br/>
		<input style="width: 70px;"
			id="<?php echo esc_attr(sightmax_QUEUE_ID);?>"
			name="<?php echo esc_attr(sightmax_QUEUE_ID); ?>" type="text"
			value="<?php echo esc_html($options[sightmax_QUEUE_ID]); ?>" /></li>
	<li><label for="<?php echo esc_attr(sightmax_SITE_ID);?>">SightMax Site ID:</label><br/>
	<input style="width: 70px;"
			id="<?php echo esc_attr(sightmax_SITE_ID);?>"
			name="<?php echo esc_attr(sightmax_SITE_ID); ?>" type="text"
			value="<?php echo esc_html($options[sightmax_SITE_ID]); ?>" /></li>
	<li><label for="sightmax_service_url">Webservice URL:</label>
		<br/><input style="width: 295px;"
			id="<?php echo esc_attr(sightmax_SERVICE_URL);?>"
			name="<?php echo esc_attr(sightmax_SERVICE_URL);?>" type="text"
			value="<?php echo esc_html($options[sightmax_SERVICE_URL]); ?>" /></li>
	<li><label for="<?php echo sightmax_USE_MONITOR;?>">Include SightMax monitor Tag:</label><br/>
	<select id="<?php echo sightmax_USE_MONITOR;?>"
			name=<?php echo sightmax_USE_MONITOR;?> " class=" select">
			<option value="1" <?php if ($options[sightmax_USE_MONITOR]) { ?>
				selected="selected" <?php }?>>Yes</option>
			<option value="0" <?php if (!$options[sightmax_USE_MONITOR]) { ?>
				selected="selected" <?php }?>>No</option>
		</select></li>
	</ul>
	<p>
	<input type="hidden" id="sightmax_widget-submit" name="sightmax_widget-submit" value="1" />	
	</form>
	<?php
}


function show_sightmax($args) {
	extract($args);
	$options  = get_option('sightmax_widget');
	if (!$options[sightmax_ENABLE]) return;
	
	$rez='<div id="sightmax-live-chat">'."\n";
	$rez.='<a'."\n";
	$rez.='	onmouseover="top.status=\'Chat with a company representative\';return true;"'."\n";   
	$rez.='	onmousedown="top.status=\'Chat with a company representative\';return true;"'."\n";
	$rez.='	onmouseout="top.status=\'\'; return true;"'."\n";
	$rez.='	href="javascript: var e = window.open(\''.$options[sightmax_SERVICE_URL].'SightMaxAgentInterface/PreChatSurvey.aspx?accountID='.$options[sightmax_ACCOUNT_ID].'&siteID='.$options[sightmax_SITE_ID].'&queueID='.$options[sightmax_QUEUE_ID].'\',\'chatWindow\',\'width=490,height=404,resizable=no,scrollbars=no,menubar=no,status=no,location=no\');">'."\n";
	$rez.='	<img border="0" alt="Live chat by SightMax" src="'.$options[sightmax_SERVICE_URL].'SightMaxAgentInterface/chat.smgif?accountID='.$options[sightmax_ACCOUNT_ID].'&siteID='.$options[sightmax_SITE_ID].'&queueID='.$options[sightmax_QUEUE_ID].'">'."\n";
	$rez.='	</a>'."\n";
	$rez.='</div>'."\n";
	
	echo   $rez;
}

function init_sightmax_widget() {
	$options  = get_option('sightmax_widget');
	/* Register our scripts. */
	if ($options[sightmax_USE_MONITOR]){
	$scriptUrl = $options[sightmax_SERVICE_URL]
			.'SightMaxAgentInterface/Monitor.smjs?accountID='.$options[sightmax_ACCOUNT_ID]
			.'&amp;siteID='.$options[sightmax_SITE_ID]
			.'&amp;queueID='.$options[sightmax_QUEUE_ID];
    wp_register_script('sightmax_monitor', $scriptUrl,NULL,null,true);
	wp_enqueue_script('sightmax_monitor', $scriptUrl,NULL,null,true);
	}
	wp_register_sidebar_widget("sightmax_widget", "SightMax Live Chat", "show_sightmax",null);
	
}

add_action("init", "init_sightmax_widget");
?>
