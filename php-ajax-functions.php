<?php
/**
 * Created by PhpStorm.
 * User: ferdware
 * Date: 11/3/18
 * Time: 12:26 PM
 */


/*======================================================================
|   Test Ajax
+*=====================================================================*/
function ajax_test_ajax() { //===> ajax_calc_turnaround_time

	$ret_val = test_ajax( $_POST );

	die();

};

add_action('wp_ajax_ajax_test_ajax', 'ajax_test_ajax');
add_action('wp_ajax_nopriv_ajax_test_ajax', 'ajax_test_ajax');

/*======================================================================
|   Save the product data to the DB
+*=====================================================================*/
function ajax_process_upload_image() { 

	$ret_val = process_upload_image( $_POST['data'] );

	if($ret_val){

		echo $ret_val;

	} else {

		echo 'fail';

	}

	die();

};

add_action('wp_ajax_ajax_process_upload_image', 'ajax_process_upload_image');
add_action('wp_ajax_nopriv_ajax_process_upload_image', 'ajax_process_upload_image');

/*======================================================================
|   Test Ajax
+*=====================================================================*/
function ajax_put_product_config_details() { //===> ajax_calc_turnaround_time

	$ret_val = put_product_config_details( $_POST['data'] );

	if($ret_val){
		echo $ret_val;
	} else {
		echo 'fail';
	}

	die();

};

add_action('wp_ajax_ajax_put_product_config_details', 'ajax_put_product_config_details');
add_action('wp_ajax_nopriv_ajax_put_product_config_details', 'ajax_put_product_config_details');

/*======================================================================
|   Test Ajax
+*=====================================================================*/
function ajax_get_order_info() { //===> ajax_calc_turnaround_time

	$ret_val = get_order_info( $_POST['data'], null );

	if($ret_val){
		echo $ret_val;
	} else {
		echo '';
	}

	die();

};

add_action('wp_ajax_ajax_get_order_info', 'ajax_get_order_info');
add_action('wp_ajax_nopriv_ajax_get_order_info', 'ajax_get_order_info');

/*======================================================================
|   Test Ajax
+*=====================================================================*/
function ajax_save_product_json(){

	$ret_val = save_product_json( $_POST['data'] );

	if($ret_val){
		echo $ret_val;
	} else {
		echo 'fail';
	}

	die();

};

add_action('wp_ajax_ajax_save_product_json', 'ajax_save_product_json');
add_action('wp_ajax_nopriv_ajax_save_product_json', 'ajax_save_product_json');

/*======================================================================
|   Test Ajax
+*=====================================================================*/
function ajax_get_product_json(){

	$ret_val = get_product_json( $_POST['data'] );

	if($ret_val){
		echo $ret_val;
	} else {
		echo '';
	}

	die();

};

add_action('wp_ajax_ajax_get_product_json', 'ajax_get_product_json');
add_action('wp_ajax_nopriv_ajax_get_product_json', 'ajax_get_product_json');

/*======================================================================
|   edit cartItem
+*=====================================================================*/
function ajax_edit_cart_item(){

	$ret_val = edit_cart_item( $_POST['data'] );

	if($ret_val){
		echo $ret_val;
	} else {
		echo 'fail';
	}

	die();

};
add_action('wp_ajax_ajax_edit_cart_item', 'ajax_edit_cart_item');
add_action('wp_ajax_nopriv_ajax_edit_cart_item', 'ajax_edit_cart_item');










add_action('wp_ajax_nopriv_get_cart_item', 'get_cart_item');
add_action('wp_ajax_get_cart_item', 'get_cart_item');
function get_cart_item() {
    global $wpdb;

    $item_guid = $_POST['data'];

    $_SESSION['item_guid'] = $item_guid;

    $wpdb->update('fware_cart_items',
        array( 'item_status'  => 0 ),
        array( 'item_guid' => $item_guid ),
        array( '%d' ),
        array( '%s' )
    );

    echo json_encode($item_guid);
    die();
};







/*======================================================================
|   delete cart item
+*=====================================================================*/
function ajax_delete_cart_item(){

	$ret_val = delete_cart_item( $_POST['data'] );

	if($ret_val){
		echo $ret_val;
	} else {
		echo 'fail';
	}

	die();

};

