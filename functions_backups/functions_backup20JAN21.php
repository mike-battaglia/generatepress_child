<?php
/*
* GeneratePress child theme functions and definitions.
*
* Add your custom PHP in this file. 
* Only edit this file if you have direct access to it on your server (to fix errors if they happen).
*/

function generatepress_child_enqueue_scripts() {
	if ( is_rtl() ) {
		wp_enqueue_style( 'generatepress-rtl', trailingslashit( get_template_directory_uri() ) . 'rtl.css' );
		
	}
	wp_enqueue_style( 'ec-jquery-ui-datepicker-style' ,'//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css');
	
	wp_enqueue_script( 'jquery-ui-datepicker' );
	wp_enqueue_script( 'password-strength-meter' );

	wp_enqueue_script('ec_validation_script','https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js');
	wp_enqueue_script('ec_custom_script',trailingslashit( get_stylesheet_directory_uri() ) . 'js/custom_script.js?time='.time() );
	wp_localize_script('ec_custom_script','ec_ajax',array( 'ec_ajax_url' => admin_url( 'admin-ajax.php' ),'ec_site_url'=>site_url()));
	
}
add_action( 'wp_enqueue_scripts', 'generatepress_child_enqueue_scripts', 100 );

/* create object of common class */
require_once get_stylesheet_directory().'/class/ec_common_cls.php';
$ec_common = new ec_common_cls();

/* Class for front end changes adding Quote form menu under my account */
require_once get_stylesheet_directory().'/class/ec_frontend_cls.php';
$ec_front = new ec_frontend_cls();

/* function for adding menu and submenu */
function ec_get_product_terms( $term_id ) {   
	$html=""; 
	$args = array( 'hide_empty' => 1, 'parent' => $term_id ,'exclude' =>array(17));    
	$terms = get_terms('product_cat', $args);

	foreach ($terms as $term) {    
		$html .= '<li class="top_li menu-item menu-item-type-taxonomy menu-item-object-product_cat menu-item-has-children menu-item-'.$term->term_id.'">';    
		$html .= '<a href="'.get_term_link($term->slug, 'product_cat').'" class="elementor-item">' . strtoupper($term->name) . '</a>';    
		/*if( $list = ec_get_product_terms( $term->term_id )) {    
			$html .= '<ul class="sub-menu elementor-nav-menu--dropdown sm-nowrap second_level">'.$list.'</ul>';    
		}*/
		$html .= '</li>';    
	}    
	return $html;    
}

/* Filter wp_nav_menu() to add additional links and other output (shop menu with all parent and sub categories which has products) */
add_filter( 'wp_nav_menu_items', 'ec_product_categories_nav_menu_items',10,2 );
function ec_product_categories_nav_menu_items($items,$args) {
	if( $args->menu == 'top-menu' )
	{
		$items .= '<li class="top_li menu-item menu-item-type-taxonomy menu-item-object-product_cat menu-item-has-children menu-item-'.wc_get_page_id('shop').'">';
		$items .= '<a href="'.get_permalink(wc_get_page_id('shop')).'" class="elementor-item">SHOP</a>';        
		$items .= '<ul class="sub-menu elementor-nav-menu--dropdown sm-nowrap">';
		if( $list = ec_get_product_terms( 0 )) {
			$items = $items .$list;
		}
		$items .= '</ul>';
		$items .= '</li>';    
	}
	return $items;
}

