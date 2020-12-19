<?php
/**
 * Plugin Name: FramedWare
 * Description: Framing plugin.
 * Author:
 * Version: 2.0.1.1
 */

define('FRAMEDWARE_ORDER_PLUGIN_VERSION', '2.0.1.1');

ini_set('xdebug.var_display_max_depth', '10');
ini_set('xdebug.var_display_max_children', '1000');
ini_set('xdebug.var_display_max_data', '1024');

set_time_limit(0);
error_reporting(E_ALL & ~(E_STRICT|E_NOTICE));

date_default_timezone_set('America/New_York');

$user_guid = null;
$order_guid = null;
$item_guid = null;
$productJSON = null;
$userInfo = null;
$support_email = 'support@frameshops.com';

use Automattic\WooCommerce\Client as WooCommerceClient;
use Automattic\WooCommerce\HttpClient\HttpClientException;
use Filestack\FilestackClient;
use Filestack\filelink;
use Filestack\FilestackSecurity;

//* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
// ********* hook the init action to start a session *******/
// this is required to enable the $_SESSION object
add_action('init', 'myStartSession', 1); // TODO: fix, not working in WP plugin
add_action('init', 'run_header_code', 1);
add_action('wp_logout', 'myEndSession');
add_action('wp_login', 'myEndSession');
add_action('wp_head', 'fineUploaderTemplate');
add_action('wp_head', 'shoppingCartTemplate');

// **** these next two must be run in order to use the session object
// make sure the system turns on the session
function myStartSession() {
	if(!session_id()) {
		session_start();
	}
};

// on endSession distroy the session variables
function myEndSession() {
	session_destroy();
};

define( 'PLUGINPATH',   WP_PLUGIN_DIR . '/framedware/' );
define( 'AJAXADMIN',    admin_url( "admin-ajax.php" ) );
define( 'DOAJAXADMIN',  home_url(). "/wp-content/plugins/do-ajax.php"  );

define('FRAMEDWARE_UPLOAD_PATH', ABSPATH . '/uploadhandler/uploads/');
define('FRAMEDWARE_ORDER_PATH', ABSPATH . '/uploadhandler/orders/');
define('FRAMEDWARE_SITE_URL', get_site_url());

if ( ! file_exists(FRAMEDWARE_UPLOAD_PATH)) {
    mkdir(FRAMEDWARE_UPLOAD_PATH, 0755, true);
}
if ( ! file_exists(FRAMEDWARE_ORDER_PATH)) {
    mkdir(FRAMEDWARE_ORDER_PATH, 0755, true);
}

require_once('vendor/autoload.php');

include( 'config.php' );
include( PLUGINPATH . 'php-global-vars.php' );
include( PLUGINPATH . 'php-class-functions.php' );
include( PLUGINPATH . 'php-templates.php' );
include( PLUGINPATH . 'php-utility-functions.php' );
include( PLUGINPATH . 'php-ajax-functions.php' );
include( PLUGINPATH . 'php-process-upload-image.php' );
include( PLUGINPATH . 'php/Exception.php' );
include( PLUGINPATH . 'php/PHPMailer.php' );
include( PLUGINPATH . 'php/SMTP.php' );

// PLUGIN-UPDATE-CHECKER
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://github.com/Mohammed2017/FrameShops-dot-com',
    __FILE__,
    'framedware'
);
//Optional: If you're using a private repository, specify the access token like this:
$myUpdateChecker->setAuthentication(GIT_TOKEN);
//Optional: Set the branch that contains the stable release.
$myUpdateChecker->setBranch('master');


//ae_nocache(); // disable caching // TODO: this is not working >  PHP Warning:  Cannot modify header information - headers already sent