add_action('wp_ajax_ajax_delete_cart_item', 'ajax_delete_cart_item');
add_action('wp_ajax_nopriv_ajax_delete_cart_item', 'ajax_delete_cart_item');


/*======================================================================
|   delete cart item
+*=====================================================================*/
function ajax_get_shipping_rate(){

	$ret_val = get_shipping_rate( $_POST['data'] );

	if($ret_val){
		echo $ret_val;
	} else {
		echo 'fail';
	}

	die();

};

add_action('wp_ajax_ajax_get_shipping_rate', 'ajax_get_shipping_rate');
add_action('wp_ajax_nopriv_ajax_get_shipping_rate', 'ajax_get_shipping_rate');

/*======================================================================
|   delete cart item
+*=====================================================================*/
function ajax_get_address_info(){

	$ret_val = get_address_info();

	if($ret_val){
		echo $ret_val;
	} else {
		echo null;
	}

	die();

};

add_action('wp_ajax_ajax_get_address_info', 'ajax_get_address_info');
add_action('wp_ajax_nopriv_ajax_get_address_info', 'ajax_get_address_info');

/*======================================================================
|   delete cart item
+*=====================================================================*/
function ajax_process_order_request_no_upload(){

	$ret_val = process_order_request_no_upload( $_POST['data'] );

	if($ret_val){
		echo $ret_val;
	} else {
		echo null;
	}

	die();

};

add_action('wp_ajax_ajax_process_order_request_no_upload', 'ajax_process_order_request_no_upload');
add_action('wp_ajax_nopriv_ajax_process_order_request_no_upload', 'ajax_process_order_request_no_upload');

/*======================================================================
|   delete cart item
+*=====================================================================*/
function ajax_process_order_request_upload(){

	$ret_val = process_order_request_upload( $_POST['data'] );

	if($ret_val){
		echo $ret_val;
	} else {
		echo null;
	}

	die();

};

add_action('wp_ajax_ajax_process_order_request_upload', 'ajax_process_order_request_upload');
add_action('wp_ajax_nopriv_ajax_process_order_request_upload', 'ajax_process_order_request_upload');

/*======================================================================
|   delete cart item
+*=====================================================================*/
function ajax_process_payment(){

	$ret_val = process_cc_payment( $_POST['data'] );

	echo $ret_val;

	die();

};

add_action('wp_ajax_ajax_process_payment', 'ajax_process_payment');
add_action('wp_ajax_nopriv_ajax_process_payment', 'ajax_process_payment');

/*======================================================================
|   check if the zip code entered is eligible for delivery
+*=====================================================================*/
function ajax_check_delivery_eligible(){

	$ret_val = check_delivery_eligible( $_POST['data'] );

	echo $ret_val;

	die();

};

add_action('wp_ajax_ajax_check_delivery_eligible', 'ajax_check_delivery_eligible');
add_action('wp_ajax_nopriv_ajax_check_delivery_eligible', 'ajax_check_delivery_eligible');

/*======================================================================
|   check if the zip code entered is eligible for delivery
+*=====================================================================*/
function ajax_calculate_discount_code(){

	$ret_val = calculate_discount_code( $_POST['data'] );

	echo $ret_val;

	die();

};

add_action('wp_ajax_ajax_calculate_discount_code', 'ajax_calculate_discount_code');
add_action('wp_ajax_nopriv_ajax_calculate_discount_code', 'ajax_calculate_discount_code');

/*======================================================================
|   check if the zip code entered is eligible for delivery
+*=====================================================================*/
function ajax_generate_discount_code(){

	$ret_val = generate_discount_code( $_POST['data'] );

	echo $ret_val;

	die();

};

add_action('wp_ajax_ajax_generate_discount_code', 'ajax_generate_discount_code');
add_action('wp_ajax_nopriv_ajax_generate_discount_code', 'ajax_generate_discount_code');

/*======================================================================
|   check if the zip code entered is eligible for delivery
+*=====================================================================*/
function ajax_add_to_woocommerce_cart(){

	$ret_val = add_to_woocommerce_cart( $_POST['data'] );

	echo $ret_val;

	die();

};

add_action('wp_ajax_ajax_add_to_woocommerce_cart', 'ajax_add_to_woocommerce_cart');
add_action('wp_ajax_nopriv_ajax_add_to_woocommerce_cart', 'ajax_add_to_woocommerce_cart');

