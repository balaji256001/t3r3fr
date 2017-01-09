<?php

namespace Waboot\functions;
use Waboot\Layout;
use WBF\components\mvc\HTMLView;
use WBF\components\utils\Utilities;

/**
 * Wrapper for \WBF\modules\options\of_get_option
 *
 * @param $name
 * @param bool $default
 *
 * @return bool|mixed
 */
function get_option($name, $default = false){
	if(class_exists("WBF")){
		return \WBF\modules\options\of_get_option($name,$default);
	}else{
		return $default;
	}
}

/**
 * Wrapper for \WBF\modules\behaviors\get_behavior
 *
 * @param $name
 * @param $default
 * @param int $post_id
 * @param string $return
 *
 * @return array|bool|mixed|string
 */
function get_behavior($name, $default = "", $post_id = 0, $return = "value"){
	if(class_exists("WBF")){
		$result = \WBF\modules\behaviors\get_behavior($name, $post_id, $return);
		if($result === false || is_wp_error($result)){
			if(is_wp_error($result) && isset($result->error_data['unable_to_retrieve_behavior']) && isset($result->error_data['unable_to_retrieve_behavior']['default'])){
				$default = $result->error_data['unable_to_retrieve_behavior']['default'];
			}
			return $default;
		}
		return $result;
	}else{
		return $default;
	}
}

/**
 * Checks if at least one widget area with $prefix is active (eg: footer-1, footer-2, footer-3...)
 *
 * @param $prefix
 *
 * @return bool
 */
function count_widgets_in_area($prefix){
	$count = 0;
	$areas = get_widget_areas();
	if(isset($areas[$prefix]) || !isset($areas[$prefix]['type']) || $areas[$prefix]['type'] != "multiple"){
		$limit = isset($areas[$prefix]['subareas']) && intval($areas[$prefix]['subareas']) > 0 ? $areas[$prefix]['subareas'] : 0;
		for($i = 1; $i <= $limit; $i++) {
			if(is_active_sidebar($prefix . "-" . $i)) {
				$count++;
			}
		}
	}
	return $count;
}

/**
 * Prints out a waboot-type widget area
 *
 * @param $prefix
 */
function print_widgets_in_area($prefix){
	$count = count_widgets_in_area($prefix);
	if($count === 0) return;
	$sidebar_class = get_grid_class_for_alignment($count);
	(new HTMLView("templates/widget_areas/parts/multi-widget-area.php"))->clean()->display([
		'widget_area_prefix' => $prefix,
		'widget_count' => $count,
		'sidebar_class' => $sidebar_class
	]);
}

/**
 * Get the correct CSS class to align $count containers
 *
 * @param int $count
 *
 * @return string
 *
 */
function get_grid_class_for_alignment($count = 4){
	$class = '';
	$count = intval($count);
	switch($count) {
		case 1:
			$class = 'col-sm-12';
			break;
		case 2:
			$class = 'col-sm-6';
			break;
		case 3:
			$class = 'col-sm-4';
			break;
		case 4:
			$class = 'col-sm-3';
			break;
		default:
			$class = 'col-sm-1';
	}
	$class = apply_filters("waboot/layout/grid_class_for_alignment",$class,$count);
	return $class;
}

/**
 * Gets theme widget areas
 *
 * @return array
 */
function get_widget_areas(){
	$areas = [
		'header' => [
			'name' =>  __('Header', 'waboot'),
			'description' => __( 'The main widget area displayed in the header.', 'waboot' ),
			'render_zone' => 'header'
		],
		'main_top' => [
			'name' => __('Main Top', 'waboot'),
			'description' => __( 'Widget area displayed above the content and the sidebars.', 'waboot' ),
			'render_zone' => 'main-top'
		],
		'sidebar_primary' => [
			'name' => __('Sidebar primary', 'waboot'),
			'description' => __('Widget area displayed in left aside', 'waboot' ),
			'render_zone' => 'aside-primary'
		],
		'content_top' => [
			'name' => __('Content Top', 'waboot'),
			'description' => __('Widget area displayed above the content', 'waboot' ),
			'render_zone' => 'content',
			'render_priority' => 9
		],
		'content_bottom' => [
			'name' => __('Content Bottom', 'waboot'),
			'description' => __('Widget area displayed below the content', 'waboot' ),
			'render_zone' => 'content',
			'render_priority' => 90
		],
		'sidebar_secondary' => [
			'name' => __('Sidebar secondary', 'waboot'),
			'description' => __('Widget area displayed in right aside', 'waboot' ),
			'render_zone' => 'aside-secondary'
		],
		'main_bottom' => [
			'name' => __('Main Bottom', 'waboot'),
			'description' => __( 'Widget area displayed below the content and the sidebars.', 'waboot' ),
			'render_zone' => 'main-bottom'
		],
		'footer' => [
			'name' => __('Footer', 'waboot'),
			'description' => __( 'The main widget area displayed in the footer.', 'waboot' ),
			//'type' => 'multiple',
			//'subareas' => 4, //this will register footer-1, footer-2, footer-3 and footer-4 as widget areas
			'render_zone' => 'footer'
		]
	];

	$areas = apply_filters("waboot/widget_areas/available",$areas);

	return $areas;
}

