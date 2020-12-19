<?php

$table_name = 'fware_woo';

global $wpdb;
$woo_consumer_key = $wpdb->get_var( 'SELECT woo_consumer_key FROM ' . $table_name );
$woo_consumer_secret = $wpdb->get_var( 'SELECT woo_consumer_secret FROM ' . $table_name );

define('PLUGIN_PATH', plugin_dir_path( __FILE__ ));
define('PLUGIN_URL', plugin_dir_url( __FILE__ ));
define('WOO_CONSUMER_KEY', $woo_consumer_key);
define('WOO_CONSUMER_SECRET', $woo_consumer_secret);

use Automattic\WooCommerce\Client as WooCommerceClient;
use Automattic\WooCommerce\HttpClient\HttpClientException;

/**
 * Attach images to product (feature/ gallery)
 */
function attach_product_thumbnail($post_id, $url, $flag)
{
    /*
     * If allow_url_fopen is enable in php.ini then use this
     */
    $image_url = $url;
    $url_array = explode('/',$url);
    $image_name = $url_array[count($url_array)-1];
    $image_data = file_get_contents($image_url); // Get image data

    /*
     * If allow_url_fopen is not enable in php.ini then use this
     */


    // $image_url = $url;
    // $url_array = explode('/',$url);
    // $image_name = $url_array[count($url_array)-1];

    // $ch = curl_init();
    // curl_setopt ($ch, CURLOPT_URL, $image_url);

    // // Getting binary data
    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);

    // $image_data = curl_exec($ch);
    // curl_close($ch);



    $upload_dir = wp_upload_dir(); // Set upload folder
    $unique_file_name = wp_unique_filename( $upload_dir['path'], $image_name ); //    Generate unique name
    $filename = basename( $unique_file_name ); // Create image file name

    // Check folder permission and define file location
    if( wp_mkdir_p( $upload_dir['path'] ) ) {
        $file = $upload_dir['path'] . '/' . $filename;
    } else {
        $file = $upload_dir['basedir'] . '/' . $filename;
    }



    // Create the image file on the server
    file_put_contents( $file, $image_data );

    // Check image file type
    $wp_filetype = wp_check_filetype( $filename, null );

    // Set attachment data
    $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => sanitize_file_name( $filename ),
        'post_content' => '',
        'post_status' => 'inherit'
    );

    // Create the attachment
    $attach_id = wp_insert_attachment( $attachment, $file, $post_id );

    // Include image.php
    require_once(ABSPATH . 'wp-admin/includes/image.php');

    // Define attachment metadata
    $attach_data = wp_generate_attachment_metadata( $attach_id, $file );

    // Assign metadata to attachment
    wp_update_attachment_metadata( $attach_id, $attach_data );

    // asign to feature image
    if( $flag == 0){
        // And finally assign featured image to post
        set_post_thumbnail( $post_id, $attach_id );
    }

    // assign to the product gallery
    if( $flag == 1 ){
        // Add gallery image to product
        $attach_id_array = get_post_meta($post_id,'_product_image_gallery', true);
        $attach_id_array .= ','.$attach_id;
        update_post_meta($post_id,'_product_image_gallery',$attach_id_array);
    }
}

function wooCreateProduct($product)
{
    try
    {
        $site_url = get_site_url();

        $woocommerce = new WooCommerceClient(
            $site_url, // Your store URL
            WOO_CONSUMER_KEY, // Your consumer key
            WOO_CONSUMER_SECRET, // Your consumer secret
            [
                'wp_api' => true, // Enable the WP REST API integration
                'version' => 'wc/v2', // WooCommerce WP REST API version
                'verify_ssl' => false,
                'timeout' => 1800,
                'query_string_auth' => true // Force Basic Authentication as query string true and using under HTTPS
            ]
        );

        $data = [
            'name' => $product['name'],
            'type' => 'simple',
            'regular_price' => $product['regular_price'],
            'description' => $product['description'],
            'short_description' => $product['short_description'],
            'sku' => $product['sku'],
            'categories' => [
                [
                    'id' => WOO_CATEGORY_ID,
                ],
            ],
            'images' => [
                [
                    'src' => $product['thumb_url'],
                    'position' => 0,
                ]
            ],
        ];

        $response = $woocommerce->post('products', $data);
    }
    catch (Exception $e)
    {
        $error = 'Caught exception: ' . $e->getMessage() . ' on line: ' . $e->getLine();

        $error_message = $e->getMessage(); // Error message.
        $error_request = $e->getRequest(); // Last request data.
        $error_response = $e->getResponse(); // Last response data.

        error_log($error);
    }

    return $response;
}

/*===========================================================
*
* Function PLG
*
* this is a general function that outputs the text as a log
*
* @param $input
* @param $flag
*
+------------------------------*/
function plg( $input, $flag ){
	global $mode;
}

/*======================================================================================================================
|
|  
|
+=====================================================================================================================*/
function ae_nocache()
{
	header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
}

