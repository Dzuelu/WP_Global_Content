<?php

/*

 Creates a generic plugin settings class that holds all your variables

*/

if( !class_exists( 'PluginSettings' ) ) {
	
	class PluginSettings {
		
		private static $option_name = 'GlobalContentPlugin';
		private static $text_domain = 'global_text_domain';
		
		// Sets up variables for first time use
		private static function install_settings() {
			//update_option( 'OptionName', 'OptionValue' );
			
		}
		
		
		//Should be used to delete any settings on uninstall
		public static function uninstall_settings() {
			
		}
		
		//Gets the option under the GCG array
		public static function get_option( $name, $default = false ) {
			$option = get_option( self::$option_name );
			
			if ( false === $option ) {
				return $default;
			}
			
			if ( isset( $option[$name] ) ) {
				return $option[$name];
			} else {
				return $default;
			}
			
		}
		
		//Sets the option under the GCG array
		public static function update_option( $name, $value ) {
			$option = get_option( self::$option_name );
			$option = ( false === $option ) ? array() : (array) $option;
			$option = array_merge( $option, array( $name => $value ) );
			update_option( $option_name, $option );
		}
		
		public static function check_install() {
			$option = get_option( self::$option_name );
			
			if ( false === $option ) {
				PluginSettings::install_settings();
			}
		}
		
		public static function text_domain() {
			return self::$text_domain;
		}
		
	}
	
	PluginSettings::check_install();
	
}