/**
 * Returns the index page title
 *
 * @return string|void
 */
function get_index_page_title(){
	return single_post_title('', false);
}

/**
 * Returns the appropriate title for the archive page
 *
 * @return string
 */
function get_archive_page_title(){
	global $post;
	
	if ( is_category() ) {
		return single_cat_title('',false);
	} elseif ( is_tag() ) {
		return single_tag_title('',false);
	} elseif ( is_post_type_archive() ) {
		return post_type_archive_title('', false);
	} elseif ( is_author() ) {
		$author_name = get_the_author_meta("display_name",$post->post_author);
		return sprintf( __( 'Author: %s', 'waboot' ), '<span class="vcard"><a class="url fn n" href="' . get_author_posts_url( $post->post_author ) . '" title="' . esc_attr( $author_name ) . '" rel="me">' . $author_name . '</a></span>' );
	} elseif ( is_day() ) {
		return sprintf( __( 'Day: %s', 'waboot' ), '<span>' . get_the_date('', $post->ID) . '</span>' );
	} elseif ( is_month() ) {
		return sprintf( __( 'Month: %s', 'waboot' ), '<span>' . get_the_date('F Y', $post->ID ) . '</span>' );
	} elseif ( is_year() ) {
		return printf( __( 'Year: %s', 'waboot' ), '<span>' . get_the_date('Y', $post->ID ) . '</span>' );
	} elseif ( is_tax( 'post_format', 'post-format-aside' ) ) {
		return __( 'Asides', 'waboot' );
	} elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) {
		return __( 'Galleries', 'waboot');
	} elseif ( is_tax( 'post_format', 'post-format-image' ) ) {
		return __( 'Images', 'waboot');
	} elseif ( is_tax( 'post_format', 'post-format-video' ) ) {
		return __( 'Videos', 'waboot' );
	} elseif ( is_tax( 'post_format', 'post-format-quote' ) ) {
		return __( 'Quotes', 'waboot' );
	} elseif ( is_tax( 'post_format', 'post-format-link' ) ) {
		return __( 'Links', 'waboot' );
	} elseif ( is_tax( 'post_format', 'post-format-status' ) ) {
		return __( 'Statuses', 'waboot' );
	} elseif ( is_tax( 'post_format', 'post-format-audio' ) ) {
		return __( 'Audios', 'waboot' );
	} elseif ( is_tax( 'post_format', 'post-format-chat' ) ) {
		return __( 'Chats', 'waboot' );
	} else {
		$arch_obj = get_queried_object();
		if(isset($arch_obj->name)) return $arch_obj->name;
		return __('Archives', 'waboot');
	}
}

/**
 * Gets the body layout
 *
 * @return string
 */
function get_body_layout(){
	$current_page_type = Utilities::get_current_page_type();
	if($current_page_type == Utilities::PAGE_TYPE_BLOG_PAGE || $current_page_type == Utilities::PAGE_TYPE_DEFAULT_HOME || is_archive()) {
		$layout = \Waboot\functions\get_option('blog_layout');
	}else{
		$layout = \Waboot\functions\get_behavior('layout');
	}
	$layout = apply_filters("waboot/layout/body_layout",$layout);
	return $layout;
}

/**
 * Checks if body layout is full width
 *
 * @return bool
 */
function body_layout_is_full_width(){
	$body_layout = get_body_layout();
	return $body_layout == Layout::LAYOUT_FULL_WIDTH;
}

/**
 * Checks if the body layout has two sidebars
 * 
 * @return bool
 */