/*======================================================================================================================
|
|  this will run on first init
|
+=====================================================================================================================*/
function run_header_code()
{
	$ignore_array = array(
		'/wp-admin/admin-ajax.php',
		'/wp-content/plugins/do-ajax.php',
		'/wp-content/plugins/framedware/includes/bootstrap.bundle.min.js.map',
		'/index.php/uploader/',
		'/wp-content/plugins/framedware/includes/bootstrap.min.css.map'
	);

	$tmp_current_url =  $_SERVER['REQUEST_URI'];

	if( ( !in_array( $tmp_current_url, $ignore_array )) && ( check_force_refresh( $tmp_current_url ) ) ){

		$_SESSION['item_guid'] = null;
		$_SESSION['productJSON'] = null;

		if (session_status() == PHP_SESSION_ACTIVE) {
			//plg('************ SESSION IS ACTIVE !!!', 1) ;

			// clear out any previous session vars and make new from cookie
			if(isset($_COOKIE['framedware'])){

				$_SESSION['user_guid'] = null;
				$_SESSION['order_guid'] = null;

			};
		}

		// get the current user id from wp database
		$_SESSION['user_guid'] = get_user_guid();
		$_SESSION['order_guid'] = get_order_guid( $_SESSION['user_guid'] );
		$_SESSION['item_guid'] = get_current_item_guid( $_SESSION['order_guid'] );

		// get item_data
		get_current_item_data();
	}
}

// this function prevents the refresh from always running
function check_force_refresh( $_current_url )
{
	if (strpos( $_current_url, 'force_refresh_get_version') == true) {
		return false;
	} else {
		return true;
	}
}

// clear the stored cookie
function clear_cookies()
{
	global $domain;

	setcookie("framedware", '', time() - 3600, '/', $domain);  // TODO: fix, not working in WP plugin
	unset( $_COOKIE['framedware'] );
}

// clear the stored cookie
function renew_cookies()
{
	global $domain;

	$Month = 2592000 + time(); // set the time interval to keep cookies ( 30 days )
	$tmp_new_cookie = UUID::v4();
	setcookie("framedware", $tmp_new_cookie, $Month, '/', $domain ); // set the cookie with the new user guid
}

// clear the stored cookie
function set_cookie()
{
	global $domain;

	$Month = 2592000 + time(); // set the time interval to keep cookies ( 30 days )
	setcookie("framedware", $_SESSION['user_guid'], $Month, '/', $domain ); // set the cookie with the new user guid
}

/*======================================================================================================================
|
|  Process the uploaded image and return the JSON to be able to render the frame preview
|
+=====================================================================================================================*/
function get_user_guid() //===> get_user_guid and populate the session guid
{
	global $wpdb, $domain;

	$Month = 2592000 + time(); // set the time interval to keep cookies ( 30 days )

	$tmp_current_wp_id = get_current_user_id(); // get the WP user id

   function set_wp_id( $_user_guid, $_wp_user_id ){
      global $wpdb;

	   // update the session table to assoc. the wp account with the framedware account
	   $qry = "UPDATE fware_user_obj SET wp_user_id = %s WHERE user_guid = %s";
	   $wpdb->query( $wpdb->prepare( $qry, $_wp_user_id, $_user_guid ) );

   }
	if ( is_user_logged_in() ){ // check to see if the user is logged in - wp_function
		//*** at this point user should have had a temp guid created prior to creating account
		//*** the user should also have a cookie

		// if logged-in search the fware_sessions table by WP_id
		$qry = "SELECT user_guid FROM fware_user_obj WHERE wp_user_id = %s";
		$tmp_user_guid = $wpdb->get_var( $wpdb->prepare( $qry, $tmp_current_wp_id ) );

		// found - this should always happen - unless they delete the cookies
		if($tmp_user_guid){
		   // create the session $user_guid
			$_SESSION['user_guid'] = $tmp_user_guid;
			// sync up the user_guid and cookies to be safe or if user deleted their cookies or expired
			set_cookie();
		} else {
		   //*** did not find the user with the wp_id - try with the cookie or the user guid
			//*** at this point means user is logging in first time as wp_user and the guest account will now
			//*** be associated with their wp account
			if( isset($_COOKIE['framedware'])){
				// get the guest credentials
				$qry = "SELECT user_guid FROM fware_user_obj WHERE user_guid = %s";
				$tmp_user_guid = $wpdb->get_var( $wpdb->prepare( $qry, $_COOKIE['framedware'] ) );

				if($tmp_user_guid){ // should always be valid if they visited as guest
                    // set the user session with the val from the db
                    $_SESSION['user_guid']  = $tmp_user_guid;
                    set_wp_id( $tmp_user_guid, $tmp_current_wp_id );
				} else {
                    $problem_txt =  'cookie = yes, valid user_guid = no, user logged in = yes';
                    login_problems( $problem_txt );
                    create_new_user();
                    create_new_order( $_SESSION['user_guid'] );
				}
			}  else if( isset( $_SESSION['user_guid'] )) {
				// get the guest credentials
				$qry = "SELECT user_guid FROM fware_user_obj WHERE user_guid = %s";
				$tmp_user_guid = $wpdb->get_var( $wpdb->prepare( $qry, $_SESSION['user_guid'] ) );

				if($tmp_user_guid){ // should always be valid if they visited as guest
					// load the credentials
					set_cookie();
					// update the session table to assoc. the wp account with the framedware account
					set_wp_id( $tmp_user_guid, $tmp_current_wp_id );
				} else {
					$problem_txt =  'cookie = no, sesssion = yes, valid user_guid = no, user logged in = yes';
					login_problems( $problem_txt );
					create_new_user();
					create_new_order( $_SESSION['user_guid'] );
				}
         } else {
            // at this point the user is logged in but does not have an established identity
            // no cookie, session variables found
            create_new_user();
            create_new_order( $_SESSION['user_guid'] );
         }
		}
	} else { // USER IS NOT logged in
		if( isset($_COOKIE['framedware']) ){ // check the cookie
			// get the user object based on the cookie
			$qry = "SELECT user_guid FROM fware_user_obj WHERE user_guid = %s";
			$tmp_user_guid = $wpdb->get_var( $wpdb->prepare( $qry, $_COOKIE['framedware'] ) );

			if($tmp_user_guid){ // should always be valid if they visited as guest
				// set the user session with the val from the db
				$_SESSION['user_guid']  = $tmp_user_guid;
			} else {
				// cookie was established but no db user entry yet
				$problem_txt =  'cookie = yes, valid user_guid = no, user logged in = no';
				login_problems( $problem_txt );
				create_new_user();
				create_new_order( $_SESSION['user_guid'] );
			}
		} else if(isset( $_SESSION['user_guid'])) { // check the session guid
			// get the guest credentials
			$qry = "SELECT user_guid FROM fware_user_obj WHERE user_guid = %s";
			$tmp_user_guid = $wpdb->get_var( $wpdb->prepare( $qry, $_SESSION['user_guid'] ) );

			if($tmp_user_guid){ // should always be valid if they visited as guest
				// load the credentials
				set_cookie();
			} else {
				$problem_txt =  'cookie = no, session = yes, valid user_guid = no, user logged in = no';
				login_problems( $problem_txt );
				create_new_user();
				create_new_order( $_SESSION['user_guid'] );
			}
		} else {
			//throw new Exception('No Cookie / No User Guid');
			//**** no cookie or no session guid - then treat user as new visitor
			//**** create a user object
			create_new_user();
			create_new_order( $_SESSION['user_guid'] );
		}
	}

	wpdb_errors( $wpdb ); // check for errors

	return $_SESSION['user_guid'];

}