/* vendor dashboard class only if the wcmp plugin active */
if ( is_plugin_active( 'dc-woocommerce-multi-vendor/dc_product_vendor.php' ) )
{
	require_once get_stylesheet_directory().'/class/ec_vendor_product_cls.php';
	global $ecVendorProduct;
	$ecVendorProduct = new ec_vendor_product_cls();	
	/* display vendor extra fields on vendor front end(vendor page) in header */
	add_action('after_wcmp_vendor_description','show_vendor_extra_info',10,1);	
	function show_vendor_extra_info($vendor_id)
	{
		global $wp_query,$wpdb;
		if(!$vendor_id)
		{
			$data = get_term_by('slug',$wp_query->query_vars['dc_vendor_shop'],'dc_vendor_shop');
			if($data)
			{
				$vendor_id = $wpdb->get_var("select user_id from ".$wpdb->prefix."usermeta where meta_key='_vendor_term_id' and meta_value='".$data->term_id."'");	
			}
		}
		if(!$vendor_id)
			return;
		
		if(taxonomy_exists('dc_vendor_shop'))
		{
		?>
			<div class="extra_info">
				<?php
					$vendor_statement = get_user_meta($vendor_id, 'vendor_statement', true);
					if($vendor_statement)
					{
				?>
				<!--h4><?php _e('STATEMENT','dc-woocommerce-multi-vendor');?></h4-->
				<p class="text-center"><?php echo substr(nl2br($vendor_statement),0,250)."..."; ?><a href='#' class="view_statement">Read More</a></p>
				<?php
					}
				?>
			</div>
			<?php	
		}
	}

	/* Create 3 tab seaction wrap the product loop section in tab */
	add_action('woocommerce_before_shop_loop','ec_start_wrap_prducts_in_tab_callback',5);
	function ec_start_wrap_prducts_in_tab_callback($vendor_id)
	{
		global $wp_query,$wpdb;
		if(taxonomy_exists('dc_vendor_shop'))
		{
			if(!$vendor_id)
			{
				if(!isset($wp_query->query_vars['dc_vendor_shop']))
					return;
				$data = get_term_by('slug',$wp_query->query_vars['dc_vendor_shop'],'dc_vendor_shop');
				if($data)
				{
					$vendor_id = $wpdb->get_var("select user_id from ".$wpdb->prefix."usermeta where meta_key='_vendor_term_id' and meta_value='".$data->term_id."'");	
				}
			}
			if(!$vendor_id)
				return;
			
			$fname = get_the_author_meta('first_name',$vendor_id);
			$lname = get_the_author_meta('last_name',$vendor_id);
		?>
		<div class="tab_content_wrapper">
			<div class="tab_links">
				<ul>
					<li><a href="#art" class="tab_link active">Art</a></li>
					<!--li><a href="#collection" class="tab_link">Collections</a></li-->
					<li><a href="#vendor_info" class="tab_link">About <?php echo $fname ." ". $lname;?></a></li>
				</ul>
			</div>
			<div class="tab_content_view">
				<div class="show_vendor_art tab_content active" id="art">
				<?php
		}

	}
	/* Create 3 tab seaction wrap the product loop section in tab */
	add_action('woocommerce_after_shop_loop','ec_end_wrap_prducts_in_tab_callback',5);
	function ec_end_wrap_prducts_in_tab_callback($vendor_id)
	{
		global $wp_query,$wpdb;
		if(taxonomy_exists('dc_vendor_shop'))
		{
			
			if(!$vendor_id)
			{
				if(!isset($wp_query->query_vars['dc_vendor_shop']))
					return;
					
				$data = get_term_by('slug',$wp_query->query_vars['dc_vendor_shop'],'dc_vendor_shop');
				if($data)
				{
					$vendor_id = $wpdb->get_var("select user_id from ".$wpdb->prefix."usermeta where meta_key='_vendor_term_id' and meta_value='".$data->term_id."'");	
				}
			}
			if(!$vendor_id)
				return;
			
				$fname = get_the_author_meta('first_name',$vendor_id);
				$lname = get_the_author_meta('last_name',$vendor_id);
			?>
				</div>
			</div>
			<!--div class="show_vendor_collection tab_content" id="collection">
				<?php
					echo do_shortcode('[woo_cl_my_collections user_id="'.$vendor_id.'" collection_title="My Favorites" show_count="true" enable_see_all="true"][/woo_cl_my_collections]');
				?>
			</div-->
			<div class="show_vendor_extra_info tab_content" id="vendor_info">
			<?php
				if(taxonomy_exists('dc_vendor_shop'))
				{
					$accept_commission = get_user_meta($vendor_id, 'accept_commission', true);
					if($accept_commission)
					{
						?>
						<div class="extra_info statement_info"><h4>Artist accepts commissions.</h4></div>
						<?php
					}
					
					$vendor_statement = get_user_meta($vendor_id, 'vendor_statement', true);
					if($vendor_statement)
					{
					?>
					<div class="extra_info statement_info">
						<h4><?php _e('STATEMENT','dc-woocommerce-multi-vendor');?></h4>
						<p><?php echo nl2br($vendor_statement); ?></p>
					</div>
					<?php
					}

					$vendor_bio = get_user_meta($vendor_id, 'vendor_bio', true);
					if($vendor_bio)
					{
						?>
						<div class="extra_info">
							<h4><?php _e('BIO','dc-woocommerce-multi-vendor');?></h4>
							<p><?php echo nl2br($vendor_bio); ?></p>
						</div>
						<?php	
					}
					
					
					$vendor_exhibition = get_user_meta($vendor_id, 'vendor_exhibition', true);
					if($vendor_exhibition)
					{
						?>
						<div class="extra_info">
							<h4><?php _e('EXHIBITIONS/SHOWS/COMMISSIONS','dc-woocommerce-multi-vendor');?></h4>
							<p><?php echo nl2br($vendor_exhibition); ?></p>
						</div>
						<?php	
					}
					$vendor_education = get_user_meta($vendor_id, 'vendor_education', true);
					if($vendor_education)
					{
						?>
						<div class="extra_info">
							<h4><?php _e('EDUCATION','dc-woocommerce-multi-vendor');?></h4>
							<p><?php echo nl2br($vendor_education); ?></p>
						</div>
						<?php	
					}
					$vendor_awards = get_user_meta($vendor_id, 'vendor_awards', true);
					if($vendor_awards)
					{
						?>
						<div class="extra_info">

							<h4><?php _e('AWARDS/DISTINCTIONS','dc-woocommerce-multi-vendor');?></h4>
							<p><?php echo nl2br($vendor_awards); ?></p>
						</div>
						<?php	
					}
					$vendor_professional_background = get_user_meta($vendor_id, 'vendor_professional_background', true);
					if($vendor_professional_background)
					{
						?>
						<div class="extra_info">
							<h4><?php _e('PROFESSIONAL EXPERIENCE','dc-woocommerce-multi-vendor');?></h4>
							<p><?php echo nl2br($vendor_professional_background); ?></p>
						</div>
						<?php	
					}
					$vendor_tags = get_user_meta($vendor_id, 'vendor_tags', true);
					if($vendor_tags)
					{
						?>
						<div class="extra_info">
							<h4><?php _e('ARTIST\'S TAGS','dc-woocommerce-multi-vendor');?></h4>
							<p><?php echo $vendor_tags; ?></p>
						</div>
						<?php	
					}
					$vendor_intro = get_user_meta($vendor_id, 'vendor_intro', true);
					if($vendor_intro!='')
					{
						$url =str_replace("youtu.be/","youtube.com/watch?v=",$vendor_intro);	
						$parts = parse_url($url);
						parse_str($parts['query'], $query);
						
						if(isset($query['v']))
						{
						?>
						<div class="extra_info">
							<h4><?php  _e($fname.'\'s Introduction Video','dc-woocommerce-multi-vendor');?></h4>
							<?php
								if($url!='')
								{
									?>
									<iframe src="https://www.youtube.com/embed/<?php echo $query['v']; ?>" width="100%" height="500px" frameborder="0"  allowfullscreen ng-show="showvideo"></iframe>
									<?php	
								}		
							?>
						</div>
						<?php	
						}
					}
			
				}	
				?>	
			</div>
			</div>
			</div>
			<?php
		}
	}
}

