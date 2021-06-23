<?php
defined( 'ABSPATH' ) || exit;

class ec_vendor_product_cls{
	public $vendor_dashboard_url;
	/* adding script and style to vendor dashboard */
	public function __construct() 
	{
		add_action('wcmp_frontend_enqueue_scripts', array($this,'ec_vendor_scripts_callback'), 100 );
		add_filter('wcmp_product_data_tabs', array($this,'ec_add_custom_product_data_tabs'));
		add_action('wcmp_product_tabs_content', array($this,'ec_add_custom_product_data_content'), 10, 3 );
		add_action('wcmp_process_product_object', array($this,'ec_save_product_data'), 100, 2 );
		//add_action('before_wcmp_vendor_dashboard', array($this,'ec_save_product_data'), 100, 1 );
		add_action('wcmp_before_post_update', array($this,'ec_wcmp_before_post_update_callback' ));
		add_filter('wcmp_vendor_dashboard_header_nav',array($this,'ec_change_header_nav_link_callback'),99);
		add_action('init', array($this,'ec_remove_progress_bar'));
		//add_action('wcmp_init',  array($this,'ec_after_wcmp_init'));
		$myOptions = get_option('wcmp_vendor_general_settings_name');
		$this->vendor_dashboard_url = get_permalink($myOptions['wcmp_vendor']);
		add_action('wp_footer', array($this,'ec_footer_script'));
		add_filter( 'wcmp_vendor_dashboard_header_right_panel_nav', array($this,'ec_wcmp_vendor_dashboard_header_right_panel_nav'), 10, 1 );
	}
	
	
	function ec_wcmp_vendor_dashboard_header_right_panel_nav( $panel_nav ) {
			unset($panel_nav ['profile']); //remove Backend Link
			return $panel_nav ;
	}
	
	public function ec_footer_script() 
	{
		if(is_vendor_dashboard() && is_user_logged_in() && (is_user_wcmp_vendor(get_current_user_id()) || is_user_wcmp_pending_vendor(get_current_user_id()) || is_user_wcmp_rejected_vendor(get_current_user_id()))) 
		{
	?>
		<script type='text/javascript'> var ec_ajax_url ='<?php echo admin_url('admin-ajax.php'); ?>';</script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js" type='text/javascript'></script>
		<script src="<?php echo trailingslashit( get_stylesheet_directory_uri() ) . 'js/custom_vendor_script.js?time='.time(); ?>" type='text/javascript'></script>
		<script type="text/javascript">
			jQuery(".delete_me").click(function(e){
				e.preventDefault();	
				var url = jQuery(this).attr('href');
				var ans = confirm('Are you sure you want to delete?');
				if(ans)
				{
					window.location.href=url;
				}	
				else
				{
					return false;
				}
			});
		</script>
	<?php
		}
	}
	

	public function ec_vendor_scripts_callback()
	{
		if(is_vendor_dashboard() && is_user_logged_in() && (is_user_wcmp_vendor(get_current_user_id()) || is_user_wcmp_pending_vendor(get_current_user_id()) || is_user_wcmp_rejected_vendor(get_current_user_id()))) {
			wp_register_style('ec_custom_vendor_style', trailingslashit( get_stylesheet_directory_uri() ) . 'css/custom_style.css');
			wp_enqueue_style('ec_custom_vendor_style');			
			wp_register_script('ec_custom_vendor_script_js',trailingslashit( get_stylesheet_directory_uri() ) . 'js/custom_vendor_script.js' );
			wp_enqueue_script('ec_custom_vendor_script_js');

		}	
	}

	/* add custom tab in product - vendor dashboard */
	public function ec_add_custom_product_data_tabs( $tabs ) {
	   $tabs['attribute'] = array(
		   'label'    => __( 'Attributes', 'your-text-domain' ),
		   'target'   => 'custom_attr_tab',
		   'class'    => array(),
		   'priority' => 100,
	   );
	   /*$tabs['collection'] = array(
		   'label'    => __( 'My Collections', 'your-text-domain' ),
		   'target'   => 'collection_tab',
		   'class'    => array(),
		   'priority' => 100,
	   );*/
	   return $tabs;
	}

