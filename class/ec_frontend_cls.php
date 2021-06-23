<?php
defined( 'ABSPATH' ) || exit;
class ec_frontend_cls{
	public function __construct() 
	{
		add_action('init', array($this,'ec_request_quote_endpoint'),0);
		add_filter( 'the_title', array( $this, 'ec_custom_endpoint_title' ) );
		add_filter ( 'woocommerce_account_menu_items', array($this,'ec_woo_my_account_menu_label_change'), 100);
		add_filter( 'the_title', array($this,'ec_woo_change_endpoint_title'), 100, 2 );
		add_filter( 'the_title', array($this,'ec_woo_change_fav_endpoint_title'), 100, 2 );
		add_action('wp_ajax_get_medium_type_by_category_name',array($this,'ec_get_medium_type_by_category_name_callback'));
		add_action('wp_ajax_nopriv_get_medium_type_by_category_name',array($this,'ec_get_medium_type_by_category_name_callback'));
		add_filter( 'woocommerce_package_rates', array($this,'ec_custom_shipping_costs'), 100, 2 );
		add_filter( 'woocommerce_add_cart_item_data', array($this,'ec_custom_add_cart_item_data'), 10, 3 );
		add_filter( 'woocommerce_get_item_data', array($this,'ec_custom_get_item_data'), 10, 2 );
		add_action( 'woocommerce_checkout_create_order_line_item', array($this,'ec_custom_checkout_create_order_line_item'), 10, 4 );
		add_filter( 'woocommerce_order_item_name', array($this,'ec_custom_order_item_name'), 10, 2 );
		add_action( 'woocommerce_before_cart_table', array($this,'ec_continue_shopping_button') );
	}	
	
	
	function ec_continue_shopping_button() {
		$shop_page_id = get_option( 'woocommerce_shop_page_id' );

	 $shop_page_url = get_permalink( $shop_page_id );
	 echo '<div class="mb10">';
	 echo ' <a href="'.$shop_page_url.'" class="button">Continue Shopping →</a>';
	 echo '</div>';
	}
//MBATT: Comment out from line 30 to 112
	public function ec_custom_add_cart_item_data( $cart_item_data, $product_id, $variation_id ) {
		$type = get_post_meta($product_id,'_shipping_type',true);
		if($type=="flat_rate")
		{
			$sprice = get_post_meta($product_id,'_shipping_price',true);
			$cart_item_data['flat_rate'] = get_woocommerce_currency_symbol().$sprice;
		}
		else if($type=="free_shipping")
		{
			$cart_item_data['flat_rate'] = "Free";
		}
		return $cart_item_data;
	}
	
	public function ec_custom_get_item_data( $item_data, $cart_item_data ) {
		//print_r($cart_item_data);
		//exit;
		if( isset( $cart_item_data['flat_rate'] ) ) {
		$item_data[] = array(
			'key' => __( 'Shipping Charge', 'woocommerce' ),
			'value' => wc_clean( $cart_item_data['flat_rate'])
			);
		}
		return $item_data;
	}

	public function ec_custom_checkout_create_order_line_item( $item, $cart_item_key, $values, $order ) {
		if( isset( $values['flat_rate'] ) ) {
			$item->add_meta_data(
				__( 'Shipping Charge', 'woocommerce' ),
				$values['flat_rate'],
				true
			);
		}
	}

	public function ec_custom_order_item_name( $product_name, $item ) {
	 if( isset( $item['flat_rate'] ) ) {
		$product_name .= sprintf(
				'<ul><li>%s: %s</li></ul>',
				__( 'Shipping Charge', 'woocommerce' ),
				esc_html( $item['flat_rate'] )
			);
		}
		return $product_name;
	}