/*======================================================================================================================
|
|  NOT LOGGED IN - this retrieves the data from the fware_user_obj table
|
+=====================================================================================================================*/
function get_order_guid( $tmp_user_guid )
{
	global $wpdb, $order_guid;

	// get the values from the fware_session table for this user
	if( ! isset($_SESSION['order_guid']) ) {

		// get the details for this user from the session table
		$qry = 'SELECT order_guid FROM fware_orders WHERE user_guid = %s AND order_status = 0';
		$tmp_order_guid = $wpdb->get_var( $wpdb->prepare( $qry, $tmp_user_guid ) );

		if ( $tmp_order_guid ) {
			$order_guid             = $tmp_order_guid;
			$_SESSION['order_guid'] = $order_guid;
		} else {
			$order_guid = create_new_order( $tmp_user_guid );
			$_SESSION['order_guid'] = $order_guid;
      }
	}

	if( $wpdb->last_error == '' ){
		return $_SESSION['order_guid'];
	} else {
		wpdb_errors( $wpdb );
		return null;
	};
}

/*======================================================================================================================
|
|  this creates a new user object
|
+=====================================================================================================================*/
function get_current_item_guid( $tmp_order_guid )
{
   global $wpdb, $item_guid;

	// get the details for this user from the session table
	$qry = 'SELECT item_guid FROM fware_cart_items WHERE order_guid = %s AND item_status = 0';
	$tmp_item_guid = $wpdb->get_var( $wpdb->prepare( $qry, $tmp_order_guid ) );

    if( $tmp_item_guid )
    {
       // should not get errors on insert
       $item_guid  = $tmp_item_guid;

    };

    if( $wpdb->last_error == '' ){
        return $item_guid;
    } else {
        wpdb_errors( $wpdb );
        return null;
    };
}

/*======================================================================================================================
|
|  this creates a new user object
|
+=====================================================================================================================*/
function create_new_user()
{
	global $wpdb, $domain;

    // very important: allows use of cookie before reload.
	 clear_cookies();

	$Month = 2592000 + time(); // set the time interval to keep cookies ( 30 days )

	$tmp_user_guid = UUID::v4();

	$_SESSION['user_guid'] = $tmp_user_guid; // set the session guid
	setcookie("framedware", $tmp_user_guid, $Month, '/', $domain ); // set the cookie with the new user guid // TODO: fix, not working in WP plugin

	$tmp_wp_user_guid = get_current_user_id(); // get the wp_user_id via WP

	$wpdb->insert(
		'fware_user_obj',
		array(
			'status'       => 1,
			'user_guid'    => $tmp_user_guid, // save the new user guid
			'wp_user_id'   => $tmp_wp_user_guid,
			'created'      => date("Y/m/d") . " - " . date("h:i"),
			'last_login'   => date("Y/m/d") . " - " . date("h:i"),
			'modified'     => date("Y/m/d") . " - " . date("h:i"),
		),
		array( '%d', '%s', '%d', '%s', '%s', '%s' )
	);

	if( $wpdb->last_error == '' ){
		return $tmp_user_guid;
	} else {
		wpdb_errors( $wpdb );
		return null;
	};
}

