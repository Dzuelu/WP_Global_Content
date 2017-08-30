<?php
/*
Plugin Name: Global Generic Content
Plugin URI: 
Description: 
Author: Kenneth Studer
Author URI: 
Text Domain: global-content-domain
Domain Path: /languages
Version: 1.0.1
*/

define( 'GC_PLUGIN', __FILE__ );

define( 'GC_PLUGIN_BASENAME', plugin_basename( GC_PLUGIN ) );

define( 'GC_PLUGIN_NAME', trim( dirname( GC_PLUGIN_BASENAME ), '/' ) );

define( 'GC_PLUGIN_DIR', untrailingslashit( dirname( GC_PLUGIN ) ) );

// Easy access to settings and variables
require_once GC_PLUGIN_DIR . '/includes/settings.php';

// Our main plugin point
require_once GC_PLUGIN_DIR . '/includes/main.php';