function ec_get_all_product_categories($parent)
{
	$html=""; 
	$args = array( 'hide_empty' => 1, 'parent' => $parent ,'exclude' =>array(17));    
	$terms = get_terms('product_cat', $args);
	foreach ($terms as $term) { 
		if($list  = ec_get_all_product_categories( $term->term_id))
		{
			$html .= $list;
		}
		else
		{
			$html .= "<option value='".$term->term_id."'>".$term->name."</option>";
		}
		
	}
	return $html;
}

/* display only parent category on product detail page url (remove subcategory slug from product detail page url) */
add_filter( 'woocommerce_product_post_type_link_parent_category_only', '__return_true' );


/* Changes checkbox to radio to allow only one product category to choose - Admin panel */
add_filter( 'wp_terms_checklist_args', 'ec_term_radio_checklist_start_el_version', 10, 2 );
function ec_term_radio_checklist_start_el_version( $args, $post_id ) {
    if ( ! empty( $args['taxonomy'] ) && $args['taxonomy'] === 'product_cat' ) {
        if ( empty( $args['walker'] ) || is_a( $args['walker'], 'Walker' ) ) { // Don't override 3rd party walkers.
            if ( ! class_exists( 'WPSE_139269_Walker_Category_Radio_Checklist_Start_El_Version' ) ) {
                class EC_Walker_Product_Category_Radio_Checklist_Start_El_Version extends Walker_Category_Checklist {
                    public function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
                        if ( empty( $args['taxonomy'] ) ) {
                            $taxonomy = 'category';
                        } else {
                            $taxonomy = $args['taxonomy'];
                        }

                        if ( $taxonomy == 'category' ) {
                            $name = 'post_category';
                        } else {
                            $name = 'tax_input[' . $taxonomy . ']';
                        }

                        $args['popular_cats'] = empty( $args['popular_cats'] ) ? array() : $args['popular_cats'];
                        $class = in_array( $category->term_id, $args['popular_cats'] ) ? ' class="popular-category"' : '';

                        $args['selected_cats'] = empty( $args['selected_cats'] ) ? array() : $args['selected_cats'];

                        /** This filter is documented in wp-includes/category-template.php */
                        if ( ! empty( $args['list_only'] ) ) {
                            $aria_cheched = 'false';
                            $inner_class = 'category';

                            if ( in_array( $category->term_id, $args['selected_cats'] ) ) {
                                $inner_class .= ' selected';
                                $aria_cheched = 'true';
                            }

                            $output .= "\n" . '<li' . $class . '>' .
                                '<div class="' . $inner_class . '" data-term-id=' . $category->term_id .
                                ' tabindex="0" role="checkbox" aria-checked="' . $aria_cheched . '">' .
                                esc_html( apply_filters( 'the_category', $category->name ) ) . '</div>';
                        } else {
                            $output .= "\n<li id='{$taxonomy}-{$category->term_id}'$class>" .
                            '<label class="selectit"><input value="' . $category->term_id . '" type="radio" name="'.$name.'[]" id="in-'.$taxonomy.'-' . $category->term_id . '"' .
                            checked( in_array( $category->term_id, $args['selected_cats'] ), true, false ) .
                            disabled( empty( $args['disabled'] ), false, false ) . ' /> ' .
                            esc_html( apply_filters( 'the_category', $category->name ) ) . '</label>';
                        }
                    }
                }
            }
            $args['walker'] = new EC_Walker_Product_Category_Radio_Checklist_Start_El_Version;
        }
    }
    return $args;
}