/*======================================================================================================================
|
|  Put the order guid in the framedware session table
|
+=====================================================================================================================*/
function create_new_order( $tmp_user_guid )
{
	global $wpdb, $order_guid;

	$tmp_order_guid      = UUID::v4();

	//plg('NEW ORDER GUID CREATED ==' .$tmp_order_guid, 1);

	$wpdb->insert(
		'fware_orders',
		array(
			'order_status'    => 0,
			'order_guid'      => $tmp_order_guid,
			'user_guid'       => $tmp_user_guid,
			'order_created'   => date("Y/m/d"),
			'order_number'    => get_order_number( 1 ),
		),
		array( '%s', '%s', '%s', '%s', '%d' )
	);

	if( $wpdb->last_error == '' ){
	    // should not get errors on insert
	    $order_guid = $tmp_order_guid;
        $_SESSION['order_guid'] = $order_guid;
	    return $tmp_order_guid;
	} else {
		wpdb_errors( $wpdb );
		return null;
   };
}

/*======================================================================================================================
|
|  Check if the user is an Admin user
|
+=====================================================================================================================*/
function check_admin()
{
	global $user_id;

	if (is_super_admin($user_id))
	{
		return true;
	} else {
		return false;
	}
}

/*======================================================================================================================
|
|  this gets from the DB the max printable height and width in pixels
|
+=====================================================================================================================*/
function get_max_print_params()
{
	global $wpdb;

	// eventually get this from the db

	$_tmp_array = array(); // initialize the array

	$_tmp_array['max_print_longest_side'] = 2880;
	$_tmp_array['max_print_shortest_side'] = 2592;

	return $_tmp_array;
}

/*======================================================================================================================
|
|  check for order
|
+=====================================================================================================================*/
function check_for_order()
{
	global $wpdb;

	$tmp_user_id = get_current_user_id();

	$strQuery = 'SELECT order_guid FROM fware_orders WHERE user_guid = %s AND status=0';
	$ret_val = $wpdb->get_var( $wpdb->prepare( $strQuery, $tmp_user_id ) );

	if( ! $ret_val){
		// create an order
		//plg('NO RECORD FOUND',1);
		$wpdb->insert(
			'fware_orders',
			array(
				'user_guid'     => $tmp_user_id,
				'order_guid'    => uuid::get(),
				'created'       => date("Y/m/d") . " - " .date("h:i:sa"),
				'status'        => 0,
			),
			array( '%s', '%s', '%s', '%d' )
		);
		//plg('ORDER CREATED',1);
	} else {
		//plg('RECORD FOUND',1);
	}
}

/*======================================================================================================================
|
|  this saves the productJSON with clacled sizes and as a well formed JSON
|
+=====================================================================================================================*/
function save_product_json($tmp_post)
{
	global $wpdb;

	$qry = "UPDATE fware_cart_items SET productJSON = %s WHERE item_guid = %s";
	$wpdb->query( $wpdb->prepare( $qry, stripslashes($tmp_post['product_json']), $tmp_post['item_guid'] ) );

	if( $wpdb->last_error == '' ){
		return 'success';
	} else {
		return 'fail';
	}
};

/*----------------------------------------------------------------------------------------------------------------------
|
|
|
+---------------------------------------------------------------------------------------------------------------------*/

function get_product_by_sku($sku)
{
    global $wpdb;

    $product_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value='%s' LIMIT 1", $sku ) );

    if ($product_id) {
        return new WC_Product($product_id);
    }
    return null;
}

function remove_item_from_cart($product_id)
{
    $cart = WC()->instance()->cart;

    $cart_id = $cart->generate_cart_id($product_id);
    $cart_item_id = $cart->find_product_in_cart($cart_id);

    if($cart_item_id){
        $cart->set_quantity($cart_item_id, 0);
        return true;
    }
    return false;
}

/**
 *  Framing (regular)
 *  Add to Cart
 *
 * @param $tmp_post
 */
