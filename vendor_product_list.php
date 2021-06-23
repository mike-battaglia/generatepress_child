<?php
if(!function_exists('wc_get_products')) {
			return;
		  }

	  $paged                   = (get_query_var('paged')) ? absint(get_query_var('paged')) : 1;
	  
	  //$ordering['orderby']     = stristr($ordering['orderby'], 'price') ? 'meta_value_num' : $ordering['orderby'];
	  //$products_per_page       = apply_filters('loop_shop_p0er_page', wc_get_default_products_per_row() * wc_get_default_product_rows_per_page());

	  $featured_products       = wc_get_products(array(
		'author'             => $vendor_id,
		'exclude'=>array($post->ID),
		'status'               => 'publish',
		'limit'                => 12,
		//'page'                 => $paged,
		//'featured'             => true,
		'paginate'             => true,
		'return'               => 'ids',
		'orderby'              => 'ID',
		'order'                => 'DESC',
	  ));

	  wc_set_loop_prop('current_page', $paged);
	  //wc_set_loop_prop('is_paginated', wc_string_to_bool(true));
	  wc_set_loop_prop('page_template', get_page_template_slug());
	  //wc_set_loop_prop('per_page', $products_per_page);
	  wc_set_loop_prop('total', $featured_products->total);
	  wc_set_loop_prop('total_pages', $featured_products->max_num_pages);

	  if($featured_products) {
		//do_action('woocommerce_before_shop_loop');
		woocommerce_product_loop_start();
		  foreach($featured_products->products as $featured_product) {
			$post_object = get_post($featured_product);
			setup_postdata($GLOBALS['post'] =& $post_object);
			wc_get_template_part('content', 'product');
		  }
		  wp_reset_postdata();
		woocommerce_product_loop_end();
		//do_action('woocommerce_after_shop_loop');
	  } else {
		do_action('woocommerce_no_products_found');
	  }