function body_layout_has_two_sidebars(){
	$body_layout = get_body_layout();
	if(in_array($body_layout,array("two-sidebars","two-sidebars-right","two-sidebars-left"))){
		return true;
	}else{
		return false;
	}
}

/**
 * Return the class relative to the $blog_layout (by default the current blog layout)
 *
 * @param bool $blog_layout
 * @return mixed
 */
function get_posts_wrapper_class(){
	$classes = [
		"blog-classic"
	];
	$classes = apply_filters("waboot/layout/posts_wrapper/class",$classes);
	return implode(" ",$classes);
}

/**
 * Get the specified sidebar size
 * @param $name ("primary" or "secondary")
 *
 * @return bool
 */
function get_sidebar_size($name){
	$page_type = Utilities::get_current_page_type();

	if($name == "primary"){
		$size = $page_type == Utilities::PAGE_TYPE_BLOG_PAGE || $page_type == Utilities::PAGE_TYPE_DEFAULT_HOME ?
			of_get_option("blog_primary_sidebar_size") : get_behavior('primary-sidebar-size');

		return $size;
	}elseif($name == "secondary"){
		$size = $page_type == Utilities::PAGE_TYPE_BLOG_PAGE || $page_type == Utilities::PAGE_TYPE_DEFAULT_HOME ?
			of_get_option("blog_secondary_sidebar_size") : get_behavior('secondary-sidebar-size');

		return $size;
	}
	return false;
}

/**
 * Returns the sizes of each column available into current layout
 * @return array of integers
 */
function get_cols_sizes(){
	$result = array("main"=>12);
	if (\Waboot\functions\body_layout_has_two_sidebars()) {
		//Primary size
		$primary_sidebar_width = get_sidebar_size("primary");
		if(!$primary_sidebar_width) $primary_sidebar_width = 0;
		//Secondary size
		$secondary_sidebar_width = get_sidebar_size("secondary");
		if(!$secondary_sidebar_width) $secondary_sidebar_width = 0;
		//Main size
		$mainwrap_size = 12 - Layout::layout_width_to_int($primary_sidebar_width) - Layout::layout_width_to_int($secondary_sidebar_width);

		$result = [
			"main" => $mainwrap_size,
			"primary" => Layout::layout_width_to_int($primary_sidebar_width),
			"secondary" => Layout::layout_width_to_int($secondary_sidebar_width)
		];
	}else{
		if(\Waboot\functions\get_body_layout() != Layout::LAYOUT_FULL_WIDTH){
			$primary_sidebar_width = get_sidebar_size("primary");
			if(!$primary_sidebar_width) $primary_sidebar_width = 0;
			$mainwrap_size = 12 - Layout::layout_width_to_int($primary_sidebar_width);

			$result = [
				"main" => $mainwrap_size,
				"primary" => Layout::layout_width_to_int($primary_sidebar_width)
			];
		}
	}
	$result = apply_filters("waboot/layout/get_cols_sizes",$result);
	return $result;
}

/**
 * Filterable version of get_template_part
 *
 * @param $slug
 * @param null $name
 */
function get_template_part($slug,$name = null){
	$page_type = Utilities::get_current_page_type();
	$tpl_part = apply_filters("waboot/layout/template_parts",[$slug,$name],$page_type);
	\get_template_part($tpl_part[0],$tpl_part[1]);
}

/**
 * Save theme options favicon as WordPress favicon
 *
 * @global \wpdb $wpdb
 * @global \WP_Site_Icon $wp_site_icon
 */