function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++)
    {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

/**
 * PRODUCT META DATA: CUSTOM PRODUCT FIELD [FRAME NUMBER] ... [START]
 */

/**
 * Display the custom text field in admin
 * @since 1.0.0
 */
function cfwc_create_custom_field() {
    $args = array(
        'id'            => 'frame_number', // custom field text field id
        'label'         => __( 'Frame number', 'cfwc' ), // custom field
        'class'			=> 'cfwc_frame_number',
        'desc_tip'      => true,
        'description'   => __( 'Frame number.', 'ctwc' ),
    );
    woocommerce_wp_text_input( $args );
}
add_action( 'woocommerce_product_options_general_product_data', 'cfwc_create_custom_field' );

/**
 * Save the custom field
 * @since 1.0.0
 */
function cfwc_save_custom_field( $post_id ) {
    $product = wc_get_product( $post_id );
    $title = isset( $_POST['frame_number'] ) ? $_POST['frame_number'] : '';
    $product->update_meta_data( 'frame_number', sanitize_text_field( $title ) );
    $product->save();
}
add_action( 'woocommerce_process_product_meta', 'cfwc_save_custom_field' );

/**
 * Display custom field on the front end
 * @since 1.0.0
 */
/*
function cfwc_display_custom_field() {

    global $post;
    // Check for the custom field value
    $product = wc_get_product( $post->ID );
    $title = $product->get_meta( 'frame_number' );
    if( $title ) {
        // Only display our field if we've got a value for the field title
        printf(
            '<div class="cfwc_frame_number-wrapper"><label for="cfwc-title-field">%s</label><input type="text" id="cfwc-title-field" name="cfwc-title-field" value=""></div>',
            esc_html( $title )
        );
    }

}
add_action( 'woocommerce_before_add_to_cart_button', 'cfwc_display_custom_field' );
*/

/**
 * Validate the text field
 * @since 1.0.0
 * @param Array 		$passed					Validation status.
 * @param Integer   $product_id     Product ID.
 * @param Boolean  	$quantity   		Quantity
 */
/*
function cfwc_validate_custom_field( $passed, $product_id, $quantity )
{
    if( empty( $_POST['cfwc-title-field'] ) ) {
        // Fails validation
        $passed = false;
        wc_add_notice( __( 'Please enter a value into the text field', 'cfwc' ), 'error' );
    }
    return $passed;
}
add_filter( 'woocommerce_add_to_cart_validation', 'cfwc_validate_custom_field', 10, 3 );
*/

/**
 * Add the text field as item data to the cart object
 * @since 1.0.0
 * @param Array 		$cart_item_data Cart item meta data.
 * @param Integer   $product_id     Product ID.
 * @param Integer   $variation_id   Variation ID.
 * @param Boolean  	$quantity   		Quantity
 */
function cfwc_add_custom_field_item_data( $cart_item_data, $product_id, $variation_id, $quantity )
{
    $product = wc_get_product( $product_id );
    $cart_item_data['frame_number'] = $product->get_meta('frame_number');
    return $cart_item_data;
}
add_filter( 'woocommerce_add_cart_item_data', 'cfwc_add_custom_field_item_data', 10, 4 );

/**
 * Update the price in the cart
 * @since 1.0.0
 */
/*
function cfwc_before_calculate_totals( $cart_obj ) {
    if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
        return;
    }
    // Iterate through each cart item
    foreach( $cart_obj->get_cart() as $key=>$value ) {
        if( isset( $value['total_price'] ) ) {
            $price = $value['total_price'];
            $value['data']->set_price( ( $price ) );
        }
    }
}
add_action( 'woocommerce_before_calculate_totals', 'cfwc_before_calculate_totals', 10, 1 );
*/

/**
 * Display the custom field value in the cart
 * @since 1.0.0
 */
/*
function cfwc_cart_item_name( $name, $cart_item, $cart_item_key ) {

    if( isset( $cart_item['frame_number'] ) ) {
        $name .= sprintf(
            '<p>%s</p>',
            esc_html( $cart_item['frame_number'] )
        );
    }
    return $name;

}
add_filter( 'woocommerce_cart_item_name', 'cfwc_cart_item_name', 10, 3 );
*/
/**
 * DISPLAY
 * Add custom field to order object
 */
function cfwc_add_custom_data_to_order( $item, $cart_item_key, $values, $order )
{
    foreach( $item as $cart_item_key => $values ) {
        if( isset( $values['frame_number'] ) ) {
            $item->add_meta_data( __( 'Frame number', 'cfwc' ), $values['frame_number'], true );
        }
    }
}
add_action( 'woocommerce_checkout_create_order_line_item', 'cfwc_add_custom_data_to_order', 10, 4 );

/**
 * PRODUCT META DATA: CUSTOM PRODUCT FIELD [FRAME NUMBER] ... [END]
 */


/**
 * Add order item meta data
 */
add_action('woocommerce_add_order_item_meta','my_meta',1,2);
if( ! function_exists('my_meta'))
{
    function my_meta($item_id, $values)
    {
        global $woocommerce, $wpdb;
        if (isset($values['product_id'])) {
            $product = new WC_Product($values['product_id']);
            wc_add_order_item_meta($item_id, 'description', $product->get_description());
        }
    }
}

add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles', PHP_INT_MAX);
function theme_enqueue_styles() {
	wp_register_style('framedware-default-css', plugin_dir_url(__FILE__).'css/style.css' , [], FRAMEDWARE_ORDER_PLUGIN_VERSION);
	wp_enqueue_style('framedware-default-css');
}

//* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
//  Add these essential CSS style sheets at the beginning of each page
add_action('wp_enqueue_scripts', 'add_my_stylesheets');
function add_my_stylesheets() {
	wp_register_style('bootstrap-sliders-css', plugin_dir_url(__FILE__).'css/bootstrap-sliders.css' , '');
	wp_enqueue_style('bootstrap-sliders-css');

	wp_register_style('bootstrap-custom-css', plugin_dir_url(__FILE__).'css/bootstrapcustom.css' , '');
    wp_enqueue_style('bootstrap-custom-css');

	wp_register_style('bootstrap-min-css', plugin_dir_url(__FILE__)."includes/bootstrap.min.css" , '');
	wp_enqueue_style('bootstrap-min-css');

	wp_register_style('bootstrap-datepicker-css', plugin_dir_url(__FILE__)."includes/bootstrap-datepicker.min.css" , '');
	wp_enqueue_style('bootstrap-datepicker-css');

	wp_register_style('select-picker', plugin_dir_url(__FILE__)."includes/bootstrap-select.min.css" );
	wp_enqueue_style('select-picker');

	wp_register_style('style-fine-uploader-css', plugin_dir_url(__FILE__)."includes/fine-uploader.css" , '');
    wp_enqueue_style('style-fine-uploader-css');

	wp_register_style('style-cropper-js-css', plugin_dir_url(__FILE__)."includes/cropper.min.css" , '');
	wp_enqueue_style('style-cropper-js-css');

	wp_register_style('bootstrap-toggle-css', plugin_dir_url(__FILE__)."includes/bootstrap-toggle.min.css" , '');
	wp_enqueue_style('bootstrap-toggle-css');

}

//* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
// Add essential Javascript libraries

function my_ajax()
{
    $uploader_page = get_page_by_title('uploader', ARRAY_A);
    $configurator_page = get_page_by_title('configurator', ARRAY_A);
    $uploader_express = get_page_by_title('uploader express', ARRAY_A);
    $configurator_express = get_page_by_title('configurator express', ARRAY_A);

    $my_ajax = [
        'ajaxurl'      => admin_url('admin-ajax.php'),
        'do_ajax'      => DOAJAXADMIN,
        'user_guid'    => $_SESSION['user_guid'],
        'order_guid'   => $_SESSION['order_guid'],
        'item_guid'    => $_SESSION['item_guid'],
        'upload_page'  => $uploader_page['guid'],
        'configurator_page'  => $configurator_page['guid'],
        'uploader_express'      => $uploader_express['guid'],
        'configurator_express'  => $configurator_express['guid'],
        'woocommerce_cart_redirect_after_add' => get_option( 'woocommerce_cart_redirect_after_add' ),
        'woocommerce_cart_url' => wc_get_cart_url(),
        'plugin_url' => PLUGIN_URL,
        'wall' => (isset($_SESSION['wall'])) ? $_SESSION['wall'] : (object) null,
    ];

    return $my_ajax;
}

add_action('wp_enqueue_scripts', 'add_my_javascripts', 10);
function add_my_javascripts() {
	global $framedware_flag;

    wp_register_script('filestack-js', '//static.filestackapi.com/filestack-js/3.x.x/filestack.min.js' , [], FRAMEDWARE_ORDER_PLUGIN_VERSION, false);
    wp_enqueue_script('filestack-js');

	wp_register_script('mobile-detect-js',plugin_dir_url(__FILE__)."includes/mobile-detect.min.js" , [], '2.5.0', false);
	wp_enqueue_script('mobile-detect-js');

	wp_register_script('load-image-js',plugin_dir_url(__FILE__)."includes/load-image.all.min.js" , [], '2.20.1', false);
	wp_enqueue_script('load-image-js');

	wp_register_script('jquery-validate-js', plugin_dir_url(__FILE__)."includes/jquery.validate.min.js" , ['jquery'], '1.19.0', false);
	wp_enqueue_script('jquery-validate-js');

	wp_register_script('jquery-validate-additional-methods-js', plugin_dir_url(__FILE__)."includes/additional-methods.min.js" , ['jquery'], '1.19.0', false);
	wp_enqueue_script('jquery-validate-additional-methods-js');

	wp_register_script('loading-overlay-js', plugin_dir_url(__FILE__)."includes/loadingoverlay.js" , ['jquery'], '2.1.6', false);
	wp_enqueue_script('loading-overlay-js');

	wp_register_script('bootstrap-datepicker-js', plugin_dir_url(__FILE__).'includes/bootstrap-datepicker.min.js' , ['jquery'], '0.10.2', false);
	wp_enqueue_script('bootstrap-datepicker-js');

	wp_register_script('konva-js',plugin_dir_url(__FILE__)."includes/konva.min.js" , [], '2.5.0', false);
	wp_enqueue_script('konva-js');

   wp_register_script('bootstrap-bundle-min-js',plugin_dir_url(__FILE__)."includes/bootstrap.bundle.min.js" , ['jquery'], '1.0', false);
   wp_enqueue_script('bootstrap-bundle-min-js');

    wp_register_script('config-js',plugin_dir_url(__FILE__). 'config.js', ['jquery'], filemtime(PLUGIN_PATH . 'config.js'), false);
    wp_enqueue_script('config-js');

   wp_register_script('global-vars-js',plugin_dir_url(__FILE__). 'global-vars.js', ['jquery'], FRAMEDWARE_ORDER_PLUGIN_VERSION, false);
   wp_enqueue_script('global-vars-js');

	wp_register_script('js-utility-functions-js',plugin_dir_url(__FILE__). 'js-utility-functions.js', ['jquery'], FRAMEDWARE_ORDER_PLUGIN_VERSION, false);
	wp_localize_script('js-utility-functions-js', 'myAjax', my_ajax());

	wp_enqueue_script('js-utility-functions-js');

	wp_register_script('pica-js',plugin_dir_url(__FILE__)."dist/pica.js" , [], '2.5.0', false);
	wp_enqueue_script('pica-js');

	wp_register_script('exif-js', plugin_dir_url(__FILE__)."includes/exif.min.js" , [], '2.5.0', false);
	wp_enqueue_script( 'exif-js' );

	wp_register_script('fine-uploader-js', plugin_dir_url(__FILE__)."includes/all.fine-uploader.min.js" , ['jquery'], '1.0', false);
	wp_enqueue_script('fine-uploader-js');

	wp_register_script('cropper-js',plugin_dir_url(__FILE__)."includes/cropper.min.js" , [], '1.0', false);
	wp_enqueue_script('cropper-js');

	wp_register_script('js-onready-functions-js',plugin_dir_url(__FILE__). 'js-onready-functions.js', ['jquery'], FRAMEDWARE_ORDER_PLUGIN_VERSION, false);
	wp_enqueue_script('js-onready-functions-js');

	wp_register_script('js-bootstrap-toggle-js',plugin_dir_url(__FILE__)."includes/bootstrap-toggle.min.js", [], '2.2.2', false);
	wp_enqueue_script('js-bootstrap-toggle-js');

	wp_register_script('js-bootstrap-select-js',plugin_dir_url(__FILE__)."includes/bootstrap-select.min.js", [], '1.13.2', false);
	wp_enqueue_script('js-bootstrap-select-js');
}

//* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
// Add the essential php utility functions
add_shortcode('framedeware_uploader', function()
{
    // TODO: require
    return file_get_contents(PLUGINPATH . 'views/public_uploader.php');
});

add_shortcode( 'framedware_configurator', function()
{
    // TODO: require
    return file_get_contents(PLUGINPATH . 'views/public_configurator.php');
});

add_shortcode('framedeware_gallery_wall_1x3', function()
{
    ob_start();
    require('views/public_gallery_wall_1x3.php');
    return ob_get_clean();
});

add_shortcode('framedeware_gallery_wall_2x4', function()
{
    ob_start();
    require('views/public_gallery_wall_2x4.php');
    return ob_get_clean();
});

add_shortcode('framedeware_gallery_wall_3x3', function()
{
    ob_start();
    require('views/public_gallery_wall_3x3.php');
    return ob_get_clean();
});

add_shortcode('framedeware_gallery_wall_4x3', function()
{
    ob_start();
    require('views/public_gallery_wall_4x3.php');
    return ob_get_clean();
});

add_shortcode('framedeware_gallery_wall_stairway', function()
{
    ob_start();
    require('views/public_gallery_wall_stairway.php');
    return ob_get_clean();
});

// Register the menu (Admin)
add_action( 'admin_menu', 'framedware_plugin_menu_func' );
function framedware_plugin_menu_func()
{
    add_menu_page(
        'FramedWare',                   // Page title
        'FramedWare',                   // Menu title
        'manage_options',               // Minimum capability (manage_options is an easy way to target Admins)
        'framedware',                   // Menu slug
        'framedware_plugin_options',    // Callback that prints the markup
        plugin_dir_url( __FILE__ ) . 'assets/img/icon.png'
    );
}

// Admin Page
function framedware_plugin_options()
{
    require('views/admin.php');
}

function plugin_add_settings_link( $links )
{
    $settings_link = '<a href="admin.php?page=framedware">' . __( 'Settings' ) . '</a>';
    array_unshift( $links, $settings_link );
    return $links;
}
$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_" . $plugin, 'plugin_add_settings_link' );


register_activation_hook( __FILE__, 'framedware_create_db' );
function framedware_create_db()
{
    global $wpdb;
    $table_name = 'fware_woo';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (woo_consumer_key text, woo_consumer_secret text,  woo_category_id text) $charset_collate;";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

    $wpdb->insert(
        $table_name,
        [
            'woo_consumer_key' => null,
            'woo_consumer_secret' => null,
            'woo_category_id' => null,
        ],
        ['%s', '%s', '%s']
    );
}

register_deactivation_hook( __FILE__, 'framedware_delete_db');
function framedware_delete_db()
{
    global $wpdb;
    $table_name = 'fware_woo';
    $wpdb->query('DROP TABLE ' . $table_name);
}

add_action( 'plugins_loaded', 'framedware_override' );
function framedware_override()
{
    global $wpdb;
    $table_name = 'fware_woo';

    $woo_consumer_key = $wpdb->get_var( 'SELECT woo_consumer_key FROM ' . $table_name );
    $woo_consumer_secret = $wpdb->get_var( 'SELECT woo_consumer_secret FROM ' . $table_name );
    $woo_category_id = $wpdb->get_var( 'SELECT woo_category_id FROM ' . $table_name );

    define('PLUGIN_PATH', plugin_dir_path( __FILE__ ));
    define('PLUGIN_URL', plugin_dir_url( __FILE__ ));
    define('WOO_CONSUMER_KEY', $woo_consumer_key);
    define('WOO_CONSUMER_SECRET', $woo_consumer_secret);
    define('WOO_CATEGORY_ID', $woo_category_id);
}

/**
 * Store images from filestack to local filesystem
 */
add_action('wp_ajax_nopriv_wall__store_x', 'wall__store_x');
add_action('wp_ajax_wall__store_x', 'wall__store_x');
function wall__store_x()
{
    //var_dump($_POST); exit;
    $data = $_POST;

    $path = FRAMEDWARE_UPLOAD_PATH . $data['wall']['sku'] . '/';
    mkdir($path, 0755, true);

    //  remove existing image from local filesystem, if any
    if ($_POST['remove'] !== null) {
        @unlink($path . $data['wall']['item_selected'] . '_' . $data['remove']['filename']);
        @unlink($path . $data['wall']['item_selected'] . '_' . $data['remove']['thumb_filename']);
    }

    $file = $data['filestack']['filesUploaded'][0];
    // main image
    $f = file_get_contents($file['url']);
    file_put_contents($path . $data['wall']['item_selected'] . '_' . $file['filename'], $f);
    // thumbnail
    $t = file_get_contents($file['thumb']);
    file_put_contents($path . $data['wall']['item_selected'] . '_' . $file['thumb_filename'], $t);

    header('Access-Control-Allow-Origin: *');
    header("Content-Type: application/json", true);
    echo json_encode(['success' => '1']);
    wp_die();
    return;
}

/**
 * Wall add to cart
 */
add_action('wp_ajax_nopriv_wall__add_to_cart', 'wall__add_to_cart');
add_action('wp_ajax_wall__add_to_cart', 'wall__add_to_cart');
function wall__add_to_cart()
{
    //var_dump($_POST); exit;
    $wall = $_POST['wall'];

    $sku = $wall['sku'];
    $path = FRAMEDWARE_UPLOAD_PATH . $sku . '/';

    if ( ! empty($wall)) {
        $description = $wall['description'];
        $product = get_product_by_sku($sku);
        if ( ! $product) {
            // CREATE WOO PRODUCT
            $product = new WC_Product();
            $product->set_name($description . ' ' . $sku);
            $product->set_sku($sku);
            $product->set_description($description);
            $product->set_short_description($description);
            $product->set_regular_price($wall['price']); // from config.js
            $product->set_category_ids([WOO_CATEGORY_ID]);
            $product->save();

            // ATTACH PRODUCT IMAGE
            $cart_thumb = PLUGIN_PATH . '/assets/img/wall_cart_' . $wall['id'] . '.jpg';
            if (file_exists($cart_thumb)) {
                attach_product_thumbnail($product->get_id(), $cart_thumb, 0);
            }

            // ADD WOO PRODUCT TO THE CART
            WC()->cart->add_to_cart($product->get_id());
        }

        @copy(PLUGIN_PATH . '/assets/img/wall_cart_' . $wall['id'] . '.jpg', $path . 'wall_cart.jpg');
    }

    header('Access-Control-Allow-Origin: *');
    header("Content-Type: application/json", true);
    echo json_encode(['function' => 'wall__add_to_cart', 'success' => '1', 'wall' => $wall]);
    wp_die();
    return;
};

add_action('wp_ajax_framedware_db_insert_wc', 'framedware_db_insert_wc');
function framedware_db_insert_wc()
{
    $key = $_POST['consumerKey'];
    $secret = $_POST['consumerSecret'];

    try
    {
        global $wpdb;

        $site_url = get_site_url();
        
        $woocommerce = new WooCommerceClient(
            $site_url, // Your store URL
            $key, // Your consumer key
            $secret, // Your consumer secret,
            [
                'wp_api' => true, // Enable the WP REST API integration
                'version' => 'wc/v2', // WooCommerce WP REST API version
                'verify_ssl' => false,
                'timeout' => 1800,
                'query_string_auth' => true // Force Basic Authentication as query string true and using under HTTPS
            ]
        );
        
        //.//.//.//.//.//.//.//.//.//.//
        //.//.//.//.//.//.//.//.//.//.//
        // if woo product category already exists, read it
        $category_exists = false;
        $response = $woocommerce->get('products/categories');
        foreach($response as $category) {
            if ($category['name'] == 'Uploads') {
                $category_exists = true;
                $category_id = $category['id'];
            }
        }

        // if woo product category does not already exist, create it
        if ( ! $category_exists) {
            $data = [
                'name' => 'Uploads'
            ];
            $response = $woocommerce->post('products/categories', $data);
            $category_id = $response['id'];
        }
        //.//.//.//.//.//.//.//.//.//.//
        //.//.//.//.//.//.//.//.//.//.//

        $query = "UPDATE fware_woo SET woo_consumer_key = %s , woo_consumer_secret = %s, woo_category_id = %s";
        $wpdb->query($wpdb->prepare($query, $key, $secret, $category_id));
    }
    catch (Exception $e)
    {
        $error = 'Caught exception: ' . $e->getMessage() . ' on line: ' . $e->getLine();

        $error_message = $e->getMessage(); // Error message.
        $error_request = $e->getRequest(); // Last request data.
        $error_response = $e->getResponse(); // Last response data.

        error_log($error);

        header('Access-Control-Allow-Origin: *');
        header("Content-Type: application/json", true);
        echo json_encode(['success' => '0', 'msg' => $e->getMessage()]);
        wp_die();
        return;
    }

    header('Access-Control-Allow-Origin: *');
    header("Content-Type: application/json", true);
    echo json_encode(['success' => '1']);
    wp_die();
    return;
}

add_action('wp_ajax_framedware_db_delete_wc', 'framedware_db_delete_wc');
function framedware_db_delete_wc()
{
    global $wpdb;

    $query = "UPDATE fware_woo SET woo_consumer_key = null , woo_consumer_secret = null, woo_category_id = null";
    $wpdb->query($wpdb->prepare($query));

    header('Access-Control-Allow-Origin: *');
    header("Content-Type: application/json", true);
    echo json_encode(['success' => '1']);
    wp_die();
    return;
}

// Register Style (Admin)
add_action( 'admin_enqueue_scripts', 'admin_custom_scripts' );
function admin_custom_scripts()
{
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'jquery-ui-datepicker', array( 'jquery' ) );

    wp_register_style('jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css');
    wp_enqueue_style( 'jquery-ui' );

    wp_register_style( 'framedware_admin_style', plugins_url('assets/css/admin.css', __FILE__), [], FRAMEDWARE_ORDER_PLUGIN_VERSION);
    wp_enqueue_style( 'framedware_admin_style' );

    wp_localize_script('framedware_ajax_script', 'framedwareWriteAjax', ['ajaxurl' => admin_url('admin-ajax.php')]);
    wp_enqueue_script('framedware_ajax_script');
}

