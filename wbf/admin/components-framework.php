<?php
/**
 * WABOOT COMPONENT FRAMEWORK
 */

$GLOBALS['loaded_components'] = array();
$GLOBALS['registered_components'] = array();

class Waboot_ComponentsManager {

    static $last_error = "";

    /**
     * Detect components into components directory and updates relative options
     */
    static function init(){
        /** Detect components in main theme **/
        self::_detect_components(get_template_directory()."/components");
        /** Detect components in child theme **/
        if(is_child_theme()){
            self::_detect_components(get_stylesheet_directory()."/components",true);
        }
        add_action( 'admin_menu', 'Waboot_ComponentsManager::add_menu', 11 );
        add_action( 'admin_enqueue_scripts', 'Waboot_ComponentsManager::scripts' );
    }

    static function scripts($hook){
		global $plugin_page;
		if($plugin_page == "waboot_components"){
            // Enqueue custom CSS
            $stylesheet = wbf_locate_template_uri('wbf/admin/css/waboot-componentsframework.css');
            if ($stylesheet != ""){
                wp_enqueue_style('waboot-theme-components-style', $stylesheet, array(), '1.0.0', 'all'); //Custom Theme Options CSS
            }
            if(WABOOT_ENV == "dev"){
                wp_register_script('component-page-script',WBF_URL."/sources/js/admin/components-page.js",array('jquery'));
            }else{
                wp_register_script('component-page-script',WBF_URL."/admin/js/components-page.min.js",array('jquery'));
            }
            wp_enqueue_script('component-page-script');
        }
    }

	/**
	 * Detect the components in the their directory and update the registered component WP option
	 *
	 * @param $components_directory
	 * @param bool $child_theme
	 *
	 * @return mixed|void
	 */
	static function _detect_components( $components_directory, $child_theme = false ) {
		$registered_components = $child_theme ? self::get_child_registered_components() : self::get_waboot_registered_components();

		//Unset deleted components
		foreach ( $registered_components as $name => $data ) {
			if ( ! is_file( $data['file'] ) ) {
				unset( $registered_components[ $name ] );
			}
		}

		$components_files = listFolderFiles( $components_directory );
		foreach ( $components_files as $file ) {
			//$component_data = get_plugin_data($file);
			$component_data = self::get_component_data( $file );
			if ( $component_data['Name'] != "" ) {
				//The component is valid, now checks if is already in registered list
				$component_name = basename( dirname( $file ) );
				if ( $component_name == "components" ) { //this means that the component file is in root directory
					$pinfo          = pathinfo( $file );
					$component_name = $pinfo['filename'];
				}
				if ( ! array_key_exists( $component_name, $registered_components ) ) {
					$registered_components[ $component_name ] = array(
						'nicename'        => $component_name,
						'file'            => $file,
						'child_component' => $child_theme,
						'enabled'         => false
					);
				}
			}
		}
		if ( ! $child_theme ) {
			self::update_waboot_registered_components( $registered_components );
		} //update the WP Option of registered component
		else {
			self::update_child_registered_components( $registered_components );
		} //update the WP Option of registered component

		return $registered_components;
    }

	/**
	 * Get the value of "{$template_name}_registered_components" option (default to empty array). $template_name is the current active template.
	 * @return mixed|void
	 */
	static function get_child_registered_components() {
		$template_name = basename( get_stylesheet_directory_uri() );

		return get_option( "{$template_name}_registered_components", array());
	}

	/**
	 * Get the value of "waboot_registered_components" option (default to empty array)
	 * @return mixed|void
	 */
	static function get_waboot_registered_components() {
		return get_option( "waboot_registered_components", array());
    }

	/**
	 * Get the component metadata from the beginning of the file. Mimics the get_plugin_data() WP funtion.
	 * @param $component_file
     * @return array
	 */
	static function get_component_data( $component_file ) {
		$default_headers = array(
			'Name'         => 'Component Name',
			'Version'      => 'Version',
			'Description'  => 'Description',
			'Author'       => 'Author',
			'AuthorURI'    => 'Author URI',
			'ComponentURI' => 'Component URI',
		);

		$component_data = get_file_data( $component_file, $default_headers );

		return $component_data;
	}