function deploy_favicon($option, $old_value, $value){
	if(!isset($value['favicon']) || $value['favicon'] == "") return;
	global $wpdb,$wp_site_icon;
	//Retrieve the attachment
	$attachment_id = call_user_func(function() use($value){
		global $wpdb;
		$attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $value['favicon'] ));
		if(isset($attachment[0])) return absint($attachment[0]);
		else return false;
	});
	if(!$attachment_id) return;

	//The code below is a slightly adapted version of: wp_ajax_crop_image() for 'site-icon'
	if(!isset($wp_site_icon)){
		$wp_site_icon = new \WP_Site_Icon();
	}

	$attachment_metadata = wp_get_attachment_metadata($attachment_id);
	$context = "site-icon";
	$cropDetails = [
		'x1' => 0,
		'y1' => 0,
		'x2' => $attachment_metadata['width'],
		'y2' => $attachment_metadata['height'],
		'width' => $attachment_metadata['width'],
		'height' => $attachment_metadata['height'],
		'dst_width' => 512,
		'dst_height' => 512
	];
	$data = array_map( 'absint', $cropDetails );

	//Waboot S3-Compatibility HACK
	add_filter("waboot-s3-manager/get_attached_file/must_download",function($must_download,$file,$attachment_id){
		return true; //This will make wp_crop_image to download the file on get_attached_file()
	}, 10, 3);
	//---|

	$cropped = wp_crop_image( $attachment_id, 0, 0, $data['width'], $data['height'], $data['dst_width'], $data['dst_height'] );

	if ( ! $cropped || is_wp_error( $cropped ) ) return;

	/** This filter is documented in wp-admin/custom-header.php */
	$cropped = apply_filters( 'wp_create_file_in_uploads', $cropped, $attachment_id ); // For replication.
	$object  = $wp_site_icon->create_attachment_object( $cropped, $attachment_id );
	unset( $object['ID'] );

	// Update the attachment.
	add_filter( 'intermediate_image_sizes_advanced', array( $wp_site_icon, 'additional_sizes' ) );

	//Waboot S3-Compatibility HACK
	do_action("waboot-s3-manager/clear_attachment",$attachment_id);
	add_action("waboot-s3-manager/before_remove_base_image_from_local_fs", function($pathname,$data,$attachment_id,$module){
		$r = $module->plugin->upload_file($pathname);
	}, 10, 4);
	//---|

	$attachment_id = $wp_site_icon->insert_attachment( $object, $cropped );

	remove_filter( 'intermediate_image_sizes_advanced', array( $wp_site_icon, 'additional_sizes' ) );

	// Additional sizes in wp_prepare_attachment_for_js().
	add_filter( 'image_size_names_choose', array( $wp_site_icon, 'additional_sizes' ) );

	//Update the theme option
	//$value['favicon'] = $object->guid; //no we can't here...

	//Update the option
	$wpdb->update($wpdb->options,['option_value' => $attachment_id],['option_name' => 'site_icon']);
}

/**
 * Print out the custom css file from the theme options value. Called during "update_option".
 *
 * It's a callback set in options "save action" param in options.php
 *
 * @param $option
 * @param $old_value
 * @param $value
 *
 * @return FALSE|string
 */
