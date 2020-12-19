<?php

//require_once( PLUGINPATH.'/vendor/autoload.php' );
//use nwtn\Respimg as Respimg;

/*======================================================================================================================
|
|  Process the uploaded image and return the JSON to be able to render the frame preview
|
|       - user_guid
|       - order_guid
|       - cart_item_guid
|       - img_uuid
|       - img_filename
|       - cart_item_guid
|       - productMode
|       - image_width
|       - image_height
|       - orig_image_width
|       - orig_image_height
|       - image_max_screen_size
|       - img_object
|
+=====================================================================================================================*/
function process_upload_image( $tmp_post ){
	global $wpdb, $item_guid;

	$_tmp_array = array(); // initialize the array

	$_tmp_array['$image_path_prefix'] = '/uploadhandler/uploads/' . $tmp_post['image_guid'] .'/' ; // this is the path where the uploaded files are stored

	// define the max frame width and height in inches
	$_max_print_sizes = get_max_print_params();
	$max_frame_longest_size = $_max_print_sizes['max_print_longest_side'];
	$max_frame_shortest_size = $_max_print_sizes['max_print_shortest_side'];

	if( $tmp_post['orig_image_width'] > $tmp_post['orig_image_height']) { // is this landscape or portrait?

		$_tmp_array['screen_scale_factor_height'] = 0;

		// >>>>>>>>>>> LANDSCAPE
		$_tmp_array['orientation'] = 'landscape';

		// get the aspect calc value
		$_tmp_array['img_aspect_ratio'] = round( $tmp_post['orig_image_height'] / $tmp_post['orig_image_width'], 3, PHP_ROUND_HALF_UP );

		if ( $tmp_post['orig_image_width'] > $max_frame_longest_size ) { // is the width is too big

			// LONGEST SIZE - WIDTH TOO BIG

			$_shrink_to_fit_max_size_factor = $max_frame_longest_size / $tmp_post['orig_image_width']; // get the scale to shrink the size to the max size

			$_tmp_array['image_width'] = round( $tmp_post['orig_image_width'] * $_shrink_to_fit_max_size_factor, 0, PHP_ROUND_HALF_UP ); // set the new max width

			$_tmp_array['image_height'] = round( $tmp_post['orig_image_height'] * $_shrink_to_fit_max_size_factor, 0, PHP_ROUND_HALF_UP ); // set the new max height

			$_tmp_array['screen_scale_factor_width'] = round( $tmp_post['image_max_screen_size'] / $_tmp_array['image_width'], 5, PHP_ROUND_HALF_UP);

			$_tmp_array['img_scale_factor'] = $_shrink_to_fit_max_size_factor;

			// check the new scaled height to see that it's not too big after the width is adjusted
			if ( $_tmp_array['image_height'] > $max_frame_shortest_size ) { // height is good after width is shrunk down

				// recalc again to adjust the height to fit within the params

				$_shrink_to_fit_max_size_factor = $max_frame_shortest_size / $_tmp_array['image_height'];

				$_tmp_array['image_height'] = round( $tmp_post['image_height'] * $_shrink_to_fit_max_size_factor, 0, PHP_ROUND_HALF_UP ); // set the new max height

				$_tmp_array['image_width'] = round( $tmp_post['image_width'] * $_shrink_to_fit_max_size_factor, 0, PHP_ROUND_HALF_UP ); // set the new max width

				$_tmp_array['screen_scale_factor_width'] = round($tmp_post['image_max_screen_size'] / $_tmp_array['image_width'], 5, PHP_ROUND_HALF_UP);

				$_tmp_array['img_scale_factor'] = $_shrink_to_fit_max_size_factor;

			}
		} else {

			// LONGEST SIZE - WIDTH WITHIN LIMIT

			$_tmp_array['image_width'] = round( $tmp_post['orig_image_width'], 3, PHP_ROUND_HALF_UP );

			$_tmp_array['image_height'] = round( $tmp_post['orig_image_height'], 3, PHP_ROUND_HALF_UP );

			$_tmp_array['screen_scale_factor_width'] = round($tmp_post['image_max_screen_size'] / $_tmp_array['image_width'], 5, PHP_ROUND_HALF_UP);

			$_tmp_array['img_scale_factor'] = 1;

			// check the new scaled height to see that it's not too big after the width is adjusted
			if ( $_tmp_array['image_height'] > $max_frame_shortest_size ) { // height is good after width is shrunk down

				// recalc again to adjust the height to fit within the params

				$_shrink_to_fit_max_size_factor = $max_frame_shortest_size / $_tmp_array['image_height'];

				$_tmp_array['image_height'] = round( $tmp_post['orig_image_height'] * $_shrink_to_fit_max_size_factor, 0, PHP_ROUND_HALF_UP ); // set the new max height

				$_tmp_array['image_width'] = round( $tmp_post['orig_image_width'] * $_shrink_to_fit_max_size_factor, 0, PHP_ROUND_HALF_UP ); // set the new max width

				$_tmp_array['screen_scale_factor_width'] = round($tmp_post['image_max_screen_size'] / $_tmp_array['image_width'], 5, PHP_ROUND_HALF_UP);

				$_tmp_array['img_scale_factor'] = $_shrink_to_fit_max_size_factor;

			}
		}

	} else {

		//^^^^^^^^^^^^ PORTRAIT
		$_tmp_array['orientation'] = 'portrait';

		$_tmp_array['screen_scale_factor_width'] = 0;

		$_tmp_array['img_aspect_ratio'] = round( $tmp_post['orig_image_width'] / $tmp_post['orig_image_height'], 3, PHP_ROUND_HALF_UP );

		if ( $tmp_post['orig_image_height'] > $max_frame_longest_size ) { // is the height is too big

			// LONGEST SIZE - HEIGHT TOO BIG

			$_shrink_to_fit_max_size_factor = $max_frame_longest_size / $tmp_post['orig_image_height']; // get the scale to shrink the size to the max size

			$_tmp_array['image_height'] = round( $tmp_post['orig_image_height'] * $_shrink_to_fit_max_size_factor, 0, PHP_ROUND_HALF_UP ); // set the new max height

			$_tmp_array['image_width'] = round( $tmp_post['orig_image_width'] * $_shrink_to_fit_max_size_factor, 0, PHP_ROUND_HALF_UP ); // set the new max width

			$_tmp_array['screen_scale_factor_height'] = round($tmp_post['image_max_screen_size'] / $_tmp_array['image_height'], 5, PHP_ROUND_HALF_UP);

			$_tmp_array['img_scale_factor'] = $_shrink_to_fit_max_size_factor;

			// check the new scaled width to see that it's not too big after the height is adjusted
			if ( $_tmp_array['image_width'] > $max_frame_shortest_size ) { // height is good after width is shrunk down

				// recalc again to adjust the width to fit within the params

				$_shrink_to_fit_max_size_factor = $max_frame_shortest_size / $_tmp_array['image_width'];

				$_tmp_array['image_width'] = round( $tmp_post['image_width'] * $_shrink_to_fit_max_size_factor, 0, PHP_ROUND_HALF_UP ); // set the new max width

				$_tmp_array['image_height'] = round( $tmp_post['image_height'] * $_shrink_to_fit_max_size_factor, 0, PHP_ROUND_HALF_UP ); // set the new max height

				$_tmp_array['screen_scale_factor_height'] =round($tmp_post['image_max_screen_size'] / $_tmp_array['image_width'], 5, PHP_ROUND_HALF_UP);

				$_tmp_array['img_scale_factor'] = $_shrink_to_fit_max_size_factor;
			}
		} else {

			// LONGEST SIZE - HEIGHT WITHIN LIMIT

			$_tmp_array['image_height'] = round( $tmp_post['orig_image_height'], 3, PHP_ROUND_HALF_UP );

			$_tmp_array['image_width'] = round( $tmp_post['orig_image_width'], 3, PHP_ROUND_HALF_UP );

			$_tmp_array['screen_scale_factor_height'] = round($tmp_post['image_max_screen_size'] / $_tmp_array['image_height'], 5, PHP_ROUND_HALF_UP);

			$_tmp_array['img_scale_factor'] = 1;

			// check the new scaled width to see that it's not too big after the height is adjusted
			if ( $_tmp_array['image_width'] > $max_frame_shortest_size ) { // height is good after width is shrunk down

				// recalc again to adjust the width to fit within the params

				$_shrink_to_fit_max_size_factor = $max_frame_shortest_size / $_tmp_array['image_width'];

				$_tmp_array['image_width'] = round( $tmp_post['image_width'] * $_shrink_to_fit_max_size_factor, 3, PHP_ROUND_HALF_UP ); // set the new max width

				$_tmp_array['image_height'] = round( $tmp_post['image_height'] * $_shrink_to_fit_max_size_factor, 3, PHP_ROUND_HALF_UP ); // set the new max height

				$_tmp_array['screen_scale_factor_height'] = round($tmp_post['image_max_screen_size'] / $_tmp_array['image_width'], 5, PHP_ROUND_HALF_UP);

				$_tmp_array['img_scale_factor'] = $_shrink_to_fit_max_size_factor;
			}
		}
	}

	$uploaded_file_extension = pathinfo( $tmp_post['image_filename'], PATHINFO_EXTENSION ); // this gets the file extension

	$extensionLen = strlen( $uploaded_file_extension ) + 1;

	$uploaded_base_filename = substr( $tmp_post['image_filename'], 0,strlen( $tmp_post['image_filename'] ) - $extensionLen);


	if($uploaded_file_extension == 'gif'){

		//$_tmp_array['scaled_filename'] = $uploaded_base_filename . ' (small).png';
		$_tmp_array['scaled_filename'] = $uploaded_base_filename . ' (small).jpg';

	} else if( $uploaded_file_extension == 'png' ){

		//$_tmp_array['scaled_filename'] = $uploaded_base_filename . ' (small).png';
		$_tmp_array['scaled_filename'] = $uploaded_base_filename . ' (small).jpg';

	} else if( $uploaded_file_extension == 'svg' ){

		//TBD

	} else if( $uploaded_file_extension == 'pdf' ){

		//TBD

	} else if( ($uploaded_file_extension == 'tif') || ($uploaded_file_extension == 'tiff') ){

		//TBD

	} else {

		$_tmp_array['scaled_filename'] = $uploaded_base_filename . ' (small).jpg';

	}

	/**-----------------------------------------------------------------------------------------------
	|
	|  these functions will add the image to the img_details table
	|
	/**----------------------------------------------------------------------------------------------*/

	// find any previous img_guids of this user with a status of 0 and set to 1.
	// this means that whatever image status was changed is now an orphan
	$query = "UPDATE fware_img_details SET status = 1 WHERE user_guid = %s AND status = 0";
	$wpdb->query( $wpdb->prepare( $query, $tmp_post['user_guid'] ));

	// save the uploaded file information to the img_details table
	$wpdb->insert( 'fware_img_details',
		array(
			'status'                    => 0,
			'img_guid'                  => $tmp_post['image_guid'],
			'user_guid'                 => $tmp_post['user_guid'],
			'img_width'                 => $_tmp_array['image_width'],
			'img_height'                => $_tmp_array['image_height'],
			'img_original_width'        => $tmp_post['orig_image_width'],
			'img_original_height'       => $tmp_post['orig_image_height'],
			'img_orientation'           => $_tmp_array['orientation'],
			'img_scale_factor'          => round($_tmp_array['img_scale_factor'],5, PHP_ROUND_HALF_UP ),
			'img_scaled_filename'       => $_tmp_array['scaled_filename'],
			'img_original_filename'     => $tmp_post['image_filename'],
			'img_aspect_ratio'          => round($_tmp_array['img_aspect_ratio'], 5, PHP_ROUND_HALF_UP ),
			'img_screen_scale_factor_width'   =>  $_tmp_array['screen_scale_factor_width'],
			'img_screen_scale_factor_height'  =>  $_tmp_array['screen_scale_factor_height'],

		), array( '%d', '%s', '%s', '%d', '%d', '%d', '%d', '%s', '%f', '%s', '%s', '%f', '%f', '%f')
	);

	if ($wpdb->last_error) {
		plg('DATABASE ERROR =='.$wpdb->last_error,1);
	} else {
		plg('#### NO DB ERRORS -> inert record in fware_img_details',1);
	}

	/**-----------------------------------------------------------------------------------------------
	|
	|  these functions will add or update the img_guid in the cart_items table
	|
	/**----------------------------------------------------------------------------------------------*/

	// check if there is an existing cart_item

	if ($tmp_post['item_guid']) {

		$_tmp_array['item_guid'] = $tmp_post['item_guid'];

		$update_query = "UPDATE fware_cart_items SET img_guid = %s WHERE item_guid = %s";
		$wpdb->query( $wpdb->prepare( $update_query, $tmp_post['image_guid'], $tmp_post['item_guid'] ) );

		$wpdb->insert( 'fware_img_objects',
			array(
				'img_object_guid' => UUID::v4(),
				'img_guid'        => $tmp_post['image_guid'],
				'user_guid'       => $tmp_post['user_guid'],
				'item_guid'       => $_tmp_array['item_guid'],
			),
			array( '%s', '%s', '%s', '%s' )
		);

	} else {

		/**-----------------------------------------------------------------------------------------------
		 * |
		 * |  If there is no current cart_items record then create a new one
		 * |
		 * /**----------------------------------------------------------------------------------------------*/

		$item_guid = UUID::v4(); // create a new item guid - adding and not updating

		$_tmp_array['item_guid'] = $item_guid;

		$wpdb->insert( 'fware_cart_items',
			array(
				'product_type' => $tmp_post['productMode'],
				'item_guid'    => $_tmp_array['item_guid'],
				'order_guid'   => $tmp_post['order_guid'],
				'user_guid'    => $tmp_post['user_guid'],
				'img_guid'     => $tmp_post['image_guid'],
				'item_qty'     => $tmp_post['qty'],
				'item_status'  => 0,
				'created'      => date( "Y/m/d" ) . " - " . date( "h:i:sa" ),
			),
			array( '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%s' )
		);

		if ( $wpdb->last_error ) {
			plg( 'DATABASE ERROR ==' . $wpdb->last_error, 1 );
		} else {
			plg( '#### NO DB ERRORS -> inert record in fware_cart_items', 1 );
		}

		$wpdb->insert( 'fware_img_objects',
			array(
				'img_object_guid' => UUID::v4(),
				'img_guid'        => $tmp_post['image_guid'],
				'user_guid'       => $tmp_post['user_guid'],
				'item_guid'       => $_tmp_array['item_guid'],
			),
			array( '%s', '%s', '%s', '%s' )
		);

	}

	if ( $wpdb->last_error ) {
		plg( 'DATABASE ERROR ==' . $wpdb->last_error, 1 );
		return null;

	} else {
		plg( '#### NO DB ERRORS -> isnert record in fware_img_objects', 1 );

		// return the processed file information back to the calling request
		$image_size_arr['img_guid']                       = $tmp_post['image_guid'];
		$image_size_arr['item_guid']                      = $_tmp_array['item_guid'];
		$image_size_arr['img_orientation']                = $_tmp_array['orientation'];
		$image_size_arr['img_aspect_ratio']               = $_tmp_array['img_aspect_ratio'];
		$image_size_arr['img_scaled_height']              = $_tmp_array['image_height'];
		$image_size_arr['img_scaled_width']               = $_tmp_array['image_width'];
		$image_size_arr['img_width']                      = $_tmp_array['image_width'];
		$image_size_arr['img_height']                     = $_tmp_array['image_height'];
		$image_size_arr['img_scale_factor']               = $_tmp_array['img_scale_factor'];
		$image_size_arr['img_scaled_filename']            = stripslashes($tmp_post['image_guid'] . "/" . $_tmp_array['scaled_filename']);
		$image_size_arr['img_original_filename']          = $tmp_post['image_filename'];
		$image_size_arr['img_screen_scale_factor_width']  = $_tmp_array['screen_scale_factor_width'];
		$image_size_arr['img_screen_scale_factor_height'] = $_tmp_array['screen_scale_factor_height'];

		return json_encode( $image_size_arr );

		$wpdb->flush();

	}

} //function process_upload_image -> END

