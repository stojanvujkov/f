<?php
/**
 * Created by PhpStorm.
 * User: ferdware
 * Date: 11/11/18
 * Time: 8:50 AM
 */

$wc_frame_product_id = 83;


global $uploadServer;
$uploadServer = 'https://bestframing.ferdware.net';
$mode = 'DEV';
$domain = $_SERVER['HTTP_HOST'];


$template_productJSON = array(
	'item_guid' => '',
    "frame_width" => 36,
    'mb1_width' => 144,


);

// generic FrameProductJSON
$FrameProductJSON = array(
    "frame_border_img"=> "bw59966-1.5in-splice.png",
    "frame_guid" => "bdeede50-c86a-4fe2-9531-a3586ec6626f",
    "frame_width" => 36,
    "frame_height" => 0,
    "frame_size_width" => 0,
    "frame_size_height" => 0,
    "frame_description" => "Black",
    "full_path_img_scaled_filename" => '',
    "glass_type" => "None",
    "height" => 0,
    'id' => '',
    'img_guid' => '',
    'img_scaled_width' => 0,
    'img_scaled_height' => 0,
    'img_width' => 0,
    'img_height' => 0,
    'img_aspect_ratio' => 0,
    'img_scale_factor' => 1,
    'img_scaled_filename' => '',
    'img_original_filename' =>  '',
    'img_screen_scale_factor_width' => 1,
    'img_screen_scale_factor_height' => 1,
    'img_filename' => '',
    'img_orientation' => null,
    'img_max_screen_size' => 300,
    'item_guid' => '',
    'item_category' => '',
    'innerMBWidth' => 0,
    'innerMBEnabled' => false,
    'innerMBColor' => 'none',
    'innerMBColorName' => 'none',
    'innerMBprice' => 0,
    'invisible_glass' => 0,
    'invisible_glass_price' => 0,
    'mb1_enabled' => '',
    'mb1_color' => '#ffffff',
    'mb1_color_name' => 'White',
    'mb1_size' => 0,
    'mb1_width' => 144,
    'mb1_width_text' => '2 in',
    'mounting' => 0,
    'mounting_description' => 'No Mounting',
    'print_price' => 0,
    'printSizes' => '',
    'print_width' => 0,
    'print_height' => 0,
    'pricingJSON' => '',
    'pricing_grid_mode' => 'custom_custom',
    'product_mode' => '',
    'scaled_filename' => '',
    'set_inside_frame_width' => 0,
    'set_inside_frame_height' => 0,
    'set_inside_top_mb_frame_height' => '',
    'set_inside_top_mb_frame_width' => '',
    'user_guid' => '',
    'width' => 0,
    'framesJSON' => '',
    'type' => 'custom',
    'wall' => (object) null,
);

$print_object_json = "{
  'height': 0,
  'width': 0,
  'price_category': 0,
}";

$pricing_json = "{
  'total_price': 0,
  'frame_price': 0,
  'print_price': 0,
  'mb_price': 0,
  'inner_mb_price': 0,
  'glass_price': 0,
  'mounting_price': 0,
  'print_sqFt': 0,
}";

$frame_details = "{
  'id': 1,
  'frame_guid': 'd1dc170d-4eb0-4718-91e8-0600d1651201',
  'frame_name': 'bw220048',
  'frame_border_img': 'bw26042-1.25in-splice.png',
  'frame_width': 54,
  'frame_border_slice': 'bw26042-1.25in-splice.png',
  'frame_sku': 'bw220048',
  'frame_description': 'Black Satin',
  'frame_size_inches': 0.75,
  'frame_weight': 1,
  'frame_preview_img': 'bw220048-FrontStraight.png',
  'frame_select_img': 'bw220048-preview.png',
  'frame_status': 1,
  'frame_cost': 1,
  'frame_price': 0.50,
}";


$order_request_detail_translate_keys = array(
	'or_arch_orderedby_name' => 'Ordered by',
	'or_arch_orderedby_company' => 'Ordered by Company',
	'or_arch_orderedby_phone' => 'Ordered by phone',
	'or_arch_orderedby_email' => 'Ordered by email',
	'or_arch_project_name' => 'Project Name',
	'or_arch_project_po' => 'PO Number',
	'or_arch_bill_third_party' => 'Bill to third party',
	'or_arch_bill_third_party_name' => 'Third party name',
	'or_arch_bill_third_party_company' => 'Third party company',
	'or_arch_job_color_option' => 'B&W or Color',
	'or_arch_qty' => 'Qty',
	'or_arch_num_each_set' => 'Number of sets',
	'or_arch_paper_size' => 'Paper Size',
	'or_arch_binding_options' => 'Binding Options',
	'or_arch_stock_type' => 'Paper',
	'or_arch_submission_type' => 'File Submission',
	'or_arch_scan_submission_method' => 'Drop off/Pickup Scans',
	'or_arch_pickup_scans_instructions' => 'Pickup Instructions',
	'or_arch_scan_type' => 'B & W or Color scan',
	'or_arch_email_scan_to' => 'Email scan recipient',
	'or_arch_ship_option' => 'Ship Method',
	'or_arch_due_date'  => 'Due Date',
	'or_arch_to_name' => 'Deliver to name',
   'or_arch_to_company' => 'Deliver to company',
   'or_arch_to_address' => 'Deliver to email',
   'or_arch_special_instructions' => 'Special Instructions',
);


?>