	/* Content to display in custom tab */
	public function ec_add_custom_product_data_content( $pro_class_obj, $product, $post ) {
	   ?>
		<div role="tabpanel" class="tab-pane fade" id="custom_attr_tab">
			<div class="row-padding">
				<div class="form-group">
					<!--label class="control-label col-sm-3 col-md-3" for="_manage_stock">
						<?php _e( 'Support Type', 'woocommerce' ); ?>
					</label>
					<?php
						$support_type = get_post_meta($post->ID,'_support_type',true);
					?>
					<div class="col-md-6 col-sm-9">
						<input class="form-control" type="text" id="_support_type" name="_support_type" value="<?php echo $support_type; ?>"/>
						<span class="form-text"><?php esc_html_e( 'Example:Canvas,Wood,Glass,Metal,Paper,Fiber,Plastic,Other', 'woocommerce' ); ?> </span>
					</div-->
				</div>
				<div class="form-group">
					<label class="control-label col-sm-3 col-md-3" for="_customizable">
						<?php _e( 'Customizable?', 'woocommerce' ); ?>
					</label>
					<?php
						$customizable = get_post_meta($post->ID,'_customizable',true);
					?>
					<div class="col-md-6 col-sm-9">
						<input class="form-control" type="checkbox" id="_customizable" name="_customizable" value="yes" <?php checked( $customizable, true ); ?>/>
						<span class="form-text"><?php esc_html_e( 'If this artwork can be customized, check this box.', 'woocommerce' ); ?> </span>
					</div>
				</div>  
				<div class="form-group">
					<label class="control-label col-sm-3 col-md-3" for="_ready_to_hang">
						<?php _e( 'Ready to hang/install?', 'woocommerce' ); ?>
					</label>
					<?php
						$ready_to_hang = get_post_meta($post->ID,'_ready_to_hang',true);
					?>
					<div class="col-md-6 col-sm-9">
						<input class="form-control" type="checkbox" id="_ready_to_hang" name="_ready_to_hang" value="yes" <?php checked( $ready_to_hang, true ); ?>/>
						<span class="form-text"><?php esc_html_e( 'If this artwork is ready to hang or install, check this box.', 'woocommerce' ); ?> </span>
					</div>
				</div>  
				<div class="form-group">
					<label class="control-label col-sm-3 col-md-3" for="_signed">
						<?php _e( 'Signed?', 'woocommerce' ); ?>
					</label>
					<?php
						$signed = get_post_meta($post->ID,'_signed',true);
					?>
					<div class="col-md-6 col-sm-9">
						<input class="form-control" type="checkbox" id="_signed" name="_signed" value="yes" <?php checked( $signed, true ); ?>/>
						<span class="form-text"><?php esc_html_e( 'If this artwork is signed then check this box.', 'woocommerce' ); ?> </span>
					</div>
				</div>  
		   </div>
		</div>
		<?php /* <div role="tabpanel" class="tab-pane fade" id="collection_tab"> 
		   <div class="row-padding">
			   <div class="form-group">
					<label class="control-label col-sm-3 col-md-3">Add artwork into a Collection</label>
					<div class="col-md-6 col-sm-9">
					   <select type="text" name="collection_id" class="collection_id form-control">
						   <option value="">Select Collection</option>
						   <option value="new">Add New</option>
						   <?php
								$args = array(
									'author' =>get_current_user_id(),
									'post_status' => array('publish'),
									'post_type' => 'woocollections',
									'posts_per_page' => -1
								);
					
							$collection = new WP_Query( $args );			
							//print_r($collection->request);
							$collection_id = get_post_meta($post->ID, 'vendor_collection',true);

							if($collection->have_posts())
							{
								foreach ($collection->posts as $apost) 
								{	
									$sel = "";
									if($collection_id == $apost->ID)
										$sel = "selected='selected'";
								?>
									<option <?php echo $sel; ?> value="<?php echo $apost->ID; ?>"><?php echo $apost->post_title; ?></option>
								<?php
								}
							}
						   ?>
						   
						   
						</select>
				   </div>
			   </div>
			   <div class="form-group add_new_collection"  style="display:none">
					<label class="control-label col-sm-3 col-md-3">Collection Name</label>
					<div class="col-md-6 col-sm-9 ">
					   <input type="text" name="collection_name" class="form-control" />
					</div>
			   </div>
		   </div>
		</div>
	   <?php */
	}

