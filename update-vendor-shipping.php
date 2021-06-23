<?php
// MBATT: Comment out this whole file
include_once("../../../wp-load.php");
if ( is_plugin_active( 'dc-woocommerce-multi-vendor/dc_product_vendor.php' ) )
{
	global $wpdb;
	$args = array(
		'role'    => 'dc_vendor',
		'orderby' => 'user_nicename',
		'order'   => 'ASC'
	);
	$users = array_merge( get_users('role=dc_vendor'), get_users('role=dc_pending_vendor') );

	//$users = get_users( $args );
	if($users)
	{
		foreach ( $users as $user ) 
		{
			echo "<br/>User id >>".$user->ID."<br>";
			echo "Instance >>".$instance_id = $wpdb->get_var("select instance_id from ".$wpdb->prefix."wcmp_shipping_zone_methods where method_id='flat_rate' and vendor_id='".$user->ID."'");
			echo "<br/>";
			$table_name = "{$wpdb->prefix}wcmp_shipping_zone_methods";
			$zone_id = $wpdb->get_var("select zone_id from ".$wpdb->prefix."woocommerce_shipping_zone_methods where method_id='wcmp_vendor_shipping' and is_enabled='1'");
			if($zone_id)
			{
				if(!$instance_id)
				{
					
					$result = $wpdb->insert(
						$table_name,
						array(
							'method_id' => 'flat_rate',
							'zone_id'   => $zone_id,
							'vendor_id' => $user->ID
						),
						array(
							'%s',
							'%d',
							'%d'
						)
					);
					$instance_id = $wpdb->insert_id;
				}
				
				$arr['method_id']='flat_rate';
				$arr['instance_id'] =$instance_id;
				$arr['zone_id'] = $zone_id;
				$arr['title'] = "Shipping Charge";
				$arr['cost'] = 0;
				$arr['tax_status'] = 'none';
				$arr['description'] = 'Lets you charge a fixed rate for shipping.';
				$arr['class_cost_487'] = '';
				$arr['calculation_type'] = "class";
				$data['settings'] = maybe_serialize($arr);
				echo $updated = $wpdb->update( $table_name, $data, array( 'instance_id' => $instance_id ), array( '%s') );
				echo "<br/>=====<br>";
			}
		}
	}
}
?>
