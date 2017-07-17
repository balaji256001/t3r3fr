<?php

namespace Waboot\hooks\generators;

use function Waboot\functions\wbf_exists;
use Waboot\Theme;
use WBF\components\mvc\HTMLView;

/**
 * Redirect to Wizard page after the first theme switch
 */
function redirect_to_wizard(){
	$wizard_done = Theme::is_wizard_done();
	if(!$wizard_done){
		$start_wizard_link = admin_url("admin.php?page=waboot_setup_wizard");
		wp_redirect($start_wizard_link);
	}
}
add_action("after_switch_theme", __NAMESPACE__."\\redirect_to_wizard");

/**
 * Adds the notice if the Wizard has never been done
 */
function add_wizard_notice(){
	if(isset($_GET['page']) && $_GET['page'] == 'waboot_setup_wizard') return;
	$wizard_done = Theme::is_wizard_done();
	if($wizard_done) return;
	//Add the notice to wizard
	$start_wizard_link = admin_url("admin.php?page=waboot_setup_wizard");
	$dismiss_wizard_link = add_query_arg(["waboot_dismiss_wizard"=>1],admin_url("themes.php"));
	$msg = sprintf(__("Thank you choosing Waboot! If you want, our wizard will help you to kickstart your theme with some initiali settings: click <a href='%s'>here</a> to start or <a href='%s'>here</a> to dismiss this notice.","waboot"),$start_wizard_link,$dismiss_wizard_link);
	WBF()->notice_manager->add_notice("waboot-wizard",$msg,"nag","_flash_");
}
add_action("admin_init",__NAMESPACE__."\\add_wizard_notice");

/**
 * Handles the dismissing of the Wizard notice
 */
function dismiss_wizard_notice(){
	if(!isset($_GET['waboot_dismiss_wizard'])) return;
	if($_GET['waboot_dismiss_wizard'] == 1){
		WBF()->notice_manager->remove_notice("waboot-wizard");
	}
}
add_action("admin_init",__NAMESPACE__."\\dismiss_wizard_notice");

/*
 * Handles wizard submit via AJAX
 */
add_action("wp_ajax_handle_generator", function(){
	$selected_generator = isset($_POST['params']) && isset($_POST['params']['generator']) ? sanitize_text_field($_POST['params']['generator']) : false;
	$step = isset($_POST['params']) && isset($_POST['params']['step']) ? sanitize_text_field($_POST['params']['step']) : Theme::GENERATOR_STEP_ALL;
	$action = isset($_POST['params']) && isset($_POST['params']['action']) ? sanitize_text_field($_POST['params']['action']) : Theme::GENERATOR_ACTION_ALL;

	if($selected_generator){
		$r = Theme::getInstance()->handle_generator($selected_generator,$step,$action);
		if($r['status'] === 'success'){
			if($r['complete']){
				$r['status'] = "complete";
			}else{
				$r['status'] = "run";
			}
			wp_send_json_success($r);
		}elseif($r['status'] === 'failed'){
			wp_send_json_error($r);
		}else{
			$r['status'] = "complete";
			wp_send_json_error($r);
		}
	}else{
		$r['status'] = "complete";
		wp_send_json_error();
	}
});

/**
 * Handle wizard submit via page refresh (not used anymore)
 *
 * @hooked 'admin_init'
 */
function handle_wizard(){
	if(!isset($_POST['waboot_wizard_nonce'])) return;

	$r = true;

	//Check generators
	$selected_generator = isset($_POST['generator']) ? sanitize_text_field($_POST['generator']) : false;
	if($selected_generator){
		$r = Theme::getInstance()->handle_generator($selected_generator);
	}

	if($r){
		WBF()->notice_manager->add_notice("waboot_wizard_completed",__("Wizard completed successfully","waboot"),"updated","_flash_");
		Theme::set_wizard_as_done();
	}else{
		WBF()->notice_manager->add_notice("waboot_wizard_completed",__("Wizard encountered some errors","waboot"),"error","_flash_");
	}
}
add_action('admin_init',__NAMESPACE__."\\handle_wizard",10);

/**
 * Adds and display Waboot Wizard page
 *
 * @hooked 'wbf_admin_submenu'
 *
 * @param $menu_slug
 */
function add_wizard_page($menu_slug){
	$menu = [
		'page_title' => __("Setup Wizard","waboot"),
		'menu_title' => __("Setup Wizard","waboot"),
		'capability' => "manage_options",
		'menu_slug'  => "waboot_setup_wizard"
	];

	add_submenu_page( $menu_slug, $menu['page_title'], $menu['menu_title'], $menu['capability'], $menu['menu_slug'], __NAMESPACE__.'\display_wizard_page');
}

/**
 * Prints out the wizard page
 */
function display_wizard_page(){
	$generators = Theme::get_generators();

	if(class_exists('WBF\components\mvc\HTMLView')){
		$v = new HTMLView("templates/admin/wizard.php");
		$v->for_dashboard()->display([
			"page_title" => '',
			"generators" => $generators,
			"images_uri" => get_template_directory_uri().'/assets/images',
			"nonce_action" => "waboot_submit_wizard",
			"nonce_name" => "waboot_wizard_nonce"
		]);
	}else{
		$page_title = __("Setup Wizard","waboot");
		$nonce_action = "waboot_submit_wizard";
		$nonce_name = "waboot_wizard_nonce";
		$images_uri = get_template_directory_uri().'/assets/images';
		require_once get_template_directory().'/templates/admin/wizard.php';
	}
}

if(!wbf_exists()){
	add_action('admin_menu', function(){
		$menu = [
			'page_title' => __("Waboot Setup Wizard","waboot"),
			'menu_title' => __("Waboot Setup Wizard","waboot"),
			'capability' => "manage_options",
			'menu_slug'  => "waboot_setup_wizard"
		];
		\add_management_page( $menu['page_title'], $menu['menu_title'], $menu['capability'], $menu['menu_slug'], __NAMESPACE__.'\display_wizard_page');
	});
}else{
	add_action("wbf_admin_submenu",__NAMESPACE__."\\add_wizard_page");
}