	/* Save data of custom tab */
	public function ec_save_product_data( $product, $post_data ) 
	{
		if(is_vendor_dashboard() && is_user_logged_in() && (is_user_wcmp_vendor(get_current_user_id()) || is_user_wcmp_pending_vendor(get_current_user_id()) || is_user_wcmp_rejected_vendor(get_current_user_id()))) 
		{
			/*if(isset($_POST['_support_type']))
			{
				update_post_meta( absint( $post_data['post_ID'] ), '_support_type', $_POST['_support_type']);	
			}*/
			
			
			if(isset($_POST['_customizable']))
			{
				update_post_meta( absint( $post_data['post_ID'] ), '_customizable', 1);	
			}
			else
			{
				update_post_meta( absint( $post_data['post_ID'] ), '_customizable', 0);	
			}
			
			if(isset($_POST['_ready_to_hang']))
			{
				update_post_meta( absint( $post_data['post_ID'] ), '_ready_to_hang', 1);	
			}
			else
			{
				update_post_meta( absint( $post_data['post_ID'] ), '_ready_to_hang', 0);	
			}
			
			if(isset($_POST['_signed']))
			{
				update_post_meta( absint( $post_data['post_ID'] ), '_signed', 1);	
			}
			else
			{
				update_post_meta( absint( $post_data['post_ID'] ), '_signed', 0);	
			}
			//MBATT: Comment out lines 228 - 240
			if(isset($_POST['_shipping_type']))
			{
				if($_POST['_shipping_type'] == "flat_rate")
				{
					update_post_meta( absint( $post_data['post_ID'] ),'_shipping_price' ,$_POST['_shipping_price'] );	
				}
				else
				{
					update_post_meta( absint( $post_data['post_ID'] ),'_shipping_price' ,"");		
				}
					
				update_post_meta( absint( $post_data['post_ID'] ),'_shipping_type' ,$_POST['_shipping_type'] );	
			}
			$product->save();
			if(isset($_POST['_is_original']) && $_POST['_is_original']=="yes")
			{
				update_post_meta( absint( $post_data['post_ID'] ), '_is_original', 1);	
				update_post_meta( absint( $post_data['post_ID'] ), '_stock', 1);	
				update_post_meta( absint( $post_data['post_ID'] ), '_manage_stock', 'yes');	
			}
			else
			{
				update_post_meta( absint( $post_data['post_ID'] ), '_is_original', 0);	
			}
			/* collection */
			/*if( isset($post_data['post_ID']) && isset($post_data['collection_id'])){
				$this->ec_add_to_collection_callback($post_data['post_ID'],$post_data['collection_id'],$post_data['collection_name']);	
			}*/
			
	   }
	}
	
	
	/* insert collection while insterting from collection tab vendord add/edit product - vendor dashborad */
	public function ec_add_to_collection_callback($prodid,$col_id,$col_name)
	{
		global $wpdb;
		$cnt=0;
		if($col_id == "new")
		{
			$col = array(
				'post_title' => sanitize_text_field($col_name),
				'post_status' => 'publish',
				'post_type' => 'woocollections',
				'post_author' => get_current_user_id(),
			);
			$col_id = wp_insert_post( $col );
		}
		else
		{
			/*echo "select count(*) from ".$wpdb->prefix."posts p,".$wpdb->prefix."postmeta pm where pm.post_id=p.ID and pm.meta_key='_woo_cl_coll_product_id' and pm.meta_value='".$prodid."' and post_type='woocollitems' and post_author='".get_current_user_id()."' and post_parent='".$col_id."'";*/
			$cnt = $wpdb->get_var("select count(*) from ".$wpdb->prefix."posts p,".$wpdb->prefix."postmeta pm where pm.post_id=p.ID and pm.meta_key='_woo_cl_coll_product_id' and pm.meta_value='".$prodid."' and post_type='woocollitems' and post_author='".get_current_user_id()."' and post_parent='".$col_id."'");
		}
		
		if($cnt == 0)
		{
		
			if($col_id && $prodid > 0)
			{
				//echo "select meta_value from ".$wpdb->prefix."postmeta where meta_key='_woo_cl_coll_product_id' and meta_value='".$prodid."' and post_id='".$colid."'";
				
				
				$colitem = array(
						'post_title' => get_the_title($prodid),
						'post_status' => 'publish',
						'post_type' => 'woocollitems',
						'post_parent' => $col_id,
						'post_author' => get_current_user_id(),
					);
				$col_item_id = wp_insert_post( $colitem );	
				if($col_item_id)
				{
					update_post_meta($col_item_id,'_woo_cl_coll_product_id',$prodid);	
					update_post_meta($col_item_id,'_woo_cl_coll_product_type','');	
				}
				update_post_meta($prodid, 'vendor_collection',$col_id);
			}
		}
	}
	
	
	/* limit the product gallery images to 5 validation */
	public function ec_wcmp_before_post_update_callback(){
	   $gallery_ids = isset( $_POST['product_image_gallery'] ) ? $_POST['product_image_gallery'] : '';
	   $gallery_ids_arr = explode( ',', $gallery_ids );
	   if( count( $gallery_ids_arr ) > 5 ) { // Max gallery image upload limit set to 2
		   wc_add_notice( 'You can upload only 5 gallery images for a product', 'error' );
		   wp_redirect( apply_filters( 'wcmp_vendor_save_product_redirect_url', wcmp_get_vendor_dashboard_endpoint_url( get_wcmp_vendor_settings( 'wcmp_edit_product_endpoint', 'vendor', 'general', 'edit-product' ), $_POST['post_ID'] ) ) );
		   exit;
	   }
	}
	
