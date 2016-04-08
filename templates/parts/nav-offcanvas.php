<?php
/**
 * The template used to load the Mobile Menu in header*.php
 *
 * @package Waboot
 * @since Waboot 1.0
 */
?>

<!-- Mobile menu -->
<div class="collapse navbar-collapse navbar-mobile-collapse offcanvas">

    <?php if ( of_get_option( 'waboot_logo_mobilenav', '1' ) ) : ?>
	    <?php
	        $navbar_mobile_logo = waboot_get_mobile_logo("offcanvas");
	        $mobile_logo = waboot_get_mobile_logo();
	        $desktop_logo = waboot_get_desktop_logo();
	    ?>
	    <div class="logo-menu">
	        <?php if ( $navbar_mobile_logo != "" ) : ?>
	            <?php waboot_mobile_logo("offcanvas",true); ?>
	        <?php elseif( $mobile_logo != "") : ?>
		        <?php waboot_mobile_logo("header",true); ?>
		    <?php elseif( $desktop_logo != "") : ?>
		        <?php waboot_desktop_logo(true); ?>
	        <?php else : ?>
	            <?php
		            waboot_site_title();
		            waboot_site_description();
		        ?>
	        <?php endif; ?>
	    </div>
    <?php endif; ?>

    <?php wp_nav_menu( array(
        'theme_location' => 'main',
        'depth'          => 0,
        'container'      => false,
        'menu_class'     => 'nav navbar-nav',
        'walker'	     => new WabootNavMenuWalker(),
        'fallback_cb' => 'waboot_nav_menu_fallback'
    )); ?>


    <?php wp_nav_menu( array(
        'theme_location' => 'top',
        'depth' => 0,
        'container' => false,
        'menu_class' => 'nav navbar-nav',
        'walker' => new WabootNavMenuWalker(),
        'fallback_cb' => 'waboot_nav_menu_fallback'
    )); ?>


    <?php if ( of_get_option( 'waboot_search_bar', '1' ) ) : ?>
        <form id="searchform" class="navbar-form" role="search" action="<?php echo site_url(); ?>" method="get">
            <div class="form-group">
                <input id="s" name="s" type="text" class="form-control" placeholder="<?php esc_attr_e( 'Search &hellip;', 'waboot' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>">
            </div>
            <button id="searchsubmit" type="submit" name="submit" class="btn btn-default">Submit</button>
        </form>
    <?php endif; ?>
</div>
<!-- End Mobile menu -->