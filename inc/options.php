<?php

/**
 * A unique identifier is defined to store the options in the database and reference them from the theme.
 * By default it uses the theme name, in lowercase and without spaces, but this can be changed if needed.
 * If the identifier changes, it'll appear as if the options have been reset.
 *
 */
function optionsframework_option_name() {

    // This gets the theme name from the stylesheet
    $themename = get_option( 'stylesheet' );
    $themename = preg_replace("/\W/", "_", strtolower($themename) );

    $optionsframework_settings = get_option('optionsframework');
    $optionsframework_settings['id'] = $themename;
    update_option('optionsframework', $optionsframework_settings);
}

/**
 * Defines an array of options that will be used to generate the settings page and be saved in the database.
 * When creating the "id" fields, make sure to use all lowercase and no spaces.
 *
 */
function optionsframework_options() {

	static $options = array();

	if(!empty($options)){
		return $options;
	}

    // Pull all the categories into an array
    /*$options_categories = array();
    $options_categories_obj = get_categories();
    foreach ($options_categories_obj as $category) {
        $options_categories[$category->cat_ID] = $category->cat_name;
    }*/

    // Pull all the tags into an array
    /*$options_tags = array();
    $options_tags_obj = get_tags( array('hide_empty' => false) );
    $options_tags[''] = __( 'Select a tag:', 'waboot' );
    foreach ($options_tags_obj as $tag) {
        $options_tags[$tag->term_id] = $tag->name;
    }*/

    // Pull all the pages into an array
    /*$options_pages = array();
    $options_pages_obj = get_pages('sort_column=post_parent,menu_order');
    $options_pages[''] = __( 'Select a page:', 'waboot' );
    foreach ($options_pages_obj as $page) {
        $options_pages[$page->ID] = $page->post_title;
    }*/

    // If using image radio buttons, define a directory path
    $imagepath = get_template_directory_uri() . '/wbf/admin/images/';

    // WABOOT SETTINGS TABS


    /*
    * LAYOUT TAB
    */

    $options[] = array(
        'name' => __( 'Layout', 'waboot' ),
        'type' => 'heading'
    );

    $options[] = array(
        'name' => __('Page', 'waboot'),
        'desc' => __('Select page width. Fluid or Boxed?', 'waboot'),
        'id' => 'waboot_page_width',
        'std' => 'container',
        'type' => 'images',
        'options' => array(
            'container-fluid' => array (
                'label' => 'Fluid',
                'value' => $imagepath . 'layout/page-fluid.png'
            ),
            'container' => array (
                'label' => 'Boxed',
                'value' => $imagepath . 'layout/page-boxed.png'
            )
        )
    );

    $options[] = array(
        'name' => __('Background Page', 'waboot'),
        'desc' => __('Change the page background color.', 'waboot'),
        'id' => 'waboot_page_bgcolor',
        'type' => 'color',
	    'std' => '#ffffff',
        'recompile_styles' => true
    );


    $options[] = array(
        'name' => __( 'Sections Inner', 'waboot' ),
        'desc' => __( 'Define Inner Sections width and background color', 'waboot' ),
        'type' => 'info'
    );

    $options[] = array(
        'name' => __('Top Nav', 'waboot'),
        'desc' => __('Select Top Nav width. Fluid or Boxed?', 'waboot'),
        'id' => 'waboot_topnav_width',
        'std' => 'container',
        'type' => 'images',
        'options' => array(
            'container-fluid' => array (
                'label' => 'Fluid',
                'value' => $imagepath . 'layout/top-nav-fluid.png'
            ),
            'container' => array (
                'label' => 'Boxed',
                'value' => $imagepath . 'layout/top-nav-boxed.png'
            )
        )
    );

    $options[] = array(
        'name' => __( 'Header', 'waboot' ),
        'desc' => __( 'Select header width. Fluid or Boxed?', 'waboot' ),
        'id' => 'waboot_header_width',
        'std' => 'container-fluid',
        'type' => 'images',
        'options' => array(
            'container-fluid' => array (
                'label' => 'Fluid',
                'value' => $imagepath . 'layout/header-fluid.png'
            ),
            'container' => array (
                'label' => 'Boxed',
                'value' => $imagepath . 'layout/header-boxed.png'
            )
        )
    );

    $options[] = array(
        'name' => __('Background Top Nav', 'waboot'),
        'desc' => __('Change the Top Nav background color.', 'waboot'),
        'id' => 'waboot_topnav_bgcolor',
        'type' => 'color',
        'recompile_styles' => true
    );

    $options[] = array(
        'name' => __('Background Header', 'waboot'),
        'desc' => __('Change the header background color.', 'waboot'),
        'id' => 'waboot_header_bgcolor',
        'type' => 'color',
	    'std' => '#ffffff',
        'recompile_styles' => true
    );

    $options[] = array(
        'name' => __( 'Navbar', 'waboot' ),
        'desc' => __( 'Select navbar width. Fluid or Boxed?', 'waboot' ),
        'id' => 'waboot_navbar_width',
        'std' => 'container',
        'type' => 'images',
        'options' => array(
            'container-fluid' => array (
                'label' => 'Fluid',
                'value' => $imagepath . 'layout/header-fluid.png'
            ),
            'container' => array (
                'label' => 'Boxed',
                'value' => $imagepath . 'layout/header-boxed.png'
            )
        )
    );

    $options[] = array(
        'name' => __( 'Banner', 'waboot' ),
        'desc' => __( 'Select banner width. Fluid or Boxed?', 'waboot' ),
        'id' => 'waboot_banner_width',
        'std' => 'container-fluid',
        'type' => 'images',
        'options' => array(
            'container-fluid' => array (
                'label' => 'Fluid',
                'value' => $imagepath . 'layout/banner-fluid.png'
            ),
            'container' => array (
                'label' => 'Boxed',
                'value' => $imagepath . 'layout/banner-boxed.png'
            )
        )
    );

    $options[] = array(
        'name' => __('Background Navbar', 'waboot'),
        'desc' => __('Change the navbar background color.', 'waboot'),
        'id' => 'waboot_navbar_bgcolor',
        'type' => 'color',
	    'std' => '#e2e2e2',
        'recompile_styles' => true
    );

    $options[] = array(
        'name' => __('Background Banner', 'waboot'),
        'desc' => __('Change the banner background color.', 'waboot'),
        'id' => 'waboot_banner_bgcolor',
        'type' => 'color',
        'recompile_styles' => true
    );

    $options[] = array(
        'name' => __( 'Content', 'waboot' ),
        'desc' => __( 'Select content width. Fluid or Boxed?', 'waboot' ),
        'id' => 'waboot_content_width',
        'std' => 'container',
        'type' => 'images',
        'options' => array(
            'container-fluid' => array (
                'label' => 'Fluid',
                'value' => $imagepath . 'layout/content-fluid.png'
            ),
            'container' => array (
                'label' => 'Boxed',
                'value' => $imagepath . 'layout/content-boxed.png'
            )
        )
    );

    $options[] = array(
        'name' => __( 'Content Bottom', 'waboot' ),
        'desc' => __( 'Select content bottom width. Fluid or Boxed?', 'waboot' ),
        'id' => 'waboot_bottom_width',
        'std' => 'container',
        'type' => 'images',
        'options' => array(
            'container-fluid' => array (
                'label' => 'Fluid',
                'value' => $imagepath . 'layout/content-bottom-fluid.png'
            ),
            'container' => array (
                'label' => 'Boxed',
                'value' => $imagepath . 'layout/content-bottom-boxed.png'
            )
        )
    );

    $options[] = array(
        'name' => __('Background Content', 'waboot'),
        'desc' => __('Change the content background color.', 'waboot'),
        'id' => 'waboot_content_bgcolor',
        'type' => 'color',
        'recompile_styles' => true
    );

    $options[] = array(
        'name' => __('Background Content Bottom', 'waboot'),
        'desc' => __('Change the content bottom background color.', 'waboot'),
        'id' => 'waboot_bottom_bgcolor',
        'type' => 'color',
	    'std' => '#ededed',
        'recompile_styles' => true
    );

    $options[] = array(
        'name' => __( 'Footer', 'waboot' ),
        'desc' => __( 'Select footer width. Fluid or Boxed?', 'waboot' ),
        'id' => 'waboot_footer_width',
        'std' => 'container',
        'type' => 'images',
        'options' => array(
            'container-fluid' => array (
                'label' => 'Fluid',
                'value' => $imagepath . 'layout/footer-fluid.png'
            ),
            'container' => array (
                'label' => 'Boxed',
                'value' => $imagepath . 'layout/footer-boxed.png'
            )
        )
    );

    $options[] = array(
        'name' => __( 'Closure', 'waboot' ),
        'desc' => __( 'Select closure width. Fluid or Boxed?', 'waboot' ),
        'id' => 'waboot_closure_width',
        'std' => 'container',
        'type' => 'images',
        'options' => array(
            'container-fluid' => array (
                'label' => 'Fluid',
                'value' => $imagepath . 'layout/closure-fluid.png'
            ),
            'container' => array (
                'label' => 'Boxed',
                'value' => $imagepath . 'layout/closure-boxed.png'
            )
        )
    );

    $options[] = array(
        'name' => __('Background Footer', 'waboot'),
        'desc' => __('Change the footer background color.', 'waboot'),
        'id' => 'waboot_footer_bgcolor',
        'type' => 'color',
	    'std' => '#f6f6f6',
        'recompile_styles' => true
    );

    $options[] = array(
        'name' => __('Background Closure', 'waboot'),
        'desc' => __('Change the closure background color.', 'waboot'),
        'id' => 'waboot_closure_bgcolor',
        'type' => 'color',
	    'std' => '#e2e2e2',
        'recompile_styles' => true
    );


    /*
     * STYLE TAB
     */

    $options[] = array(
        'name' => __( 'Backgrounds', 'waboot' ),
        'type' => 'heading'
    );

    $options[] = array(
        'name' => __( 'Body Background Image', 'waboot' ),
        'desc' => __( 'Upload a background image, or specify the image address of your image. (http://yoursite.com/image.png)', 'waboot' ),
        'id' => 'waboot_body_bgimage',
        'std' => '',
        'type' => 'upload',
        'recompile_styles' => true
    );

    $options[] = array(
        'name' => __('Body Background Color', 'waboot'),
        'desc' => __('Change the body background color.', 'waboot'),
        'id' => 'waboot_body_bgcolor',
        'std' => "#ededed",
        'type' => 'color',
	    'recompile_styles' => true
    );

    $options[] = array(
        'name' => __( 'Body Background Image Repeat', 'waboot' ),
        'desc' => __( 'Select how you want your background image to display.', 'waboot' ),
        'id' => 'waboot_body_bgrepeat',
        'type' => 'select',
        'options' => array( 'no-repeat' => 'No Repeat', 'repeat' => 'Repeat','repeat-x' => 'Repeat Horizontally', 'repeat-y' => 'Repeat Vertically' ),
        'recompile_styles' => true
    );

    $options[] = array(
        'name' => __( 'Body Background image position', 'waboot' ),
        'desc' => __( 'Select how you would like to position the background', 'waboot' ),
        'id' => 'waboot_body_bgpos',
        'std' => 'top left',
        'type' => 'select',
        'options' => array(
            'top left' => 'top left', 'top center' => 'top center', 'top right' => 'top right',
            'center left' => 'center left', 'center center' => 'center center', 'center right' => 'center right',
            'bottom left' => 'bottom left', 'bottom center' => 'bottom center', 'bottom right' => 'bottom right'
        ),
        'recompile_styles' => true
    );

    $options[] = array(
        'name' => __( 'Body Background Attachment', 'waboot' ),
        'desc' => __( 'Select whether the background should be fixed or move when the user scrolls', 'waboot' ),
        'id' => 'waboot_body_bgattach',
        'std' => 'scroll',
        'type' => 'select',
        'options' => array( 'scroll' => 'scroll','fixed' => 'fixed' ),
        'recompile_styles' => true
    );

    /*
     * BOOTSTRAP VARIABLES TAB
     */

    $options[] = array(
        'name' => __( 'Style', 'waboot' ),
        'type' => 'heading'
    );

    $options[] = array(
        'name' => __('Primary color', 'waboot'),
        'desc' => __('Change the color of @brand-primary.', 'waboot'),
        'id' => 'waboot_brand_primary',
        'std' => "#428bca",
        'type' => 'color',
	    'recompile_styles' => true
    );

    $options[] = array(
        'name' => __('Text color', 'waboot'),
        'desc' => __('Change the color of @text-color.', 'waboot'),
        'id' => 'waboot_text_color',
        'std' => "#333333",
        'type' => 'color',
        'recompile_styles' => true
    );

    $options[] = array(
        'name' => __('Info color', 'waboot'),
        'desc' => __('Change the color of @brand-info.', 'waboot'),
        'id' => 'waboot_brand_info',
        'std' => "#5bc0de",
        'type' => 'color',
        'recompile_styles' => true
    );

    $options[] = array(
        'name' => __('Success color', 'waboot'),
        'desc' => __('Change the color of @brand-success.', 'waboot'),
        'id' => 'waboot_brand_success',
        'std' => "#5cb85c",
        'type' => 'color',
        'recompile_styles' => true
    );

    $options[] = array(
        'name' => __('Warning color', 'waboot'),
        'desc' => __('Change the color of @brand-warning.', 'waboot'),
        'id' => 'waboot_brand_warning',
        'std' => "#f0ad4e",
        'type' => 'color',
        'recompile_styles' => true
    );

    $options[] = array(
        'name' => __('Danger color', 'waboot'),
        'desc' => __('Change the color of @brand-danger.', 'waboot'),
        'id' => 'waboot_brand_danger',
        'std' => "#d9534f",
        'type' => 'color',
        'recompile_styles' => true
    );

    $options[] = array(
        'name' => __('Border radius base', 'waboot'),
        'desc' => __('Change the width in pixel of @border-radius-base.', 'waboot'),
        'id' => 'waboot_border_radius_base',
        'std' => "4",
        'type' => 'text',
        'recompile_styles' => true
    );

    $options[] = array(
        'name' => __('Border radius large', 'waboot'),
        'desc' => __('Change the width in pixel of @border-radius-large.', 'waboot'),
        'id' => 'waboot_border_radius_lg',
        'std' => "6",
        'type' => 'text',
        'recompile_styles' => true
    );

    $options[] = array(
        'name' => __('Border radius small', 'waboot'),
        'desc' => __('Change the width in pixel of @border-radius-small.', 'waboot'),
        'id' => 'waboot_border_radius_sm',
        'std' => "3",
        'type' => 'text',
        'recompile_styles' => true
    );

    $options[] = array(
        'name' => __('Well background', 'waboot'),
        'desc' => __('Change the color of @well-bg.', 'waboot'),
        'id' => 'waboot_well_bg',
        'std' => "#f5f5f5",
        'type' => 'color',
        'recompile_styles' => true
    );

    /*
     * TYPOGRAPHY
     */

    $options[] = array(
        'name' => __( 'Typography', 'waboot' ),
        'type' => 'heading'
    );

	$options[] = array(
		'name' => 'Primary font (body, p, ul, li)',
		'id' => 'waboot_primaryfont',
		'std' => array(
			'family' => 'Source Sans Pro',
			'style'  => 'regular',
			'charset' => 'latin',
			'color'  => '#666666'
		),
		'type' => 'typography',
		'fonts_type' => 'google',
		'recompile_styles' => true
	);

    $options[] = array(
        'name' => 'Secondary font (h1, h2, h3, h4, h5, h6)',
        'id' => 'waboot_secondaryfont',
        'std' => array(
            'family' => 'Source Sans Pro',
            'style'  => 'bold',
            'charset' => 'latin',
            'color'  => '#666666'
        ),
        'type' => 'typography',
        'fonts_type' => 'google',
        'recompile_styles' => true
    );

    /*
    * HEADER TAB
    */

    $options[] = array(
        'name' => __( 'Header', 'waboot' ),
        'type' => 'heading'
    );

    $options[] = array(
        'name' => __( 'Show Logo?', 'waboot' ),
        'desc' => __( 'Displays the logo on your site.', 'waboot' ),
        'id'   => 'waboot_logo_in_navbar',
	    'std'  => get_template_directory_uri()."/assets/images/default/waboot-color.png",
        'type' => 'upload'
    );

    $options[] = array(
        'name' => __( 'Header', 'waboot' ),
        'desc' => __( 'Select your header layout' ,'waboot' ),
        'id'   => 'waboot_header_layout',
        'std' => 'header1',
        'type' => 'images',
        'options' => array(
            'header1' => array(
                'label' => 'header1',
                'value' => $imagepath . 'header/header-1.png'
            ),
            'header2' => array(
                'label' => 'header2',
                'value' => $imagepath . 'header/header-2.png'
            ),
            'header3' => array(
                'label' => 'header3',
                'value' => $imagepath . 'header/header-3.png'
            )
        )
    );

    $options[] = array(
        'name' => __('Navbar Align', 'waboot'),
        'desc' => __('Select align of navbar', 'waboot'),
        'id' => 'waboot_navbar_align',
        'std' => 'navbar-left',
        'type' => 'select',
        'options' => array('navbar-left' => __("Left","waboot"), 'navbar-right' => __("Right","waboot"), 'navbar-center' => __("Center","waboot"))
    );

    $options[] = array(
        'name' => __( 'Show search bar in Header?', 'waboot' ),
        'desc' => __( 'Default is enabled. Uncheck this box to turn it off.', 'waboot' ),
        'id'   => 'waboot_search_bar',
        'std'  => '0',
        'type' => 'checkbox'
    );

    $options[] = array(
        'name' => __( 'Mobile Nav Style', 'waboot' ),
        'desc' => __( 'Select your mobile nav style' ,'waboot' ),
        'id'   => 'waboot_mobilenav_style',
        'std' => 'offcanvas',
        'type' => 'images',
        'options' => array(
            'bootstrap' => array(
                'label' => 'Bootstrap',
                'value' => $imagepath . 'mobile/nav-bootstrap.png'
            ),
            'offcanvas' => array(
                'label' => 'OffCanvas',
                'value' => $imagepath . 'mobile/nav-offcanvas.png'
            )
        )
    );

    $options[] = array(
        'name' => __('Top Nav Menu Position', 'waboot'),
        'desc' => __('Select the Top Nav Menu position', 'waboot'),
        'id' => 'waboot_topnavmenu_position',
        'std' => 'left',
        'type' => 'images',
        'options' => array(
            'left' => array (
                'label' => 'Left',
                'value' => $imagepath . 'topnav/top-nav-left.png'
            ),
            'right' => array (
                'label' => 'Right',
                'value' => $imagepath . 'topnav/top-nav-right.png'
            )
        )
    );

    $options[] = array(
        'name' => __( 'Show Logo in Mobile Nav?', 'waboot' ),
        'desc' => __( 'Default is enabled. Uncheck this box to turn it off.', 'waboot' ),
        'id'   => 'waboot_logo_mobilenav',
        'std'  => '1',
        'type' => 'checkbox'
    );


    if (class_exists('\WBF\modules\behaviors\BehaviorsManager')) {

	    $get_archive_pages_type = function($blacklist = array()){
		    static $result;

		    if(isset($result)) return $result;

		    $archive_types = array(
			    "archive" => __("Archive page","waboot"),
			    "tag"     => __("Tag archive","waboot"),
			    "tax"     => __("Taxonomy archive","waboot"),
		    );
		    $blacklist = array_unique(array_merge($blacklist,array()));
		    $result = array();
		    foreach($archive_types as $name => $label){
			    if(!in_array($name,$blacklist)){
				    $result[$name] = $label;
			    }
		    }

		    return $result;
	    };

        $bd_locs = array_merge(array("homepage"=>"Homepage"),wbf_get_filtered_post_types(),$get_archive_pages_type());

        if (!empty($bd_locs)) {
            $options[] = array(
                'id' => 'waboot_breadcrumb_locations',
                'name' => __('Breadcrumb Locations', 'waboot'),
                'desc' => __('Where to show breadcrumb', 'waboot'),
                'type' => 'multicheck',
                'options' => $bd_locs,
                'std' => array(
	                'homepage' => 1,
                    'post' => 1,
                    'page' => 1,
	                'archive' => 1,
	                'tag' => 1,
	                'tax' => 1
                )
            );
        }
    }


    /*
    * FOOTER TAB
    */

    $options[] = array(
        'name' => __( 'Footer', 'waboot' ),
        'type' => 'heading'
    );

    $options[] = array(
        'name' => __( 'Show custom footer text?', 'waboot' ),
        'desc' => __( 'Default is disabled. Check this box to use custom footer text. Fill in your text below.', 'waboot' ),
        'id'   => 'waboot_custom_footer_toggle',
        'std'  => '1',
        'type' => 'checkbox'
    );

    $options[] = array(
        'name' => __( 'Custom footer text', 'waboot' ),
        'desc' => __( 'Enter the text here that you would like displayed at the bottom of your site. This setting will be ignored if you do not enable "Show custom footer text" above.', 'waboot' ),
        'id'   => 'waboot_custom_footer_text',
        'std'  => '&copy; '.date("Y")." - you business name",
        'type' => 'textarea'
    );


    /*
    * BEHAVIORS TAB
    */

    if(\WBF::module_is_loaded("behaviors") && class_exists('\WBF\modules\behaviors\BehaviorsManager')) :

        $options[] = array(
            'name' => __( 'Posts & Pages', 'waboot' ),
            'type' => 'heading'
        );

        //Get post types
        $post_types = wbf_get_filtered_post_types();

        foreach($post_types as $ptSlug => $ptLabel){
            if(\WBF\modules\behaviors\BehaviorsManager::count_behaviors_for_post_type($ptSlug) > 0){
                //get predefined options
                //$predef_behavior = waboot_behavior_get_options();
                $predef_behavior = \WBF\modules\behaviors\BehaviorsManager::getAll();

                $options[] = array(
                    'name' => $ptLabel,
                    'desc' => sprintf(__( 'Edit default options for "%s" post type', 'waboot' ),strtolower($ptLabel)),
                    'type' => 'info'
                );

                foreach($predef_behavior as $b){
                    if($b->is_enabled_for_post_type($ptSlug)){
                        $option = $b->generate_of_option($ptSlug);
                        $options[] = $option;
                    }
                }
            }
        }

    endif;

    /*
     * BLOG PAGE TAB
     */

    $options[] = array(
        'name' => __( 'Blog', 'waboot' ),
        'type' => 'heading'
    );

    $blogpage_layouts = \WBF\modules\options\of_add_default_key(apply_filters("waboot_blogpage_layout",array(
        'classic' =>  array(
            'label' => 'Classic',
            'value' => $imagepath . 'blog/default-blog.png'
        ),
        'masonry' =>  array(
            'label' => 'Masonry',
            'value' => $imagepath . 'blog/masonry-blog.png'
        ),
        'timeline' =>  array(
            'label' => 'Timeline',
            'value' => $imagepath . 'blog/timeline-blog.png'
        ),
        '_default' => 'classic'
    )));

    $options[] = array(
        'name' => __('Blog Style', 'waboot'),
        'desc' => __('Select blog page style', 'waboot'),
        'id' => 'waboot_blogpage_layout',
        'std' => $blogpage_layouts['default'],
        'type' => 'images',
        'options' => $blogpage_layouts['values'],
	    'deps' => array(
          "timeline" => array('components'=>array('timeline')),
          "masonry" => array('components'=>array('masonry'))
        )
    );


    $sidebar_layouts = \WBF\modules\options\of_add_default_key(waboot_get_available_body_layouts());
	if(isset($sidebar_layouts['values'][0]['thumb'])){
		$opt_type = "images";
		foreach($sidebar_layouts['values'] as $k => $v){
			$final_sidebar_layouts[$v['value']]['label'] = $v['name'];
			$final_sidebar_layouts[$v['value']]['value'] = isset($v['thumb']) ? $v['thumb'] : "";
		}
	}else{
		$opt_type = "select";
		foreach($sidebar_layouts['values'] as $k => $v){
			$final_sidebar_layouts[$v['value']]['label'] = $v['name'];
		}
	}
    $options[] = array(
        'name' => __('Blog Layout', 'waboot'),
        'desc' => __('Select blog page layout', 'waboot'),
        'id' => 'waboot_blogpage_sidebar_layout',
        'std' => $sidebar_layouts['default'],
        'type' => $opt_type,
        'options' => $final_sidebar_layouts
    );

    $options[] = array(
        'name' => __( 'Display Blog page title', 'waboot' ),
        'desc' => __( 'Check this box to show page title.', 'waboot' ),
        'id'   => 'waboot_blogpage_displaytitle',
        'std'  => '1',
        'type' => 'checkbox'
    );

    $options[] = array(
        'name' => __('Title position', 'waboot'),
        'desc' => __('Select where to display page title', 'waboot'),
        'id' => 'waboot_blogpage_title_position',
        'std' => 'top',
        'type' => 'select',
        'options' => array('top' => __("Above primary","waboot"), 'bottom' => __("Below primary","waboot"))
    );

    /*
     * WOOCOMMERCE PAGE TAB
     */

    $options[] = array(
        'name' => __( 'Woocommerce', 'waboot' ),
        'type' => 'heading'
    );

    $options[] = array(
        'name' => __( 'Woocommerce Shop Page', 'waboot' ),
        'desc' => __( '', 'waboot' ),
        'type' => 'info'
    );

    $options[] = array(
        'name' => __('Woocommerce Shop Layout', 'waboot'),
        'desc' => __('Select Woocommerce shop page layout', 'waboot'),
        'id' => 'waboot_woocommerce_shop_sidebar_layout',
        'std' => $sidebar_layouts['default'],
        'type' => $opt_type,
        'options' => $final_sidebar_layouts
    );

    $options[] = array(
        'name' => __("Primary Sidebar width","waboot"),
        'desc' => __("Choose the primary sidebar width","waboot"),
        'id' => 'woocommerce_shop_primary_sidebar_size',
        'std' => '1/4',
        'type' => "select",
        'options' => array("1/2"=>"1/2","1/3"=>"1/3","1/4"=>"1/4","1/6"=>"1/6")
    );

    $options[] = array(
        'name' => __("Secondary Sidebar width","waboot"),
        'desc' => __("Choose the secondary sidebar width","waboot"),
        'id' => 'woocommerce_shop_secondary_sidebar_size',
        'std' => '1/4',
        'type' => "select",
        'options' => array("1/2"=>"1/2","1/3"=>"1/3","1/4"=>"1/4","1/6"=>"1/6")
    );

    $options[] = array(
        'name' => __( 'Display Woocommerce page title', 'waboot' ),
        'desc' => __( 'Check this box to show page title.', 'waboot' ),
        'id'   => 'woocommerce_shop_displaytitle',
        'std'  => '1',
        'type' => 'checkbox'
    );

    $options[] = array(
        'name' => __('Title position', 'waboot'),
        'desc' => __('Select where to display page title', 'waboot'),
        'id' => 'woocommerce_shop_title_position',
        'std' => 'top',
        'type' => 'select',
        'options' => array('top' => __("Above primary","waboot"), 'bottom' => __("Below primary","waboot"))
    );

    $options[] = array(
        'name' => __( 'Woocommerce Archives and Categories', 'waboot' ),
        'desc' => __( '', 'waboot' ),
        'type' => 'info'
    );

    $options[] = array(
        'name' => __('Woocommerce Archive Layout', 'waboot'),
        'desc' => __('Select Woocommerce archive layout', 'waboot'),
        'id' => 'waboot_woocommerce_sidebar_layout',
        'std' => $sidebar_layouts['default'],
        'type' => $opt_type,
        'options' => $final_sidebar_layouts
    );

    $options[] = array(
        'name' => __("Primary Sidebar width","waboot"),
        'desc' => __("Choose the primary sidebar width","waboot"),
        'id' => 'waboot_woocommerce_primary_sidebar_size',
        'std' => '1/4',
        'type' => "select",
        'options' => array("1/2"=>"1/2","1/3"=>"1/3","1/4"=>"1/4","1/6"=>"1/6")
    );

    $options[] = array(
        'name' => __("Secondary Sidebar width","waboot"),
        'desc' => __("Choose the secondary sidebar width","waboot"),
        'id' => 'waboot_woocommerce_secondary_sidebar_size',
        'std' => '1/4',
        'type' => "select",
        'options' => array("1/2"=>"1/2","1/3"=>"1/3","1/4"=>"1/4","1/6"=>"1/6")
    );

    $options[] = array(
        'name' => __( 'Display Woocommerce page title', 'waboot' ),
        'desc' => __( 'Check this box to show page title.', 'waboot' ),
        'id'   => 'waboot_woocommerce_displaytitle',
        'std'  => '1',
        'type' => 'checkbox'
    );

    $options[] = array(
        'name' => __('Title position', 'waboot'),
        'desc' => __('Select where to display page title', 'waboot'),
        'id' => 'waboot_woocommerce_title_position',
        'std' => 'top',
        'type' => 'select',
        'options' => array('top' => __("Above primary","waboot"), 'bottom' => __("Below primary","waboot"))
    );

    $options[] = array(
        'name' => __('Items for Row', 'waboot'),
        'desc' => __('How many items display for row', 'waboot'),
        'id' => 'waboot_woocommerce_cat_items',
        'std' => 'col-sm-3',
        'type' => 'select',
        'options' => array('col-sm-3' => '4', 'col-sm-4' => '3')
    );

	$options[] = array(
		'name' => __('Products per page', 'waboot'),
		'desc' => __('How many products display per page', 'waboot'),
		'id' => 'woocommerce_products_per_page',
		'std' => '10',
		'type' => 'text'
	);

    /*
     * POST META TAB
     */

    $options[] = array(
        'name' => __( 'Post Meta', 'waboot' ),
        'type' => 'heading'
    );

    $options[] = array(
        'name' => __( 'Show content navigation above posts?', 'waboot' ),
        'desc' => __( 'Displays links to next and previous posts above the current post and above the posts on the index page. Default is hide. Check this box to show content nav above posts.', 'waboot' ),
        'id'   => 'waboot_content_nav_above',
        'std'  => '0',
        'type' => 'checkbox'
    );

    $options[] = array(
        'name' => __( 'Show content navigation below posts?', 'waboot' ),
        'desc' => __( 'Displays links to next and previous posts below the current post and below the posts on the index page. Default is show. Uncheck this box to hide content nav above posts.', 'waboot' ),
        'id'   => 'waboot_content_nav_below',
        'std'  => '1',
        'type' => 'checkbox'
    );

    $options[] = array(
        'name' => __( 'Show post author?', 'waboot' ),
        'desc' => __( 'Displays the post author. Default is show. Uncheck this box to hide the post author.', 'waboot' ),
        'id'   => 'waboot_post_author',
        'std'  => '0',
        'type' => 'checkbox'
    );

    $options[] = array(
        'name' => __( 'Show published date?', 'waboot' ),
        'desc' => __( 'Displays the date the article was posted. Default is show. Uncheck this box to hide post published date.', 'waboot' ),
        'id'   => 'waboot_published_date',
        'std'  => '1',
        'type' => 'checkbox'
    );

    $options[] = array(
        'name' => __( 'Show post categories?', 'waboot' ),
        'desc' => __( 'Displays the categories in which a post was published. Default is show. Uncheck this box to hide post categories.', 'waboot' ),
        'id'   => 'waboot_post_categories',
        'std'  => '1',
        'type' => 'checkbox'
    );

    $options[] = array(
        'name' => __( 'Show post tags?', 'waboot' ),
        'desc' => __( 'Displays the tags attached to a post. Default is show. Uncheck this box to hide post tags.', 'waboot' ),
        'id'   => 'waboot_post_tags',
        'std'  => '1',
        'type' => 'checkbox'
    );

    $options[] = array(
        'name' => __( 'Show link for # of comments / Leave a comment?', 'waboot' ),
        'desc' => __( 'Displays the number of comments and/or a Leave a comment message on posts. Default is show. Uncheck this box to hide.' ,'waboot' ),
        'id'   => 'waboot_post_comments_link',
        'std'  => '1',
        'type' => 'checkbox'
    );

	/*
    * CONTACT FORM TAB
    */

	$options[] = array(
		'name' => __( 'Contact form', 'waboot' ),
		'type' => 'heading'
	);

	$options[] = array(
		'name' => __('Recipient type', 'waboot'),
		'desc' => __('Select who will receive the mail', 'waboot'),
		'id' => 'contact_form_mail_recipient_type',
		'std' => 'admin',
		'type' => 'select',
		'options' => array('admin' => __('The site admin'), 'author' => __('The author of the post in which the form is inserted',"waboot"), 'specific_contact' => __("A specific contact"))
	);

	$options[] = array(
		'name' => __( 'Specific contact data', 'waboot' ),
		'desc' => __( 'Contact information for "specific contact" option ', 'waboot' ),
		'type' => 'info'
	);

	$options[] = array(
		'name' => __( 'Name', 'waboot' ),
		'desc' => __( 'Enter the contact name', 'waboot' ),
		'id'   => 'contact_form_mail_recipient_name',
		'type' => 'text',
		'std'  => ''
	);

	$options[] = array(
		'name' => __( 'Email', 'waboot' ),
		'desc' => __( 'Enter the contact email address', 'waboot' ),
		'id'   => 'contact_form_mail_recipient_email',
		'type' => 'text',
		'std'  => ''
	);

    /*
     * FAVICON TAB
     */

	$options[] = array(
		'name' => __( 'Favicons', 'waboot' ),
		'type' => 'heading'
	);

	$options[] = array(
		'name' => __( 'Icon', 'waboot' ),
		'desc' => __( 'Upload a favicon (only .png and .ico files are allowed).', 'waboot' ),
		'id' => 'favicon_icon',
		'std' => '',
		'type' => 'upload',
		'readonly' => true,
		'upload_to' => ABSPATH,
		'upload_as' => 'favicon',
		'allowed_extensions' => array("png","ico")
	);

	$options[] = array(
		'name' => __( 'Apple Touch 120x120 Icon', 'waboot' ),
		'desc' => __( 'Upload a favicon (only .png and .ico files are allowed).', 'waboot' ),
		'id' => 'favicon_apple120',
		'std' => '',
		'type' => 'upload',
		'readonly' => true,
		'upload_to' => ABSPATH,
		'upload_as' => 'touch-icon-iphone-retina',
		'allowed_extensions' => array("png","ico")
	);

	$options[] = array(
		'name' => __( 'Apple Touch 152x152 Icon', 'waboot' ),
		'desc' => __( 'Upload a favicon (only .png and .ico files are allowed).', 'waboot' ),
		'id' => 'favicon_apple152',
		'std' => '',
		'type' => 'upload',
		'readonly' => true,
		'upload_to' => ABSPATH,
		'upload_as' => 'touch-icon-ipad-retina',
		'allowed_extensions' => array("png","ico")
	);

    /*
     * SOCIAL TAB
     */

    $options[] = array(
        'name' => __( 'Social', 'waboot' ),
        'type' => 'heading'
    );

    $options[] = array(
        'name' => __( 'Social Position', 'waboot' ),
        'desc' => __( 'Select the social widget position', 'waboot' ),
        'id' => 'waboot_social_position',
        'type' => 'images',
        'std'  => 'navigation',
        'options' => array(
            'footer' =>  array(
                'label' => 'Footer',
                'value' => $imagepath . 'social/footer.png'
            ),
            'header-right' =>  array(
                'label' => 'Header Right',
                'value' => $imagepath . 'social/header-right.png'
            ),
            'header-left' =>  array(
                'label' => 'Header Left',
                'value' => $imagepath . 'social/header-left.png'
            ),
            'topnav-right' =>  array(
                'label' => 'Topnav Right',
                'value' => $imagepath . 'social/topnav-right.png'
            ),
            'topnav-left' =>  array(
                'label' => 'Topnav Left',
                'value' => $imagepath . 'social/topnav-left.png'
            ),
            'navigation' =>  array(
                'label' => 'Navigation',
                'value' => $imagepath . 'social/nav.png'
            )
        )
    );

    $options[] = array(
        'name' => __( 'Facebook', 'waboot' ),
        'desc' => __( 'Enter your facebook fan page link', 'waboot' ),
        'id'   => 'waboot_social_facebook',
        'type' => 'text',
	    'std'  => '#'
    );

    $options[] = array(
        'name' => __( 'Twitter', 'waboot' ),
        'desc' => __( 'Enter your twitter page link', 'waboot' ),
        'id'   => 'waboot_social_twitter',
        'type' => 'text',
        'std'  => '#'
    );

    $options[] = array(
        'name' => __( 'Google+', 'waboot' ),
        'desc' => __( 'Enter your google+ page link', 'waboot' ),
        'id'   => 'waboot_social_google',
        'type' => 'text',
        'std'  => '#'
    );

    $options[] = array(
        'name' => __( 'YouTube', 'waboot' ),
        'desc' => __( 'Enter your youtube page link', 'waboot' ),
        'id'   => 'waboot_social_youtube',
        'type' => 'text'
    );

    $options[] = array(
        'name' => __( 'Pinterest', 'waboot' ),
        'desc' => __( 'Enter your pinterest page link', 'waboot' ),
        'id'   => 'waboot_social_pinterest',
        'type' => 'text'
    );

    $options[] = array(
        'name' => __( 'Linkedin', 'waboot' ),
        'desc' => __( 'Enter your linkedin page link', 'waboot' ),
        'id'   => 'waboot_social_linkedin',
        'type' => 'text'
    );

    $options[] = array(
        'name' => __( 'Instagram', 'waboot' ),
        'desc' => __( 'Enter your instagram page link', 'waboot' ),
        'id'   => 'waboot_social_instagram',
        'type' => 'text'
    );

    $options[] = array(
        'name' => __( 'Feed RSS', 'waboot' ),
        'desc' => __( 'Enter your feed RSS link', 'waboot' ),
        'id'   => 'waboot_social_feedrss',
        'type' => 'text'
    );

    /*
     * CUSTOM CSS TAB
     */

    $options[] = array(
        'name' => __( 'Custom CSS', 'waboot' ),
        'type' => 'heading'
    );

    $options[] = array(
        'name' => __( 'Custom CSS', 'waboot' ),
        'desc' => __( 'Enter custom css to apply to the theme (press CTRL-SPACE on Windows, or CTRL-F on Mac for suggestions).', 'waboot' ),
        'id'   => 'waboot_custom_css',
        'type' => 'csseditor'
    );

    return $options;
}