	/* change the url of Add Product (to remove the first screen of add product with search) in header bar */
	public function ec_change_header_nav_link_callback($header_nav)
	{
		$header_nav['add-product'] = array(
				'label' => __('Add Product', 'dc-woocommerce-multi-vendor')
				, 'url' => apply_filters('wcmp_vendor_submit_product', esc_url(wcmp_get_vendor_dashboard_endpoint_url(get_wcmp_vendor_settings('wcmp_add_product_endpoint', 'vendor', 'general', 'edit-product'))))
				, 'class' => ''
				, 'capability' => apply_filters('wcmp_vendor_dashboard_menu_add_product_capability', 'edit_products')
				, 'position' => 20
				, 'link_target' => '_self'
				, 'nav_icon' => 'wcmp-font ico-product-icon'
			);
		return $header_nav;
	}
	
	/* remove the progress bar from the vendor dashboard */	
	public function ec_remove_progress_bar() {
		global $WCMp;
		remove_action( 'before_wcmp_vendor_dashboard_content', array( $WCMp->vendor_hooks, 'before_wcmp_vendor_dashboard_content' ) );
		
		/* save collection */
		if(isset($_POST['hidden_action']) && $_POST['hidden_action']=="save_collection" && isset($_POST['collection_name']) && $_POST['collection_name']!='' && isset($_POST['col_id']) && $_POST['col_id']!='')
		{
			if($_POST['col_id']=="new")
			{
				$col = array(
					'post_title' => sanitize_text_field($_POST['collection_name']),
					'post_status' => 'publish',
					'post_type' => 'woocollections',
					'post_author' => get_current_user_id(),
				);
				$col_id = wp_insert_post( $col );
				$status="new";
				
			}
			else
			{
				$col_id=$_POST['col_id'];
				$col = array(
					'ID' =>$_POST['col_id'],
					'post_title' => sanitize_text_field($_POST['collection_name']),
				);
				wp_update_post( $col );
				$status="updated";
			}
			
			if($_POST['product_ids'] && count($_POST['product_ids']) > 0)
			{
				foreach($_POST['product_ids'] as $prodid)
				{
					if($col_id && $prodid > 0)
					{
						$colitem = array(
								'post_title' => get_the_title($prodid),
								'post_status' => 'publish',
								'post_type' => 'woocollitems',
								'post_parent' => $col_id,
								'post_author' => get_current_user_id(),
							);
						$col_item_id = wp_insert_post( $colitem );	
						if($col_item_id)
						{
							update_post_meta($col_item_id,'_woo_cl_coll_product_id',$prodid);	
							update_post_meta($col_item_id,'_woo_cl_coll_product_type','');	
						}
						update_post_meta($prodid, 'vendor_collection',$col_id);
					}
				}
				
			}
			wp_redirect($this->vendor_dashboard_url.'my-collection/?msg='.$status);
			exit;

		}
		
		/* delete collection */
		if (isset($_GET['delete_coll']) && wp_verify_nonce($_GET['delete_coll'], 'del_col') && isset($_GET['delcol']) && $_GET['delcol']> 0)
		{
			wp_delete_post($_GET['delcol']);
			wp_redirect($this->vendor_dashboard_url.'my-collection/?msg=del');
			exit;
		} 
		
		/* delete products from collection */
		if (isset($_GET['delete_item']) && wp_verify_nonce($_GET['delete_item'], 'del_col_item') && isset($_GET['del']) && $_GET['del']> 0 && isset($_GET['colid']) && $_GET['colid']> 0) 
		{
			wp_delete_post($_GET['del'],true);
			wp_redirect($this->vendor_dashboard_url.'my-collection/?msg=del&id='.$_GET['colid']);
			exit;
		}
		
	}
	
	
	/* get product count of vendor */
	public function ec_get_product_count_of_vendor($vendor_id)
	{
		global $wpdb;
		$products_count = $wpdb->get_var( "SELECT count(*) FROM $wpdb->posts WHERE (post_status = 'publish' || post_status = 'pending' || post_status = 'draft') AND post_type = 'product' and post_author='".$vendor_id."'" );		
		return $products_count;
	}
	
