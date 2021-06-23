<?php
defined( 'ABSPATH' ) || exit;

class ec_common_cls{
	public function __construct() 
	{
		global $wpdb;
		$table_name = $wpdb->prefix."parent_child_taxonomy";
		$checkSQL = "show tables like '$table_name'";
		if($wpdb->get_var($checkSQL) != $table_name)
		{
			$ec_table = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "parent_child_taxonomy` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`parent_id` int(11) NOT NULL,
				`child_id` int(11) NOT NULL,
				`taxonomy` varchar(255) NOT NULL,
				PRIMARY KEY (`id`)
			 ) DEFAULT CHARSET=utf8;";
			/*$wpdb->query($bwg_image);*/
			require_once(ABSPATH . "wp-admin/includes/upgrade.php");
			dbDelta($ec_table);
		}
		
		$table_name1 = $wpdb->prefix."request_quote";
		$checkSQL1 = "show tables like '$table_name1'";
		if($wpdb->get_var($checkSQL1) != $table_name1)
		{
			$ec_table1 = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "request_quote` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`product_id` int(11) NOT NULL,
				`buyer_id` int(11) NOT NULL,
				`request_no` varchar(255) NOT NULL,
				`request_date` date NOT NULL,
				`custom_modification` text NOT NULL,
				`sample_proof` tinyint(1) NOT NULL DEFAULT 0,
				`piece_framed` text NOT NULL,
				`special_instruction` text NOT NULL,
				`comment_ec` text NOT NULL,
				`delivery_location` varchar(255) NOT NULL,
				`delivery_date` date NOT NULL,
				`qty` int(11) NOT NULL,
				`shipping_method` text NOT NULL,
				`status` varchar(255) NOT NULL,
				`status_dt` date NOT NULL,
				`note` text NOT NULL,
				PRIMARY KEY (`id`)
			 ) DEFAULT CHARSET=utf8;";
			/*$wpdb->query($bwg_image);*/
			require_once(ABSPATH . "wp-admin/includes/upgrade.php");
			dbDelta($ec_table1);
		}
		
		add_action( 'medium_add_form_fields', array($this,'ec_custom_taxonomy_add_new_meta_field'), 10, 2 );
		add_action( 'medium_edit_form_fields', array($this,'ec_medium_taxonomy_edit_meta_field'), 10, 2 );
		add_action( 'edited_medium', array($this,'ec_save_medium_taxonomy_custom_meta'), 10, 2 );  
		add_action( 'create_medium', array($this,'ec_save_medium_taxonomy_custom_meta'), 10, 2 );

		add_action( 'types_add_form_fields', array($this,'ec_custom_taxonomy_add_new_meta_field'), 10, 2 );
		add_action( 'types_edit_form_fields', array($this,'ec_type_taxonomy_edit_meta_field'), 10, 2 );
		add_action( 'edited_types', array($this,'ec_save_type_taxonomy_custom_meta'), 10, 2 );  
		add_action( 'create_types', array($this,'ec_save_type_taxonomy_custom_meta'), 10, 2 );
		
		add_action('wp_ajax_get_taxonomy_child',array($this,'ec_get_taxonomy_child'));
		add_action('wp_ajax_nopriv_get_taxonomy_child',array($this,'ec_get_taxonomy_child'));
		add_action('wp_ajax_add_request_quote_info',array($this,'ec_add_request_quote'));
		add_action('wp_ajax_nopriv_add_request_quote_info',array($this,'ec_add_request_quote'));
		//add_action( 'woocommerce_after_add_to_cart_button', array($this,'ec_add_request_quote_button'), 10, 0 );
		add_shortcode('ec_request_quote_button', array($this,'ec_add_request_quote_button'));
		add_action( 'wp_footer', array($this,'ec_add_request_quote_form'), 10, 0 );
		
		add_action( 'show_user_profile', array($this,'ec_extra_user_profile_fields'),10);
		add_action( 'edit_user_profile', array($this,'ec_extra_user_profile_fields'),10);
		
		add_action( 'personal_options_update', array($this,'ec_save_extra_fields_info'));
		add_action( 'edit_user_profile_update', array($this,'ec_save_extra_fields_info'));
		
		/* redirect artist dashboard page to my acooount if not logged in*/
		add_action( 'template_redirect', array($this,'ec_redirect_to_specific_page'));
		
	}
	
	
	function ec_redirect_to_specific_page() {
		global $post;
		
		$vendor_page = get_option('wcmp_product_vendor_vendor_page_id');
		if (!is_user_logged_in() && $post->ID == $vendor_page ) {
			$login_page = get_permalink( get_option('woocommerce_myaccount_page_id') );
			wp_redirect($login_page); 
			exit;
		}
	}

	function ec_extra_user_profile_fields($vendor)
	{
		$vendor_id = $vendor->data->ID;
		$vendor_primary_dis = get_user_meta($vendor_id, 'primary_discipline', true);
		$accept_commission = get_user_meta($vendor_id, 'accept_commission', true);
		$vendor_statement = get_user_meta($vendor_id, 'vendor_statement', true);
		$vendor_bio = get_user_meta($vendor_id, 'vendor_bio', true);
		$vendor_exhibition = get_user_meta($vendor_id, 'vendor_exhibition', true);
		$vendor_education = get_user_meta($vendor_id, 'vendor_education', true);
		$vendor_awards = get_user_meta($vendor_id, 'vendor_awards', true);
		$vendor_professional_background = get_user_meta($vendor_id, 'vendor_professional_background', true);
		$vendor_tags = get_user_meta($vendor_id, 'vendor_tags', true);
		$vendor_intro = get_user_meta($vendor_id, 'vendor_intro', true);

		?>
		<h3><?php _e("Extra Vendor information", "blank"); ?></h3>

		<table class="form-table">
		<tr>
			<th><label for="primary_discipline"><?php _e("Primary Discipline"); ?></label></th>
			<td>
				<select name="primary_discipline" id="primary_discipline" class="form-control">
					<option value="">Select</option>
					<option <?php if(isset($vendor_primary_dis) && $vendor_primary_dis=="Ceramics") { echo "selected='selected'"; } ?> value="Ceramics">Ceramics</option>
					<option <?php if(isset($vendor_primary_dis) && $vendor_primary_dis=="Digital Art") { echo "selected='selected'"; } ?> value="Digital Art">Digital Art</option>
					<option <?php if(isset($vendor_primary_dis) && $vendor_primary_dis=="Fiber/Textiles") { echo "selected='selected'"; } ?> value="Fiber/Textiles">Fiber/Textiles</option>
					<option <?php if(isset($vendor_primary_dis) && $vendor_primary_dis=="Furniture") { echo "selected='selected'"; } ?> value="Furniture">Furniture</option>
					<option <?php if(isset($vendor_primary_dis) && $vendor_primary_dis=="Glass") { echo "selected='selected'"; } ?> value="Glass">Glass</option>
					<option <?php if(isset($vendor_primary_dis) && $vendor_primary_dis=="Home Goods") { echo "selected='selected'"; } ?> value="Home Goods">Home Goods</option>
					<option <?php if(isset($vendor_primary_dis) && $vendor_primary_dis=="Illustration") { echo "selected='selected'"; } ?> value="Illustration">Illustration</option>
					<option <?php if(isset($vendor_primary_dis) && $vendor_primary_dis=="Industrial Design") { echo "selected='selected'"; } ?> value="Industrial Design">Industrial Design</option>
					<option <?php if(isset($vendor_primary_dis) && $vendor_primary_dis=="Lighting Design") { echo "selected='selected'"; } ?> value="Lighting Design">Lighting Design</option>
					<option <?php if(isset($vendor_primary_dis) && $vendor_primary_dis=="Mixed Media") { echo "selected='selected'"; } ?> value="Mixed Media">Mixed Media</option>
					<option <?php if(isset($vendor_primary_dis) && $vendor_primary_dis=="Painting") { echo "selected='selected'"; } ?> value="Painting">Painting</option>
					<option <?php if(isset($vendor_primary_dis) && $vendor_primary_dis=="Photography") { echo "selected='selected'"; } ?> value="Photography">Photography</option>
					<option <?php if(isset($vendor_primary_dis) && $vendor_primary_dis=="Printmaking/Paper") { echo "selected='selected'"; } ?> value="Printmaking/Paper">Printmaking/Paper</option>
					<option <?php if(isset($vendor_primary_dis) && $vendor_primary_dis=="Product Design") { echo "selected='selected'"; } ?> value="Product Design">Product Design</option>
					<option <?php if(isset($vendor_primary_dis) && $vendor_primary_dis=="Sculpture") { echo "selected='selected'"; } ?> value="Sculpture">Sculpture</option>
					<option <?php if(isset($vendor_primary_dis) && $vendor_primary_dis=="Woodworking") { echo "selected='selected'"; } ?> value="Woodworking">Woodworking</option>
					<option <?php if(isset($vendor_primary_dis) && $vendor_primary_dis=="Other") { echo "selected='selected'"; } ?> value="Other">Other</option>
				</select>
			</td>
		</tr>
		<tr>
			<th><label for="vendor_statement"><?php _e("Artist Statement (About)"); ?></label></th>
			<td>
				<textarea class="form-control" id="vendor_statement" name="vendor_statement" ><?php echo isset($vendor_statement) ? $vendor_statement : ''; ?></textarea>
			</td>
		</tr>
		<tr>
			<th><label for="vendor_bio"><?php _e("Bio"); ?></label></th>
			<td>
				<textarea class="form-control" id="vendor_bio" name="vendor_bio" ><?php echo isset($vendor_bio) ? $vendor_bio : ''; ?></textarea>
			</td>
		</tr>
		<tr>
			<th><label for="accept_commission"><?php _e("Are you currently accepting commissions?"); ?></label></th>
			<td>
				<input type='checkbox' class="form-control" id="accept_commission" name="accept_commission" value="1" <?php if(isset($accept_commission) && $accept_commission ==1) { echo "checked='checked'"; } ?> />
			</td>
		</tr>
		<tr>
			<th><label for="vendor_exhibition"><?php _e("Exhibition/Shows/Commissions"); ?></label></th>
			<td>
				<textarea class="form-control" name="vendor_exhibition" id="vendor_exhibition" ><?php echo isset($vendor_exhibition) ? $vendor_exhibition : ''; ?></textarea>
			</td>
		</tr>
		<tr>
			<th><label for="vendor_education"><?php _e("Education"); ?></label></th>
			<td>
				<textarea class="form-control" id="vendor_education" name="vendor_education" ><?php echo isset($vendor_education) ? $vendor_education : ''; ?></textarea>
			</td>
		</tr>
		<tr>
			<th><label for="vendor_awards"><?php _e("Awards & Distinctions"); ?></label></th>
			<td>
				<textarea class="form-control" name="vendor_awards" ><?php echo isset($vendor_awards) ? $vendor_awards : ''; ?></textarea>
			</td>
		</tr>
		<tr>
			<th><label for="vendor_professional_background"><?php _e("Professional Experience"); ?></label></th>
			<td>
				<textarea class="form-control" id="vendor_professional_background" name="vendor_professional_background" ><?php echo isset($vendor_professional_background) ? $vendor_professional_background : ''; ?></textarea>
			</td>
		</tr>
		<tr>
			<th><label for="vendor_tags"><?php _e("Artist Tags (keywords)"); ?></label></th>
			<td>
				<input class="form-control" type="text" id="vendor_tags"  name="vendor_tags" value="<?php echo isset($vendor_tags) ? $vendor_tags : ''; ?>">
			</td>
		</tr>
		<tr>
			<th><label for="vendor_intro"><?php _e("Introduction Video (Youtube Link)"); ?></label></th>
			<td>
				<input class="form-control" type="text" id="vendor_intro"  name="vendor_intro" value="<?php echo isset($vendor_intro) ? $vendor_intro : ''; ?>"><br/>
                <span>30-45 seconds max length video is best.</span>
			</td>
		</tr>
		</table>
	<?php 
	}
	function ec_save_extra_fields_info($vendor_id)
	{
		if(isset($_POST['primary_discipline']))
		{
			update_user_meta($vendor_id,'primary_discipline',$_POST['primary_discipline']);	
		}
		if(isset($_POST['accept_commission']) &&$_POST['accept_commission']=="1")
		{
			update_user_meta($vendor_id,'accept_commission',1);	
		}
		else
			update_user_meta($vendor_id,'accept_commission',0);	
		if(isset($_POST['vendor_statement']))
		{
			update_user_meta($vendor_id,'vendor_statement',$_POST['vendor_statement']);	
		}
		if(isset($_POST['vendor_bio']))
		{
			update_user_meta($vendor_id,'vendor_bio',$_POST['vendor_bio']);	
		}
		if(isset($_POST['vendor_exhibition']))
		{
			update_user_meta($vendor_id,'vendor_exhibition',$_POST['vendor_exhibition']);	
		}
		if(isset($_POST['vendor_education']))
		{
			update_user_meta($vendor_id,'vendor_education',$_POST['vendor_education']);	
		}
		if(isset($_POST['vendor_awards']))
		{
			update_user_meta($vendor_id,'vendor_awards',$_POST['vendor_awards']);	
		}
		if(isset($_POST['vendor_professional_background']))
		{
			update_user_meta($vendor_id,'vendor_professional_background',$_POST['vendor_professional_background']);	
		}
		if(isset($_POST['vendor_tags']))
		{
			update_user_meta($vendor_id,'vendor_tags',$_POST['vendor_tags']);	
		}
		if(isset($_POST['vendor_intro']))
		{
			update_user_meta($vendor_id,'vendor_intro',$_POST['vendor_intro']);	
		}

	
	}
	
	function ec_add_request_quote()
	{
		if(isset($_POST['product_id']) && $_POST['product_id'] > 0 && isset($_POST['request_quote_no']) && $_POST['request_quote_no']!='' && isset($_POST['delivery_location']) && $_POST['delivery_location']!='' && isset($_POST['delivery_date']) && $_POST['delivery_date']!='' && 	isset($_POST['qty']) && $_POST['qty'] > 0)
		{
			global $wpdb;
			$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."request_quote (product_id,buyer_id,request_no,request_date,custom_modification,sample_proof,piece_framed,special_instruction,delivery_location,delivery_date,qty,shipping_method,comment_ec,status) values(%d,%d,%s,%s,%s,%d,%s,%s,%s,%s,%d,%s,%s,%s)",
				$_POST['product_id'],
				$_POST['buyer_id'],
				$_POST['request_quote_no'],
				$_POST['request_date'],
				$_POST['custom_modification'],
				$_POST['sample_proof'],
				$_POST['piece_framed'],
				$_POST['special_instruction'],
				$_POST['delivery_location'],
				$_POST['delivery_date'],
				$_POST['qty'],
				$_POST['shipping_method'],
				$_POST['comment_ec'],
				'pending')
			);
			$this_insert = $wpdb->insert_id;
			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
			$buyer = get_userdata($_POST['buyer_id']);
			$to = get_option('admin_email');
			$subject = 'New request quote form';
			$message = 'Hello,<br/><br/>';
			$message .='You have new request for quote form by <b>'.get_user_meta($_POST['buyer_id'],'first_name',true).' '.get_user_meta($_POST['buyer_id'],'last_name',true).'</b><br/><br/>';
			$message .='Request No. : #'.$_POST['request_quote_no'].'<br/>';
			$message .='Request Date : '.$_POST['request_date'].'<br/>';
			$message .='Product :'.get_the_title($_POST['product_id']).'<br/>';
			$message .='Delivery Location : '.$_POST['delivery_location'].'<br/>';
			$message .='Delivery Date : '.$_POST['delivery_date'].'<br/>';
			$message .="You can check full detail on site. Click <a href='".site_url('wp-admin/admin.php?page=request_info_page&action=edit&id='.$this_insert)."'>here</a>";
			$headers[] = 'From: '.$blogname.' <'.$buyer->user_email.'>;';
			$headers[] = 'Content-Type: text/html; charset=UTF-8';
			
			wp_mail( $to, $subject, $message,$headers);
			echo $this_insert;
		}
		else
		{
			echo "Please fill proper details";	
		}
		exit;
	}
	
	function ec_add_request_quote_form(){
		if(is_product())
		{
			global $post;
			include_once(get_stylesheet_directory().'/request-quote-form.php');	
		}
	}
	
	function ec_add_request_quote_button() { 
		if(is_product())
		{
			global $post;
			return '<div class="request_quote_sec"><a class="button ec_request_quote" href="#" prodid="'.$post->ID.'">'. __("Request a Quote", "woocommerce").'</a><p class="ec_note">Certified Trade Buyers only. To register, click <a href="'.site_url('trade-registration').'">here.</a></p></div>';
		}
	}
	
	/* add term - add custom meta for product cat in medium */
	/* Edit Term - add custom meta for product cat in type */
	public function ec_medium_taxonomy_edit_meta_field($term) 
	{
		// put the term ID into a variable
		$t_id = $term->term_id;
		// retrieve the existing value(s) for this meta field. This returns an array
		$term_meta = get_option( "medium_$t_id" ); 
		$val = isset($term_meta['parent_cat']) ?  $term_meta['parent_cat'] 	: array();
		?>
		<tr class="form-field">
			<th scope="row" valign="top"><label for="parent_cat"><?php _e( 'Product Category', 'pippin' ); ?></label></th>
			<td>
				<select name="term_meta[parent_cat][]"  multiple  id="parent_cat">
					<option>Please Select</option>
					<?php
						$args = array( 'hide_empty' => 1, 'parent' => 0 ,'exclude' =>array(17));    
						$terms = get_terms('product_cat', $args);
						if($terms)
						{
							foreach($terms as $cat)
							{
								?>
								<option <?php if(in_array($cat->term_id,$val)) { echo "selected='selcted'"; } ?> value="<?php echo $cat->term_id; ?>"><?php echo $cat->name; ?></option>
								<?php
							}
						}
					?>
				</select>
				<p class="description"><?php _e( 'Select Parent Category','pippin' ); ?></p>
			</td>
		</tr>
	<?php
	}
	
	/* save custom taxonomy meta */
	public function ec_save_medium_taxonomy_custom_meta($term_id)
	{
		if ( isset( $_POST['term_meta'] ) ) {
			$t_id = $term_id;
			$term_meta = get_option( "medium_$t_id" );
			$cat_keys = array_keys( $_POST['term_meta'] );
			foreach ( $cat_keys as $key ) {
				if( isset ( $_POST['term_meta'][$key] ) ) {
					$term_meta[$key] = $_POST['term_meta'][$key];
					if($term_meta[$key])
					{
						foreach($term_meta[$key] as $parentid)
						{
							if(!$this->ec_check_parent_child_taxonomy($parentid,$t_id,'medium'))
							{
								$this->ec_insert_parent_child_taxonomy($parentid,$t_id,'medium');
								
							}
						}
					}
				}
			}
			// Save the option array.
			update_option( "medium_$t_id", $term_meta );
		}
	}

	/* add term - add custom meta for product cat in medium */
	public function ec_custom_taxonomy_add_new_meta_field() {
		// this will add the custom meta field to the add new term page
		?>
		<div class="form-field">
			<label for="parent_cat"><?php _e( 'Category', 'pippin' ); ?></label>
			<select name="term_meta[parent_cat][]" multiple id="parent_cat">
				<option>Please Select</option>
				<?php
					$args = array( 'hide_empty' => 0, 'parent' => 0 ,'exclude' =>array(17));    
					$terms = get_terms('product_cat', $args);
					if($terms)
					{
						foreach($terms as $cat)
						{
							?>
							<option value="<?php echo $cat->term_id; ?>"><?php echo $cat->name; ?></option>
							<?php
						}
					}
				?>
			</select>
			<p class="description"><?php _e( 'Select Parent Category','pippin' ); ?></p>
		</div>
	<?php
	}
	
	/* Edit Term - add custom meta for product cat in type */
	public function ec_type_taxonomy_edit_meta_field($term) {
		// put the term ID into a variable
		$t_id = $term->term_id;
		// retrieve the existing value(s) for this meta field. This returns an array
		$term_meta = get_option( "types_$t_id" ); 
		$val = isset($term_meta['parent_cat']) ?  $term_meta['parent_cat'] 	: '';
		?>
		<tr class="form-field">
			<th scope="row" valign="top"><label for="parent_cat"><?php _e( 'Product Category', 'pippin' ); ?></label></th>
			<td>
				<select name="term_meta[parent_cat][]"  multiple  id="parent_cat">
					<option>Please Select</option>
					<?php
						$args = array( 'hide_empty' => 0, 'parent' => 0 ,'exclude' =>array(17));    
						$terms = get_terms('product_cat', $args);
						if($terms)
						{
							foreach($terms as $cat)
							{
								?>
								<option <?php if(in_array($cat->term_id,$val)) { echo "selected='selcted'"; } ?> value="<?php echo $cat->term_id; ?>"><?php echo $cat->name; ?></option>
								<?php
							}
						}
					?>
				</select>
				<p class="description"><?php _e( 'Select Parent Category','pippin' ); ?></p>
			</td>
		</tr>
	<?php
	}
	
	/* save custom taxonomy meta */
	public function ec_save_type_taxonomy_custom_meta($term_id)
	{
		global $wpdb;
		if ( isset( $_POST['term_meta'] ) ) {
			$t_id = $term_id;
			$term_meta = get_option( "types_$t_id" );
			$cat_keys = array_keys( $_POST['term_meta'] );
			foreach ( $cat_keys as $key ) {
				
				if( isset ( $_POST['term_meta'][$key] ) ) {
					$term_meta[$key] = $_POST['term_meta'][$key];
					if($term_meta[$key])
					{
						foreach($term_meta[$key] as $parentid)
						{
							if(!$this->ec_check_parent_child_taxonomy($parentid,$t_id,'types'))
							{
								$this->ec_insert_parent_child_taxonomy($parentid,$t_id,'types');
								
							}
						}
					}
				}
			}
			//exit;
			// Save the option array.
			update_option( "types_$t_id", $term_meta );
		}
	}
	
	public function ec_check_parent_child_taxonomy($parentid,$termid,$taxonomy)
	{
		global $wpdb;
		return $wpdb->get_var("select count(*) from ".$wpdb->prefix."parent_child_taxonomy where taxonomy='".$taxonomy."' and child_id='".$termid."' and parent_id='".$parentid."'");
	}
	
	public function ec_insert_parent_child_taxonomy($parentid,$childid,$taxonomy)
	{
		global $wpdb;
		$wpdb->query(
			$wpdb->prepare("insert into ".$wpdb->prefix."parent_child_taxonomy (parent_id,child_id,taxonomy) values(%d,%d,%s)",	
				$parentid,
				$childid,
				$taxonomy)
			);
	}
	
	public function ec_get_taxonomy_child()
	{
		$data = array();
		$arr['data'] = $data;
		$arr['selected'] =array();
		$arr['success'] = 0;
		if(isset($_POST['parentid']) && $_POST['parentid'] > 0 && isset($_POST['type']) && $_POST['type']!='')
		{
			global $wpdb;	
			//echo "select * from ".$wpdb->prefix."parent_child_taxonomy where taxonomy='".$_POST['type']."' and parent_id='".$_POST['parentid']."'";
			if(isset($_POST['product_id']) && $_POST['product_id'] > 0)
			{
				$terms =get_the_terms($_POST['product_id'], $_POST['type']);
				if($terms)
				{
					foreach($terms as $sel)
					{
						$selArr[]=$sel->term_id;
					}
					$arr['selected']=$selArr;
				}
			}
			$data = $wpdb->get_results("select p.*,t.name from ".$wpdb->prefix."parent_child_taxonomy p, ".$wpdb->prefix."terms  t where p.child_id =t.term_id and taxonomy='".$_POST['type']."' and parent_id='".$_POST['parentid']."'");	
			$arr['data'] = $data;
			$arr['success'] = 1;
			
		}	
		echo json_encode($arr);
		exit;
	}

}