/* limit image upload size to 8MB */
add_filter('wp_handle_upload_prefilter', 'ec_limit_image_size');
function ec_limit_image_size($file) 
{
	// Calculate the image size in KB
	$image_size = $file['size']/1024;
	// File size limit in KB
	$limit = 8000;
	// Check if it's an image
	$is_image = strpos($file['type'], 'image');
	if ( ( $image_size > $limit ) && ($is_image !== false) )
		$file['error'] = 'Maximum image size allowed is 8 MB';
	return $file;
}


/* limit image upload size to 8MB */
add_filter( 'upload_size_limit', 'ec_filter_site_upload_size_limit', 20 );
function ec_filter_site_upload_size_limit( $size ) {
	$current_user=wp_get_current_user();
	if ( is_user_wcmp_vendor($current_user->ID) )
    // Set the upload size limit to 10 MB for users lacking the 'manage_options' capability.
    if ( !current_user_can( 'manage_options' ) ) {
        // 8 MB.
        $size = 1024 * 8000;//chnage this code as oer your size requirement
    }
    return $size;
}

/* Upload image type restriction */
add_filter('wp_handle_upload_prefilter', 'ec_restrict_image_type');
function ec_restrict_image_type($file) {
	if(is_user_wcmp_vendor(get_current_user_id()))
	{
		$image = getimagesize($file['tmp_name']);
	    $image_type = $image['mime'];

	    $invalid_image_type = "Invalid file type. Only JPEG, JPG and PNG allowed";

	    if ( $image_type != 'image/png' && $image_type != 'image/jpg' && $image_type != 'image/jpeg' ) {
	        $file['error'] = $invalid_image_type; 
	        return $file;
	    }
	    else
	        return $file;
	} 
	else 
	{
			return $file;
	}
}