    /**
     * Get the possibile paths for a component named $c_name. The component does not have to exists.
     * @param $c_name
     * @return array
     */
    static function generate_component_mainfile_path($c_name){
        $core_dir = waboot_get_root_components_directory();
        $child_dir = waboot_get_child_components_directory();

        $c_name = strtolower($c_name);

        return array(
            'core' => $core_dir.$c_name."/$c_name.php",
            'child' => $core_dir.$c_name."/$c_name.php"
        );
    }

	/**
	 * Update the "waboot_registered_components" option
	 *
	 * @param $registered_components
	 */
	static function update_waboot_registered_components( $registered_components ) {
		update_option( "waboot_registered_components", $registered_components );
    }

	/**
	 * Update the "{$template_name}_registered_components" option, where $template_name is the current active template.
	 * @param $registered_components
	 */
	static function update_child_registered_components( $registered_components ) {
		$template_name = basename( get_stylesheet_directory_uri() );
		update_option( "{$template_name}_registered_components", $registered_components );
	}

	static function add_menu() {
		add_submenu_page( "waboot_options", __( "Waboot Components", "wbf" ), __( "Components", "wbf" ), "activate_plugins", "waboot_components", "Waboot_ComponentsManager::components_admin_page", "", 66 );
    }

	/**
	 * Exec the setup() method on registered components
	 */
	static function setupRegisteredComponents() {
		$components = self::getAllComponents();
		foreach ( $components as $c ) {
			if ( self::is_active( $c ) ) {
				require_once( $c['file'] );
				$className  = ucfirst( $c['nicename'] ) . "Component";
				$oComponent = new $className( $c );
				$oComponent->setup();
            }
		}
	}

    static function addRegisteredComponentOptions(){
        $components = self::getAllComponents();
        foreach ( $components as $c ) {
            if ( self::is_active( $c ) ) {
                require_once( $c['file'] );
                $className  = ucfirst( $c['nicename'] ) . "Component";
                $oComponent = new $className( $c );
                $oComponent->register_options();
            }
        }
    }

	/**
	 * Returns and array of components data (aka in array mode, this do not retrive Waboot_Component)
	 * @return array
	 */
	static function getAllComponents() {
		global $registered_components;
		if ( ! empty( $registered_components ) ) {
			return $registered_components;
		} else {
            $components = self::retrieve_components();
            self::update_global_components_vars();
			return $components;
		}
	}

    /**
     * Gets all registered components (in array mode). Maybe this is a duplicate of getAllComponents()
     * @return array
     */
    static function getRegisteredComponents(){
        $registered_components = array();

        $main_components = self::get_waboot_registered_components();
        $registered_components = array_merge($registered_components,$main_components);
        if(is_child_theme()){
            $child_components = self::get_child_registered_components();
            $registered_components = array_merge($child_components,$main_components);
        }

        return $registered_components;
    }

    static function update_global_components_vars(){
        global $registered_components;
        $registered_components = self::retrieve_components();
    }

    static function retrieve_components(){
        $core_components  = self::get_waboot_registered_components();
        $child_components = is_child_theme() ? self::get_child_registered_components() : array();
        if ( is_child_theme() ) {
            foreach ( $core_components as $name => $comp ) {
                if ( array_key_exists( $name, $child_components ) ) {
                    $child_components[ $name ]['override'] = true;
                    //unset($child_components[$name]); //todo: per ora, non permettere la sovrascrizione
                }
            }
            $components = array_merge( $core_components, $child_components ); //this will override core_components with child_components with same name
        } else {
            /*foreach($core_components as $name => $comp){
                if(in_array($name,$child_components)){
                    unset($child_components[$name]);
                }
            }*/
            $components = $core_components;
        }

        return $components;
    }

	/**
	 * Check if the registered component is active (the component must exists)
	 * @param String|Array $component (l'array può essere ottenuto da get_option("waboot_registered_components"))
	 * @return bool
	 */
	static function is_active( $component ) {

        if(is_array($component)){
            if ( $component['enabled'] == true ) {
                return true;
            }
        }else{
            $registered_components = self::getAllComponents();
            if(isset($registered_components[$component]) && $registered_components[$component]['enabled']){
                return true;
            }
        }

		return false;
	}

