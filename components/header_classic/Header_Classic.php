<?php
/**
Component Name: Header Classic
Description: Header Classic Component
Category: Layout
Tags: Header
Version: 1.0
Author: WAGA Team <dev@waga.it>
Author URI: http://www.waga.it
*/

class Header_Classic extends \Waboot\Component{
	/**
	 * This method will be executed at Wordpress startup (every page load)
	 */
	public function setup(){
		parent::setup();
	}

    public function styles(){
        parent::styles();
        //wp_enqueue_style('header_classic_style', $this->directory_uri . '/assets/dist/css/headerClassic.css');
        //wp_enqueue_style('offcanvas_style', $this->directory_uri . '/assets/dist/css/offcanvas.css');
        Waboot()->add_inline_style('header_classic_style', $this->directory_uri . '/assets/dist/css/headerClassic.css');
        Waboot()->add_inline_style('offcanvas_style', $this->directory_uri . '/assets/dist/css/offcanvas.css');
    }

    public function scripts() {
	    parent::scripts();
	    // wp_enqueue_script('header_classic_js', $this->directory_uri . '/assets/dist/js/headerClassic.js', ['jquery'], false, true);
	    wp_enqueue_script('offcanvas_js', $this->directory_uri . '/assets/dist/js/offcanvas.js', ['jquery'], false, true);
    }

	public function run(){
		parent::run();
		$display_zone = $this->get_display_zone();
		$display_priority = $this->get_display_priority();
		Waboot()->layout->add_zone_action($display_zone,[$this,"display_tpl"],intval($display_priority));
	}

    public function widgets() {
        add_filter("waboot/widget_areas/available",function($areas){
            $areas['header-left'] = [
                'name' => __('Header Left (Component)', 'waboot'),
                'description' => __( 'The widget areas registered by Header Classic', 'waboot' ),
            ];
            $areas['header-right'] = [
                'name' => __('Header Right (Component)', 'waboot'),
                'description' => __( 'The widget areas registered by Header Classic', 'waboot' ),
            ];
            return $areas;
        });
    }
	
	public function display_tpl(){
		$v = new \WBF\components\mvc\HTMLView($this->theme_relative_path."/templates/header-wrapper.php");

        $vHeader = new \WBF\components\mvc\HTMLView($this->theme_relative_path."/templates/header-content.php");
        $vNavbar = new \WBF\components\mvc\HTMLView($this->theme_relative_path."/templates/navbar-content.php");
        $vOffcanvas = new \WBF\components\mvc\HTMLView($this->theme_relative_path."/templates/navbar-offcanvas.php");
        
        $v->clean()->display([
            "header_width" => Waboot\functions\get_option("headerclassic_header_width"),
            "navbar_width" => Waboot\functions\get_option("headerclassic_nav_width"),
            "header_content" => $vHeader->get([
                "logo_position" => Waboot\functions\get_option("headerclassic_logo_position"),
            ]),
			"navbar_content" => $vNavbar->get([
                "nav_align" => Waboot\functions\get_option("headerclassic_nav_align"),
                "offcanvas" => Waboot\functions\get_option("headerclassic_nav_mobilestyle") == "offcanvas",
                "display_searchbar" => Waboot\functions\get_option("headerclassic_nav_searchbar"),
                "navbar_offcanvas" => $vOffcanvas->get([
                    "display_searchbar" => Waboot\functions\get_option("headerclassic_nav_searchbar"),
                    "logo_mobile" => Waboot\functions\get_option("mobile_logo"),
                    "logo_offcanvas" => Waboot\functions\get_option("show_offcanvas_logo"),
                ])
            ])
		]);
	}
	
	public function register_options() {
		parent::register_options();
		$orgzr = \WBF\modules\options\Organizer::getInstance();

		$imagepath = get_template_directory_uri()."/assets/images/options/";

		$orgzr->set_group($this->name."_component");
        $orgzr->add_section("header",_x("Header","Theme options section","waboot"));
        $orgzr->add_section("navigation",_x("Navigation","Theme options section","waboot"));

		$orgzr->update('headerclassic_header_width',[
			'name' => __( 'Header Width', 'waboot' ),
			'desc' => __( 'Select header width. Fluid or Boxed?', 'waboot' ),
			'id' => 'headerclassic_header_width',
			'std' => 'container-fluid',
			'type' => 'images',
			'options' => [
				'container-fluid' => [
					'label' => 'Fluid',
					'value' => $imagepath . 'layout/header-fluid.png'
				],
				'container' => [
					'label' => 'Boxed',
					'value' => $imagepath . 'layout/header-boxed.png'
				]
			]
		],"header");

        $orgzr->update('headerclassic_logo_position',[
            'name' => __( 'Logo Position', 'waboot' ),
            'desc' => __( 'Select logo align position', 'waboot' ),
            'id' => 'headerclassic_logo_position',
            'std' => 'center',
            'type' => 'select',
            'options' => [
                'center' => 'Center',
                'left' => 'Left',
                'right' => 'Right'
            ]
        ],"header");

        $orgzr->update("headerclassic_nav_width", [
            'name' => __( 'Navbar Width', 'waboot' ),
            'desc' => __( 'Select navbar width. Fluid or Boxed?', 'waboot' ),
            'id' => 'headerclassic_nav_width',
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
        ],'navigation');

        $orgzr->update('headerclassic_nav_align',[
            'name' => __( 'Navbar Align', 'waboot' ),
            'desc' => __( 'Select navbar align position', 'waboot' ),
            'id' => 'headerclassic_nav_align',
            'std' => 'center',
            'type' => 'select',
            'options' => [
                'center' => 'Center',
                'left' => 'Left',
                'right' => 'Right'
            ]
        ],"navigation");

        $orgzr->update('headerclassic_nav_searchbar',[
            'name' => __( 'Show search bar in Navbar?', 'waboot' ),
            'desc' => __( 'Default is enabled. Uncheck this box to turn it off.', 'waboot' ),
            'id'   => 'headerclassic_nav_searchbar',
            'std'  => '0',
            'type' => 'checkbox'
        ],'navigation');

        $orgzr->update('headerclassic_nav_mobilestyle',[
            'name' => __( 'Navbar Mobile Style', 'waboot' ),
            'desc' => __( 'Select your mobile nav style' ,'waboot' ),
            'id'   => 'headerclassic_nav_mobilestyle',
            'std' => 'offcanvas',
            'type' => 'images',
            'options' => array(
                'inline' => array(
                    'label' => _x('Inline',"mobilenav_style","waboot"),
                    'value' => $imagepath . 'mobile/nav-bootstrap.png'
                ),
                'offcanvas' => array(
                    'label' => _x('OffCanvas',"mobilenav_style","waboot"),
                    'value' => $imagepath . 'mobile/nav-offcanvas.png'
                )
            )
        ],"navigation");

        $orgzr->update('show_offcanvas_logo',[
            'name' => __( 'Show Logo in Offcanvas Mobile Nav?', 'waboot' ),
            'desc' => __( 'Choose the visibility of site logo in mobile navigation.', 'waboot' ),
            'id'   => 'show_offcanvas_logo',
            'std'  => '1',
            'type' => 'checkbox'
        ],"navigation");

        $orgzr->update('offcanvas_logo',[
            'name' => __( 'Mobile Offcanvas logo', 'waboot' ),
            'desc' => __( 'Choose the logo to display in mobile offcanvas navigation bar', 'waboot' ),
            'id'   => 'offcanvas_logo',
            'std'  => '',
            'type' => 'upload'
        ],"navigation");

		$orgzr->reset_group();
		$orgzr->reset_section();
	}
}