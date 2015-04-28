<?php
/*
*	Plugin Name: Heroic Webfonts
*	Plugin URI:  http://wordpress.org/plugins/heroic-webfonts/
*	Description: Webfonts plugin for themes
*	Author: Hero Themes
*	Version: 1.2
*	Author URI: http://www.herothemes.com/
*	Text Domain: ht-webfonts
*/




if( !class_exists( 'HT_Webfonts' ) ){

	if(!defined('HT_WEBFONT_HIDDEN')){
		define('HT_WEBFONT_HIDDEN', 0);
	}

	if(!defined('HT_WEBFONT_READONLY')){
		define('HT_WEBFONT_READONLY', 1);
	}

	if(!defined('HT_WEBFONT_EDITABLE')){
		define('HT_WEBFONT_EDITABLE', 2);
	}

	class HT_Webfonts {

		private $list_font_count;
		private $default_webfonts;
		private $webfonts_sources;
		private $list_fonts;
		private $list_font_weights;
		private $priority;

		//Constructor
		function __construct(){
			load_plugin_textdomain('ht-webfonts', false, basename( dirname( __FILE__ ) ) . '/languages' );
			//actions

			add_action( 'customize_register' , array( $this , 'register' ), 80 );

			add_action( 'wp_head', array( $this, 'ht_webfonts_head_css') );

			add_action( 'customize_controls_enqueue_scripts', array( $this, 'ht_webfonts_customizer_live_preview' ) );

			add_action( 'customize_controls_print_footer_scripts', array( $this, 'ht_webfonts_customizer_styles' ) );

			add_action( 'wp_ajax_ht_add_custom_font', array( $this, 'ht_ajax_add_new_custom_font' ) );

			add_action( 'wp_ajax_ht_delete_custom_font', array( $this, 'ht_ajax_delete_new_custom_font' ) );

			//declare list_font_counts
			$this->list_font_counts = array();

			//webfonts admin
			include_once('php/ht-webfonts-admin.php');
			//webfont class
			include_once('php/custom-webfont-class.php');


		}

		/**
		* Add all the fonts, combined function
		*/
		function add_all_fonts(&$list_fonts, &$list_font_weights){
			$list_fonts['default'] 	= 'Theme Default';
			$this->add_websafe_fonts($list_fonts, $list_font_weights);
			$this->add_google_fonts($list_fonts, $list_font_weights);
		}


		/**
		* Add websafe fonts
		*/
		function add_websafe_fonts(&$list_fonts, &$list_font_weights){

			$webfonts_array	= file( plugins_url( 'font-sources/websafe-fonts.json' , __FILE__ ) );

			$webfonts_array = implode( '', $webfonts_array );
			$list_fonts_decode	= json_decode( $webfonts_array, true );

			$count = 0;

			foreach ( $list_fonts_decode['items'] as $key => $value ) {
				$item_name = $list_fonts_decode['items'][$key]['name'];
				$item_family = $list_fonts_decode['items'][$key]['family'];
				$list_fonts[$item_family] = $item_name;
				$list_font_weights[$item_family] = $list_fonts_decode['items'][$key]['variants'];
				$count++;
			}

			$this->list_font_counts['websafe'] = $count;

		}

		/**
		* Add google fonts
		*/
		function add_google_fonts(&$list_fonts, &$list_font_weights){

			$webfonts_array	= file( plugins_url( 'font-sources/google-fonts.json' , __FILE__ ) );

			$webfonts_array = implode( '', $webfonts_array );
			$list_fonts_decode	= json_decode( $webfonts_array, true );

			$count = 0;

			foreach ( $list_fonts_decode['items'] as $key => $value ) {
				$item_family                     = $list_fonts_decode['items'][$key]['family'];
				$list_fonts[$item_family]        = $item_family;
				$list_font_weights[$item_family] = $list_fonts_decode['items'][$key]['variants'];
				$count++;
			}


			$this->list_font_counts['gfonts'] = $count;

		}

		/**
		* Register the customizer
		*/
		function register($wp_customize){

			$this->list_fonts  = array();
			$this->list_font_weights	= array();

			//add fonts
			$this->add_all_fonts($this->list_fonts, $this->list_font_weights);

			//get the webfont sources
			$this->webfonts_sources = $this->get_webfont_sources();

			//create an array to add the fonts
			$this->default_webfonts = $this->get_theme_fonts_array();
			
			//webfonts panel
			$wp_customize->add_panel( 'ht_webfont_pane', array(
					'priority'       => 10,
					'title'          => 'Heroic Webfonts',
			));

			//webfonts section
		    $wp_customize->add_section( 'ht_webfont', array(
				'title' => __( 'Heroic Webfonts', 'framework' ),
				'description' => '',
				'priority' => 10,
				'panel'  => 'ht_webfont_pane',
			));

		    $this->priority = 0;

		    //render theme fonts
		    $max = count($this->default_webfonts);
		    for( $key = 0; $key<$max; $key++ ) {
		    	$this->render_webfont_control($wp_customize, $key, 'theme');

		    	//Save Default Selectors Fix.
		    	$font = $this->default_webfonts[$key];

		    	if(false == get_theme_mod('ht_webfont_name_' . $key)) {
					set_theme_mod('ht_webfont_name_' . $key, $font->name);
				}
				if(false == get_theme_mod('ht_webfont_selector_' . $key)) {
					set_theme_mod('ht_webfont_selector_' . $key, $font->selector);
				}
				if(false == get_theme_mod('ht_webfont_family_' . $key)) {
					set_theme_mod('ht_webfont_family_' . $key, $font->family);
				}
				if(false == get_theme_mod('ht_webfont_style_' . $key)) {
					set_theme_mod('ht_webfont_style_' . $key, $font->style);
				}
				if(false == get_theme_mod('ht_webfont_color_' . $key)) {
					set_theme_mod('ht_webfont_color_' . $key, $font->color);
				}
				if(false == get_theme_mod('ht_webfont_size_' . $key)) {
					set_theme_mod('ht_webfont_size_' . $key, $font->size);
				}

				if(false == get_theme_mod('ht_webfont_height_' . $key)) {
					set_theme_mod('ht_webfont_height_' . $key, $font->height);
				}

				if(false == get_theme_mod('ht_webfont_spacing_' . $key)) {
					set_theme_mod('ht_webfont_spacing_' . $key, $font->spacing);
				}
		    }

		    //render custom fonts
		    $custom_font_count = 0;
		    $max = 400;
		    $mods = get_theme_mods();
		    for( $key = 200; $key<$max; $key++ ) {
		    	if(!empty($mods) && array_key_exists('ht_webfont_name_'.$key, $mods)){
		    		$this->render_webfont_control($wp_customize, $key, 'custom');

		    		//increment count
		    		$custom_font_count++;
		    	}
		    }

		    $total_fonts_count = count($this->default_webfonts) + $custom_font_count;

		    $this->list_font_counts['count'] = $total_fonts_count;
		}

		/**
		* Get the default theme fonts array
		*/
		function get_theme_fonts_array(){
			$fonts = array();

			//check current theme supports ht-webfonts
			if(current_theme_supports('ht-webfonts')){
				return apply_filters('ht_webfonts_themefonts', $fonts);
			}

			//--- else return the default list

			$fonts[0] = new HT_Custom_Webfont(
				array(
					'name' => 'Body',
					'name-visibility' => HT_WEBFONT_EDITABLE,
					'selector' => 'body',
					'selector-visibility' => HT_WEBFONT_EDITABLE,
					'source' => 'websafe',
					'source-visibility' => HT_WEBFONT_EDITABLE,
					'family' => 'Arial, Helvetica, sans-serif',
					'family-visibility' => HT_WEBFONT_EDITABLE,
					'style' => 'default',
					'style-visibility' => HT_WEBFONT_EDITABLE,
					'color' => '#b2b2b2',
					'color-visibility' => HT_WEBFONT_EDITABLE,
					'size'	=> '10',
					'size-visibility'	=> HT_WEBFONT_EDITABLE,
					'height' => '10',
					'height-visibility' => HT_WEBFONT_EDITABLE,
					'spacing' => '0',
					'spacing-visibility' => HT_WEBFONT_EDITABLE,

					)
				);

			$fonts[1] = new HT_Custom_Webfont(
				array(
					'name' => 'Title H1',
					'name-visibility' => HT_WEBFONT_EDITABLE,
					'selector' => 'h1',
					'selector-visibility' => HT_WEBFONT_EDITABLE,
					'source' => 'websafe',
					'source-visibility' => HT_WEBFONT_EDITABLE,
					'family' => 'Arial, Helvetica, sans-serif',
					'family-visibility' => HT_WEBFONT_EDITABLE,
					'style' => 'default',
					'style-visibility' => HT_WEBFONT_EDITABLE,
					'color' => '#b2b2b2',
					'color-visibility' => HT_WEBFONT_EDITABLE,
					'size'	=> '20',
					'size-visibility' => HT_WEBFONT_EDITABLE,
					'height' => '10',
					'height-visibility' => HT_WEBFONT_EDITABLE,
					'spacing' => '0',
					'spacing-visibility' => HT_WEBFONT_EDITABLE,
					)
				);

			$fonts[2] = new HT_Custom_Webfont(
				array(
					'name' => 'Title H2',
					'name-visibility' => HT_WEBFONT_EDITABLE,
					'selector' => 'h2',
					'selector-visibility' => HT_WEBFONT_EDITABLE,
					'source' => 'websafe',
					'source-visibility' => HT_WEBFONT_EDITABLE,
					'family' => 'Arial, Helvetica, sans-serif',
					'family-visibility' => HT_WEBFONT_EDITABLE,
					'style' => 'default',
					'style-visibility' => HT_WEBFONT_EDITABLE,
					'color' => '#b2b2b2',
					'color-visibility' => HT_WEBFONT_EDITABLE,
					'size'	=> '15',
					'size-visibility' => HT_WEBFONT_EDITABLE,
					'height' => '10',
					'height-visibility' => HT_WEBFONT_EDITABLE,
					'spacing' => '0',
					'spacing-visibility' => HT_WEBFONT_EDITABLE,
					)
				);

			return $fonts;

		}


		/**
		* Get the webfont source options
		*/
		static function get_webfont_sources(){
			return array( 'websafe' => __('Websafe', 'ht-webfonts'), 'gfonts' => __('Google Fonts', 'ht-webfonts') );
		}

		/**
		* Get the font styles
		*/
		static function get_font_styles(&$family_styles){

			$font_styles = array();
			foreach ($family_styles as $font => $styles) {
				foreach ($styles as $key => $variant) {
					//get font weight
					$font_weight = preg_replace("/[^0-9?! ]/","", $variant);
					//get the style
					$font_style = preg_replace("/[^A-Za-z?! ]/","", $variant);
					//make nicename
					$nicename = $font_weight . ' ' . ucfirst($font_style);
					$font_styles[(string)$variant] = $nicename;
				}

			}

			return array_unique($font_styles);
		}

		/**
		* Render an indivdual webfont section
		*/
		function render_webfont_control($wp_customize, $key, $type, $ajax=false){

				$webfonts_sources = $this->get_webfont_sources();

				//reuse initiatited fonts if in object context
				$list_fonts = empty($this->list_fonts) ? array() : $this->list_fonts;
				$list_font_weights = empty($this->list_font_weights) ? array() : $this->list_font_weights;

				if(empty($list_fonts)&&empty($list_font_weights)){
					$this->add_all_fonts($list_fonts, $list_font_weights);
				}



				$webfont = $ajax==false && array_key_exists($key, $this->default_webfonts) ? $this->default_webfonts[$key] : null;

				//title name
				$default_name = empty($webfont) ? 'New Webfont' : $webfont->name;

				//Section name
				$section_name = 'section_'.$default_name;

				$section_title = get_theme_mod('ht_webfont_name_' . $key);
				//Add section for each
				$wp_customize->add_section($section_name, array(
					'title' => 	$section_title ? $section_title : $default_name,
					'priority' => $this->priority++,
					'panel'  => 'ht_webfont_pane',
				));

				//Title
				$wp_customize->add_setting( 'ht_webfont_title_' . $key, array('default' => $default_name, 'transport'  => 'postMessage', 'dirty' => true ) );

				$wp_customize->add_control(
				new ht_webfonts_sub_title( $wp_customize, 'ht_webfont_title_' . $key, array(
				'label'		=> $default_name,
				'section'   => $section_name,
				'priority' 	=> $this->priority++,
				'type'		=> $type
				) ) );


				//name... continued
				$wp_customize->add_setting( 'ht_webfont_name_' . $key, array('default' => $default_name, 'transport'  => 'postMessage', 'dirty' => true) );

				$wp_customize->add_control( 'ht_webfont_name_' . $key, array(
				'type'     => 'text',
				'label'    => __( 'Style Name', 'ht-webfonts' ),
				'section'  => $section_name,
				'priority' => $this->priority++,
				));


				//selector
				$default_selector = empty($webfont) ? '' : $webfont->selector;
				if($default_selector===false){
					//do nothing
				} else {
					$wp_customize->add_setting( 'ht_webfont_selector_' . $key, array('default' => $default_selector, 'transport'  => 'postMessage', 'dirty' => true) );

					$wp_customize->add_control( 'ht_webfont_selector_' . $key, array(
					'type'     => 'text',
					'label'    => __( 'Style Selector', 'ht-webfonts' ),
					'section'  => $section_name,
					'priority' => $this->priority++,
					));

				}



				//webfont source
				$default_source = empty($webfont) ? '' : $webfont->source;
				if($default_source===false){
					//do nothing
				} else {
					$wp_customize->add_setting( 'ht_webfont_source_' . $key, array('default' => $default_source, 'transport'  => 'postMessage', 'dirty' => true) );

					$wp_customize->add_control( 'ht_webfont_source_' . $key, array(
					'type'     => 'select',
					'label'    => __( 'Font Source', 'ht-webfonts' ),
					'section'  => $section_name,
					'priority' => $this->priority++,
					'choices'  => $webfonts_sources
					));
				}


				//font family
				$default_family = empty($webfont) ? '' : $webfont->family;
				if($default_family===false){
					//do nothing
				} else {
					$wp_customize->add_setting( 'ht_webfont_family_' . $key, array('default' => $default_family, 'transport'  => 'postMessage', 'dirty' => true) );

					$wp_customize->add_control( 'ht_webfont_family_' . $key, array(
					'type'     => 'select',
					'label'    => __( 'Font Family', 'ht-webfonts' ),
					'section'  => $section_name,
					'priority' => $this->priority++,
					'choices'  => $list_fonts
					));
				}

				//style
				$default_style = empty($webfont) ? '' : $webfont->style;
				if($default_style===false){
					//do nothing
				} else {
					$wp_customize->add_setting( 'ht_webfont_style_' . $key, array('default' => $default_style, 'transport'  => 'postMessage', 'dirty' => true) );


					$font_styles = $this->get_font_styles($this->list_font_weights);

					$wp_customize->add_control( 'ht_webfont_style_' . $key, array(
					'type'     => 'select',
					'label'    => __( 'Font Style', 'ht-webfonts' ),
					'section'  => $section_name,
					'priority' => $this->priority++,
					'choices'  => $font_styles
					));
				}


				//color
				$default_color = empty($webfont) ? '' : $webfont->color;
				if($default_color===false){
					//do nothing
				} else {
					$wp_customize->add_setting( 'ht_webfont_color_' . $key, array('default' => $default_color, 'transport' => 'postMessage', 'dirty' => true));

					$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'ht_webfont_color_'. $key , array(
						'label'	=> __( 'Font Color', 'ht-webfonts' ),
						'section' =>  $section_name,
						'priority' => $this->priority++

					)));
				}

				//size
				$default_size = empty($webfont) ? '' : $webfont->size;
				if($default_size===false){
					//do nothing
				} else {
					$wp_customize->add_setting( 'ht_webfont_size_' . $key, array('default' => $default_size, 'transport'  => 'postMessage', 'dirty' => true) );

					$wp_customize->add_control( 'ht_webfont_size_' . $key, array(
					'type'     => 'text',
					'label'    => __( 'Font Size', 'ht-webfonts' ),
					'section'  => $section_name,
					'priority' => $this->priority++,
					));
				}

				//height
				$default_height = empty($webfont) ? '' : $webfont->height;
				if($default_height===false){
					//do nothing
				} else {
					$wp_customize->add_setting( 'ht_webfont_height_' . $key, array('default' => $default_height, 'transport'  => 'postMessage', 'dirty' => true) );

					$wp_customize->add_control( 'ht_webfont_height_' . $key, array(
					'type'     => 'text',
					'label'    => __( 'Line Height', 'ht-webfonts' ),
					'section'  => $section_name,
					'priority' => $this->priority++,
					));
				}

				//spacing
				$default_spacing = empty($webfont) ? '' : $webfont->spacing;
				if($default_spacing===false){
					//do nothing
				} else {
					$wp_customize->add_setting( 'ht_webfont_spacing_' . $key, array('default' => $default_spacing, 'transport'  => 'postMessage', 'dirty' => true) );

					$wp_customize->add_control( 'ht_webfont_spacing_' . $key, array(
					'type'     => 'text',
					'label'    => __( 'Font Spacing', 'ht-webfonts' ),
					'section'  => $section_name,
					'priority' => $this->priority++,
					));
				}
		}


		/**
		* Echo the custom fonts CSS on each page
		*/
		function ht_webfonts_head_css(){
			//get theme mods
			$mods = get_theme_mods();
			if(empty($mods)){
				//if there are no mods, we can exit here
				return;
			}
			//check if mod
			//allows upto 200 theme fonts (0-199)
			//allows upto 200 custom fonts (200-399)
	
			for ($i=0; $i<400; $i++) {
				if(array_key_exists('ht_webfont_name_'.$i, $mods)){
					$source = $mods['ht_webfont_source_'.$i];
					$selector = $mods['ht_webfont_selector_'.$i];
					$font_family = $mods['ht_webfont_family_'.$i];
					$font_weight_style = $mods['ht_webfont_style_'.$i];
					$font_weight = preg_replace("/[^0-9?! ]/","", $font_weight_style);
					if($font_weight_style=='regular'||$font_weight_style=='italic'){
						$font_weight = '400';
					}
					$font_style = preg_replace("/[^A-Za-z?! ]/","", $font_weight_style);

					$font_color = $mods['ht_webfont_color_'.$i];
					$font_size = $mods['ht_webfont_size_'.$i];
					$line_height = $mods['ht_webfont_height_'.$i];
					$letter_spacing = $mods['ht_webfont_spacing_'.$i];
					?>

						<?php if($source=='gfonts'): ?>
							<?php
								//set the gfont_style
								$gfont_style = $font_weight_style;
								$gfont_style = ($gfont_style=='regular') ? '400' : $gfont_style;
								$gfont_style = ($gfont_style=='italic') ? '400italic' : $gfont_style;

							?>
							<link id='ht-webfont-<?php echo $i; ?>-font-family' href="http://fonts.googleapis.com/css?family=<?php echo str_replace(" ", "+", $mods['ht_webfont_family_'.$i]) . ":" . $gfont_style; ?>" rel='stylesheet' type='text/css'>
						<?php endif; ?>
						<style id="<?php echo "ht-webfont-" . $i ."-style"; ?>">

						<?php echo $selector; ?>{

							<?php if($font_family != 'default'): ?>
								<?php if($source=='gfonts'): ?>
									font-family: '<?php echo $font_family;?>', sans-serif !important;
								<?php else: ?>
									font-family: <?php echo $font_family;?>, sans-serif !important;
								<?php endif; //end gfonts if ?>
							<?php endif; //end font_family if  ?>
							font-weight: <?php echo $font_weight;?> !important;
							font-style: <?php echo $font_style;?> !important;
							<?php if($font_color != false){ ?>
							color: <?php echo $font_color;?> !important;
							font-size: <?php echo $font_size;?>px !important;
							line-height: <?php echo $line_height;?>px !important;
							letter-spacing: <?php echo $letter_spacing;?>px !important;
							<?php } ?>
						}

						</style>
					<?php
				}
			} //end for

		}

		/**
		* Add the live preview scripts
		*/
		function ht_webfonts_customizer_live_preview(){
			global $wp_scripts;
			wp_enqueue_script( 'ht-webfonts-scripts',  plugins_url( 'js/ht-webfonts-scripts.js' , __FILE__ ),  array( 'jquery', 'jquery-ui-core', 'jquery-ui-slider' ),	'', true );
			$ht_webfonts_i18n = array();
			$ht_webfonts_i18n['newNamePlaceholder'] = __('New Webfont Name', 'ht-webfonts');
			$ht_webfonts_i18n['newNameAddBtn'] = __('Add Font', 'ht-webfonts');
			$ht_webfonts_i18n['pleaseSaveNew'] = __('Please save and publish before adding a new custom font', 'ht-webfonts');
			$ht_webfonts_i18n['nameRequired'] = __('Please enter a name for the new font', 'ht-webfonts');
			$ht_webfonts_i18n['confirmDelete'] = __('Are you sure you wish to delete this custom font?', 'ht-webfonts');
			$ht_webfonts_i18n['pleaseSaveDelete'] = __('Please save and publish before deleting a custom font', 'ht-webfonts');
			$ht_webfonts_i18n['confirmRestore'] = __('Are you sure you wish to restore this font to theme default?', 'ht-webfonts');

			$localization_data =   array(
				'fontCount'=>$this->list_font_counts,
				'variants'=>$this->list_font_weights,
				'defaults'=>$this->default_webfonts,
				'ajaxnonce' => wp_create_nonce('ht-ajax-nonce'),
				'i18n' => $ht_webfonts_i18n,
				);

			wp_localize_script( 'ht-webfonts-scripts', 'htWebfonts', $localization_data);

			wp_enqueue_style("jquery-ui-css", "http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/themes/ui-lightness/jquery-ui.min.css");
			}


		function ht_webfonts_customizer_styles(){
			wp_enqueue_style( 'ht-webfonts-customizer',  plugins_url( 'css/ht-webfonts-customizer.css' , __FILE__ ));
		}


		/**
		* Add a new custom font
		*/
		function ht_ajax_add_new_custom_font(){
			//prepare response
			$response = array();

			//check security
			check_ajax_referer( 'ht-ajax-nonce', 'security' );

			try{
				$new_index = $this->get_max_custom_font_index();

				$name = sanitize_text_field( $_POST['name'] );

				//add all the theme mods

				//title
				set_theme_mod('ht_webfont_title_'.$new_index, $name);
				//name
				set_theme_mod('ht_webfont_name_'.$new_index, $name);
				//selector
				set_theme_mod('ht_webfont_selector_'.$new_index, '');
				//source
				set_theme_mod('ht_webfont_source_'.$new_index, '');
				//family
				set_theme_mod('ht_webfont_family_'.$new_index, '');
				//style
				set_theme_mod('ht_webfont_style_'.$new_index, '');
				//color
				set_theme_mod('ht_webfont_color_'.$new_index, '');
				//size
				set_theme_mod('ht_webfont_size_'.$new_index, '');
				//height
				set_theme_mod('ht_webfont_height_'.$new_index, '');
				//spacing
				set_theme_mod('ht_webfont_spacing_'.$new_index, '');

				$response = array( 'state' => 'success', 'index' => $new_index );


			} catch (Exception $e){
				//return failure
				$response = array('state' => 'failure', 'message' => $e->getMessage() ) ;
			}

			echo json_encode( $response );

			//required
			exit;
		}


		/**
		* Delete Custom Font
		*/
		function ht_ajax_delete_new_custom_font(){
			//prepare response
			$response = array();

			//check security
			check_ajax_referer( 'ht-ajax-nonce', 'security' );

			try{

				//index to remove
				$index = intval( $_POST['index'] );

				//add all the theme mods

				//title
				remove_theme_mod('ht_webfont_title_'.$index);
				//name
				remove_theme_mod('ht_webfont_name_'.$index);
				//selector
				remove_theme_mod('ht_webfont_selector_'.$index);
				//source
				remove_theme_mod('ht_webfont_source_'.$index);
				//family
				remove_theme_mod('ht_webfont_family_'.$index);
				//style
				remove_theme_mod('ht_webfont_style_'.$index);
				//color
				remove_theme_mod('ht_webfont_color_'.$index);
				//size
				remove_theme_mod('ht_webfont_size_'.$index);
				//height
				remove_theme_mod('ht_webfont_height_'.$index);
				//spacing
				remove_theme_mod('ht_webfont_spacing_'.$index);

				$response = array( 'state' => 'success', 'index' => $index );


			} catch (Exception $e){
				//return failure
				$response = array('state' => 'failure', 'message' => $e->getMessage() ) ;
			}

			echo json_encode( $response );

			//required
			exit;
		}

		/**
		* Get the current max index for custom fonts
		*/
		function get_max_custom_font_index(){
			$lower_limit = 200;

			$mods = get_theme_mods();

			if( empty($mods) ){
				return $lower_limit;
			}
			//check if mod exists
			//allows upto 200 custom font (200-399)
			for ($i=$lower_limit; $i<400; $i++) {
				if(array_key_exists('ht_webfont_name_'.$i, $mods)){
					continue;
				} else {
					//return
					break;
				}
			}
			//return as max index
			return $i;
		}


	} //end class HT_Webfonts
}//end class exists test