add_action( 'wp_loaded', 'framedware_wp_loaded' );
function framedware_wp_loaded()
{
    if (is_admin()) // admin
    {
        wp_register_script('framedware_ajax_script', plugins_url('assets/js/admin.js', __FILE__), ['jquery'], FRAMEDWARE_ORDER_PLUGIN_VERSION);

        wp_localize_script('framedware_ajax_script', 'framedwareWriteAjax', ['ajaxurl' => admin_url('admin-ajax.php')]);

        wp_enqueue_script('jquery');
        wp_enqueue_script('framedware_ajax_script');
    }
}

/**
 * Delete Woocommerce product and its attachment images.
 * (internally deletes WP post)
 *
 * @param $product_id
 */
function woo_delete_product($product_id)
{
    global $wpdb;
    $arg = [
        'post_parent' => $product_id,
        'post_type'   => 'attachment',
        'numberposts' => -1,
        'post_status' => 'any'
    ];
    $children = get_children($arg);
    if($children) {
        foreach ($children as $attachment) {
            //echo $attachment->ID . "<br>";
            wp_delete_attachment($attachment->ID, true);
            $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE post_id = " . $attachment->ID);
            wp_delete_post($attachment->ID, true); // delete attachments
        }
    }
    wp_delete_post($product_id, true); // delete product
}