function ec_unique($prefix="EC",$length=8)
{
   return substr(strtoupper(uniqid($prefix)), 0, $length);
}

add_action( 'admin_menu', 'ec_admin_menu_callback' );
function ec_admin_menu_callback()
{
	add_menu_page("Request Quote Forms", "Request Quote Forms", 'administrator','request_info_page', 'request_info_page_callback');
}
function request_info_page_callback()
{
	global $wpdb;
	if(isset($_POST['update_request_quote']) && $_POST['update_request_quote']=="Submit" && isset($_POST['update_id']) && $_POST['update_id'] > 0 && isset($_POST['status']) && $_POST['status']!='' && isset($_POST['note']) && $_POST['note']!='')
	{
		
		$dt = date('Y-m-d');
		$wpdb->query("update ".$wpdb->prefix."request_quote set note='".$_POST['note']."', status='".$_POST['status']."', status_dt='".$dt."' where id='".$_POST['update_id']."'");
		wp_redirect(site_url('wp-admin/admin.php?page=request_info_page&msg=submitted'));
	}
	
	include("class/ec_request_list_cls.php");
	$requestList = new ec_request_list_cls();
	
	
	$doaction = $requestList->current_action();
	if ( $doaction && isset( $_REQUEST['SOMEVAR'] ) ) {
		// do stuff
	} elseif ( ! empty( $_GET['_wp_http_referer'] ) ) {
		wp_redirect( remove_query_arg( array( '_wp_http_referer', '_wpnonce' ), stripslashes( $_SERVER['REQUEST_URI'] ) ) );
		exit;
	} 
	
	if(isset($_GET['action']) && $_GET['action']=="edit" && isset($_GET['id']) && $_GET['id'] >0)
	{
		?>
		<div class="wrap">
			<h2>Request Quote Forms</h2>
			<div>
				<?php
					$request = $wpdb->get_row("select * from ".$wpdb->prefix."request_quote where id='".$_GET['id']."'");
					
				?>
				<form method="post">
					<table class="form-table">
						<tr>
							<th>Request No.</th>	
							<td><?php echo $request->request_no; ?></td>
						</tr>
						<tr>
							<th>Request Date</th>	
							<td><?php echo $request->request_date; ?></td>
						</tr>
						
						<tr>
							<th>Product Title</th>	
							<td><?php echo get_the_title($request->product_id); ?></td>
						</tr>
						<tr>
							<th>Certified Buyer</th>	
							<td><?php echo get_user_meta($request->buyer_id,'first_name',true)." ".get_user_meta($request->buyer_id,'last_name',true); ?></td>
						</tr>
						<tr>
							<th>Delivery Location</th>
							<td><?php echo $request->delivery_location; ?></td>
						</tr>
						<tr>
							<th>Delivery Date</th>
							<td><?php echo $request->delivery_date; ?></td>
						</tr>
						<tr>
							<th>Quantity</th>
							<td><?php echo $request->qty; ?></td>
						</tr>
						<tr>
							<th>Shipping Method</th>
							<td><?php echo $request->shipping_method; ?></td>
						</tr>
						
						<tr>
							<th>Do you need custom modifications to this item? If so specify the details.</th>	
							<td><?php echo $request->custom_modification; ?></td>
						</tr>
						<tr>
							<th>Do you want to purchase a sample/artist proof for your client?</th>	
							<td><?php if($request->sample_proof==1){ echo "Yes"; } else { echo "No"; } ?></td>
						</tr>
						<tr>
							<th>If you need this piece framed, specify details.</th>
							<td><?php echo $request->piece_framed; ?></td>
						</tr>
						<tr>
							<th>Special Instructions or Requests for Artist</th>
							<td><?php echo $request->special_instruction; ?></td>
						</tr>
						<tr>
							<th>Comments for Embrace Creative's team</th>
							<td><?php echo $request->comment_ec; ?></td>
						</tr>
						<tr>
							<th>Your Comments</th>
							<td><textarea name="note" rows="8" cols="60"><?php echo $request->note; ?></textarea></td>
						</tr>
						<tr>
							<th>Status</th>
							<td>
								<input type='radio' name="status" value="approved" <?php if($request->status=='approved') { echo "checked='checked'"; } ?> id="approved"/><label for="approved">Approv</label>
								<input type='radio' name="status" value="rejected" <?php if($request->status=='rejected') { echo "checked='checked'"; } ?> id="rejected"/><label for="rejected">Rejected</label>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<input type="submit" name="update_request_quote" value="Submit" class="button button-primary button-large"/>
								<input type="hidden" name="update_id" value="<?php echo $request->id; ?>" />
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
		<?php
	}
	else
	{	
	?>
		<div class="wrap">
			<h2>Request Quote Forms</h2>
			<?php
				if(isset($_REQUEST['msg']) && $_REQUEST['msg']=="submitted")
				{
					?>
					<div class="notice is-dismissible notice-success">
						<p>Status updated and submitted successfully.</p>
					</div>
					<?php	
				}
			?>
			<ul class="subsubsub">
				<li><a href="<?php echo site_url('wp-admin/admin.php?page=request_info_page');?>">All</a> | </li>
				<li><a href="<?php echo site_url('wp-admin/admin.php?page=request_info_page&status=approved');?>">Approved</a> | </li>
				<li><a href="<?php echo site_url('wp-admin/admin.php?page=request_info_page&status=rejected');?>">Rejected</a> | </li>
				<li><a href="<?php echo site_url('wp-admin/admin.php?page=request_info_page&status=pending');?>">Pending</a></li>
			</ul>
			<form method="get">
				<input type="hidden" name="page" value="deal_info_page" />	
				<?php
					$requestList->prepare_items(); 
					$requestList->display();	
				?>
			</form>	
		</div>
	<?php
	}
}
/* display shop listing page */
add_filter( 'woocommerce_loop_add_to_cart_link', 'quantity_inputs_for_woocommerce_loop_add_to_cart_link', 10, 2 );
function quantity_inputs_for_woocommerce_loop_add_to_cart_link( $html, $product ) {
	
	if ( $product && $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock() && ! $product->is_sold_individually() ) {
		//print_r($product);
		$html ="";
		//$html .= woocommerce_quantity_input( array(), $product, false );
		//$height = get_post_meta($product->ID,'_width',true);
		
		
		$id = $product->get_id();
		$author_id = get_post_field ('post_author', $id);
		$display_name = get_the_author_meta( 'display_name' , $author_id ); 
		$vendor = get_wcmp_vendor($author_id);
		//print_r($vendor);
		$shop_url ="#";
		if(isset($vendor) && is_object($vendor))
		{
			$shop_url = $vendor->permalink; // vendor shop url;
		}

		$html .= '<div class="ec_product_author"><a href="'.$shop_url.'">'.$display_name.'</a></div>';
		$width = $product->get_width();
		$height = $product->get_height();
		$depth = $product->get_length();
		if($width!='' && $height!='' && $depth!='')
		{
			if(get_option( 'woocommerce_dimension_unit' )=="in")
			{
				$width = $width.'"W';
				$height = $height.'"H';
				$depth = $depth.'"D';
			}
			$html .= '<div class="ec_dimension">'.$height.' X '.$width.' X '.$depth.'</div>';
		}
		$html .='<div class="ec_product_sec"><div class="ec_wish_list">'.do_shortcode("[ti_wishlists_addtowishlist loop=yes]").'</div>';
		$html .= '<div class="ec_cart_sec"><form action="' . esc_url( $product->add_to_cart_url() ) . '" class="cart ec_cart" method="post" enctype="multipart/form-data">';
		$html .= '<div><button type="submit" class="button alt ec_btncart"><i class="fa fa-shopping-cart"></i></button></div>';
		$html .= '</form></div></div>';
	}
	
	return $html;
}