function put_product_config_details( $tmp_post )
{
	global $wpdb;

	$ret_array = [];

	///// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =
	///// UPDATE THE CART ITEMS TABLE
	///// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =
	$iii = $wpdb->update( 'fware_cart_items',
		array(
			'item_status'       => 1,
			'img_guid'          => $tmp_post['img_guid'],
			'item_price'        => $tmp_post['item_price'],
			'item_qty'          => $tmp_post['item_qty'],
			'ProductJSON'       => stripslashes($tmp_post['ProductJSON']),
			'modified'          => date("Y-m-d H:i:s")
		),
		array('item_guid' => $tmp_post['item_guid']),
		array('%d', '%s', '%f', '%d', '%s', '%s'),
		array('%s')
	);

	///// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =
	///// GET THE CART ITEMS FOR THE ORDER
	///// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =
	$query="SELECT * FROM fware_cart_items WHERE item_status = 1 AND order_guid = %s";
	$cart_items_val = $wpdb->get_results( $wpdb->prepare( $query, $tmp_post['order_guid'] ) );

	if ($cart_items_val) {
		$ret_array['cart_items'] = $cart_items_val; // cart_items to return
		$total = 0;
		$count = 0;
		foreach ($cart_items_val as $row) { // cycle through all the cart items to get the total qty and the total amount
			$total += $row->item_price;
			$count += 1;
		}
		$tmp_post['order_subtotal'] = $total;
		$tmp_post['order_item_qty'] = $count;
	} else {
		// should never drop down to this piece of code
		$ret_array['cart_items'] = "";
		$tmp_post['order_subtotal'] = 0;  // if no cart items then qty and amount equals zero
		$tmp_post['order_item_qty'] = 0;
	}

	///// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =
	///// UPDATE THE ORDERS TABLE WITH TOTALS AND QUANTITIES FROM THE CART TABLE
	///// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =
	$wpdb->update(  // update the orders table
		'fware_orders',
		array(
			'order_subtotal' => $tmp_post['order_subtotal'],
			'order_total'    => $tmp_post['order_subtotal'],
			'items_in_order' => $tmp_post['order_item_qty']
		),
		array('order_guid' => $tmp_post['order_guid'] ),
		array( '%f', '%f', '%d' ),
		array( '%s' )
	);

	///// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =
	///// CREATE / UPDATE WOO PRODUCT
	///// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =
    //error_log(stripcslashes($tmp_post['ProductJSON']));
    $product_source = json_decode(stripcslashes($tmp_post['ProductJSON']), true);

    // matting
    $description_matting = '<br>Matting: ' . $product_source['mb1_width_text'];
    if ($product_source['type'] == 'express') {
        $description_matting = '';
    }

    // glass
    $description_invisible_glass = '<br>Regular Glass';
    $final_price = $product_source['print_price'];
    //error_log('invisible_glass = ' . $product_source['invisible_glass']);
    //error_log('invisible_glass_price = ' . $product_source['invisible_glass_price']);
    if ($product_source['invisible_glass'] == '1') {
        $final_price = $product_source['print_price'] + $product_source['invisible_glass_price'];
        $description_invisible_glass = '<br>Invisible Glass';
    }

    $description = '
        Framing type: ' . ucfirst($product_source['type']). '<br>
        Outside Dimensions:<br>' . $product_source['outer_dimension'] . '<br>
        Printed Image Size:<br>' . $product_source['print_dimension'] . '<br>
        Glass Size:<br>' . $product_source['glass_dimension'] . '<br>
        Frame Description: ' . $product_source['frame_description']
        . $description_matting
        . $description_invisible_glass;

    $file = get_site_url() . '/uploadhandler/uploads/' . $tmp_post['img_guid'] . '/' . $tmp_post['img_guid'] . '.png';

    $product = get_product_by_sku($product_source['item_guid']); // if product exists, get it and update
    if ($product) {
        //error_log('WC_PRODUCT ................................... UPDATE');
        remove_item_from_cart($product->get_id());

        $product->set_description($description);
        $product->set_short_description($description);
        $product->set_regular_price($final_price);
        $product->save();
    } else { // new
        //error_log('WC_PRODUCT ................................... NEW');
        $product = new WC_Product();
        $product->set_name($product_source['img_original_filename']);
        $product->set_sku($product_source['img_guid']);
        $product->set_description($description);
        $product->set_short_description($description);
        $product->set_regular_price($final_price);
        $product->set_category_ids([WOO_CATEGORY_ID]);
        $product->save();
    }
    // UPDATE META DATA
    $product->update_meta_data('frame_number', $product_source['frame_number']); // key, value
    $product->update_meta_data('description', $description); // key, value
    $product->save();

    error_log($product);

    // ATTACH PRODUCT IMAGE
    attach_product_thumbnail($product->get_id(), $file, 0);

    // SESSION ITEM TO NULL
    $_SESSION['item_guid'] = null;



    ///// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =
    ///// ADD WOO PRODUCT TO THE CART
    ///// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =
    WC()->cart->add_to_cart($product->get_id());


    //
    exit();
};

