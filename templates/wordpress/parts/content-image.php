<article role="article" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php //do_action( 'waboot/entry/header' ); ?>
	<div class="entry-content">
		<?php
		// If we have a featured image
		if(has_post_thumbnail()): ?>
			<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Link to %s', 'waboot' ), the_title_attribute( 'echo=0' ) ); ?>">
				<?php echo get_the_post_thumbnail( $post->ID, 'medium', array( 'class' => 'alignnone', 'title' => "" ) ); ?>
			</a>
		<?php endif; ?>
		<?php the_content(); ?>
		<?php wp_link_pages(); ?>
	</div><!-- .entry-content -->
	<?php do_action( 'waboot/entry/footer' ); ?>
</article><!-- #post-<?php the_ID(); ?> -->