	public function ec_after_wcmp_init() {
		// add a setting field to wcmp endpoint settings page
		add_action('settings_vendor_general_tab_options',  array($this,'add_my_collection_endpoint_option'));
		// save setting option for custom endpoint
		add_filter('settings_vendor_general_tab_new_input',  array($this,'save_custom_endpoint_option'), 10, 2);
		// add custom endpoint
		add_filter('wcmp_endpoints_query_vars', array($this,'add_wcmp_endpoints_query_vars'));
		// add custom menu to vendor dashboard
		add_filter('wcmp_vendor_dashboard_nav',  array($this,'ec_add_tab_to_vendor_dashboard'));
		// display content of custom endpoint
		add_action('wcmp_vendor_dashboard_my-collection_endpoint', array($this,'ec_my_collection_menu_endpoint_content'));
	}
	
	public function add_my_collection_endpoint_option($settings_tab_options) {
		$settings_tab_options['sections']['wcmp_vendor_general_settings_endpoint_section']['fields']['wcmp_custom_vendor_endpoint'] = 	array(
			'title' => __('My Collections', 'dc-woocommerce-multi-vendor'), 
			'type' => 'text', 
			'id' => 'wcmp_custom_vendor_endpoint', 
			'label_for' => 'wcmp_custom_vendor_endpoint', 
			'name' => 'wcmp_custom_vendor_endpoint', 
			'hints' => __('Set endpoint for custom menu page', 'dc-woocommerce-multi-vendor'), 
			'placeholder' => 'my-collection'
		);
		return $settings_tab_options;
	}

	public function save_custom_endpoint_option($new_input, $input) {
		if (isset($input['wcmp_custom_vendor_endpoint']) && !empty($input['wcmp_custom_vendor_endpoint'])) {
			$new_input['wcmp_custom_vendor_endpoint'] = sanitize_text_field($input['wcmp_custom_vendor_endpoint']);
		}
		return $new_input;
	}
	
	public function add_wcmp_endpoints_query_vars($endpoints) {
		$endpoints['my-collection'] = array(
			'label' => __('My Collections', 'dc-woocommerce-multi-vendor'),
			'endpoint' => get_wcmp_vendor_settings('wcmp_custom_vendor_endpoint', 'vendor', 'general', 'my-collection')
		);
		return $endpoints;
	}

	public function ec_add_tab_to_vendor_dashboard($nav) {
		$nav['my-collection'] = array(
			'label' => __('My Collection', 'dc-woocommerce-multi-vendor'), // menu label
			'url' => wcmp_get_vendor_dashboard_endpoint_url('my-collection'), // menu url
			'capability' => true, // capability if any
			'position' => 35, // position of the menu
			'submenu' => array(), // submenu if any
			'link_target' => '_self',
			'nav_icon' => 'dashicons dashicons-layout', // menu icon
		);
		return $nav;
	}