/**
 * Check if directory is empty
 *
 * @param $dir
 * @return bool|null
 */
function is_dir_empty($dir) {
    if ( ! is_readable($dir)) return null;
    return (count(scandir($dir)) == 2);
}

/**
 *  Recursively delete a directory and its entire contents (files + sub dirs).
 *
 * @param $dir
 */
function rrmdir($dir) {
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (is_dir($dir . DIRECTORY_SEPARATOR . $object) && ! is_link($dir ."/" . $object)) {
                    @rrmdir($dir . DIRECTORY_SEPARATOR . $object);
                }
                else {
                    @unlink($dir . DIRECTORY_SEPARATOR . $object);
                }
            }
        }
        @rmdir($dir);
    }
}

/**
 * Get array value if it exist, and append a separator.
 *
 * @param $value
 * @param string $separator
 * @return string
 */
function retrieve($value, $separator = '')
{
    $output = '';
    if (isset($value)) {
        $output .= $value . $separator;
    }
    return $output;
}

/**
 * Create order invoice.
 *
 * @param $e Order data array
 * @return false|string
 */
function create_invoice($e)
{
    $invoice = 'Invoice ' . $e['id'] . ' for order ' . $e['id'];

    $billing_address = '';
    $billing_address .= retrieve($e['billing']['first_name'], ' ');
    $billing_address .= retrieve($e['billing']['last_name'], "<br>\n");
    $billing_address .= retrieve($e['billing']['company'], "<br>\n");
    $billing_address .= retrieve($e['billing']['address_1'], ' ');
    $billing_address .= retrieve($e['billing']['address_2'], "<br>\n");
    $billing_address .= retrieve($e['billing']['city'], ', ');
    $billing_address .= retrieve($e['billing']['state'], ', ');
    $billing_address .= retrieve($e['billing']['postcode'], ', ');
    $billing_address .= retrieve($e['billing']['country'], "<br>\n");
    $billing_address .= retrieve($e['billing']['email'], "<br>\n");
    $billing_address .= retrieve($e['billing']['phone'], '');

    $shipping_address = '';
    $shipping_address .= retrieve($e['shipping']['first_name'], ' ');
    $shipping_address .= retrieve($e['shipping']['last_name'], "<br>\n");
    $shipping_address .= retrieve($e['shipping']['company'], "<br>\n");
    $shipping_address .= retrieve($e['shipping']['address_1'], ' ');
    $shipping_address .= retrieve($e['shipping']['address_2'], "<br>\n");
    $shipping_address .= retrieve($e['shipping']['city'], ', ');
    $shipping_address .= retrieve($e['shipping']['state'], ', ');
    $shipping_address .= retrieve($e['shipping']['postcode'], ', ');
    $shipping_address .= retrieve($e['shipping']['country'], '');

    $shipping_method = '';
    if (isset($e['shipping_lines']) && is_array($e['shipping_lines'])) {
        foreach ($e['shipping_lines'] as $line) {
            $shipping_method .= '<strong>' . retrieve($line['method_title'], "<br>\n") . '</strong>';
            if (isset($line['meta_data']) && is_array($line['meta_data'])) {
                foreach ($line['meta_data'] as $meta) {
                    if ($meta['key'] == '_pickup_location_name') {
                        $shipping_method .= $meta['value'] . "<br>\n";
                    }
                    if ($meta['key'] == '_pickup_location_address') {
                        if (isset($meta['value']) && is_array($meta['value'])) {
                            $shipping_method .= retrieve($meta['value']['address_1'], "<br>\n");
                            $shipping_method .= retrieve($meta['value']['address_2'], "<br>\n");
                            $shipping_method .= retrieve($meta['value']['city'], ', ');
                            $shipping_method .= retrieve($meta['value']['state'], ', ');
                            $shipping_method .= retrieve($meta['value']['postcode'], ', ');
                            $shipping_method .= retrieve($meta['value']['country'], '');
                        }
                    }
                }
            }
        }
    }

    $subtotal = 0;
    if (is_array($e['line_items'])) {
        foreach ($e['line_items'] as $item) {
            $subtotal += $item['subtotal'];
        }
    }

    ob_start();
    include PLUGINPATH . '/views/invoice.php';
    $output = ob_get_clean();

    return $output;
}

