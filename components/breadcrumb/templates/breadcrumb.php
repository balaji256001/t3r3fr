<?php
if($is_woocommerce){
	woocommerce_breadcrumb([
		'wrap_before'   => '<div class="breadcrumb-trail breadcrumbs" itemprop="breadcrumb"><div class="container">',
		'wrap_after'   => '</div></div>',
		'delimiter'  => '<span class="sep">&nbsp;&#47;&nbsp;</span>'
	]);
}else{
	\Waboot\template_tags\breadcrumb(null, 'before_inner', ['wrapper_start' => '<div class="container">', 'wrapper_end' => '</div>']);
}