/**---------------------------------------------------------------------------------------------------------------------
|
|  Get the details of the image that was in process but not added to the cart
|
+---------------------------------------------------------------------------------------------------------------------*/
function delete_cart_item( $tmp_post )
{
	global $wpdb;

	$qry = "DELETE FROM fware_cart_items WHERE item_guid = %s;";
	$wpdb->query( $wpdb->prepare( $qry, $tmp_post['item_guid'] ) );

	// get the user data
	$qry_get_user_data = "SELECT * FROM fware_user_obj INNER JOIN fware_orders ON fware_user_obj.user_guid = fware_orders.user_guid WHERE fware_user_obj.user_guid = %s AND fware_orders.order_status = 0";
	$ret_val1 = $wpdb->get_row( $wpdb->prepare( $qry_get_user_data, $_SESSION['user_guid'] ), ARRAY_A);

	if ($ret_val1){
		$tmp_ret_array['order_guid'] = $ret_val1['order_guid'];
		$tmp_ret_array['user_order_data'] = $ret_val1;

	}

	///// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =
	///// recalc the cart totals
	///// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =

	// check to see how many order items connected to this order
	$query = "SELECT item_price FROM fware_cart_items WHERE order_guid = %s";
	$ret_val = $wpdb->get_results( $wpdb->prepare( $query, $tmp_ret_array['order_guid'] ));

	if ($ret_val) {

		$total = 0;
		$count = 0;
		foreach ($ret_val as $row) {
			$total += $row->item_price;
			$count += 1;
		}

		$tmp_ret_array['order_subtotal'] = $total;
		$tmp_ret_array['items_in_order'] = $count;

	} else {

		// set to 0.00 if there are no cart_items records
		$tmp_ret_array['order_subtotal'] = 0.00;
		$tmp_ret_array['items_in_order'] = 0;

	}

	///// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =
	///// update the order table with the new totals
	///// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =
	$wpdb->update(
		'fware_orders',
		array(
			'order_subtotal'    => $tmp_ret_array['order_subtotal'],
			'order_total'       => $tmp_ret_array['order_subtotal'],
			'order_tax_total'   => $tmp_ret_array['order_tax'],
			'items_in_order'    =>  $tmp_ret_array['items_in_order']

		),
		array('order_guid' => $tmp_ret_array['order_guid'] ),
		array( '%f', '%f', '%f', '%d' ),
		array( '%s' )
	);

	//'order_shipping'    => 0.00,
	//'ship_type'         => 'Pickup',
	//'ship_method'       => 'Will Call',

	// Select data from the orders, cart_items and img_item tables
	$query = "SELECT * FROM fware_cart_items INNER JOIN fware_img_details ON fware_img_details.img_guid = fware_cart_items.img_guid INNER JOIN fware_product_preview ON fware_cart_items.item_guid = fware_product_preview.item_guid WHERE fware_cart_items.order_guid = %s AND fware_cart_items.item_status <> 2";
	$ret_val2 = $wpdb->get_results( $wpdb->prepare( $query, $tmp_ret_array['order_guid'] ), ARRAY_A);

	if ( $ret_val2 ){

		$tmp_ret_array['cart_items'] = $ret_val2;

	}

	$wpdb->flush();

	return json_encode( $tmp_ret_array, JSON_UNESCAPED_SLASHES );
}

/*----------------------------------------------------------------------------------------------------------------------
|
|  This function converts the image which is in base64 into an image file
|
+---------------------------------------------------------------------------------------------------------------------*/
function save_img_blob_to_file( $img_blob, $tmp_image_guid, $tmp_product_type )
{
	$tmp_root =  $_SERVER["DOCUMENT_ROOT"];

	define('UPLOAD_DIR', $tmp_root .'/uploadhandler/uploads/'. $tmp_image_guid .'/' );

	$img = str_replace('data:image/png;base64,', '', $img_blob );
	$img = str_replace(' ', '+', $img );
	$data = base64_decode( $img );
	$file = UPLOAD_DIR . $tmp_product_type . '-productPreview' . '.png';
	$success = file_put_contents( $file, $data );
};

/**--------------------------------------------------------------------------------*
|
|  This function gets a view of the order_item and the product
|
*---------------------------------------------------------------------------------*/
function get_order_info( $tmp_post, $post_user_guid  ){
	global $wpdb;

	// this is because this function is being used by two different calling funcs
	// passing in two different types of params
	if( $post_user_guid ){
		$tmp_user_guid = $post_user_guid;
	} else {
		$tmp_user_guid = $tmp_post['user_guid'];
	}

	$tmp_ret_array = [];

	// get the user data
	$qry_get_user_data = "SELECT * FROM fware_user_obj INNER JOIN fware_orders ON fware_user_obj.user_guid = fware_orders.user_guid WHERE fware_user_obj.user_guid = %s AND fware_orders.order_status = 0";
	$ret_val1 = $wpdb->get_row( $wpdb->prepare( $qry_get_user_data, $tmp_user_guid ), ARRAY_A);

	if ($ret_val1){
		$tmp_ret_array['order_guid'] = $ret_val1['order_guid'];
		$tmp_ret_array['user_order_data'] = $ret_val1;

	}

	// Select data from the orders, cart_items and img_item tables
	$query = "SELECT * FROM fware_cart_items INNER JOIN fware_img_details ON fware_img_details.img_guid = fware_cart_items.img_guid INNER JOIN fware_product_preview ON fware_cart_items.item_guid = fware_product_preview.item_guid WHERE fware_cart_items.order_guid = %s AND fware_cart_items.item_status <> 2";
	$ret_val2 = $wpdb->get_results( $wpdb->prepare( $query, $tmp_ret_array['order_guid'] ), ARRAY_A);

	if ( $ret_val2 ){
		$tmp_ret_array['cart_items'] = $ret_val2;
	}

	$wpdb->flush();

	if( $post_user_guid ){
		return $tmp_ret_array;
	} else {
		return json_encode( $tmp_ret_array, JSON_UNESCAPED_SLASHES );
	}
}

/*======================================================================================================================
|
|  this will run on first init
|
+=====================================================================================================================*/
function wpdb_errors( $_wpdb ){

	if( $_wpdb->last_error !== '' ){

		$_wpdb->print_error();

		//plg('WPDB ERROR ===' . $_wpdb->last_error, 1);

		return 'ERROR';

	} else {

		return null;

	}
};

