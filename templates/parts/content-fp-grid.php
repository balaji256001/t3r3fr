<?php
/** Featured posts grid template
 * @since Waboot 1.0
 */
?>
	<li>
		<div class="innergrid">
            <?php do_action( 'waboot_entry_header' ); ?>
			<?php if ( has_post_thumbnail() ) : ?>
				<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Link to %s', 'waboot' ), the_title_attribute( 'echo=0' ) ); ?>"><?php echo get_the_post_thumbnail( ''. $post->ID .'', array(of_get_option('waboot_featured_posts_image_width'), of_get_option('waboot_featured_posts_image_height')), array('title' => "" )); ?></a>
			<?php else : ?>
				<?php the_excerpt(); ?>
			<?php endif; ?>
		</div><!-- #innergrid -->
		<div class="grid-footer">
			<p class="grid-footer-meta">
				<?php waboot_do_posted_on(); ?>
			</p>
		</div><!-- #grid-footer -->
	</li>