    /**
     * Checks if the main file of the $component is present
     * @param $component
     * @return bool
     */
    static function is_present( $component ){
        if(is_array($component)){
            if(is_file($component['file'])) return true;
        }else{
            $registered_components = self::getAllComponents();
            if(isset($registered_components[$component]) && is_file($registered_components[$component]['file'])) return true;
        }
        return false;
    }

	/**
	 * Exec onInit(), scripts() and styles() methods on registered components
	 */
	static function enqueueRegisteredComponent( $action ) {
		$components = self::getAllComponents();
		foreach ( $components as $c ) {
			if ( self::is_active( $c ) ) {
				require_once( $c['file'] );
				$className  = ucfirst( $c['nicename'] ) . "Component";
				$oComponent = new $className( $c );
				if ( self::is_enable_for_current_page( $oComponent ) ) {
					self::addLoadedComponent( $oComponent );
					switch ( $action ) {
						case "wp":
							$oComponent->onInit();
							break;
						case "wp_enqueue_scripts":
							$oComponent->scripts();
							$oComponent->styles();
							break;
					}
				}
			}
		}
	}

	/**
	 * Checks if the component is allowed for the page\post being displayed
	 * @param Waboot_Component $c
	 * @return bool
	 */
	static function is_enable_for_current_page( Waboot_Component $c ) {
		global $post;

		if ( is_admin() )
			return false;

		if ( empty( $c->filters ) ) {
			return false;
		}

		if ( $c->filters['node_id'] == "*" ) {
			return true;
		} else {
			$current_post_type = get_post_type( $post->ID );
			if ( is_home() ) {
				$current_post_id = get_option( "page_for_posts" );
			} else {
				$current_post_id = $post->ID;
			}
			if ( in_array( $current_post_id, $c->filters['node_id'] ) || in_array( $current_post_type, $c->filters['post_type'] ) ) {
				return true;
			} else {
				return false;
			}
		}
	}

	static function addLoadedComponent( Waboot_Component $c ) {
		global $loaded_components;
		if ( ! in_array( $c->name, $loaded_components ) ) {
			$loaded_components[ $c->name] = $c;
		}
	}

    /**
     * Enable or disable components if necessary
     */
    static function toggle_components(){
        global $plugin_page;
        if(is_admin() && isset($_GET['page']) && $_GET['page'] == "waboot_components"){
            if ( isset( $_GET['enable'] ) ) {
                $component_name = $_GET['enable'];
                try {
                    self::enable( $component_name, Waboot_ComponentsManager::is_child_component( $component_name ) );
                } catch ( Exception $e ) {
                    self::$last_error = $e->getMessage();
                }
            } elseif ( isset( $_GET['disable'] ) ) {
                $component_name = $_GET['disable'];
                try {
                    self::disable( $component_name, Waboot_ComponentsManager::is_child_component( $component_name ) );
                } catch ( Exception $e ) {
                    self::$last_error = $e->getMessage();
                }
            } elseif( isset( $_POST['submit-components-options']) ){
				$registered_components = self::getAllComponents();
	            $registered_components_status = isset($_POST['components_status']) ? $_POST['components_status'] : array();
	            foreach($registered_components as $component_name => $component_data){
		            if(!array_key_exists($component_name,$registered_components_status)){
			            $registered_components_status[$component_name] = "off";
		            }
	            }
	            foreach($registered_components_status as $component_name => $component_status){
					if($component_status == "on" ){
						if(!self::is_active($registered_components[$component_name])){
							self::enable( $component_name, Waboot_ComponentsManager::is_child_component( $component_name ) );
						}
					}else{
						if(self::is_active($registered_components[$component_name])){
							self::disable( $component_name, Waboot_ComponentsManager::is_child_component( $component_name ) );
						}
					}
				}
            }
        }
    }