/*======================================================================================================================
|
|  generate order numbers
|
+=====================================================================================================================*/
function get_order_number( $type_flag ) { //===> get_order_number
	global $wpdb;

    // not in use
}

/*======================================================================================================================
|
|
|
+=====================================================================================================================*/
function get_product_json( $tmp_post ) {
	global $wpdb, $productJSON, $FrameProductJSON;

	$qry = 'SELECT productJSON FROM fware_cart_items WHERE item_guid = %s';
	$tmp_productJSON = $wpdb->get_var( $wpdb->prepare( $qry, $tmp_post['item_guid'] ) );

	if ( $tmp_productJSON ) {
		$productJSON = json_decode( $tmp_productJSON );
		return json_encode( $productJSON, JSON_UNESCAPED_SLASHES );
	} else {
		// return a blank JSON
		return json_encode( $FrameProductJSON, JSON_UNESCAPED_SLASHES );
	}
}

/*======================================================================================================================
|
|
|
+=====================================================================================================================*/
function get_current_item_data(){
   global $wpdb, $item_guid, $productJSON, $FrameProductJSON;

   $qry = 'SELECT * FROM fware_cart_items WHERE order_guid = %s';
   $ret_val = $wpdb->get_row(  $wpdb->prepare( $qry, $_SESSION['order_guid'] ) );

   if($ret_val){
      $item_guid = $ret_val->item_guid;
      $productJSON = $ret_val->productJSON;
   } else {
	   $item_guid = '';
	   $productJSON = $FrameProductJSON;
   }
}


/*======================================================================================================================
|
|
|
+=====================================================================================================================*/
function edit_cart_item( $tmp_post ){
	global $wpdb;

	$wpdb->update('fware_cart_items',
      array( 'item_status'  => 0 ),
      array( 'item_guid' => $tmp_post['item_guid'] ),
		array( '%d' ),
		array( '%s' )
   );

	wpdb_errors( $wpdb );

	$qry = 'SELECT productJSON FROM fware_cart_items WHERE item_guid = %s';
	$ret_val = $wpdb->get_var( $wpdb->prepare( $qry, $tmp_post['item_guid'] ) );

	if ($ret_val){
	   return $ret_val;
   } else {
		return null;
   }
}

/*======================================================================================================================
|
|
|
+=====================================================================================================================*/
function _delete_cart_item( $tmp_post ){
	global $wpdb;

   $qry = "DELETE FROM fware_cart_items WHERE item_guid = %s;";
   $wpdb->query( $wpdb->prepare( $qry, $tmp_post['item_guid'] ) );

	// get the user data
	$qry_get_user_data = "SELECT * FROM fware_user_obj INNER JOIN fware_orders ON fware_user_obj.user_guid = fware_orders.user_guid WHERE fware_user_obj.user_guid = %s AND fware_orders.order_status = 0";
	$ret_val1 = $wpdb->get_row( $wpdb->prepare( $qry_get_user_data, $_SESSION['user_guid'] ), ARRAY_A);

	if ($ret_val1){
		$tmp_ret_array['order_guid'] = $ret_val1['order_guid'];
		$tmp_ret_array['user_order_data'] = $ret_val1;
	}

	// Select data from the orders, cart_items and img_item tables
	$query = "SELECT * FROM fware_cart_items INNER JOIN fware_img_details ON fware_img_details.img_guid = fware_cart_items.img_guid INNER JOIN fware_product_preview ON fware_cart_items.item_guid = fware_product_preview.item_guid WHERE fware_cart_items.order_guid = %s AND fware_cart_items.item_status <> 2";
	$ret_val2 = $wpdb->get_results( $wpdb->prepare( $query, $tmp_ret_array['order_guid'] ), ARRAY_A);

	if ( $ret_val2 ){
		$tmp_ret_array['cart_items'] = $ret_val2;
	}

	$wpdb->flush();

	return json_encode( $tmp_ret_array, JSON_UNESCAPED_SLASHES );
}

/*======================================================================================================================
|  get shopping cart totals
+=====================================================================================================================*/
function get_shopping_cart_totals()
{
    global $wpdb;

    $qry = "SELECT * FROM fware_orders WHERE order_guid = %s";
    $ret_val = $wpdb->get_row( $wpdb->prepare( $qry, $_SESSION['order_guid']), ARRAY_A );
    return $ret_val;
}

/*======================================================================================================================
|
|
|
+=====================================================================================================================*/
function save_order_table_info( $tmp_post )
{
   global $wpdb;

   $wpdb->update( 'fware_orders',
      array(
         'order_tax_total'    => $tmp_post['order_tax'],
	     'order_ship_total'   => $tmp_post['order_shipping'],
	     'order_total'        => $tmp_post['order_final_total'],
         'special_instructions'  => $tmp_post['special_instructions'],
      ),
      array(  'order_guid'    => $_SESSION['order_guid']  ),
      array(  '%f', '%f', '%f', '%s'  ),
      array(  '%s'  )
   );
}

