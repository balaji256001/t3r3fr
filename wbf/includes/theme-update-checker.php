<?php

namespace WBF\includes;

class Theme_Update_Checker extends \ThemeUpdateChecker{
	/**
	 * Class constructor.
	 *
	 * @param string $theme Theme slug, e.g. "twentyten".
	 * @param string $metadataUrl The URL of the theme metadata file.
	 * @param boolean $enableAutomaticChecking Enable/disable automatic update checking. If set to FALSE, you'll need to explicitly call checkForUpdates() to, err, check for updates.
	 */
	public function __construct($theme, $metadataUrl, $enableAutomaticChecking = true){
		$this->metadataUrl = $metadataUrl;
		$this->enableAutomaticChecking = $enableAutomaticChecking;
		$this->theme = $theme;
		$this->optionName = 'external_theme_updates-'.$this->theme;

		if(!$this->automaticCheckDone)
			update_option("wbf_unable_to_update",false);

		if(\WBF\admin\License_Manager::get_license_status() == "Active"){
			$this->installHooks();
		}else{
			$state = $this->requestUpdate();
			if(!is_null($state) && !$this->automaticCheckDone){
				update_option("wbf_unable_to_update",true);
				add_action( 'admin_notices', array($this,'update_available_notice') );
			}
			$this->automaticCheckDone = true;
		}
	}

	public function update_available_notice(){
		$unable_to_update = get_option("wbf_unable_to_update",false);
		if($unable_to_update) :
		?>
		<div class="waboot-upgrade-notice update-nag">
			<?php echo sprintf(__( 'A new version of Waboot is available! <a href="%s" title="Enter a valid license">Enter a valid license</a> to get latest updates.', 'wbf' ),"admin.php?page=waboot_license"); ?>
		</div>
		<?php endif;
	}
}