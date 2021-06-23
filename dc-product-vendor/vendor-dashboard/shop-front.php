<?php
/*
 * The template for displaying vendor dashboard
 * Override this template by copying it to yourtheme/dc-product-vendor/vendor-dashboard/shop-front.php
 *
 * @author 	WC Marketplace
 * @package 	WCMp/Templates
 * @version   2.4.5
 */
if (!defined('ABSPATH')) {
    // Exit if accessed directly
    exit;
}
global $WCMp;

$vendor = get_current_vendor();
if (!$vendor) {
    return;
}

/* save extra fields info */
if(isset($_POST['primary_discipline']))
{
	update_user_meta($vendor->id,'primary_discipline',$_POST['primary_discipline']);	
}
if(isset($_POST['accept_commission']) &&$_POST['accept_commission']=="1")
{
	update_user_meta($vendor->id,'accept_commission',1);	
}
else
	update_user_meta($vendor->id,'accept_commission',0);	
if(isset($_POST['vendor_statement']))
{
	update_user_meta($vendor->id,'vendor_statement',$_POST['vendor_statement']);	
}
if(isset($_POST['vendor_bio']))
{
	update_user_meta($vendor->id,'vendor_bio',$_POST['vendor_bio']);	
}
if(isset($_POST['vendor_exhibition']))
{
	update_user_meta($vendor->id,'vendor_exhibition',$_POST['vendor_exhibition']);	
}
if(isset($_POST['vendor_education']))
{
	update_user_meta($vendor->id,'vendor_education',$_POST['vendor_education']);	
}
if(isset($_POST['vendor_awards']))
{
	update_user_meta($vendor->id,'vendor_awards',$_POST['vendor_awards']);	
}
if(isset($_POST['vendor_professional_background']))
{
	update_user_meta($vendor->id,'vendor_professional_background',$_POST['vendor_professional_background']);	
}
if(isset($_POST['vendor_tags']))
{
	update_user_meta($vendor->id,'vendor_tags',$_POST['vendor_tags']);	
}
if(isset($_POST['vendor_intro']))
{
	update_user_meta($vendor->id,'vendor_intro',$_POST['vendor_intro']);	
}

/* update social media */
if(isset($_POST['_vendor_fb_profile']))
{
	update_user_meta($vendor->id,'vendor_fb_profile',$_POST['_vendor_fb_profile']);	
}
if(isset($_POST['_vendor_twitter_profile']))
{
	update_user_meta($vendor->id,'vendor_twitter_profile',$_POST['_vendor_twitter_profile']);	
}
if(isset($_POST['_vendor_linkdin_profile']))
{
	update_user_meta($vendor->id,'vendor_linkdin_profile',$_POST['_vendor_linkdin_profile']);	
}
if(isset($_POST['_vendor_youtube']))
{
	update_user_meta($vendor->id,'vendor_youtube',$_POST['_vendor_youtube']);	
}
if(isset($_POST['_vendor_instagram']))
{
	update_user_meta($vendor->id,'vendor_instagram',$_POST['_vendor_instagram']);	
}


$vendor_hide_description = get_user_meta($vendor->id, '_vendor_hide_description', true);
$vendor_hide_email = get_user_meta($vendor->id, '_vendor_hide_email', true);
$vendor_hide_address = get_user_meta($vendor->id, '_vendor_hide_address', true);
$vendor_hide_phone = get_user_meta($vendor->id, '_vendor_hide_phone', true);

$vendor_primary_dis = get_user_meta($vendor->id, 'primary_discipline', true);
$accept_commission = get_user_meta($vendor->id, 'accept_commission', true);
$vendor_statement = get_user_meta($vendor->id, 'vendor_statement', true);
$vendor_bio = get_user_meta($vendor->id, 'vendor_bio', true);
$vendor_exhibition = get_user_meta($vendor->id, 'vendor_exhibition', true);
$vendor_education = get_user_meta($vendor->id, 'vendor_education', true);
$vendor_awards = get_user_meta($vendor->id, 'vendor_awards', true);
$vendor_professional_background = get_user_meta($vendor->id, 'vendor_professional_background', true);
$vendor_tags = get_user_meta($vendor->id, 'vendor_tags', true);
$vendor_intro = get_user_meta($vendor->id, 'vendor_intro', true);