function ec_display_product_meta_callback($atts, $content = null )
{
	//ob_start();
	$data='<div class="ec_product_data">';
	$data .=do_shortcode($content);
	$data .='</div>';
	return $data;
}
add_shortcode('ec_product_meta_info','ec_display_product_meta_callback');
function ec_product_each_meta_callback($atts)
{
	global $post;
	$atts = shortcode_atts( array(
		'label' => '',
		'key' => '',
		'type' => ''
		), $atts );
	$val ="";
	$str="";
	$content="";
	if(isset($atts['label']) && $atts['label']!='' && isset($atts['key']) && $atts['key']!='')
	{
		$meta_key = $atts['key'];
		if(isset($atts['type']) && $atts['type']!='')
		{
			if($atts['type']=="excerpt")
			{
				$val =$post->post_excerpt;
			}
			else if($atts['type']=="product_cat" || $atts['type']=="medium" || $atts['type']=="subject" || $atts['type']=="style" || $atts['type']=="types")
			{
				$terms = wp_get_post_terms($post->ID,$meta_key);
				if($terms)
				{
					$val = $terms[0]->name;	
				}
				
			}
			if($val!='')
			{
				$content = "<div class='product_".$meta_key." ec_product_meta'>
					<label><b>".$atts['label']."</b></label>
					<span>".$val."</span>
				</div>";
			}
		}
		else
		{
		
			if(strpos($meta_key,',')!== false)
			{
				$keyArr=explode(",",$meta_key);	
				if($keyArr)
				{
					foreach($keyArr as $val)
					{
						
						if(get_post_meta($post->ID,$val,true)!='')
						{
							if($str=="")
							{
								$str = get_post_meta($post->ID,$val,true).'"';								
							}
							else
								$str .= ' X '. get_post_meta($post->ID,$val,true).'"';
							
							if($val == "_height")
							{
								$str .="H";
							}
							else if($val == "_width")
							{
								$str .="W";
							}
							else if($val == "_length")
							{
								$str .="D";
							}
								
						}
					}	
				}
				$val = $str;
			}
			else
				$val = get_post_meta($post->ID,$meta_key,true);
			if($val!='')
			{
				if($meta_key == "_customizable"  || $meta_key == "_ready_to_hang" || $meta_key=="_signed")
				{
					if($val=="1")
						$val="Yes";	
					else
						$val="";
				}
				if($val!='')
				{	
					$content = "<div class='product".$meta_key." ec_product_meta'>
						<label><b>".$atts['label']."</b></label>
						<span>".$val."</span>
					</div>";	
				}
			}	
		}
	}	
	return $content;
}
add_shortcode('ec_product_meta','ec_product_each_meta_callback');


