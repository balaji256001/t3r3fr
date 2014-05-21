<?php
/**
 * The main template file.
 *
 * @package Waboot
 * @since Alien Ship 0.1
 */

get_header(); ?>
		<div id="primary" class="<?php echo apply_filters( 'waboot_primary_container_class', 'content-area col-sm-8' ); ?>">
			<?php do_action( 'waboot_main_before' ); ?>
			<main id="main" class="site-main" role="main">
				<?php if ( have_posts() ) : ?>
                    <?php
                        waboot_content_nav( 'nav-above' ); // display content nav above posts

                        /**
                         * Featured Posts
                         */
                        if ( of_get_option('alienship_featured_posts') ) {

                            if ( of_get_option( 'alienship_featured_posts_display_type', 1 ) == "1" ) {
                                waboot_featured_posts_slider();
                            } else {
                                waboot_featured_posts_grid();
                            }

                            /**
                             * Show or hide featured posts in the main post index
                             */
                            // Do not duplicate featured posts in the post index
                            if ( of_get_option( 'alienship_featured_posts_show_dupes' ) == "0" ) {
                                global $wp_query;
                                $wp_query->set( 'tag__not_in', array( of_get_option( 'alienship_featured_posts_tag' ) ) );
                                $wp_query->get_posts();
                            }

                            // Duplicate featured posts in the post index
                            if ( of_get_option( 'alienship_featured_posts_show_dupes' ) == "1" ) {
                                global $wp_query;
                                $wp_query->set( 'post_status', 'publish' );
                                $wp_query->get_posts();
                            }

                        } // if (of_get_option('alienship_featured_posts') )

                        /**
                         * Loop
                         */
                        while ( have_posts() ) {
                            the_post();

                            do_action( 'waboot_loop_before' );

                            /* Include the Post-Format-specific template for the content.
                             * If you want to override this in a child theme then include a file
                             * called content-___.php (where ___ is the Post Format name) and that will be used instead.
                             */
                            get_template_part( '/templates/parts/content', get_post_format() );

                            do_action( 'waboot_loop_after' );
                        }

                        waboot_content_nav( 'nav-below' ); // display content nav below posts?
                    ?>
			<?php else: ?>
            <?php
				// No results
				get_template_part( '/templates/parts/content', 'none' );
            ?>
			<?php endif; //have_posts ?>
		</main><!-- #main -->
		<?php do_action( 'waboot_main_after' ); ?>
	</div><!-- #primary -->
<?php
    get_sidebar();
    get_footer();
?>