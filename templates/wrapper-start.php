<?php
$main_wrapper_vars = \Waboot\functions\get_main_wrapper_template_vars();
?>
<div id="main-wrapper" class="<?php echo $main_wrapper_vars['classes']; ?>">
	<div class="main-inner">
		<?php
		/*
		 * main-top zone
		 */
		Waboot()->layout->render_zone("main-top");
		?>
		<?php do_action("waboot/site-main/before"); ?>
		<div class="<?php \Waboot\template_tags\container_classes(); ?>">
			<div class="row">