	public function ec_my_collection_menu_endpoint_content(){
		if(is_vendor_dashboard() && is_user_logged_in() && (is_user_wcmp_vendor(get_current_user_id()) || is_user_wcmp_pending_vendor(get_current_user_id()) || is_user_wcmp_rejected_vendor(get_current_user_id()))) 
		{
			$res = $this->getCollectionsByUserId(get_current_user_id());
			?>
			<div class="col-md-12 all-collection-wrapper">
				<div class="panel panel-default panel-pading">
					<div class="form-group">
					<?php
					if(isset($_GET['id']) && $_GET['id'] !='')
					{
						$colData = array();
						if($_GET['id']> 0)
						{
							$colData = $this->getCollectionDetailById($_GET['id']);
						}
						if(isset($_GET['msg']) && $_GET['msg']=="del")
						{
							?>
								<div class="woocommerce-message woocommerce-success">
									<?php _e("Product removed from collection.",""); ?>
								</div>
							
							<?php
						}
						?>
						<div class="panel-heading">
							<h1>
								<?php _e( 'Edit Collection', 'dc-woocommerce-multi-vendor' );?>
							</h1>
						</div>
						<form method="post">
							<div class="row">		
								<label class="col-md-3">Collection Name:</label>
								<div class="col-md-6"><input type='text' name="collection_name" class="form-control" value="<?php echo isset($colData->post_title) ? $colData->post_title : '' ; ?>" /></div>
							</div>
							<div class="row mt15">		
								<label class="col-md-3">Add Artwork:</label>
								<div class="col-md-6">
									<?php
										$myProducts = $this->getMyProducts(get_current_user_id());
									?>
									<select name="product_ids[]" class="form-control" multiple>
										<option value="">Select Product</option>
										<?php
											if($myProducts)
											{
												$colProdcuts = array();
												if($_GET['id'] > 0)
												{
													$colProdcuts = $this->getProductsFromCollection(get_current_user_id(),$_GET['id']);
												}	
												foreach($myProducts as $prod)
												{
													if(!in_array($prod->ID,$colProdcuts))
													{
													?>
														<option value="<?php echo $prod->ID; ?>"><?php echo $prod->post_title; ?></option>
													<?php
													}
												}
											}
										?>
									</select>
								</div>
							</div>
							<div class="row">		
								<div class="col-md-6">
									<input type="hidden" name="hidden_action" value="save_collection" />
									<input type="hidden" name="col_id" value="<?php echo isset($_GET['id']) ? $_GET['id'] : 'new' ;?>" />
									<input type="submit" class="btn btn-default" value="Save" />
									<a href="<?php echo $this->vendor_dashboard_url.'my-collection/';?>" class="btn btn-default">Back</a>
								</div>
							</div>
						</form>
							<div class="mb15 mt15">
								<?php
								if($_GET['id'] > 0)
								{
									$colItems = $this->getCollectionItemsByCollectionId($_GET['id']);
									//print_r($colItems);
									if($colItems)
									{
										$i=1;
										?>
										<br/>
										
										<table id="collection_table" class="table table-striped table-bordered" cellspacing="0" width="100%">
											
										<?php
										foreach($colItems as $item)
										{
											$productId = get_post_meta($item->ID,'_woo_cl_coll_product_id',true);		
											$name = get_the_title($productId);
											$url = $this->vendor_dashboard_url.'my-collection/?del='.$item->ID.'&colid='.$item->col_id;
											$url = wp_nonce_url($url, 'del_col_item','delete_item');
											$imgurl = get_the_post_thumbnail_url($productId,array(100,100));
											if($imgurl=="")
												$imgurl = wc_placeholder_img_src(array(100,100));
											?>
											<tr>
												<td><?php echo $i; ?></td>
												<td><img src='<?php echo $imgurl; ?>' /></td>
												<td><?php echo $name; ?></td>
												<td><a href="<?php echo $url; ?>" class="delete_me"><i class="fa fa-trash"></i></a></td>
											</tr>
											<?php
											$i++;
										}
										?>
										</table>
										<?php
										
									
									}
									else
									{
										?>
										<p><?php _e('No Products added in this collection','');?></p>
										<?php
									}
								}	
								?>	
							</div>
							
						<?php
					}
					else
					{
						if(isset($_GET['msg']) && $_GET['msg']=="new")
						{
							?>
								<div class="woocommerce-message woocommerce-success">
									<?php _e("Collection created successfully.",""); ?>
								</div>
							
							<?php
						}	
						else if(isset($_GET['msg']) && $_GET['msg']=="updated")
						{
							?>
								<div class="woocommerce-message woocommerce-success">
									<?php _e("Collection updated successfully.",""); ?>
								</div>
							
							<?php
						}
						else if(isset($_GET['msg']) && $_GET['msg']=="del")
						{
							?>
								<div class="woocommerce-message woocommerce-success">
									<?php _e("Collection deleted successfully.",""); ?>
								</div>
							
							<?php
						}
					?>
						<div class="text-right mb15" >
							<a class="btn btn-default" href="<?php echo $this->vendor_dashboard_url.'my-collection/?id=new'; ?>">Add New</a>
						</div>
						<?php
							if($res)
							{
						?>
								<table id="collection_table" class="table table-striped table-bordered" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th>No.</th>
											<th>Collection Name</th>
											<th>Products Count</th>
											<th>Edit</th>
											<th>Delete</th>
										</tr>
									</thead>
									<tbody>
									<?php	
										$k=1;
										foreach($res as $col)
										{
											
											$delurl = $this->vendor_dashboard_url.'my-collection/?delcol='.$col->ID;
											$delurl = wp_nonce_url($delurl, 'del_col','delete_coll');
											?>
											<tr>
												<td><?php echo $k; ?></td>
												<td><?php echo $col->post_title; ?></td>
												<td><?php echo $this->getCollectionItemCountByCollectionId($col->ID); ?></td>
												<td><a href="<?php echo $this->vendor_dashboard_url.'my-collection/?id='.$col->ID; ?>" class="btn btn-default">Edit</a></td>
												<td><a href='<?php echo $delurl; ?>' class="btn btn-default delete_me">Delete</a></td>
											</tr>
											<?php
											$k++;
										}
									?>
									</tbody>	
								</table>
						<?php
							}
							else
							{
								$pageid = get_option('cl_coll_lists_page',true);
								$colurl  = "#";
								if($pageid > 0)
									$colurl = get_permalink($pageid);
								?>
								<p>Do you have artwork created in a series or grouping? If so, this is where you place them together as a Collection.  Buyers review Collections to see more in a particular “color range” or “medium type” so grouping your items together is a great way to offer more workable options and increase your chance of a sale.
									If you need help navigating this Collection area, see our <a href="<?php echo $colurl; ?>">Artist Handbook.</a></p>
								<?php	
							}
						}
					?>
				</div>	
			</div>
		</div>
		<?php
		}
	}

