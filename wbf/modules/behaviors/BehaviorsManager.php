<?php

namespace WBF\modules\behaviors;

class BehaviorsManager{

	static function get($name, $post_id)
	{
		$current_post_type = $post_id != 0 ? get_post_type($post_id) : "page"; //"0" is received when in archives pages, so set the post type to "Pages"
		$behaviors = self::getAll($current_post_type); //retrive all behaviours
		$selected_behavior = new \stdClass();

		foreach ($behaviors as $b) { //find the desidered behaviour
			if ($b->name == $name) {
				$selected_behavior = $b;
			}
		}

		if ($selected_behavior instanceof Behavior) {
			$current_behavior_value = $selected_behavior->get_value($post_id);
			return $selected_behavior;
		} else {
			return false;
		}
	}

	static function getAll($post_type = null){
		$imported_behaviors = self::importPredefined(); //per ora si possono specificare solo via file...
		$behaviors = array();
		foreach($imported_behaviors as $b){
			if(isset($post_type)) $b['get_for_posttype'] = $post_type;
			$behaviors[] = new Behavior($b);
		}

		return $behaviors;
	}

	static function count_behaviors_for_post_type($slug){
		$behaviors = self::getAll();
		$count = 0;
		foreach($behaviors as $b){
			if($b->is_enabled_for_post_type($slug)){
				$count++;
			}
		}

		return $count;
	}

	static function importPredefined(){
		$predef_behaviors = array();

		//Get behaviors from .json files
		$behavior_file = get_theme_root()."/".get_template()."/inc/behaviors.json";
		if (file_exists($behavior_file)) {
			$predef_behaviors = json_decode(file_get_contents($behavior_file, true),true);
		}

		if(is_child_theme()){
			$child_behavior_file = get_stylesheet_directory()."/inc/behaviors.json";
			if(file_exists($child_behavior_file)){
				$child_behaviors = json_decode(file_get_contents($child_behavior_file, true),true);
				$predef_behaviors = array_replace_recursive($predef_behaviors,$child_behaviors);
			}
		}

		//Get from filters
		$predef_behaviors = apply_filters("wbf_add_behaviors",$predef_behaviors);

		return $predef_behaviors;
	}

	static function debug($post_id){
		$behaviors = self::getAll();
		echo "<div style='border: 1px solid #ccc;'><pre>";
		foreach($behaviors as $b){
			echo $b->name.": ";
			var_dump($b->get_value($post_id));
		}
		echo "</div></pre>";
	}
}