	static function components_admin_page() {

        if( (isset($_GET['enable']) || isset($_GET['disable'])) && !empty(self::$last_error) ){
            ?>
            <div class="error">
                <p><?php echo self::$last_error; ?></p>
            </div>
            <?php
        }

        if(isset($_POST['reset'])){
            WBF::reset_components_state();
        }

		$registered_components = self::getAllComponents();

        if(isset($_POST['submit-components-options'])){
            $of_config = get_option( 'optionsframework' );
            $of_options = get_option( $of_config['id'] );
	        $must_update = false;
            if(isset($_POST[$of_config['id']])){
                foreach($_POST[$of_config['id']] as $opt_name => $opt_value){
	                preg_match("/^([a-zA-Z0-9]+)_/",$opt_name,$matches);
	                $component_name = $matches[1];
	                $component_data = $registered_components[$component_name];
	                if($component_data && self::is_active($component_data)){
		                $of_options[$opt_name] = $opt_value;
		                $must_update = true;
	                }
                }
            }
	        if($must_update)
                update_option($of_config['id'],$of_options);

            //Set the flag that tells that the components was saved at least once
            $theme = wp_get_theme();
            $components_already_saved = (array) get_option( "wbf_components_saved_once", array() );
            $components_already_saved[] = $theme->get_stylesheet();
            update_option("wbf_components_saved_once", $components_already_saved);
        }

        $components_options = apply_filters("wbf_components_options",array());
        $compiled_components_options = array();
        $current_element = "";
        foreach($components_options as $key => $option){
            if($option['type'] == "heading"){
                $current_element = preg_replace("/ Component/","",$option['name']);
                $compiled_components_options[$current_element] = array();
                continue;
            }
            $compiled_components_options[$current_element][] = $components_options[$key];
        }

		?>
		<div id="componentframework-wrapper" class="wrap">
            <div class="componentframework-header">
                <h2><?php _e( "Components", "wbf" ); ?></h2>
            </div>

            <div id="componentframework-content-wrapper">
                <div class="nav-tab-wrapper">
                    <ul>
                        <li><a class="nav-tab" id="component-main-tab" data-show-comp-settings='component-main' href="#component-main">Available components</a></li>
                        <?php foreach($registered_components as $comp_data): if(self::is_active($comp_data)) : ?>
                        <li><a class="nav-tab" id="component-<?php echo $comp_data['nicename']; ?>-link" data-show-comp-settings='component-<?php echo $comp_data['nicename']; ?>' href="#component-<?php echo $comp_data['nicename']; ?>"><?php echo ucfirst($comp_data['nicename']); ?></a></li>
                        <?php endif; endforeach; ?>
                    </ul>
                </div>
                <div id="componentframework-metabox" class="metabox-holder">
                    <div id="componentframework" class="postbox">
                        <form method="post" action="admin.php?page=waboot_components">
                        <div id="component-main" class="group">
                            <table class="wp-list-table widefat components">
                                <thead>
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col" id="name"
                                        class="manage-column column-name"><?php _e( "Component", "wbf" ) ?></th>
                                    <th scope="col" id="description"
                                        class="manage-column column-description"><?php _e( "Enable\Disable", "wbf" ) ?></th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col" id="name"
                                        class="manage-column column-name"><?php _e( "Component", "wbf" ) ?></th>
                                    <th scope="col" id="description"
                                        class="manage-column column-description"><?php _e( "Enable\Disable", "wbf" ) ?></th>
                                </tr>
                                </tfoot>
                                <tbody id="the-list">
                                <?php $i=1; foreach ( $registered_components as $comp_data ) : ?>
                                    <tr id="<?php echo $comp_data['nicename']; ?>" <class="<?php print_component_status( $comp_data ); ?> <?php if($i%2 == 0) echo "even"; else echo "odd"; ?>">
                                        <?php
                                            //$data = get_plugin_data($comp_data['file']);
                                            $data = self::get_component_data( $comp_data['file'] );
                                        ?>
                                        <th></th>
                                        <th class="component-data column-description desc">
                                            <strong><?php echo $data['Name']; ?></strong>
	                                        <div class="component-description">
		                                        <?php echo $data['Description']; ?>
		                                        <?php if ( self::is_child_component( $comp_data ) ) : ?>
			                                        <p class="child-component-notice">
				                                        <?php _e( "This is a component of the current child theme", "wbf" ); ?>
				                                        <?php
				                                        if ( isset( $comp_data['override'] ) ) {
					                                        if ( $comp_data['override'] ) {
						                                        _e( ", and <strong>override a core component</strong>", "wbf" );
					                                        }
				                                        }
				                                        ?>
			                                        </p>
		                                        <?php endif; ?>
	                                        </div>
	                                        <div class="<?php print_component_status($comp_data); ?> second plugin-version-author-uri">
		                                        <?php
		                                        $component_meta = array();
		                                        if ( ! empty( $data['Version'] ) ) {
			                                        $component_meta[] = sprintf( __( 'Version %s' ), $data['Version'] );
		                                        }
		                                        if ( ! empty( $data['Author'] ) ) {
			                                        $author = $data['Author'];
			                                        if ( ! empty( $data['AuthorURI'] ) ) {
				                                        $author = '<a href="' . $data['AuthorURI'] . '" title="' . esc_attr__( 'Visit author homepage' ) . '">' . $data['Author'] . '</a>';
			                                        }
			                                        $component_meta[] = sprintf( __( 'By %s' ), $author );
		                                        }
		                                        if ( ! empty( $plugin_data['PluginURI'] ) ) {
			                                        $component_meta[] = '<a href="' . $data['ComponentURI'] . '" title="' . esc_attr__( 'Visit plugin site' ) . '">' . __( 'Visit plugin site' ) . '</a>';
		                                        }

		                                        echo implode( ' | ', $component_meta );

		                                        ?>
	                                        </div>
                                        </th>
                                        <th class="component-actions">
	                                        <div class="row-actions visible">
		                                        <div class="onoffswitch">
		                                            <?php if ( ! self::is_active( $comp_data ) ) : ?>
			                                        <input id="<?php echo $comp_data['nicename']; ?>_status" class="checkbox of-input onoffswitch-checkbox" type="checkbox" name="components_status[<?php echo $comp_data['nicename']; ?>]" >
		                                            <?php else: ?>
			                                        <input id="<?php echo $comp_data['nicename']; ?>_status" class="checkbox of-input onoffswitch-checkbox" type="checkbox" name="components_status[<?php echo $comp_data['nicename']; ?>]" checked="checked">
		                                            <?php endif; ?>
			                                        <label class="onoffswitch-label" for="<?php echo $comp_data['nicename']; ?>_status"><span class="onoffswitch-inner"></span>
				                                        <span class="onoffswitch-switch"></span>
			                                        </label>
		                                        </div>
	                                        </div>
                                        </th>
                                    </tr>
                                <?php $i++; endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php foreach($registered_components as $comp_data): if(self::is_active($comp_data)) : ?>
                        <div id="component-<?php echo $comp_data['nicename']; ?>" class="group" style="display: none;">
                            <h3><?php _e(sprintf("%s Component Settings",ucfirst($comp_data['nicename'])),"wbf"); ?></h3>
                            <?php Waboot_Options_Interface::optionsframework_fields($compiled_components_options[$comp_data['nicename']]); ?>
                        <!-- </div> not necessary (for some reason) -->
                        <?php endif; endforeach; ?>
                        <div id="componentframework-submit">
                            <input type="submit" name="submit-components-options" id="submit" class="button button-primary" value="Save Changes">
                            <input type="submit" class="reset-button button-secondary" name="reset" value="<?php esc_attr_e( 'Restore default component activation state', 'wbf' ); ?>" onclick="return confirm( '<?php print esc_js( __( 'Click OK to reset. Any theme settings will be lost!', 'wbf' ) ); ?>' );" />
                        </div>
                        </form>
                    </div>
                </div><!-- #componentframework-content -->
            </div><!-- #componentframework-wrap -->
			<?php WBF::print_copyright(); ?>
		</div><!-- .wrap: end -->
	<?php
	}

