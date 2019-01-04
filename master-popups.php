<?php
/**
 * Plugin Name: HYP Optin
 * Plugin URI: https://github.com/hypericumimpex/
 * Description: Multi-Purpose Popup Plugin for WordPress with Powerful and Easy Email Marketing Integration
 * Version: 2.4.7
 * Author: CodexHelp
 * Author URI: https://github.com/hypericumimpex/
 * Text Domain: masterpopups
 * Domain Path: /languages/
 */

if ( ! class_exists( 'MasterPopups', false ) ) {
	include dirname( __FILE__ ) . '/includes/class-master-popups.php';
}

function MasterPopups(){
	return MasterPopups::get_instance(array(
		'version'            => '2.4.7',
		'name'               => 'Master Popups',
		'short_name'         => 'MasterPopups',
		'slug'               => 'master-popups',
		'text_domain'        => 'master-popups',
		'post_type'          => 'master-popups',
		'post_type_audience' => 'mpp_audience',
		'prefix'             => 'mpp_',
		'xbox_ids'           => array(
			'settings'           => 'settings-master-popups',
			'popup-editor'       => 'popup-editor-master-popups',
			'audience-editor'    => 'audience-editor-master-popups',
		),
		'item_id'            => '20142807',//MasterPopups
	));
}
MasterPopups();



/*
|---------------------------------------------------------------------------------------------------
| Register hooks that are fired when the plugin is activated or deactivated.
|---------------------------------------------------------------------------------------------------
*/
register_activation_hook( __FILE__, array( 'MasterPopups', 'on_activate'   ) );
register_deactivation_hook( __FILE__, array( 'MasterPopups', 'on_deactivate' ) );