add_action('parse_request', 'my_custom_url_handler');
function my_custom_url_handler()
{
    $site_url = get_site_url();

    // CRONJOB ROUTE (MAINTENANCE TASKS)
    /*
    if (strpos($_SERVER["REQUEST_URI"], '/framedware/filestack/test') !== false)
    {
        $security = new FilestackSecurity(FRAMEDWARE_FILESTACK_SECRET);
        $filelink = new Filelink('eWK0NP66TN6HzevLKtOW', FRAMEDWARE_FILESTACK_API_KEY, $security);

        # delete remote file
        $filelink->delete();

        echo 'o o o';
        exit;
    }
    */

    // CRONJOB ROUTE (MAINTENANCE TASKS)
    /*
    if (strpos($_SERVER["REQUEST_URI"], '/framedware/filestack/test') !== false)
    {
        $security = new FilestackSecurity(FRAMEDWARE_FILESTACK_SECRET);
        $filelink = new Filelink('eWK0NP66TN6HzevLKtOW', FRAMEDWARE_FILESTACK_API_KEY, $security);

        # delete remote file
        $filelink->delete();

        echo 'o o o';
        exit;
    }
    */

    // LOCATION TEST
    /*
    if($_SERVER["REQUEST_URI"] == '/framedware/location/test')
    {
        $location = new WC_Local_Pickup_Plus_Pickup_Location(2683);
        $e = $location->get_email_recipients();
        var_dump($e);

        //
        $locations = new WC_Local_Pickup_Plus_Pickup_Locations();
        //var_dump($list = $locations->get_pickup_locations());
        $list = $locations->get_pickup_locations();
        //var_dump($list);

        foreach ($list as $id => $item) {
            //var_dump($id);
            $location = new WC_Local_Pickup_Plus_Pickup_Location($id);
            var_dump($location->get_name());
            var_dump($location->get_email_recipients());
        }

        exit;
    }
    */

    /*
     * Read all pickup locations and create order root folder (if it does not exist already)
     * Note: Depends on 'WooCommerce Local Pickup Plus' plugin
     */
    if($_SERVER["REQUEST_URI"] == '/framedware/location/prep')
    {
        $locations = new WC_Local_Pickup_Plus_Pickup_Locations();
        $list = $locations->get_pickup_locations();
        foreach ($list as $id => $item) {
            $order_path = ABSPATH . 'uploadhandler/orders/frameshops_store_' . $id . '/'; // ROOT
            if ( ! file_exists($order_path)) {
                $msg = 'Creating folder ' . $order_path;
                error_log($msg);
                echo $msg . "<br>\n";
                mkdir($order_path, 0755, true);
            }
        }
        exit;
    }

    // REPORT EXPORT AS .CSV [ALL]
    if(strpos($_SERVER["REQUEST_URI"], '/framedware/report/export/summary') !== false)
    {
        //$after = '2020-10-01'; // test
        //$before = '2020-10-24'; // test
        $after = $_GET['after'];
        $before = $_GET['before'];
        $orders = order_list($after, $before);

        $nini = [];
        foreach ($orders as $e) {
            $place = 'unknown';
            if (isset($e['shipping_lines']['0']['meta_data']) && ! empty(isset($e['shipping_lines']['0']['meta_data']))) {
                foreach ($e['shipping_lines']['0']['meta_data'] as $item) {
                    if (isset($item['key']) && $item['key'] == '_pickup_location_id') {
                        $place = $item['value'];
                    }
                }
            }
            $subtotal = 0;
            if (is_array($e['line_items'])) {
                foreach ($e['line_items'] as $item) {
                    $subtotal += $item['subtotal'];
                }
            }
            $nini[$place]['number_of_orders'] += 1;
            $nini[$place]['subtotal_sum'] += $subtotal;
            $nini[$place]['shipping_total_sum'] += $e['shipping_total'];
            $nini[$place]['tax_total_sum'] += $e['total_tax'];
            $nini[$place]['total_sum'] += $e['total'];
            $nini[$place]['currency'] = $e['currency'];
            $nini[$place]['currency_symbol'] = $e['currency_symbol'];
        }
        $i = 1;
        $output = [];
        $output[] = [
            '#',
            'Store ID',
            'Number of orders',
            'Subtotal Sum',
            'Shipping total Sum',
            'Tax total Sum',
            'Order total Sum',
        ];
        foreach ($nini as $place => $item) {
            $output[] = [
                $i . '.',
                $place,
                $item['number_of_orders'],
                number_format($item['subtotal_sum'], 2, '.', ''),
                number_format($item['shipping_total_sum'], 2, '.', ''),
                number_format($item['total_tax_sum'], 2, '.', ''),
                number_format($item['total_sum'], 2, '.', ''),
            ];
            $i++;
        }
        $output = getCSV($output);

        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename=report_summary_' . date('Y-m-d-h-i-s') . '.csv');
        header('Pragma: no-cache');
        echo $output;

        exit;
    }

    // REPORT EXPORT AS .CSV [ALL]
    if(strpos($_SERVER["REQUEST_URI"], '/framedware/report/export/all') !== false)
    {
        //$after = '2020-10-01'; // test
        //$before = '2020-10-24'; // test
        $after = $_GET['after'];
        $before = $_GET['before'];
        $orders = order_list($after, $before);

        $i = 1;
        $output = [];
        $output[] = [
            '#',
            'Store ID',
            'Order number',
            'Order date',
            'Subtotal',
            'Shipping total',
            'Tax total',
            'Order total',
        ];
        foreach ($orders as $e) {
            $place = 'unknown';
            if (isset($e['shipping_lines']['0']['meta_data']) && ! empty(isset($e['shipping_lines']['0']['meta_data']))) {
                foreach ($e['shipping_lines']['0']['meta_data'] as $item) {
                    if (isset($item['key']) && $item['key'] == '_pickup_location_id') {
                        $place = $item['value'];
                    }
                }
            }
            $date_created = new DateTime($e['date_created']);
            $subtotal = 0;
            if (is_array($e['line_items'])) {
                foreach ($e['line_items'] as $item) {
                    $subtotal += $item['subtotal'];
                }
            }
            $output[] = [
                $i . '.',
                $place,
                $e['number'],
                $date_created->format('Y-m-d'),
                number_format($subtotal, 2, '.', ''),
                $e['shipping_total'],
                $e['total_tax'],
                $e['total'],
            ];
            $i++;
        }
        $output = getCSV($output);

        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename=report_all_' . date('Y-m-d-h-i-s') . '.csv');
        header('Pragma: no-cache');
        echo $output;

        exit;
    }

    // WOOCOMMERCE WEBHOOK
    if($_SERVER["REQUEST_URI"] == '/framedware/woo-webhook-order-complete')
    {
        error_log('woo-webhook-order-complete fired');

        try
        {
            $use_zip = false;
            if (class_exists('ZipArchive')) { // use Zip if available on server
                $use_zip = true;
            }
            //$use_zip = false; // <-- force not to use zip

            $input = file_get_contents('php://input');
            //error_log($input);
            $e = json_decode($input, true);

            $place = 'unknown';
            if (isset($e['shipping_lines']['0']['meta_data']) && ! empty(isset($e['shipping_lines']['0']['meta_data']))) {
                foreach ($e['shipping_lines']['0']['meta_data'] as $item) {
                    if (isset($item['key']) && $item['key'] == '_pickup_location_id') {
                        $place = 'frameshops_store_' . $item['value'];
                    }
                }
            }

            // CREATE ORDER ITEMS
            $delete = []; // list of files to delete after zip operation
            if (is_array($e['line_items']))
            {
                $order_path = ABSPATH . 'uploadhandler/orders/' . $place . '/order_' . $e['id'] . '/';
                $order_url = $site_url . '/uploadhandler/orders/' . $place . '/order_' . $e['id'] . '/order_' . $e['id'] . '.zip';
                if ( ! file_exists($order_path)) {
                    mkdir($order_path, 0755, true);
                }

                $note = 'processing ...';

                foreach ($e['line_items'] as $item)
                {
                    $sku = $item['sku'];
                    $item_upload_url = $site_url . '/uploadhandler/uploads/';
                    $item_upload_path = ABSPATH . '/uploadhandler/uploads/' . $sku . '/';
                    $item_order_path = $order_path . $sku . '/';

                    if ( ! file_exists($item_order_path)) {
                        mkdir($item_order_path, 0755, true);
                    }

                    // COPY FILES AND FOLDERS FROM UPLOAD FOLDER TO ORDER FOLDER
                    $source = $item_upload_path;
                    $destination = $item_order_path;
                    $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST);
                    foreach ($iterator as $item) {
                        if ($item->isDir()) {
                            mkdir($destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
                        } else {
                            copy($item, $destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
                        }
                    }

                    // BUILD LIST OF FILES TO DELETE
                    if (class_exists('ZipArchive')) { // use Zip if available on server
                        $delete[] = $item_order_path;
                    }
                }
            }

            // CREATE ORDER INVOICE
            $invoice = @create_invoice($e);
            if ( ! empty($invoice)) {
                file_put_contents($order_path . 'invoice.html', $invoice);
            }

            // ZIP
            if ($use_zip)
            {
                $zip = new ZipArchive();
                $zip_file = 'order_' . $e['id'] . '.zip';
                $zip->open($order_path . '/' . $zip_file, ZipArchive::OVERWRITE | ZipArchive::CREATE);

                // Create recursive directory iterator
                $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($order_path), RecursiveIteratorIterator::LEAVES_ONLY);
                foreach ($iterator as $name => $file) {
                    // Skip directories (they would be added automatically)
                    if ( ! $file->isDir()) {
                        // Get real and relative path for current file
                        $filePath = $file->getRealPath();
                        $relativePath = substr($filePath, strlen($order_path));

                        // Add current file to archive
                        $zip->addFile($filePath, $relativePath);
                    }
                }
                // Zip archive will be created only after closing object
                $r = $zip->close();

                /**
                 * Current setup is to have both, zip and unziped file/folders, so DO NOT delete.
                 */
                // DELETE FOLDERS & FILES
                /*
                if ($r) {
                    foreach ($delete as $dir) {
                        $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
                        $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
                        foreach($files as $file) {
                            if ($file->isDir()) {
                                //rmdir($file->getRealPath());
                            }
                            else {
                                //error_log($file->getRealPath());
                                unlink($file->getRealPath());
                            }
                        }
                        unset($it, $files);
                        rmdir($dir);
                    }
                }
                */

                // Note
                $note = '<a href="' . $order_url . '">DOWNLOAD IMAGES</a>';
            }

            $site_url = get_site_url();

            // UPDATE WOOCOMMERCE ORDER NOTE
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
                'note' => $note,
            ];

            $woocommerce->post('orders/' . $e['id'] . '/notes', $data);
        }
        catch (Exception $e) {
            $error = 'Caught exception: ' . $e->getMessage() . ' on line: ' . $e->getLine();
            error_log($error);
        }

        exit();
    }

    // CRON JOB
    if (strpos($_SERVER["REQUEST_URI"], '/framedware/cron') !== false)
    {
        $msg = 'CRON SCRIPT START';
        error_log($msg);
        echo $msg . "<br>\n";

        try
        {
            /*
             * Call route to:
             * Read all pickup locations and create order root folder (if it does not exist already)
             */
            $msg = 'CRON call /framedware/location/prep';
            error_log($msg);
            echo $msg . "<br>\n";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $site_url . '/framedware/location/prep');
            curl_setopt($ch, CURLOPT_POST, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec($ch);
            curl_close ($ch);


            /*
             * CRITERIA 0
             * DELETE TRANSACTIONAL UPLOADS, THAT ARE OLDER THEN FRAMEDWARE_CRON_UPLOADS_DAYS
             */
            if (FRAMEDWARE_CRON_UPLOADS_DELETE == 1) {
                error_log('CRON FRAMEDWARE_CRON_UPLOADS_DAYS = ' . FRAMEDWARE_CRON_UPLOADS_DAYS);
                $i = 0;
                $j = 0;
                $now = new \DateTime('now', new DateTimeZone('America/New_York'));
                $upload_path = ABSPATH . 'uploadhandler/uploads/';
                $folders = glob($upload_path . '*');
                foreach($folders as $folder) {
                    if(is_dir($folder)) {
                        $pos = strpos($folder, 'image_assets');
                        if ($pos == false) { // not found
                            $folder_date = new DateTime();
                            $folder_date->setTimestamp(filectime($folder));
                            if($folder_date->diff($now)->days > FRAMEDWARE_CRON_UPLOADS_DAYS) { // number of days in the past
                                //echo $folder . "/  [" . $folder_date->format('Y-m-d h:i') . "] diff = " . $folder_date->diff($now)->days . "\n";
                                // MSG
                                $files = glob($folder . '/*');
                                foreach($files as $file) {
                                    if(is_file($file)) {
                                        $msg = 'CRON [CRITERIA 0] Deleting upload = ' . $file;
                                        error_log($msg);
                                        echo $msg . "<br>\n";
                                        $j++;
                                    }
                                }
                                // delete folder and all of its content
                                rrmdir($folder);
                            }
                        }
                    }
                    $j++;
                }
                //echo 'TOTAL FOLDERS: ' . $i . "\n";
                //echo 'TOTAL FILES: ' . $j . "\n";
                unset($i, $j);
            }

            if (FRAMEDWARE_CRON_PRODUCTS_DELETE == 1) {
                $msg = 'CRON FRAMEDWARE_CRON_PRODUCT_DAYS = ' . FRAMEDWARE_CRON_PRODUCT_DAYS;
                error_log($msg);
                echo $msg . "<br>\n";

                $url = get_site_url();
                $woocommerce = new WooCommerceClient(
                    $url, // Your store URL
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


                /*
                 * CRITERIA 1
                 * DELETE PRODUCTS USED IN ORDERS FOR ORDERS THAT ARE OLDER THAN SPECIFIED NUMBER OF DAYS
                 */

                // GET THE LIST OF ORDERS THAT ARE OLDER THAN FRAMEDWARE_CRON_PRODUCT_DAYS
                $order_list = [];
                $product_list = [];
                $parameters = [];

                $now = new \DateTime('now', new DateTimeZone('America/New_York'));
                $diff = $now->sub(new DateInterval('P400D'));
                $parameters['before'] = $diff->format('Y-m-d\TH:i:s'); // ISO8601 compliant date

                $orders = $woocommerce->get('orders', $parameters);
                if (is_array($orders)) {
                    foreach($orders as $order) {
                        //error_log(json_encode($order));
                        foreach($order['line_items'] as $line_item) {
                            if ($line_item['product_id'] != 0) { // product exists
                                $product_list[] = $line_item;
                                //echo "\t" . 'Product id = ' . $line_item['product_id'] . "\n";
                            }
                        }
                    }
                }
                //var_dump($order_list); exit;
                //var_dump($product_ids); exit;
                error_log('CRON [CRITERIA 1] ' . json_encode($order_list));
                error_log('CRON [CRITERIA 1] ' . json_encode($product_list));

                // DELETE PRODUCTS USED IN SELECTED ORDERS
                foreach ($product_list as $item) {
                    $msg = 'CRON [CRITERIA 1] Deleting product id = ' . $item['id'];
                    error_log($msg);
                    echo $msg . "<br>\n";
                    $upload_path = ABSPATH . 'uploadhandler/uploads/' . $item['sku'] . '/';
                    // 1. delete folder and all of its content
                    rrmdir($upload_path);
                    // 2. delete product and attachment images
                    woo_delete_product($item['id']); // delete product and attachment images
                }


                /*
                 * [CRITERIA 2]
                 * DELETE PRODUCTS FROM SPECIFIED CATEGORIES THAT ARE NOT USED IN ANY ORDER
                 * (TRANSACTIONAL PRODUCTS FROM ABANDONED CART)
                 */

                // GET THE LIST OF PRODUCTS USED IN ORDERS
                $orders = $woocommerce->get('orders', ['per_page' => 100]);
                //var_dump(count($orders)); exit;
                $products_used_ids = [];
                if (is_array($orders)) {
                    foreach($orders as $order) {
                        foreach($order['line_items'] as $line_item) {
                            //echo $line_item['product_id'] . '<br>';
                            if ($line_item['product_id'] != 0) { // product exists
                                $products_used_ids[] = $line_item['product_id'];
                            }
                        }
                    }
                }
                //error_log(json_encode($products_used_ids));
                //var_dump($products_used_ids); exit;

                // FIND PRODUCTS NOT USED IN ANY ORDER
                $products = $woocommerce->get('products', ['per_page' => 100, 'exclude' => $products_used_ids]);
                $products_not_used_ids = [];
                if (is_array($products)) {
                    foreach($products as $product) {
                        $products_not_used_ids[] = $product['id'];
                    }
                }
                //error_log(json_encode($products_not_used));
                //var_dump($products_not_used_ids); exit;

                // GET THE LIST OF PRODUCTS FROM SPECIFIED CATEGORIES
                $products_specific_categories_id_sku = [];
                $args = [
                    'limit' => -1,
                    'paginate' => false,
                    'category' => ['uploads', 'uncategorized'], // <--
                    'orderby'  => 'date_created',
                ];
                $products = wc_get_products($args);
                if (is_array($products)) {
                    foreach($products as $product) {
                        $products_specific_categories_id_sku[] = [
                            'id' => $product->get_id(),
                            'sku' => $product->get_sku(),
                        ];
                    }
                }
                //var_dump($products); exit;
                //var_dump($products_specific_categories_id_sku); exit;

                // GET THE LIST OF PRODUCTS FROM SPECIFIED CATEGORIES, NOT USED IN ANY ORDER
                $products_not_used_and_specific_categories_id_sku = [];
                foreach ($products_specific_categories_id_sku as $item) {
                    if (in_array($item['id'], $products_not_used_ids)) {
                        $products_not_used_and_specific_categories_id_sku[] = $item;
                    }
                }
                //var_dump($products_not_used_and_specific_categories_id_sku); exit;

                // DELETE PRODUCTS FROM SPECIFIED CATEGORIES, NOT USED IN ANY ORDER
                foreach ($products_not_used_and_specific_categories_id_sku as $item) {
                    $msg = 'CRON [CRITERIA 2] Deleting product id = ' . $item['id'];
                    error_log($msg);
                    echo $msg . "<br>\n";
                    $upload_path = ABSPATH . 'uploadhandler/uploads/' . $item['sku'] . '/';
                    // 1. delete folder and all of its content
                    rrmdir($upload_path);
                    // 2. delete product and attachment images
                    woo_delete_product($item['id']); // delete product and attachment images
                }
            }
        }
        catch (Exception $e) {
            $error = 'Caught exception: ' . $e->getMessage() . ' on line: ' . $e->getLine();
            error_log($error);
        }

        $msg = 'CRON SCRIPT END';
        error_log($msg);
        echo $msg . "<br>\n";
        exit;
    }
}