    /**
     * Force enable a component
     * @param $component_name
     * @throws Exception
     */
    static function ensure_enabled( $component_name ){
        if(self::is_present($component_name)){
            if(!self::is_active($component_name)){
                self::enable($component_name, Waboot_ComponentsManager::is_child_component( $component_name ));
            }
        }
    }

	/**
	 * Enable a component or throw an error
	 * @param $component_name
	 * @param bool $child_component
	 * @throws Exception
	 */
	static function enable( $component_name, $child_component = false ) {
		//chiamo onActivate() del componente
		$registered_components = ! $child_component ? self::get_waboot_registered_components() : self::get_child_registered_components();
		if ( array_key_exists( $component_name, $registered_components ) ) {
			$component = $registered_components[ $component_name ];
			require_once( $component['file'] );
			$className = ucfirst( $component_name ) . "Component";
			if ( class_exists( $className ) ) {
				$oComponent = new $className( $component );
				$oComponent->onActivate();
				$oComponent->active = true;
				$registered_components[ $component_name ]['enabled'] = true;
				if ( ! $child_component ) {
					self::update_waboot_registered_components( $registered_components );
				} //update the WP Option of registered component
				else {
					self::update_child_registered_components( $registered_components );
				} //update the WP Option of registered component
                self::update_global_components_vars();
			} else {
				throw new Exception( __( "Component class not defined. Unable to activate the component.", "wbf" ) );
			}
		} else {
			throw new Exception( __( "Component not found among registered components. Unable to activate the component.","wbf" ) );
		}
	}

