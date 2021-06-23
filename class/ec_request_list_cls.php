<?php
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-screen.php' );//added
    require_once( ABSPATH . 'wp-admin/includes/screen.php' );//added
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
    require_once( ABSPATH . 'wp-admin/includes/template.php' );
}

class ec_request_list_cls extends WP_List_Table {
	public function __construct() {
		parent::__construct( array(
			'singular'  => 'Request Quote Form',     //singular name of the listed records
			'plural'    => 'Request Quote Forms',    //plural name of the listed records
			'ajax'      => false        //does this table support ajax?
		) );      
		
		$this->screen = get_current_screen();
	}
	
	public function no_items() {
		_e( 'No Request Quote Forms', 'sp_woocommerce' );
	}
	public function column_default( $item, $column_name ) 
	{
		
		switch ( $column_name ) {
			//case 'no':
			case 'buyer_id':
				return get_user_meta($item[$column_name],'first_name',true)." ".get_user_meta($item[$column_name],'last_name',true);
			case 'product_id':
				return '<a href="'.get_permalink($item[$column_name]).'" target="_blank">'.get_the_title($item[$column_name]).'</a>';
			case 'request_no':
			case 'request_date':
			case 'qty':
				if($item[$column_name]=="")
					return '-';
				else
					return $item[$column_name];
			case 'status':
				return ucwords($item[$column_name]);
			case 'action':
					$link = '<a class="" href="'.site_url('wp-admin/admin.php?page=request_info_page&action=edit&id='.$item['id']).'">Edit</a>';
					
				return $link; //. " <br/> <a href='admin.php?page=deal_info_page&action=edit&dealid=".$item['id']."'>Edit</a>";	
			default:
				//return print_r( $item, true ); 
				break;
		}
	}
	
	public function get_columns() {
		$columns = [
			'request_no'    => __( 'Request No.', 'sp_woocommerce' ),
			'product_id'    => __( 'Product', 'sp_woocommerce' ),
			'buyer_id'    => __( 'Certified Buyer', 'sp_woocommerce' ),
			'request_date' => __( 'Request Date', 'sp_woocommerce' ),
			'qty' => __( 'Quantity', 'sp_woocommerce' ),
			'status' => __( 'Statue', 'sp_woocommerce' ),
			'action' => __( 'Action', 'sp_woocommerce' ),
		];

		return $columns;
	}
	
	public function get_sortable_columns() {
		$sortable_columns = array(
			'request_no' => array( 'request_no', false ),
			'request_date' => array( 'request_date', false ),
			'qty' => array( 'qty', false )
		 );
   
	 return $sortable_columns;
	}
	
	public function ec_get_all_request_quote_forms($per_page,$page_number)
	{
		global $wpdb;
		//$curdt = date('Y-m-d');
		$cond="";
		if(isset($_REQUEST['status']) && $_REQUEST['status']!='')
		{
			$cond .=" and status ='".$_REQUEST['status']."'";
		}
		$sql ="select q.* from ".$wpdb->prefix."request_quote q, ".$wpdb->prefix."posts p where p.ID=q.product_id and p.post_type='product' and p.post_status='publish' $cond";
		if ( ! empty( $_REQUEST['orderby'] ) ) {
		  $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
		  $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}else{
		  $sql .= ' ORDER BY q.id desc';
		}
		
		if( $per_page > 0 ){
			$sql .= " LIMIT $per_page";
			$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;
		}
		//echo $sql;
		$results = $wpdb->get_results($sql,'ARRAY_A');
		return $this->items = $results;
	}
	
	public function ec_get_all_request_quote_forms_count()
	{
		global $wpdb;
		$cond ="";
		if(isset($_REQUEST['status']) && $_REQUEST['status']!='')
		{
			$cond .=" and status ='".$_REQUEST['status']."'";
		}
		$sql ="select count(*) from ".$wpdb->prefix."request_quote q, ".$wpdb->prefix."posts p where p.ID=q.product_id and p.post_type='product' and p.post_status='publish' $cond ";
		$cnt = $wpdb->get_var($sql);
		return $cnt;
	}
	
	public function prepare_items() {
		$columns = $this->get_columns();
		$hidden = array('id');
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array($columns, $hidden, $sortable);
		//$this->process_bulk_action();

		$post_per_page = 20;//get_option('posts_per_page');
		$per_page     = $this->get_items_per_page( 'orders_per_page', $post_per_page );
		$current_page = $this->get_pagenum();
		$total_items  = $this->ec_get_all_request_quote_forms_count();

		$this->set_pagination_args( [
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page //WE have to determine how many items to show on a page
		] );

		$this->items = $this->ec_get_all_request_quote_forms( $per_page, $current_page);
	}
	
	
}