add_shortcode( 'product_description', 'ec_display_product_description' );
function ec_display_product_description( $atts ){
	global $post;
    return $post->post_content;
}

add_shortcode( 'product_vendor_info', 'ec_display_product_vendor_info' );
function ec_display_product_vendor_info( $atts ){
	global $post;
    $vendor_id =  $post->post_author;
	$fname = get_the_author_meta('first_name',$vendor_id);
	$lname = get_the_author_meta('last_name',$vendor_id);
	$vendor = get_wcmp_vendor($vendor_id);
	$shop_url ="#";
	if(isset($vendor) && is_object($vendor))
	{
		$shop_url = $vendor->permalink; // vendor shop url;
	}	

	$vendor_statement = get_user_meta($vendor_id, 'vendor_statement', true);
	if($vendor_statement)
	{
	
	$data = '<div class="extra_info statement_info">
		<h4>'.__('STATEMENT','dc-woocommerce-multi-vendor').'</h4>
		<p>'.nl2br($vendor_statement).'</p>
		<a href="'.$shop_url.'" class="link-red">More About Artist</a>
	</div>';
	}
	
	return $data;
}

add_shortcode('vendor_products_list','ec_display_vendor_products_callback');
function ec_display_vendor_products_callback()
{
	global $post;
    $vendor_id =  $post->post_author;
	ob_start();
	//echo do_shortcode('[wcmp_products id="" vendor="'.$vendor_id.'" columns="4" orderby="title" order="ASC"]');
	include get_stylesheet_directory().'/vendor_product_list.php';
	$data1= ob_get_contents();
	ob_clean();
	return $data1;
	
}
add_shortcode('vendor_address','ec_get_vendor_address');
function ec_get_vendor_address()
{
	global $post;
	$vendor_id = $post->post_author;	
	$city = get_user_meta( $vendor_id, '_vendor_city', true ); 
	$state = get_user_meta( $vendor_id, '_vendor_state', true ); 
	$country = get_user_meta( $vendor_id, '_vendor_country', true ); 
	$location="";
	if($city!='')
		$location = $city;
	if($state!='' && $location!='')
		$location .= ", ". $state;
	
	if($country!='' && $location!='')
		$location .= ", ". $country;
	
	if($location!='')
	{
		return "<div class='ec_vendor_address'>".$location."</div>";
	}
}