add_filter( 'woocommerce_cart_item_name', 'product_details', 10, 3 );
function product_details( $product_name,  $cart_item,  $cart_item_key )
{
    $description = $cart_item['data']->get_description();

    $product_name = '<div>
	      <div><span class="cart-item-title">'.$product_name.'</span></div>
	      <div><span class="cart-item-description">'.$description.'</span></div>
	   </div>';

    return $product_name;
}

function framedware_wp_footer() {
    $page_title = get_the_title();
    if (strtolower($page_title) == 'uploader') {
        wp_register_script('framedware_wp_footer', plugin_dir_url(__FILE__). 'footer_uploader.js', ['jquery'], FRAMEDWARE_ORDER_PLUGIN_VERSION, false);
        wp_enqueue_script('framedware_wp_footer');
    } else if (strtolower($page_title) == 'configurator') {
        wp_register_script('framedware_wp_footer', plugin_dir_url(__FILE__). 'footer_configurator.js', ['jquery'], FRAMEDWARE_ORDER_PLUGIN_VERSION, false);
        wp_enqueue_script('framedware_wp_footer');
    }
    // GALLERY WALL
    wp_register_script('framedware_wp_footer', plugin_dir_url(__FILE__). 'gallery_wall.js', ['jquery'], FRAMEDWARE_ORDER_PLUGIN_VERSION, false);
    wp_enqueue_script('framedware_wp_footer');
}
add_action('wp_footer', 'framedware_wp_footer');


