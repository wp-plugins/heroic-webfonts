<?php

if( !class_exists( 'HT_Webfonts_Admin' ) ){
	class HT_Webfonts_Admin{
		
		//constructor
		function __construct(){
			
			add_action('admin_menu', array( $this, 'ht_webfonts_admin_add_page' ) );

			add_action('admin_init', array( $this, 'ht_webfonts_admin_init' ) );

		}
		
		/**
		* Add the option page to wp menus
		*/
		function ht_webfonts_admin_add_page() {
			$menu_option_title = __('Heroic Webfonts Settings', 'ht-webfonts');
			$menu_option_name = __('Heroic Webfonts', 'ht-webfonts');
			add_options_page($menu_option_title, $menu_option_name, 'manage_options', 'ht-webfonts-admin', array( $this, 'ht_webfonts_options_page' ) );
		}

		/**
		* Render the options page
		*/
		function ht_webfonts_options_page() {
		?>
			<div>
			<h2><?php _e('Heroic Webfonts Settings', 'ht-webfonts') ?></h2>
			<form action="options.php" method="post">
			<?php settings_fields('ht_webfont_options'); ?>
			<?php do_settings_sections('ht-webfonts-admin'); ?>
			</form></div>
			 
			<?php
		}

		/**
		* Register the options
		*/		
		function ht_webfonts_admin_init(){
			register_setting( 'ht_webfont_options', 'ht_webfont_options' );
			add_settings_section('ht_webfont_options_main', __('Installed Fonts', 'ht-webfonts'), array($this, 'ht_webfont_options'), 'ht-webfonts-admin');	
		}

		/**
		* Render the options
		*/
		function ht_webfont_options(){
			//display loaded fonts
			$list_fonts = array();
			$list_font_weights = array();
			ht_webfonts_add_all_fonts($list_fonts, $list_font_weights);

			//return if failure
			if(!is_array($list_fonts))
				return;

			echo "<ul>";
			foreach ($list_fonts as $key => $value) {
				echo "<li>";
				print_r($value);
				echo "</li>";
			}
			echo "</ul>";
		}
	}
}

//load the admin options
if(class_exists('HT_Webfonts_Admin')){
	$ht_webfonts_admin_admin = new HT_Webfonts_Admin();
}