/**
* Custom Customizer Controls
*/
function ht_webfonts_add_customizer_custom_controls( $wp_customize ) {


	//subtitle class
	class ht_webfonts_sub_title extends WP_Customize_Control {
		public function render_content() {
		?>

			<div class="ht-webfont-top"></div>
			<!-- <span class="ht-webfonts-custom-sub-title"><?php //echo esc_html( $this->value() ); ?></span> -->
			<?php if($this->type == 'theme'): ?>
				<span class="ht-webfonts-custom-sub-info"><?php _e('Theme Font', 'ht-webfonts') ?></span>
				<a class="button ht-webfonts-custom-sub-btn restore-ht-theme-font"><?php _e('Restore Default', 'ht-webfonts') ?></a>
			<?php else: ?>
				<span class="ht-webfonts-custom-sub-info"><?php _e('Custom Font', 'ht-webfonts') ?></span>
				<a class="button ht-webfonts-custom-sub-btn delete-ht-custom-font"><?php _e('Remove Font', 'ht-webfonts') ?></a>
			<?php endif; ?>
		<?php
		}
	}

}

//add the custom controls
add_action( 'customize_register', 'ht_webfonts_add_customizer_custom_controls', 70  );


//run the plugin
if( class_exists( 'HT_Webfonts' ) ){
	$ht_webfonts_init = new HT_Webfonts();

	function ht_webfonts_add_all_fonts(&$list_fonts, &$list_font_weights){
		global $ht_webfonts_init;
		return $ht_webfonts_init->add_all_fonts($list_fonts, $list_font_weights);
	}

}
