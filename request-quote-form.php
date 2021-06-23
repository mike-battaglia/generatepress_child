<?php
$user = wp_get_current_user();
if ( !in_array( 'certified_buyer', (array) $user->roles ) ) {
	?>
	<div class="ec_overlap">
		<div class="ec_popup">
		<div class="ec_close"><a href="#" class='ec_close_me'>X</a></div>
		
		<div class="ec_popup_content">
			<div class="">
				<h3>Product Quote Request</h3>
				<p>We would love for you to request a quote for this artwork but that’s only available to certified trade buyers. If that’s you, register <a href="<?php echo site_url('trade-registration'); ?>">here.</a></p>
			</div>
			<p
		</div>
	</div>
	<?php
}
else
{
?>
<div class="ec_overlap">
<?php
	global $post;
	$reqno = ec_unique();
	$dt = date('Y-m-d');
	$product = wc_get_product($post->ID);
	$colors =get_the_terms($post->ID, 'color');
	$color_str ="";
	if($colors)
	{
		foreach($colors as $term)
		{
			if($color_str=="")
				$color_str = $term->name;
			else
				$color_str .= ", ".$term->name;
		}	
	}
	
	$city = get_user_meta( $post->post_author, '_vendor_city', true ); 
	$state = get_user_meta( $post->post_author, '_vendor_state', true ); 
	$country = get_user_meta( $post->post_author, '_vendor_country', true ); 
	$location="";
	if($city!='')
		$location = $city;
	if($state!='' && $location!='')
		$location .= ",". $state;
	
	if($country!='' && $location!='')
		$location .= ",". $country;
?>

	<div class="ec_popup">
		<div class="ec_close"><a href="#" class='ec_close_me'>X</a></div>
		
		<div class="ec_popup_content">
			<div class="">
				<h3>Product Quote Request</h3>
				<p>Thank you for your interest in this special artwork! We expect to get back to you within 24 hours after reaching out to the artist. Your quote should include responses, estimated shipping costs and lead time. If this product is no longer available, we will ask the artist for suitable options. </p>
				<div class="mb10"><span>Request Quote No.:#<?php echo $reqno?></span>&nbsp;&nbsp;<span>Request Date.:<?php echo $dt; ?></span></div>
			</div>
			
			<form method="post" class='frmrequestquote'>
				<input type='hidden' name="request_date" value="<?php echo $dt; ?>" />
				<input type='hidden' name="request_quote_no" value="<?php echo $reqno; ?>" />
				<input type='hidden' name="product_id" value="<?php echo $post->ID; ?>" />
				<input type='hidden' name="buyer_id" value="<?php echo get_current_user_id(); ?>" />
				<div  class="row">
					<div class="form-group">
						<div class="left-sec">
							<?php
								echo $product->get_image();
							?>
						</div>
						<div class="reight-sec">
							<div class="col-lg-6 col-sm-12 mb10">
								<label class="left-sec">Artist</label>
								<div class="col-lg-8 prod-info"><?php echo get_the_author_meta('first_name',$post->post_author); ?></div>
							</div>
							<div class="col-lg-6 col-sm-12 mb10">
								<label class="left-sec">Title</label>
								<div class="col-lg-8 prod-info"><?php echo $product->get_title(); ?></div>
							</div>
							<div class="col-lg-6 col-sm-12 mb10">
								<label class="left-sec">SKU</label>
								<div class="col-lg-8 prod-info"><?php echo $product->get_sku(); ?></div>
							</div>
							<div class="col-lg-6 col-sm-12 mb10">
								<label class="left-sec">Colors</label>
								<div class="col-lg-8 prod-info"><?php echo $color_str; ?></div>
							</div>
							<div class="col-lg-6 col-sm-12 mb10">
								<label class="left-sec">Size</label>
								<div class="col-lg-8 prod-info">
									<?php echo $dimensions = wc_format_dimensions($product->get_dimensions(false)); ?>
								</div>
							</div>
							<div class="col-lg-6 col-sm-12 mb10">
								<label class="left-sec">Material/Medium</label>
								<div class="col-lg-8 prod-info"><?php echo $product->get_short_description(); ?></div>
							</div>
							<div class="col-lg-6 col-sm-12 mb10">
								<label class="left-sec">Ship From</label>
								<div class="col-lg-8 prod-info"><?php echo $location; ?></div>
							</div>
						</div>
						<div class="full-sec">
							<div class="ec_error"></div>
							<div class="col-lg-6 col-sm-12 mb10">
								<label class="col-lg-4"><b>Do you need custom modifications to this item? If so specify the details.</b></label>
								<div class="col-lg-8">
									<textarea name="custom_modification"></textarea>
								</div>
							</div>
							
							<div class="col-lg-6 col-sm-12 mb10">
								<label class="col-lg-4"><b>Product Price </b></label>
								<div class="col-lg-8">
									<span><span class="actual_price"><?php echo get_woocommerce_currency_symbol(); ?></span><?php echo $product->get_price(); ?></span>
									<input type='hidden' name="product_price" value="<?php echo $product->get_price(); ?>" />
								</div>
							</div>
							<div class="col-lg-6 col-sm-12 mb10">
								<label class="col-lg-4"><b>Designer Net</b> </label>
								<div class="col-lg-8">
									<span><span class="designer_price"><?php echo get_woocommerce_currency_symbol(); ?></span><?php $designer_price = ($product->get_price()*0.80); echo $designer_price; ?></span>
									<input type='hidden' name="designer_price" value="<?php echo $designer_price; ?>" />
								</div>
							</div>
							<div class="col-lg-6 col-sm-12 mb10">
								<label class="col-lg-4"><b>Number of Units Requested </b></label>
								<div class="col-lg-8">
									<input type='number' name="qty" min="1" step="1" class="request_qty required number" />
								</div>
							</div>
							<div class="col-lg-6 col-sm-12 mb10">
								<label class="col-lg-8"><b>Order Subtotal (less shipping & handling) </b></label>
								<div>
									<span class='designer_total_sec'><b>Designer : </b><span class='designer_total'></span><?php echo get_woocommerce_currency_symbol(); ?></span>
								</div>
							</div>	
							<div class="col-lg-6 col-sm-12 mb10">
								<div>
									<span class='client_total_sec'><b>Client : </b><span class='client_total'></span><?php echo get_woocommerce_currency_symbol(); ?></span>
								</div>
							</div>	
							<div class="col-lg-6 col-sm-12 mb10">
								<label class="col-lg-4"><b>Delivery Location: </b></label>
								<div class="col-lg-8">
									<textarea  name="delivery_location" class="required"></textarea>
								</div>
							</div>
							<div class="col-lg-6 col-sm-12 mb10">
								<label class="col-lg-4"><b>Delivery Date</b> </label>
								<div class="col-lg-8">
									<input type='text' name="delivery_date" class="ec_date required" />
								</div>
							</div>
							<div class="col-lg-6 col-sm-12 mb10">
								<label class="col-lg-4"><b>Do you want to purchase a sample/artist proof for your client?  </b></label>
								<div class="col-lg-8">
									<input type="checkbox" name="sample_proof" value="1"/>
								</div>
							</div>
							<div class="col-lg-6 col-sm-12 mb10">
								<label class="col-lg-4"><b>Preferred Method of Shipping  </label>
								<div class="col-lg-8">
									<textarea  name="shipping_method"></textarea>
									<span class='ec_note'>The cost of shipping product by ground carrier is calculated by distance and carton (packing) dimensions and weight. We recommend you choose UPS or FEDEX.</span>
								</div>
							</div>
							<div class="col-lg-6 col-sm-12 mb10">
								<label class="col-lg-4"><b>If you need this piece framed, specify details.</b></label>
								<div class="col-lg-8">
									<textarea name="piece_framed"></textarea>
								</div>
							</div>
							<div class="col-lg-6 col-sm-12 mb10">
								<label class="col-lg-4"><b>Special Instructions or Requests for Artist</b></label>
								<div class="col-lg-8">
									<textarea name="special_instruction"></textarea>
								</div>
							</div>
							<div class="col-lg-6 col-sm-12 mb10">
								<label class="col-lg-4"><b>Comments for Team EC:</b></label>
								<div class="col-lg-8">
									<textarea name="comment_ec"></textarea>
								</div>
							</div>
							<input type="submit" value="Submit" />
						</div>
					</div>					
				</div>
			</form>
		</div>
	</div>
</div>

<?php
}
?>