$fb_profile = get_user_meta($vendor->id, 'vendor_fb_profile', true);
$twitter_profile = get_user_meta($vendor->id, 'vendor_twitter_profile', true);
$linkedin_profile = get_user_meta($vendor->id, 'vendor_linkdin_profile', true);
$youtube_profile = get_user_meta($vendor->id, 'vendor_youtube', true);
$instagram_profile = get_user_meta($vendor->id, 'vendor_instagram', true);

$field_type = (apply_filters('wcmp_vendor_storefront_wpeditor_enabled', true, $vendor->id)) ? 'wpeditor' : 'textarea';
$_wp_editor_settings = array('tinymce' => true);
if (!$WCMp->vendor_caps->vendor_can('is_upload_files')) {
    $_wp_editor_settings['media_buttons'] = false;
}
$_wp_editor_settings = apply_filters('wcmp_vendor_storefront_wp_editor_settings', $_wp_editor_settings);
?>
<style>
    .store-map-address{
        margin-top: 10px;
        border: 1px solid transparent;
        border-radius: 2px 0 0 2px;
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        height: 40px;
        outline: none;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
    }
    #searchStoreAddress {
        background-color: #fff;
        font-family: Roboto;
        font-size: 15px;
        margin-left: 12px;
        padding: 0 11px 0 13px;
        text-overflow: ellipsis;
        width: 44%;
    }