add_action( 'woocommerce_after_customer_login_form', 'ec_registartion_text_callback' );
function ec_registartion_text_callback() {
    if(!is_user_logged_in() )
    {
        $link = home_url( '/ec-registration' );

        // The displayed (output)
        echo '<div class="ec_register_sec"><h3>Not part of our Art + Design Showroom yet?</h3><br/><a href="'.$link.'">Register here!<a/></div>';
    }
}

add_filter(  'gettext','ec_translate_words_array');
add_filter(  'ngettext','ec_translate_words_array');
function ec_translate_words_array( $translated ) {
     $words = array(
		'Related Products' => 'Related Artwork',  
     );
     $translated = str_ireplace(  array_keys($words),  $words,  $translated );
     return $translated;
}



/* Remove Map on All Artist Page - Custom code from plugin */
add_filter('wcmp_vendor_list_enable_store_locator_map', '__return_false');

/* Remove Address and Email from Single Artist Store Page - Custom code from plugin */
add_filter('wcmp_vendor_store_header_hide_store_address' , '__return_true');
add_filter('wcmp_vendor_store_header_hide_store_email' , '__return_true');

/* Remove Wordpress Admin Link from Artist Dashboard - Custom code from plugin */
add_filter('wcmp_vendor_dashboard_header_right_panel_nav', 'ec_remove_admin_access');
function ec_remove_admin_access($panel_nav){
   unset($panel_nav['wp-admin']);
   return $panel_nav;
}

add_filter ( 'is_vendor_can_see_customer_details', '__return_false');
add_filter ( 'show_cust_address_field', '__return_false');