	/**
	 * Disable a component ot throw an error
	 * @param $component_name
	 * @param bool $child_component
	 * @throws Exception
	 */
	static function disable( $component_name, $child_component = false ) {
		//chiamo onDeactivate() del componente
		$registered_components = ! $child_component ? self::get_waboot_registered_components() : self::get_child_registered_components();
		if ( array_key_exists( $component_name, $registered_components ) ) {
			$component = $registered_components[ $component_name ];
			require_once( $component['file'] );
			$className = ucfirst( $component_name ) . "Component";
			if ( class_exists( $className ) ) {
				$oComponent = new $className( $component );
				$oComponent->onDeactivate();
				$oComponent->active = false;
			}
			$registered_components[ $component_name ]['enabled'] = false; //If there is no class defined (eg. due to an previous error), then simply disable the component
			if ( ! $child_component ) {
				self::update_waboot_registered_components( $registered_components );
			} //update the WP Option of registered component
			else {
				self::update_child_registered_components( $registered_components );
			} //update the WP Option of registered component
            self::update_global_components_vars();
		} else {
			throw new Exception( __( "Component not found among registered components. Unable to deactivate the component.","wbf"));
        }
    }

    static function is_child_component( $registered_component ) {
        if ( is_array( $registered_component ) ) {
            if ( $registered_component['child_component'] == true ) {
                return true;
            }
        } else {
            $components = Waboot_ComponentsManager::getAllComponents();
            foreach ( $components as $name => $c ) {
                if ( $name == $registered_component ) {
                    if ( $c['child_component'] == true ) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Delete the options which stores the registere components
     */
    static function reset_registered_components(){
        delete_option("waboot_registered_components");
        if(is_child_theme()){
            $template_name = basename(get_stylesheet_directory_uri());
            delete_option( "{$template_name}_registered_components");
        }
    }
}

class Waboot_Component {

    var $name;
    var $active;
    var $file;
    var $files;
    var $is_child_component;
    var $directory_uri;

    //Se il filtro è su * il componente viene caricato sempre, altrimenti solo nelle robe specificate
    var $filters = array(
        'post_type' => '*',
        'node_id' => '*'
    );

    public function __construct($component){
        $this->name = $component['nicename'];
        $this->active = $component['enabled'];
        $this->file = $component['file'];
        $this->is_child_component = $component['child_component'];
        if($this->is_child_component){
            $this->directory_uri = waboot_get_child_components_directory_uri()."/".$this->name;
        }else{
            $this->directory_uri = waboot_get_root_components_directory_uri()."/".$this->name;
        }

        //Detect the filters
        if(of_get_option($this->name."_selective_disable","0") == 1){
            $this->filters = array();
        }elseif(of_get_option($this->name."_enabled_for_all_pages","1") == 1){
            $this->filters = array(
                'post_type' => '*',
                'node_id' => '*'
            );
        }else{
            $this->filters = array(
                'post_type' => array(),
                'node_id' => array()
            );
            $allowed_post_types = of_get_option($this->name."_load_locations",array());
            if($allowed_post_types['front'] == 1){
                array_push($this->filters['node_id'],get_option("page_on_front"));
                unset($allowed_post_types['front']);
            }
            if($allowed_post_types['home'] == 1){
                array_push($this->filters['node_id'],get_option("page_for_posts"));
                unset($allowed_post_types['home']);
            }
            foreach($allowed_post_types as $k => $val){
                if($val == 1){
                    array_push($this->filters['post_type'],$k);
                }
            }
            $specific_ids = of_get_option($this->name."_load_locations_ids",array());
            if(!empty($specific_ids)){
                $specific_ids = explode(',',trim($specific_ids));
                foreach($specific_ids as $id){
                    $id = trim($id);
                    if(!in_array($id,$this->filters['node_id']))
                        array_push($this->filters['node_id'],$id);
                }
            }
        }
    }

    /**
     * Metodo che verrà automaticamente chiamato per ogni componente registrato durante l'init
     */
    public function setup(){}

    public function register_options(){
        add_filter("of_options",array($this,"theme_options"));
        add_filter("wbf_components_options",array($this,"theme_options"));
    }

    public function onInit(){}

    public function scripts(){}

    public function styles(){}

	public function theme_options($options){
		$options[] = array(
			'name' => $this->name." Component",
			'type' => 'heading',
			'component' => true
		);

		$options[] = array(
			'name' => __( 'Enable on all pages', 'waboot' ),
			'desc' => __( 'Check this box to load the component in every page (load locations will be ignored).', 'waboot' ),
			'id'   => $this->name.'_enabled_for_all_pages',
			'std'  => '1',
			'type' => 'checkbox'
		);

		$filter_locs = array_merge(array("front"=>"Frontpage","home"=>"Blog"),wp_get_filtered_post_types());

		$options[] = array(
			'id' => $this->name.'_load_locations',
			'name' => __('Load locations','waboot'),
			'desc' => __('Where to load the component', 'waboot'),
			'type' => 'multicheck',
			'options' => $filter_locs
		);

        $options[] = array(
            'id' => $this->name.'_load_locations_ids',
            'name' => __('Load locations by ID','waboot'),
            'desc' => __('You can load the component for specific pages by enter here the respective ids (comma separated)'),
            'type' => 'text'
        );

		return $options;
	}

    public function onActivate(){
        //echo "Attivato: $this->name";
        add_action( 'admin_notices', array($this,'activationNotice') );
	    $this->register_options();
    }

    public function onDeactivate(){
        //echo "Disattivato: $this->name";
        add_action( 'admin_notices', array($this,'deactivationNotice') );
    }

    public function activationNotice(){
        ?>
        <div class="updated">
            <p><?php _e( sprintf("Activated: %s",$this->name), "wbf" ); ?></p>
        </div>
        <?php
    }

    public function deactivationNotice(){
        ?>
        <div class="updated">
            <p><?php _e( sprintf("Deactivated: %s",$this->name), "wbf" ); ?></p>
        </div>
        <?php
    }

    /**
     * Retrive a file from component directory
     * @param $filepath
     * @return string
     */
    public function file($filepath){
        if(is_child_theme()){
            $child_file = waboot_get_child_components_directory_uri().$this->name."/".$filepath;
            $child_file_path = url_to_path($child_file);
            if(is_file($child_file_path)){
                return $child_file;
            }
        }
        return $this->directory_uri."/".$filepath;
    }
}

/** HELPERS */

function waboot_get_root_components_directory_uri(){
    return get_template_directory_uri()."/components/";
}

function waboot_get_child_components_directory_uri(){
    return get_stylesheet_directory_uri()."/components/";
}

function waboot_get_root_components_directory(){
    return get_template_directory()."/components/";
}

function waboot_get_child_components_directory(){
    return get_stylesheet_directory()."/components/";
}

function print_component_status($comp_data){
	if ( Waboot_ComponentsManager::is_active( $comp_data ) ) {
		echo "active";
	} else {
		echo "inactive";
	}
}