	public function ec_custom_shipping_costs( $rates, $package ) {
		// New shipping cost (can be calculated)
		$shipping_price =0;
		if(isset($package))
		{
			foreach($package['contents'] as $pack){
				if(isset($pack['product_id']))
				{
					$pid = $pack['product_id'];
					$qty = $pack['quantity'];
					$shipping_type = get_post_meta($pid,'_shipping_type',true);
					if($shipping_type == "flat_rate")
					{
						$price = get_post_meta($pid,'_shipping_price',true);
						if($qty > 0)
							$price = $qty * $price;
						$shipping_price +=$price;
						
					}
				}				
			}
			foreach( $rates as $rate_key => $rate ){
				if($rate->method_id == 'wcmp_vendor_shipping'){
					$rates[$rate_key]->cost = $shipping_price;
					/*$taxes = array();
					foreach ($rates[$rate_key]->taxes as $key => $tax){
						if( $rates[$rate_key]->taxes[$key] > 0 )
							$taxes[$key] = $new_cost * $tax_rate;
					}
					$rates[$rate_key]->taxes = $taxes;*/
				}
			}
		}
		return $rates;
	} 
	public function ec_woo_my_account_menu_label_change($items) {
		
		$items['orders'] = __('Order History','woocommerce');
		$items['edit-address'] = __('Billing & Shipping','woocommerce');
		$items['edit-account'] = __( 'Account Settings', 'woocommerce' );
		unset($items['downloads']);
		unset($items['wc-smart-coupons']);
		return $items;
	}
	
	public function ec_woo_change_endpoint_title( $title, $id ) {
		if ( is_wc_endpoint_url( 'orders' ) && in_the_loop() ) {
			$title = "Order History";
		}
		elseif ( is_wc_endpoint_url( 'edit-account' ) && in_the_loop() ) {
			$title = "Account Settings";
		}
		elseif ( is_wc_endpoint_url( 'edit-address' ) && in_the_loop() ) {
			$title = "Billing & Shipping";
		}
		return $title;
	}