function deploy_theme_options_css($option, $old_value, $value){
	$input_file_path = apply_filters("waboot/assets/theme_options_style_file/source", get_template_directory()."/assets/src/css/_theme-options.src");
	$output_file_path = apply_filters("waboot/assets/theme_options_style_file/destination", WBF()->resources->get_working_directory()."/theme-options.css");

	if(!is_array($value)) return false;

	$output_string = "";

	$tmpFile = new \SplFileInfo($input_file_path);
	if((!$tmpFile->isFile() || !$tmpFile->isWritable())){
		return false;
	}

	$parsedFile = $output_file_path ? new \SplFileInfo($output_file_path) : null;
	if(!is_dir($parsedFile->getPath())){
		mkdir($parsedFile->getPath());
	}

	$genericOptionfindRegExp = "/{{ ?([a-zA-Z0-9\-_]+) ?}}/";
	$funcRegExp = "/{{ ?apply:([a-zA-Z\-_]+)\(([a-zA-Z0-9\-_, ]+)\) ?}}/";
	$fontOptionfindRegExp = "/\{\{ ?font: ?([a-z]+) ?\}\}/";
	$assignOptionFindRegExp = "/\{\{ ?font-assignment: ?([a-z]+) ?\}\}/";

	$tmpFileObj = $tmpFile->openFile( "r" );
	$parsedFileObj = $parsedFile->openFile( "w" );
	$byte_written = 0;

	while(!$tmpFileObj->eof()){
		$line = $tmpFileObj->fgets();
		//Replace {{ <theme-option-id> }}
		if(preg_match($genericOptionfindRegExp, $line, $matches)){
			if(array_key_exists( $matches[1], $value)){
				if($value[ $matches[1] ] != ""){
					$line = preg_replace( $genericOptionfindRegExp, $value[$matches[1]], $line);
				}else{
					$line = "\t/*{$matches[1]} is empty*/\n";
				}
			}else{
				$line = "\t/*{$matches[1]} not found*/\n";
			}
		}

		//Replace {{ apply:<func>(<theme-option-id>) }}
		if(preg_match($funcRegExp, $line, $matches)){
			require_once get_template_directory()."/inc/styles-functions.php";
			if(count($matches) == 3 && function_exists($matches[1])){
				$func = $matches[1];
				$args = explode(",",$matches[2]);
				foreach ($args as $k => $v){
					if(isset($value[$v])){
						$args[$k] = $value[$v]; //If one of the args is a theme option name, replace it with it's value!
					}
				}
				if(function_exists($func)){
					$r = call_user_func($func,$args);
					$line = preg_replace( $funcRegExp, $r, $line);
				}else{
					$line = "\t/*$func not found*/\n";
				}
			}else{
				$line = "\t/*Invalid function call*/\n";
			}
		}

		//Replace {{ font: <theme-option-id> }}
		if(preg_match($fontOptionfindRegExp, $line, $matches)){
			if(array_key_exists( $matches[1], $value) && $value[ $matches[1] ] != "" && isset($value[ $matches[1] ]['import'])){
				$fonts = $value[ $matches[1] ]['import'];
				$families = '';
				$subsets = '';
				$arr_subsets = [];

				if (is_array($fonts) && count($fonts) > 0) {
					$i = 0;
					foreach ( $fonts as $font ) {
						$families_separator = ($i>0) ? '|' : '';

						if (count($font['weight']) == 1){
							if ($font['weight'][0] == 'regular') {
								$weight = "";
							} else {
								$weight = ":" . implode(",", $font['weight']);
							}
						} else if (count($font['weight'])>1) {
							$weight = ":".implode(",", $font['weight']);
							$weight = str_replace ( "regular" , "400" , $weight);
						} else {
							$weight = "";
						}

						$font['family'] = preg_replace("/[\s]/", "+", $font['family']);
						$families .= $families_separator . $font['family'] . $weight;

						// builds an array with all the subsets of all the selected fonts
						foreach ( $font['subset'] as $subset ) {
							if ($subset != 'latin') {       // latin is excluded
								array_push($arr_subsets, $subset);
							}
						}
						$i++;
					}

					$arr_subsets = array_unique($arr_subsets);
					if (count($arr_subsets) > 0) {          // if we have some subset different from latin
						// another loop for array of subsets
						$j = 0;
						foreach ( $arr_subsets as $subset ) {
							$subsets_start = ($j==0) ? '&subset=' : '';
							$subsets_separator = ($j>0) ? ',' : '';
							$subsets .= $subsets_start . $subsets_separator . $subset; // e.g. &subset=greek,latin-ext';

							$j++;
						}
					}

					$css_rule = "@import 'https://fonts.googleapis.com/css?family=".$families."".$subsets."';";
					$line = preg_replace( $fontOptionfindRegExp, $css_rule, $line);
				} else {
					$line = "\t/*{$matches[1]} no fonts assigned*/\n";
				}
			}else{
				$line = "\t/*{$matches[1]} not found or invalid*/\n";
			}
		}

		//Replace {{ font-assignment: <theme-option-id> }}
		if(preg_match($assignOptionFindRegExp, $line, $matches)){
			if(array_key_exists( $matches[1], $value) &&  $value[ $matches[1] ] != "" && isset($value[ $matches[1] ]['assign'])){
				$assignments = $value[ $matches[1] ]['assign'];
				$css_rule = "";
				foreach($assignments as $selector => $props){

					if($props['weight'] == "regular") {
						$props['weight'] = "400";
					} elseif (preg_match('/italic/', $props['weight'])) {
						$props['weight'] = preg_replace('/italic/', '', $props['weight']);
						if ($props['weight'] == '') $props['weight'] = '400';
						$props['style'] = 'italic';
					}
					$selector = preg_replace('/-/',',', $selector);

					$css_rule .= "{$selector}{\n";
					$css_rule .= "\tfont-family: '{$props['family']}';\n";
					$css_rule .= "\tfont-weight: {$props['weight']};\n";
					if ($props['style'] != '') $css_rule .= "\tfont-style: {$props['style']};\n";
					$css_rule .= "}\n";
				}
				$line = preg_replace( $assignOptionFindRegExp, $css_rule, $line);
			}else{
				$line = "\t/*{$matches[1]} not found*/\n";
			}
		}

		$byte_written += $parsedFileObj->fwrite($line);
	}

	return $output_string;
}