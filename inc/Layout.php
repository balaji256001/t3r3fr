<?php

namespace Waboot;

use WBF\includes\mvc\HTMLView;
use wbf\includes\mvc\View;

class Layout{
	/**
	 * @var Layout
	 */
	private static $instance;
	/**
	 * @var array
	 */
	private $zones = [];
	
	/**
	 * Returns the *Singleton* instance.
	 *
	 * @return Layout The *Singleton* instance.
	 */
	public static function getInstance(){
		if (null === static::$instance) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Creates a new template zone
	 *
	 * @param $slug
	 * @param $template
	 *
	 * @return $this
	 * @throws \Exception
	 */
	public function create_zone($slug,$template){
		//Check for valid slug
		if(preg_match("/ /",$slug)){
			throw new \Exception("You cannot have whitespaces in a zone slug");
		}
		//Checks for valid template
		if(!is_string($template) && !is_array($template) && !$template instanceof View){
			throw new \Exception("You cannot create a zone thats is neither a string, an array or a View instance");
		}
		//Check template existence
		if(is_string($template) || is_array($template)){
			if(is_array($template)){
				$template = implode("-",$template);
			}
			$tpl_file = locate_template($template);
			if(!$tpl_file){
				throw new \Exception("The {$template} for the zone {$slug} does not exists");
			}
		}
		//Save the zone
		$this->zones[$slug] = [
			'slug' => $slug,
			'template' => $template,
			'actions_hook' => 'waboot/zones/'.$slug,
			'actions' => []
		];
		return $this;
	}

	/**
	 * Renders a template zone
	 *
	 * @param $slug
	 *
	 * @throws \Exception
	 */
	public function render_zone($slug){
		$this->check_zone($slug);

		$zone = $this->zones[$slug];

		if(is_string($zone['template'])){
			get_template_part($zone['template']);
		}elseif(is_array($zone['template'])){
			list($template,$part) = $zone['template'];
			get_template_part($template,$part);
		}else{
			//Here we have a View instance
			$zone['template']->clean()->display([
				"name" => $zone['slug']
			]);
		}
	}

	/**
	 * Adds an action to the zone
	 * 
	 * @param $slug
	 * @param $function_to_call
	 * @param $priority
	 * @param $accepted_args
	 *
	 * @throws \Exception
	 */
	public function add_zone_action($slug,$function_to_call,$priority = 10,$accepted_args = 1){
		$this->check_zone($slug);

		$zone = $this->zones[$slug];
		
		add_action($zone['actions_hook'],$function_to_call,$priority,$accepted_args);
	}

	/**
	 * Performs zone actions
	 * 
	 * @param $slug
	 *
	 * @throws \Exception
	 */
	public function do_zone_action($slug){
		$this->check_zone($slug);

		$zone = $this->zones[$slug];
		
		do_action($zone['actions_hook']);
	}

	/**
	 * Checks whether a zone exists or not
	 * 
	 * @param $slug
	 *
	 * @return bool
	 * @throws \Exception
	 */
	private function check_zone($slug){
		if(!isset($this->zones[$slug])) throw new \Exception("Zone {$slug} not found");
		return true;
	}

	/**
	 * Private clone method to prevent cloning of the instance of the *Singleton* instance.
	 */
	private function __clone(){}

	/**
	 * Private unserialize method to prevent unserializing of the *Singleton* instance.
	 */
	private function __wakeup(){}
}