add_action('wp_ajax_nopriv_process_filestack', 'process_filestack');
add_action('wp_ajax_process_filestack', 'process_filestack');
function process_filestack() {
    global $wpdb;

    $data = $_POST['data'];

    error_log(json_encode($data));

    echo json_encode($data);
    die();
};

add_action( 'admin_menu', 'framedware_report_view_register_hidden_page' );
function framedware_report_view_register_hidden_page()
{
    add_submenu_page(
        'framedware',
        'Report',
        'Report',
        'manage_options',
        'framedware_report',
        'framedware_report_view_render_hidden_page'
    );
    # /wp-admin/admin.php?page=framedware_report
}

/**
 *  outputCSV creates a line of CSV and outputs it to browser
 */
function outputCSV($array)
{
    $fp = fopen('php://output', 'w'); // this file actually writes to php output
    //fputcsv($fp, $array);
    foreach ($array as $item) {
        if (count($item) < 10) { // add extra elements up to 40
            $item = array_merge($item, array_fill(count($item) + 1, 10 - count($item), ''));
        }
        fputcsv($fp, $item, ',', '""');
    }
    fclose($fp);
}

/**
 *  getCSV creates a line of CSV and returns it.
 */
function getCSV($array)
{
    ob_start(); // buffer the output ...
    outputCSV($array);
    return ob_get_clean(); // ... then return it as a string!
}

/**
 * Filter orders, and return the list.
 *
 * @param $after
 * @param $before
 * @return array
 */
