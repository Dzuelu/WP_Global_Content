<?php

add_action( 'admin_menu', 'start_gcg_admin_menu' );
// Plugs into our custom post-type menu and adds more functionality
function start_gcg_admin_menu() {
	
	//Add subpages to our custom post type menu
	add_submenu_page( 'edit.php?post_type=global_block', 'Cached Content', 'Cache', PluginSettings::get_option( 'menu_edit_capabilities', 'manage_options' ), 'edit.php?post_type=global_cached_block');
	add_submenu_page( 'edit.php?post_type=global_block', 'Settings', 'Settings', PluginSettings::get_option( 'menu_edit_capabilities', 'manage_options' ), 'global-content-settings', 'gc_settings_menu_page' );
	
}

//This will list the plugin settings
function gc_settings_menu_page() {
	echo '<div class="wrap"><div id="icon-tools" class="icon32"></div>';
        echo '<h2>Settings Menu Page</h2>';
    echo '</div>';
    
    //
	
}