	public function getCollectionsByUserId($userid)
	{
		global $wpdb;
		//echo "select * from ".$wpdb->prefix."posts where post_author='".$userid."' and post_status='publish' and post_type='woocollections'";
		$res = $wpdb->get_results("select * from ".$wpdb->prefix."posts where post_author='".$userid."' and post_status='publish' and post_type='woocollections'");	
		return $res;
	}

	public function getCollectionItemCountByCollectionId($col_id)
	{
		global $wpdb;
		return $cnt = $wpdb->get_var("select count(*) from ".$wpdb->prefix."posts p where  post_type='woocollitems' and post_parent='".$col_id."'");		
	}

	public function getCollectionItemsByCollectionId($col_id)
	{
		global $wpdb;
		return $wpdb->get_results("select ID,post_parent as col_id from ".$wpdb->prefix."posts p where  post_type='woocollitems' and post_parent='".$col_id."'");		
	}

	public function getCollectionDetailById($colid)
	{
		global $wpdb;
		$res = $wpdb->get_row("select * from ".$wpdb->prefix."posts where ID='".$colid."'");	
		return $res;
	}
	
	public function getMyProducts($userid)
	{
		global $wpdb;
		$res = $wpdb->get_results("select ID,post_title from ".$wpdb->prefix."posts where post_author='".$userid."' and post_status='publish' and post_type='product'");	
		return $res;
	}
	
	public function getProductsFromCollection($userid,$colid)
	{
		global $wpdb;
		$res = $wpdb->get_col("select meta_value from ".$wpdb->prefix."postmeta pm, ".$wpdb->prefix."posts p where pm.post_id=p.ID and post_type='woocollitems' and post_author='".$userid."' and post_parent='".$colid."'");	
		return $res;
	}
}
?>
