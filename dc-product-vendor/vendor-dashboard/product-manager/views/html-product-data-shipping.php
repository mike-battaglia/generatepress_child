<?php
/**
 * Shipping product tab template
 *
 * Used by wcmp-afm-add-product.php template
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/vendor-dashboard/product-manager/views/html-product-data-shipping.php
 *
 * @author  WC Marketplace
 * @package     WCMp/Templates
 * @version   3.3.0
 */
defined( 'ABSPATH' ) || exit;
?>
<div role="tabpanel" class="tab-pane fade" id="shipping_product_data">
    <div class="row-padding"> 
        <?php if ( wc_product_weight_enabled() ) : ?> 
            <div class="form-group">
                <label class="control-label col-sm-3 col-md-3" for="_weight"><?php printf( __( 'Artwork Weight (%s)', 'woocommerce' ), get_option( 'woocommerce_weight_unit' ) ); ?>*</label>
                <div class="col-md-6 col-sm-9">
                    <input class="form-control required" type="text" id="_weight" name="_weight" value="<?php echo $product_object->get_weight( 'edit' ); ?>" placeholder="<?php echo wc_format_localized_decimal( 0 ); ?>" />
                </div>
            </div> 
        <?php endif; ?>
        <?php if ( wc_product_dimensions_enabled() ) : ?> 
            <div class="form-group">
                <label class="control-label col-sm-3 col-md-3" for="product_length"><?php printf( __( 'Artwork Dimensions (%s)', 'woocommerce' ), get_option( 'woocommerce_dimension_unit' ) ); ?>*</label>
                <div class="col-md-6 col-sm-9">
                    <div class="row">
						<div class="col-md-4">
                            <input class="form-control col-md-4 required input-text wc_input_decimal last" placeholder="<?php esc_attr_e( 'Height', 'woocommerce' ); ?>"  size="6" type="text" name="_height" value="<?php echo esc_attr( wc_format_localized_decimal( $product_object->get_height( 'edit' ) ) ); ?>" />
                            <span>Height*</span>
                        </div>
                        <div class="col-md-4">
                            <input class="form-control col-md-4 required input-text wc_input_decimal" placeholder="<?php esc_attr_e( 'Width', 'woocommerce' ); ?>" size="6" type="text" name="_width" value="<?php echo esc_attr( wc_format_localized_decimal( $product_object->get_width( 'edit' ) ) ); ?>" />
                            <span>Width*</span>
                        </div>
						<div class="col-md-4">
                            <input class="form-control col-md-4" id="product_length" placeholder="<?php esc_attr_e( 'Depth', 'woocommerce' ); ?>" size="6" type="text" name="_length" value="<?php echo esc_attr( wc_format_localized_decimal( $product_object->get_length( 'edit' ) ) ); ?>" />
                            <span>Depth</span>
                        </div>
                    </div>
                </div>
            </div> 
        <?php endif; ?>
        <?php do_action( 'wcmp_afm_product_options_dimensions', $post->ID, $product_object, $post ); ?> 
        <div class="form-group hide_me">
            <label class="control-label col-sm-3 col-md-3" for="product_shipping_class"><?php esc_html_e( 'Shipping class', 'woocommerce' ); ?></label>
            <div class="col-md-6 col-sm-9">
                <select name="product_shipping_class" id="product_shipping_class" class="form-control regular-select">
                    <?php foreach ( get_current_vendor_shipping_classes() as $key => $class_name  ) : ?>
                        <option value="<?php esc_attr_e( $key ); ?>" <?php selected( $product_object->get_shipping_class_id( 'edit' ), $key ); ?>><?php esc_html_e( $class_name ); ?></option>
                    <?php endforeach; ?>
                    <option value="-1"><?php esc_html_e( 'No shipping class', 'woocommerce' ); ?></option>
                </select>
            </div>
        </div>
    <!-- MBATT: Delete lines 58 - 76 -->
        <div class="form-group">
			<?php $shipping_type = get_post_meta($post->ID,'_shipping_type',true); ?>
			<label class="control-label col-sm-3 col-md-3" for="shipping_type"><?php esc_html_e( 'Shipping Type*', 'woocommerce' ); ?></label>
			<div class="col-md-6 col-sm-9">
                <select name="_shipping_type" id="shipping_type" class="form-control regular-select required shipping_type">
                   <option value="" >Select Shipping Method</option>
                   <option value="free_shipping" <?php if($shipping_type == "free_shipping") { echo "selected='selected'"; } ?>>Free Shipping</option>
                   <option value="flat_rate"  <?php if($shipping_type == "flat_rate") { echo "selected='selected'"; } ?>>Flat Rate</option>
                </select>
            </div>
        </div>
        <div class="form-group shipping_price_sec" <?php if($shipping_type == "flat_rate") { echo "style='display:block;'"; } else { echo "style='display:none;'"; } ?>>
			<?php $shipping_price = get_post_meta($post->ID,'_shipping_price',true); ?>
			<label class="control-label col-sm-3 col-md-3" for="_shipping_price"><?php esc_html_e( 'Flat Rate Price*', 'woocommerce' ); ?></label>
			<div class="col-md-6 col-sm-9 shipping_price_div">
				<input type="number" name="_shipping_price" class="form-control shipping_price" value="<?php echo $shipping_price; ?>"/>
			</div>
        </div>
        <?php do_action( 'wcmp_afm_product_options_shipping', $post->ID, $product_object, $post ); ?> 
    </div>
</div>