</style>
<div class="col-md-12">
    <!-- <div class="wcmp_headding2 card-header"><?php _e('General', 'dc-woocommerce-multi-vendor'); ?></div> -->
    <form method="post" name="shop_settings_form" class="wcmp_shop_settings_form form-horizontal">
        <?php do_action('wcmp_before_shop_front'); ?>

        <div class="panel panel-default pannel-outer-heading vendor-cover-panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="vendor-cover-wrap">
                            <img id="vendor-cover-img" src="<?php echo (isset($vendor_banner['url']) && (!empty($vendor_banner['url'])) ) ? $vendor_banner['url'] : $WCMp->plugin_url . 'assets/images/banner_placeholder.jpg'; ?>" alt="banner">

                            <div class="vendor-profile-pic-wraper pull-left">
                                <img id="vendor-profile-img" src="<?php echo (isset($vendor_image['url']) && (!empty($vendor_image['url']))) ? $vendor_image['url'] : $WCMp->plugin_url . 'assets/images/logo_placeholder.jpg'; ?>" alt="dp">
                                <div class="wcmp-media profile-pic-btn">
                                    <button type="button" class="wcmp_upload_btn" data-target="vendor-profile"><i class="wcmp-font ico-edit-pencil-icon"></i> <?php _e('Artist Profile Picture', 'dc-woocommerce-multi-vendor'); ?></button>
                                </div>
                                <input type="hidden" name="vendor_image" id="vendor-profile-img-id" class="user-profile-fields" value="<?php echo (isset($vendor_image['value']) && (!empty($vendor_image['value']))) ? $vendor_image['value'] : $WCMp->plugin_url . 'assets/images/WP-stdavatar.png'; ?>"  />
                            </div>
                            <div class="wcmp-media cover-pic-button pull-right">
                                <button type="button" class="wcmp_upload_btn" data-target="vendor-cover"><i class="wcmp-font ico-edit-pencil-icon"></i> <?php _e('Upload Cover Picture', 'dc-woocommerce-multi-vendor'); ?></button>
                            </div>
                            <input type="hidden" name="vendor_banner" id="vendor-cover-img-id" class="user-profile-fields" value="<?php echo (isset($vendor_banner['value']) && (!empty($vendor_banner['value'])) ) ? $vendor_banner['value'] : $WCMp->plugin_url . 'assets/images/banner_placeholder.jpg'; ?>"  />
                        </div>
                    </div>
                    <!-- 
                    <div class="col-md-3">
                        <div class="wcmp_media_block">
                            <span class="dc-wp-fields-uploader">
                                <img class="one_third_part" id="vendor_image_display" width="300" src="<?php echo (isset($vendor_image['value']) && (!empty($vendor_image['value']))) ? $vendor_image['value'] : $WCMp->plugin_url . 'assets/images/logo_placeholder.jpg'; ?>" class="placeHolder" />
                                <input type="text" name="vendor_image" id="vendor_image" style="display: none;" class="user-profile-fields" readonly value="<?php echo (isset($vendor_image['value']) && (!empty($vendor_image['value']))) ? $vendor_image['value'] : $WCMp->plugin_url . 'assets/images/logo_placeholder.jpg'; ?>"  />
                            </span>
                            <div class="button-group">                            
                                <button class="upload_button wcmp_black_btn moregap two_third_part btn btn-primary" name="vendor_image_button" id="vendor_image_button" value="<?php _e('Upload', 'dc-woocommerce-multi-vendor') ?>" style=" display: block; "><span class="dashicons dashicons-upload"></span> <?php _e('Upload', 'dc-woocommerce-multi-vendor') ?></button>
                                <button class="remove_button wcmp_black_btn moregap two_third_part btn btn-primary" name="vendor_image_remove_button" id="vendor_image_remove_button" value="<?php _e('Replace', 'dc-woocommerce-multi-vendor') ?>"><span class="dashicons dashicons-upload"></span> <?php _e('Replace', 'dc-woocommerce-multi-vendor') ?></button>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                    <div class="col-md-7 col-md-offset-2">
                        <div class="wcmp_media_block">
                            <span class="dc-wp-fields-uploader">
                                <img class="one_third_part" id="vendor_banner_display" width="300" src="<?php echo (isset($vendor_banner['value']) && (!empty($vendor_banner['value'])) ) ? $vendor_banner['value'] : $WCMp->plugin_url . 'assets/images/banner_placeholder.jpg'; ?>" class="placeHolder" />
                                <input type="text" name="vendor_banner" id="vendor_banner" style="display: none;" class="user-profile-fields" readonly value="<?php echo (isset($vendor_banner['value']) && (!empty($vendor_banner['value'])) ) ? $vendor_banner['value'] : $WCMp->plugin_url . 'assets/images/banner_placeholder.jpg'; ?>"  />
                            </span>
                            <div class="button-group">   
                                <button class="upload_button wcmp_black_btn moregap two_third_part btn btn-primary" name="vendor_banner_button" id="vendor_banner_button"><span class="dashicons dashicons-upload"></span> <?php _e('Upload', 'dc-woocommerce-multi-vendor') ?></button>
                                <button class="remove_button wcmp_black_btn moregap two_third_part btn btn-primary" name="vendor_banner_remove_button" id="vendor_banner_remove_button"><span class="dashicons dashicons-upload"></span> <?php _e('Replace', 'dc-woocommerce-multi-vendor') ?></button>
                            </div>
                            <div class="clear"></div>
                        </div>       
                    </div>
                    -->
                </div>         
            </div>
        </div>

        <div class="panel panel-default panel-pading pannel-outer-heading">
            <div class="panel-heading">
                <h3><?php _e('General', 'dc-woocommerce-multi-vendor'); ?></h3>
            </div>
            <div class="panel-body panel-content-padding">
                <div class="wcmp_form1">
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e('Artist Name *', 'dc-woocommerce-multi-vendor'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <input class="no_input form-control" type="text" name="vendor_page_title" value="<?php echo isset($vendor_page_title['value']) ? $vendor_page_title['value'] : ''; ?>"  placeholder="<?php _e('Enter your Store Name here', 'dc-woocommerce-multi-vendor'); ?>">
                        </div>  
                    </div>
                    <div class="form-group hide_me">
                        <label class="control-label col-sm-3 col-md-3"><?php _e('Store Slug *', 'dc-woocommerce-multi-vendor'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon3">
                                    <?php
                                    $dc_vendors_permalinks_array = get_option('dc_vendors_permalinks');
                                    if (isset($dc_vendors_permalinks_array['vendor_shop_base']) && !empty($dc_vendors_permalinks_array['vendor_shop_base'])) {
                                        $store_slug = trailingslashit($dc_vendors_permalinks_array['vendor_shop_base']);
                                    } else {
                                        $store_slug = trailingslashit('vendor');
                                    } echo $shop_page_url = trailingslashit(get_home_url());
                                    echo $store_slug;
                                    ?>
                                </span>		
                                <input class="small no_input form-control" id="basic-url" aria-describedby="basic-addon3" type="text" name="vendor_page_slug" value="<?php echo isset($vendor_page_slug['value']) ? $vendor_page_slug['value'] : ''; ?>" placeholder="<?php _e('Enter your Store Name here', 'dc-woocommerce-multi-vendor'); ?>">
                            </div>	
                        </div>	
                    </div>	
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e('Primary Discipline', 'dc-woocommerce-multi-vendor'); ?></label>
                        <div class="col-md-6 col-sm-9">
							<select name="primary_discipline" class="form-control">
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
						</div>
                        <!--div class="col-md-6 col-sm-9">
                            <?php/* $vendor_description = isset($vendor_description['value']) ? $vendor_description['value'] : '';
                            $WCMp->wcmp_wp_fields->dc_generate_form_field(array("vendor_description" => array('name' => 'vendor_description', 'type' => $field_type, 'class' => 'no_input form-control regular-textarea', 'value' => $vendor_description, 'settings' => $_wp_editor_settings))); */?>
                            <!--textarea class="no_input form-control" name="vendor_description" cols="" rows=""><?php //echo isset($vendor_description['value']) ? $vendor_description['value'] : ''; ?></textarea-->
                        </div-->
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e('Artist Statement (About) *', 'dc-woocommerce-multi-vendor'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <textarea class="form-control" name="vendor_statement" ><?php echo isset($vendor_statement) ? $vendor_statement : ''; ?></textarea>
                        </div>  
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e('Biography', 'dc-woocommerce-multi-vendor'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <textarea class="form-control" name="vendor_bio" ><?php echo isset($vendor_bio) ? $vendor_bio : ''; ?></textarea>
                        </div>  
                    </div>
                    <?php /*if (apply_filters('can_vendor_add_message_on_email_and_thankyou_page', true)) { ?>
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e('Message to Buyers', 'dc-woocommerce-multi-vendor'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <?php $message_to_buyer = isset($vendor_message_to_buyers['value']) ? $vendor_message_to_buyers['value'] : '';
                            $WCMp->wcmp_wp_fields->dc_generate_form_field(array("vendor_message_to_buyers" => array('name' => 'vendor_message_to_buyers', 'type' => $field_type, 'class' => 'no_input form-control regular-textarea', 'value' => $message_to_buyer, 'settings' => $_wp_editor_settings))); ?>
                            <!--textarea class="no_input form-control" name="vendor_message_to_buyers" cols="" rows=""><?php //echo isset($vendor_message_to_buyers['value']) ? $vendor_message_to_buyers['value'] : ''; ?></textarea-->
                        </div>
                    </div>
                    <?php } */?>
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e('Phone', 'dc-woocommerce-multi-vendor'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <input class="no_input form-control" type="text" name="vendor_phone" placeholder="" value="<?php echo isset($vendor_phone['value']) ? $vendor_phone['value'] : ''; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e('Email *', 'dc-woocommerce-multi-vendor'); ?></label>
                        <div class="col-md-6 col-sm-9">                            
                            <input class="no_input vendor_email form-control" type="text" placeholder="" readonly  value="<?php echo isset($vendor->user_data->user_email) ? $vendor->user_data->user_email : ''; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e('"Ship From" Address', 'dc-woocommerce-multi-vendor'); ?></label>     
                        <div class="col-md-6 col-sm-9">                      
                            <div class="row">
                                <div class="col-md-12">
                                    <input class="no_input form-control inp-btm-margin" type="text" placeholder="<?php _e('Address line 1', 'dc-woocommerce-multi-vendor'); ?>" name="vendor_address_1"  value="<?php echo isset($vendor_address_1['value']) ? $vendor_address_1['value'] : ''; ?>">
                                    <input class="no_input form-control inp-btm-margin" type="text" placeholder="<?php _e('Address line 2', 'dc-woocommerce-multi-vendor'); ?>" name="vendor_address_2"  value="<?php echo isset($vendor_address_2['value']) ? $vendor_address_2['value'] : ''; ?>">
                                </div>
                                <div class="col-md-6">
                                    <select name="vendor_country" id="vendor_country" class="country_to_state user-profile-fields form-control inp-btm-margin regular-select" rel="vendor_country">
                                        <option value=""><?php _e( 'Select a country&hellip;', 'dc-woocommerce-multi-vendor' ); ?></option>
                                        <?php $country_code = get_user_meta($vendor->id, '_vendor_country_code', true);
                                            foreach ( WC()->countries->get_allowed_countries() as $key => $value ) {
                                                echo '<option value="' . esc_attr( $key ) . '"' . selected( esc_attr( $country_code ), esc_attr( $key ), false ) . '>' . esc_html( $value ) . '</option>';
                                            }
                                        ?>
                                    </select>
                                    <!--input class="no_input form-control inp-btm-margin" type="text" placeholder="<?php //_e('Country', 'dc-woocommerce-multi-vendor'); ?>" name="vendor_country" value="<?php echo isset($vendor_country['value']) ? $vendor_country['value'] : ''; ?>"-->
                                </div>
                                <div class="col-md-6">
                                    <?php $country_code = get_user_meta($vendor->id, '_vendor_country_code', true);
                                    $states = WC()->countries->get_states( $country_code ); ?>
                                    <select name="vendor_state" id="vendor_state" class="state_select user-profile-fields form-control inp-btm-margin regular-select" rel="vendor_state">
                                        <option value=""><?php esc_html_e( 'Select a state&hellip;', 'dc-woocommerce-multi-vendor' ); ?></option>
                                        <?php $state_code = get_user_meta($vendor->id, '_vendor_state_code', true);
                                        if($states):
                                            foreach ( $states as $ckey => $cvalue ) {
                                                echo '<option value="' . esc_attr( $ckey ) . '" ' . selected( $state_code, $ckey, false ) . '>' . esc_html( $cvalue ) . '</option>';
                                            }
                                        endif;
                                        ?>
                                    </select>
                                    <!--input class="no_input form-control inp-btm-margin"  type="text" placeholder="<?php //_e('State', 'dc-woocommerce-multi-vendor'); ?>"  name="vendor_state" value="<?php echo isset($vendor_state['value']) ? $vendor_state['value'] : ''; ?>"-->
                                </div>
                                <div class="col-md-6">
                                    <input class="no_input form-control inp-btm-margin" type="text" placeholder="<?php _e('City', 'dc-woocommerce-multi-vendor'); ?>"  name="vendor_city" value="<?php echo isset($vendor_city['value']) ? $vendor_city['value'] : ''; ?>">
                                </div>
                                <div class="col-md-6">
                                    <input class="no_input form-control inp-btm-margin" type="text" placeholder="<?php _e('ZIP code', 'dc-woocommerce-multi-vendor'); ?>" name="vendor_postcode" value="<?php echo isset($vendor_postcode['value']) ? $vendor_postcode['value'] : ''; ?>">
                                </div>
                                <?php
                                if (apply_filters('is_vendor_add_external_url_field', false)) {
                                    ?>
                                    <div class="col-md-6">
                                        <input class="no_input form-control inp-btm-margin" type="text" placeholder="<?php _e('External store URL', 'dc-woocommerce-multi-vendor'); ?>" name="vendor_external_store_url" value="<?php echo isset($vendor_external_store_url['value']) ? $vendor_external_store_url['value'] : ''; ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <input class="no_input form-control inp-btm-margin" type="text" placeholder="<?php _e('External store URL Label', 'dc-woocommerce-multi-vendor'); ?>" name="vendor_external_store_label" value="<?php echo isset($vendor_external_store_label['value']) ? $vendor_external_store_label['value'] : ''; ?>">
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <!-- from group end -->
                </div>
            </div>
        </div>
		
		<div class="panel panel-default panel-pading pannel-outer-heading">
			<div class="panel-heading">
                <h3><?php _e('Professional Information', 'dc-woocommerce-multi-vendor'); ?></h3>
            </div>
            <div class="panel-body panel-content-padding form-horizontal">
                <div class="wcmp_media_block">
                    
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e('Are you currently accepting commissions?', 'dc-woocommerce-multi-vendor'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <input type='checkbox' class="form-control" name="accept_commission" value="1" <?php if(isset($accept_commission) && $accept_commission ==1) { echo "checked='checked'"; } ?> />
                            <span>(Click for "YES")</span>
                        </div>  
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e('Exhibition/Shows/Commissions', 'dc-woocommerce-multi-vendor'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <textarea class="form-control" name="vendor_exhibition" ><?php echo isset($vendor_exhibition) ? $vendor_exhibition : ''; ?></textarea>
                        </div>  
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e('Education', 'dc-woocommerce-multi-vendor'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <textarea class="form-control" name="vendor_education" ><?php echo isset($vendor_education) ? $vendor_education : ''; ?></textarea>
                        </div>  
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e('Awards & Distinctions', 'dc-woocommerce-multi-vendor'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <textarea class="form-control" name="vendor_awards" ><?php echo isset($vendor_awards) ? $vendor_awards : ''; ?></textarea>
                        </div>  
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e('Professional Experience', 'dc-woocommerce-multi-vendor'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <textarea class="form-control" name="vendor_professional_background" ><?php echo isset($vendor_professional_background) ? $vendor_professional_background : ''; ?></textarea>
                        </div>  
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e('Artist Tags (keywords)', 'dc-woocommerce-multi-vendor'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <input class="form-control" type="text"  name="vendor_tags" value="<?php echo isset($vendor_tags) ? $vendor_tags : ''; ?>">
                        </div>  
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e('Introduction Video (Youtube Link)', 'dc-woocommerce-multi-vendor'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <input class="form-control" type="text"  name="vendor_intro" value="<?php echo isset($vendor_intro) ? $vendor_intro : ''; ?>"><br/>
                            <span>30-45 seconds max length video is best.</span>
                        </div>  
                    </div>
               </div>
            </div>
		</div>

        <div class="panel panel-default pannel-outer-heading">
            <div class="panel-heading">
                <h3><?php _e('Social Media (Please fill in so EC can tag you in social posts when appropriate.)', 'dc-woocommerce-multi-vendor'); ?></h3>
            </div>
            <div class="panel-body panel-content-padding form-horizontal">
                <div class="wcmp_media_block">

                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3 facebook"><?php _e('Facebook', 'dc-woocommerce-multi-vendor'); ?></label>
                        <div class="col-md-6 col-sm-9">
							<input class="form-control" type="text" placeholder="Example: @embracecreatives"  name="_vendor_fb_profile" value="<?php echo isset($fb_profile) ? $fb_profile : ''; ?>">
                        </div>  
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3 twitter"><?php _e('Twitter', 'dc-woocommerce-multi-vendor'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <input class="form-control" type="text" placeholder="Example: @EmbraceCreativs"  name="_vendor_twitter_profile" value="<?php echo isset($twitter_profile) ? $twitter_profile : ''; ?>">
                        </div>  
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3 linkedin"><?php _e('LinkedIn', 'dc-woocommerce-multi-vendor'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <input class="form-control" type="text" placeholder="Example: @embrace-creatives" name="_vendor_linkdin_profile" value="<?php echo isset($linkedin_profile) ? $linkedin_profile : ''; ?>">
                        </div>  
                    </div>

                    <!--div class="form-group">
                        <label class="control-label col-sm-3 col-md-3 google-plus"><?php _e('Google Plus', 'dc-woocommerce-multi-vendor'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <input class="form-control" type="url"   name="vendor_google_plus_profile" value="<?php echo isset($vendor_google_plus_profile['value']) ? $vendor_google_plus_profile['value'] : ''; ?>">
                        </div>  
                    </div-->

                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3 youtube"><?php _e('YouTube', 'dc-woocommerce-multi-vendor'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <input class="form-control" type="text" placeholder="Example: Embrace Creatives"  name="_vendor_youtube" value="<?php echo isset($youtube_profile) ? $youtube_profile : ''; ?>">
                        </div>  
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3 instagram"><?php _e('Instagram', 'dc-woocommerce-multi-vendor'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <input class="form-control" type="text" placeholder="Example: @embracecreatives"   name="_vendor_instagram" value="<?php echo isset($instagram_profile) ? $instagram_profile : ''; ?>">
                        </div>  
                    </div>
                    <?php do_action( 'wcmp_vendor_add_extra_social_link', $vendor ); ?>
                </div>
            </div>
        </div>    

<?php if (apply_filters('can_vendor_edit_shop_template', false)): ?>
            <div class="panel panel-default panel-pading">
                <div class="panel-heading">
                    <h3><?php _e('Shop Template', 'dc-woocommerce-multi-vendor'); ?></h3>
                </div>
                <div class="panel-body">
                    <ul class="wcmp_template_list list-unstyled">
                        <?php
                        $template_options = apply_filters('wcmp_vendor_shop_template_options', array('template1' => $WCMp->plugin_url . 'assets/images/template1.png', 'template2' => $WCMp->plugin_url . 'assets/images/template2.png', 'template3' => $WCMp->plugin_url . 'assets/images/template3.png'));
                        $shop_template = get_wcmp_vendor_settings('wcmp_vendor_shop_template', 'vendor', 'dashboard', 'template1');
                        $shop_template = get_wcmp_vendor_settings('can_vendor_edit_shop_template', 'vendor', 'dashboard', false) && get_user_meta($vendor->id, '_shop_template', true) ? get_user_meta($vendor->id, '_shop_template', true) : $shop_template;
                        foreach ($template_options as $template => $template_image):
                            ?>
                            <li>
                                <label>
                                    <input type="radio" <?php checked($template, $shop_template); ?> name="_shop_template" value="<?php echo $template; ?>" />
                                    <i class="dashicons dashicons-yes"></i>
                                    <div class="template-overlay"></div>
                                    <img src="<?php echo $template_image; ?>" />
                                </label>
                            </li>
            <?php endforeach; ?>
                    </ul>                    
                </div>
            </div>    
<?php endif; ?>
<?php do_action('wcmp_after_shop_front'); ?>
<?php do_action('other_exta_field_dcmv'); ?>
        <div class="action_div_space"> </div>
        <div class="wcmp-action-container">
            <button type="submit" class="btn btn-default" name="store_save"><?php _e('Save Options', 'dc-woocommerce-multi-vendor'); ?></button>
            <div class="clear"></div>
        </div>
    </form>
</div>