/*======================================================================================================================
|
|  Process the uploaded image and return the JSON to be able to render the frame preview
|  This library uses php-respimg to scale the image
|  details at: https://github.com/nwtn/php-respimg
|
|
|	>> Below taken from: https://www.smashingmagazine.com/2015/06/efficient-image-resizing-with-imagemagick/
|	PHP has ImageMagick integration called Imagick that makes it relatively easy to run ImageMagick
|  operations from within your PHP scripts. Unfortunately, Imagick is a bit limited and doesn’t let
|  you do some things that I recommend, like setting a resampling filter to be used with
|  the thumbnail function.
|
|	But, again, you’re in luck: I’ve created a composer package called php-respimg (packagist) that
|  handles everything described above. You can include it in your projects with Composer by running:
|  ( ex: composer require nwtn/php-respimg )
|
|	Imagick .. http://php.net/manual/en/book.imagick.php
|
|  Install the *Imagick.so* extension to Media Temple.
|  link .. https://gist.github.com/robbiegod/d384af2f1aca479e7115
|
|  make sure the entire /vendor/nwtn is uploaded
|
+=====================================================================================================================*/
function create_image_thumbnail( $orientation, $image_height, $image_width, $img_guid, $target_file ){

	plg('############ CREATE THUMBNAIL IMAGES #################',1);

	$uploaded_file_extension = pathinfo( $target_file, PATHINFO_EXTENSION ); // this gets the file extension

	$extensionLen = strlen( $uploaded_file_extension ) + 1;

	$uploaded_base_filename = substr( $target_file, 0,strlen( $target_file ) - $extensionLen);

	if($uploaded_file_extension == 'gif'){

		//$output_target_file = $uploaded_base_filename . ' (small).png';
		$output_target_file = $uploaded_base_filename . '-small.jpg';

	} else if( $uploaded_file_extension == 'png' ){

		//$output_target_file = $uploaded_base_filename . ' (small).png';
		$output_target_file = $uploaded_base_filename . '-small.jpg';

	} else if( $uploaded_file_extension == 'svg' ){

		//TBD

	} else if( $uploaded_file_extension == 'pdf' ){

		//TBD

	} else if( ($uploaded_file_extension == 'tif') || ($uploaded_file_extension == 'tiff') ){

		//TBD

	} else {

		$output_target_file = $uploaded_base_filename . '-small.jpg';

	}

	$full_path_target_file = $_SERVER["DOCUMENT_ROOT"].'/uploadhandler/uploads/'. $img_guid .'/'. $target_file;

	$image = new \nwtn\Respimg($full_path_target_file );

	if( $orientation == 'landscape' ){

		$image->smartResize(1000, 0, true);

	} else {

		$scaled_hight_factor = 1000 / $image_height;

		$dest_image_width = round($image_width * $scaled_hight_factor);

		$image->smartResize( $dest_image_width, 0, true);

	}

	$full_path_output_target_file = $_SERVER["DOCUMENT_ROOT"].'/uploadhandler/uploads/'. $img_guid .'/'. $output_target_file;

	$image->writeImage( $full_path_output_target_file );

	nwtn\Respimg::optimize( $full_path_output_target_file, 0, 1, 1, 1);

	return $output_target_file;

}