/*======================================================================================================================
|
|
|
+=====================================================================================================================*/
function login_problems( $problem_text )
{
	//plg('****** HOUSTON - WE GOT LOGIN PROBLEMS - 1 ********',1);
	//plg('****** ISSUE =='+ $problem_text,1);
}

/**---------------------------------------------------------------------------------------------------------------------
|
|  This gets the shipping rate from the orders table
|
+---------------------------------------------------------------------------------------------------------------------*/
function get_shipping_rate( $tmp_post_arr )
{
	$myRate = new fedexRate;
	$tmp_ship_return_details = $myRate->getRate( $tmp_post_arr );
	return json_encode( $tmp_ship_return_details );

}

/**---------------------------------------------------------------------------------------------------------------------
|  this completes the order confirmation by setting the order status to 1 and then sending the email
+---------------------------------------------------------------------------------------------------------------------*/
function get_cart_order_info()
{
    global $wpdb, $local_tax;

    // get the data info from all the tables related to the order confirmation
    $get_address_info_qry =
        "SELECT fware_orders.* ,fware_bill_details.billing_JSON,fware_ship_details.shipping_JSON 
        FROM fware_orders INNER JOIN fware_bill_details ON fware_orders.user_guid = fware_bill_details.user_guid
        INNER JOIN fware_ship_details ON fware_orders.order_guid = fware_ship_details.order_guid
        WHERE fware_orders.order_guid = %s";

    $ret_val = $wpdb->get_row( $wpdb->prepare( $get_address_info_qry, $_SESSION['order_guid'] ), ARRAY_A );

    $tmp_address_array = json_decode( $ret_val['billing_JSON'], true ); // decode the billing json into an array
    $tmp_shipping_array = json_decode( $ret_val['shipping_JSON'], true ); // decode the shippping json into an array

    if( isset( $ret_val['shipping_JSON'] ) ){ // merge the arrays into a single array
        $return_array = array_merge( $ret_val, $tmp_address_array, $tmp_shipping_array );
    } else {
        $return_array = array_merge( $ret_val, $tmp_address_array );
    }

    unset( $ret_val['billing_JSON'] ); // remove the excess data not needed
    unset( $ret_val['shipping_JSON'] );

    if( strtolower( $tmp_address_array['bill_state'] ) == 'ca' ){
	    $taxable = 1;
    } else {
	    $taxable = 0;
    }

    // compute the tax
    if( $taxable == 1 ){
        $tmp_tax = floatVal( $ret_val['order_subtotal'] ) * $local_tax; // compute the tax
        $return_array['order_tax_total'] = number_format( $tmp_tax, 2 , '.', '' );
    } else {
        $tmp_tax = 0; // compute the tax
        $return_array['order_tax_total'] = number_format( $tmp_tax, 2 , '.', '' );
    }

    $tmp_order_subtotal = number_format( floatval( $ret_val['order_subtotal'] ), 2, '.', '' );
    $return_array['order_subtotal'] = $tmp_order_subtotal;

    // compute the new order total
    $tmp_order_total =  $tmp_order_subtotal + floatVal( $ret_val['order_ship_total'] ) + $tmp_tax;
    $return_array['order_total'] = $tmp_order_total;

    // update the orders table with the latest order total
    $wpdb->update( 'fware_orders',
        array(
            'order_tax_total' => $tmp_tax,
            'order_total' => $tmp_order_total,
            'taxable' => $taxable,
            'order_discount_code' => null,
           'order_discount_amount' => null
        ),
        array( 'order_guid' => $_SESSION['order_guid'] ),
        array( '%f', '%f', '%d', '%s', '%f' ),
        array( '%f' )
    );

    return $return_array;
}

/*======================================================================================================================
|  save order totals - calc the tax and save with new total
+=====================================================================================================================*/
function save_order_totals( $tmp_post ){
    global $wpdb, $local_tax;

    if( isset( $_SESSION['order_guid'] ) ){

        $tmp_tax = floatval( $tmp_post['order_final_subtotal'] ) * $local_tax;

        $wpdb->update( 'fware_orders',
            array(
                'order_tax_total'       => $tmp_tax,
                'special_instructions'  => $tmp_post['special_instructions']

            ),
            array( 'order_guid' => $_SESSION['order_guid'] ),
            array('%f', '%s'),
            array('%s')
        );
    }
}

/*======================================================================================================================
|
|  creates a zip file
|
+=====================================================================================================================*/
function get_list_of_uploaded_files( $file_collection_list, $tmp_file_upload_guid ){

	$tmp_file_list_collection = explode( ",", stripslashes( $file_collection_list ) ) ;

	$final_file_list_array = array();

	foreach ( $tmp_file_list_collection as $list_element ){

		$file_list_item = explode(":", $list_element );
		$file_name = ltrim( $file_list_item[1], '"' );
		$file_name = str_replace( " ", "", $file_name );
		$full_path_filename = ABSPATH . '/uploadhandler/uploads/'. $tmp_file_upload_guid . '/' . $file_name;
		array_push( $final_file_list_array, $full_path_filename );
	};

	if( isset( $final_file_list_array ) ){
		return $final_file_list_array;
	} else {
		return NULL;
	}
}