function order_list($after, $before)
{
    $orders = [];
    $run = false;
    $parameters = [
        'per_page' => 100,
    ];
    if (isset($after)) {
        $parameters['after'] = $after . 'T00:00:00'; // ISO8601 compliant date
        $run = true;
    }
    if (isset($before)) {
        $parameters['before'] = $before . 'T23:59:59'; // to ISO8601 compliant date
        $run = true;
    }
    if ($run) {
        $url = get_site_url();
        $woocommerce = new WooCommerceClient(
            $url, // Your store URL
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

        $orders = $woocommerce->get('orders', $parameters);
    }
    //echo $output;
    //var_dump(count($orders));
    //var_dump($orders);
    return $orders;
}

/**
 * Admin Report page view.
 */
function framedware_report_view_render_hidden_page()
{
    //$after = '2020-10-01'; // test
    //$before = '2020-10-24'; // test
    $after = $_GET['after'];
    $before = $_GET['before'];
    $orders = order_list($after, $before);

    // Create summary
    $nini = [];
    foreach ($orders as $e) {
        $place = 'unknown';
        if (isset($e['shipping_lines']['0']['meta_data']) && ! empty(isset($e['shipping_lines']['0']['meta_data']))) {
            foreach ($e['shipping_lines']['0']['meta_data'] as $item) {
                if (isset($item['key']) && $item['key'] == '_pickup_location_id') {
                    $place = $item['value'];
                }
            }
        }
        $subtotal = 0;
        if (is_array($e['line_items'])) {
            foreach ($e['line_items'] as $item) {
                $subtotal += $item['subtotal'];
            }
        }
        $nini[$place]['number_of_orders'] += 1;
        $nini[$place]['subtotal_sum'] += $subtotal;
        $nini[$place]['shipping_total_sum'] += $e['shipping_total'];
        $nini[$place]['tax_total_sum'] += $e['total_tax'];
        $nini[$place]['total_sum'] += $e['total'];
        $nini[$place]['currency'] = $e['currency'];
        $nini[$place]['currency_symbol'] = $e['currency_symbol'];
    }
    //var_dump($nini); exit;

    $file = plugin_dir_path( __FILE__ ) . "views/admin_report.php";
    if ( file_exists( $file ) ) {
        require $file;
    }
}

add_action( 'admin_head', function() {
    remove_submenu_page( 'framedware', 'framedware' );
    remove_submenu_page( 'framedware', 'framedware_report' );
});

/**
 * Get pickup location (store) details (name & email).
 *
 * @param $store_id
 * @return array|null
 */
function getPickupLocationDetails($store_id)
{
    $output = [];
    $location = new WC_Local_Pickup_Plus_Pickup_Location($store_id);
    if ($location) {
        $name = $location->get_name();
        $emails = $location->get_email_recipients();
        //var_dump($emails);
        //
        $output['name'] = $name;
        if (isset($emails[0])) {
            $output['email'] = $emails[0];
        }
        return $output;
    }
    return null;
}

/**
 * Format PayPal payout data.
 *
 * @param $input
 * @return array
 */
function payPalFormatPayoutData($recipients)
{
    // from documentation
    $example = '{
        "sender_batch_header": {
            "email_subject": "You have a payment",
            "sender_batch_id": "batch-1604934282020"
        },
        "items": [
            {
                "recipient_type": "EMAIL",
                "amount": {
                    "value": "1.00",
                    "currency": "USD"
                },
                "receiver": "email@aol.com",
                "note": "Payouts sample transaction",
                "sender_item_id": "item-2-1604934282021"
            }
        ]
    }';

    $data = [];
    $data['sender_batch_header'] = [
        'email_subject' => 'You have a payment from Frameshops.com',
        'sender_batch_id' => 'frameshops-' . time(),
    ];
    foreach ($recipients as $store_id => $details) {
        $data['items'][] = [
            'recipient_type' => 'EMAIL',
            'amount' => [
                'value' => $details['amount'],
                'currency' => 'USD',
            ],
            'receiver' => $details['email'],
            'note' => 'Frameshops payout',
            'sender_item_id' => 'store-' . $store_id . '-' . time(),
        ];
    }
    $data = json_encode($data);
    return $data;
}

/**
 * PayPal get Access Token
 *
 * https://developer.paypal.com/docs/api/get-an-access-token-curl/
 */
function payPalGetAccessToken($client_id, $secret)
{
    $sandbox = '';
    if (PAYPAL_SANDBOX == true) {
        $sandbox = 'sandbox.';
    }
    $endpoint = 'https://api.' . $sandbox . 'paypal.com/v1/oauth2/token';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $endpoint);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Accept-Language: en_US',
    ]);
    curl_setopt($ch, CURLOPT_USERPWD, $client_id . ':' . $secret);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');

    // receive server response
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $server_output = curl_exec($ch);
    error_log($server_output);
    curl_close ($ch);

    //var_dump($server_output); // test
    $o = json_decode($server_output, true);
    $o['endpoint'] = $endpoint;
    //var_dump($o); // test

    return $o;
}

/**
 * PayPal Create Payout
 *
 * https://developer.paypal.com/docs/payouts/integrate/api-integration/#create-payout
 */
function payPalCreatePayout($access_token, $data)
{
    $sandbox = '';
    if (PAYPAL_SANDBOX == true) {
        $sandbox = 'sandbox.';
    }
    $endpoint = 'https://api-m.' . $sandbox . 'paypal.com/v1/payments/payouts';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $endpoint);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'accept: application/json',
        'authorization: Bearer ' . $access_token,
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    // receive server response
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $server_output = curl_exec($ch);
    error_log($server_output);
    curl_close($ch);

    //var_dump($server_output);
    $o = json_decode($server_output, true);
    $o['endpoint'] = $endpoint;
    //var_dump($o);

    return $o;
}

/**
 * Paypal get Payout Details
 *
 * https://developer.paypal.com/docs/payouts/integrate/api-integration/#show-payout-details
 */
function payPalGetPayoutDetails($access_token, $payout_batch_id)
{
    $sandbox = '';
    if (PAYPAL_SANDBOX == true) {
        $sandbox = 'sandbox.';
    }
    $endpoint = 'https://api.' . $sandbox . 'paypal.com/v1/payments/payouts/' . $payout_batch_id;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $endpoint);
    curl_setopt($ch, CURLOPT_POST, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'accept: application/json',
        'authorization: Bearer ' . $access_token,
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $server_output = curl_exec($ch);
    error_log($server_output);
    curl_close($ch);

    //var_dump($server_output);
    $o = json_decode($server_output, true);
    $o['endpoint'] = $endpoint;
    //var_dump($o);

    return $o;
}

// TODO: validate input (email, etc.)

/**
 * Send PayPal payout
 * AJAX
 */
add_action('wp_ajax_paypal_payout_send', 'paypal_payout_send');
function paypal_payout_send()
{
    if (is_admin()) {
        parse_str($_POST['data'], $data);
        //var_dump($data); return;

        $response = payPalGetAccessToken($data['client_id'], $data['secret']); // test
        error_log(json_encode($response));
        //var_dump($response);

        $access_token = null;
        $html = 'Request failed.'; $status = 'error';
        if (isset($response['error_description'])) {
            $html = $response['error_description'] . '.'; $status = 'error';
        } else if (isset($response['access_token'])) {
            $access_token = $response['access_token'];
            //var_dump($access_token);
            $recipients = [];
            foreach ($data as $key => $array) {//
                if (strpos($key, 'store_') !== false) {
                    $store_id = (int) str_replace('store_', '', $key);
                    if (is_int($store_id)) {
                        $recipients[$store_id] = [
                            'email' => $array['email'],
                            'amount' => $array['amount'],
                        ];
                    }
                }
            }
            //var_dump($recipients);
            error_log(json_encode($recipients));

            $payout_data = payPalFormatPayoutData($recipients);
            error_log($payout_data);
            //var_dump($payout_data);

            $response = payPalCreatePayout($access_token, $payout_data);
            error_log(json_encode($response));
            //var_dump($response);

            if (isset($response['batch_header']['payout_batch_id'])) {
                $html = 'Payout sent.'; $status = 'success';
            }
            if (isset($response['name'])) {
                $html = str_replace('_', ' ', $response['name']) . '.'; $status = 'error';
            }
        }

        $html = '<div id="form-response-html-message" class="form-response-' . $status .'">' . $html . '</div><div id="form-response-html-details">' . json_encode($response) . '</div>';

        header('Access-Control-Allow-Origin: *');
        header("Content-Type: application/json", true);
        echo json_encode(['success' => '1', 'html' => $html]);
        wp_die();
        return;
    }
}