	public function ec_custom_endpoint_title($title)
	{
		global $wp_query;
		$endpoint = "quote-forms";
		$is_endpoint = isset( $wp_query->query_vars[ $endpoint ] );
		if ( $is_endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {
			$title = __( 'Quote Forms', 'woocommerce' );
			remove_filter( 'the_title', array( $this, 'ec_custom_endpoint_title' ) );
		}

		return $title;	
	}
	
	public function ec_woo_change_fav_endpoint_title($title)
	{
		global $wp_query;
		$endpoint = "favorites";
		$is_endpoint = isset( $wp_query->query_vars[ $endpoint ] );
		if ( $is_endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {
			$title = __( 'Favorites', 'woocommerce' );
			remove_filter( 'the_title', array( $this, 'ec_custom_endpoint_title' ) );
		}
		return $title;	
	}

	
	public function ec_request_quote_endpoint()
	{
		add_filter( 'woocommerce_account_menu_items',array($this, 'ec_request_quote_menu'),1);
		add_filter( 'woocommerce_get_query_vars', array($this, 'ec_quote_form_query_vars'));
		$user = wp_get_current_user();
		if ( in_array( 'certified_buyer', (array) $user->roles ) ) 
		{
			add_action( 'woocommerce_account_quote-forms_endpoint', array($this, 'ec_quote_form_endpoint') );
			add_rewrite_endpoint( 'Quote Forms', EP_ROOT | EP_PAGES );
			
			/*add_action( 'woocommerce_account_favorites_endpoint', array($this, 'ec_favorites_form_endpoint') );
			add_rewrite_endpoint( 'Favorites', EP_ROOT | EP_PAGES );*/
		}

		flush_rewrite_rules();		
		
	}
	
	public function ec_request_quote_menu($items)
	{
		$user = wp_get_current_user();
		if ( in_array( 'certified_buyer', (array) $user->roles ) ) 
		{
			$items['quote-forms'] = __('Quote Forms', 'woocommerce');
			
		}
		//$items['favorites'] = __('Favorites', 'woocommerce');
		return $items;
	}
	
	public function ec_quote_form_endpoint()
	{
		?>
		<div>
			<?php
				if(isset($_GET['id']) && $_GET['id'] > 0)
				{
					$request = $this->ec_get_my_request_by_id($_GET['id']);
					?>
					<table class="quote-form-data">
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
							<td><?php echo $request->note; ?></td>
						</tr>
						<tr>
							<th>Status</th>
							<td><?php echo $request->status; ?></td>
						</tr>
					</table>
					<a href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id'));?>quote-forms/">Back</a>
					<?php
				}
				else
				{
					$requestForms = $this->ec_get_my_request_quote_forms()
			?>
			<table>
				<tr>
					<th>Request No.</th>
					<th>Request Date</th>
					<th>Product</th>
					<th>Delivery Location</th>
					<th>Delivery Date</th>
					<th>Quantity</th>
					<th>View</th>
				</tr>
				<?php
					if($requestForms)
					{
						foreach($requestForms as $request)
						{
				?>
				<tr>
					<td><?php echo "#".$request->request_no; ?></td>
					<td><?php echo $request->request_date; ?></td>
					<td><a target='_blank' href='<?php echo get_permalink($request->product_id); ?>'><?php echo get_the_title($request->product_id); ?></a></td>
					<td><?php echo $request->delivery_location; ?></td>
					<td><?php echo $request->delivery_date; ?></td>
					<td><?php echo $request->qty; ?></td>
					<td><a href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')); ?>quote-forms/?id=<?php echo $request->id; ?>"><?php _e('View','') ; ?></a></td>
				</tr>
				<?php
						}
					}
				?>
			</table>
			<?php
				}
			?>
		</div>
		<?php
	}
	
	public function ec_favorites_form_endpoint()
	{
		?>
		<div>
			<?php echo do_shortcode('[woo_cl_my_collections user_id="'.get_current_user_id().'" collection_title="My Collections" show_count="true" enable_see_all="true"][/woo_cl_my_collections]'); ?>
		</div>
		<?php	
	}
	
	public function ec_quote_form_query_vars($vars)
	{
		$user = wp_get_current_user();
		if ( in_array( 'certified_buyer', (array) $user->roles ) ) 
		{
			$vars[] = 'quote-forms';
		}
		$vars[] = 'favorites';
		return $vars;	
	}
	
	public function ec_get_my_request_quote_forms_count()
	{
		global $wpdb;
		$cond ="";
		
		$sql ="select count(*) from ".$wpdb->prefix."request_quote q, ".$wpdb->prefix."posts p where p.ID=q.product_id and p.post_type='product' and p.post_status='publish' and buyer_id='".get_current_user_id()."'";
		$cnt = $wpdb->get_var($sql);
		return $cnt;
	}
	
	public function ec_get_my_request_quote_forms()
	{
		global $wpdb;
		$cond ="";
		
		$sql ="select * from ".$wpdb->prefix."request_quote q, ".$wpdb->prefix."posts p where p.ID=q.product_id and p.post_type='product' and p.post_status='publish' and buyer_id='".get_current_user_id()."'";
		$res = $wpdb->get_results($sql);
		return $res;
	}
	
	public function ec_get_my_request_by_id($id)
	{
		global $wpdb;
		$cond ="";
		
		$sql ="select q.* from ".$wpdb->prefix."request_quote q, ".$wpdb->prefix."posts p where p.ID=q.product_id and p.post_type='product' and p.post_status='publish' and buyer_id='".get_current_user_id()."' and q.id='".$id."'";
		$res = $wpdb->get_row($sql);
		return $res;
	}


	public function ec_get_medium_type_by_category_name_callback()
	{
		global $wpdb;
		$arr['result']=array();
		$arr['success']=0;
		if(isset($_POST['cat'])&& $_POST['cat']!='' && isset($_POST['type']) && $_POST['type']!='')
		{
			if(is_numeric($_POST['cat']))
			{
				$category = get_term_by('ID',$_POST['cat'],'product_cat');
			}
			else
			{
				$category = get_term_by('name',$_POST['cat'],'product_cat');
			}
				
			//print_r($category);	
			$type = $_POST['type'];
			if($category)
			{
				$data= $wpdb->get_results("select p.*,t.name,t.slug from ".$wpdb->prefix."parent_child_taxonomy p,".$wpdb->prefix."terms t where p.child_id =t.term_id and parent_id='".$category->term_id."' and taxonomy='".$type."'",ARRAY_A);	
				if($data)
				{
					$arr['data']=$data;
					$arr['success']=1;
				}
			}
		}	
		echo json_encode($arr);
		exit;
	}
}
?>
