
var wall = null;
var wall_id = null;

function file_picker_name() {
    //console.log(file_picker);
    const pickers = ['local', 'filestack'];
    if (pickers.includes(file_picker) == false) {
        return 'local';
    }
    return file_picker;
}

function getKeyByValue(object, value) {
    return Object.keys(object).find(key => object[key] === value);
}



function generateRandomString(length) {
    var result           = '';
    var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var charactersLength = characters.length;
    for ( var i = 0; i < length; i++ ) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    return result;
}



jQuery(document).ready( function()
{
    //console.log(myAjax); // TEST

    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const added = urlParams.get('added');
    if (added == '1') {
      $('#public_uploader').prepend('<div class="woocommerce-message x-alert x-alert-info x-alert-block" role="alert"><a href="' + myAjax.woocommerce_cart_url + '" tabindex="1" class="button wc-forward">View cart</a> Item has been added to your cart.</div>');
    }

    // SINGLE IMAGE, SHOW/HIDE CUSTOM FRAMING OPTION
    //console.log(ui);
    for (const [key, value] of Object.entries(ui)) {
        //console.log(`${key}: ${value}`);
        if (value == 0) {
            $('#framing_option_' + key).hide();
        }
    }

    // SINGLE IMAGE, FILESTACK FILE PICKER
    if (file_picker_name() == 'filestack' && $('#filestack_picker').length) {
        //ui
        $('#filestack_picker').show();
        $('#local_picker').hide();

        // file picker
        const client = filestack.init(filestack_api_key);
        const options = {
            fromSources: filestack_sources_regular,
            displayMode: 'inline',
            container: '#filestack_picker',
            maxFiles: 1,
            uploadInBackground: false,
            transformations: {
                crop: false,
                circle: false,
                rotate: false
            },
            //onUploadDone: (res) => console.log(res),
            onUploadDone: (res) => ula(res),
        };
        client.picker(options).open();
    }

    //  DEFAULT FOR INVISIBLE GLASS TOGGLE
    if (invisible_glass_on == 1) {
        if ($('#switch-invisible-glass').length) {
            if ($('#switch-invisible-glass').prop('checked') == false) {
                //console.log('click invisible glass checkbox');
                $("#switch-invisible-glass").trigger('click');
            }
        }
    }
});

/**
    Single image uploader
    Upload to FileStack
*/
function ula(data)
{
    //console.log(data);
    //console.log(data.filesUploaded[0].url);
    if (data.filesUploaded[0].url !== 'undefined') {
        select_file_to_upload_framed__filestack(data);
    } else {
        alert('Unable to load image from Filestack.');
    }
}

function get_cart_item(data)
{
    jQuery.ajax({
        url: myAjax.ajaxurl,
        type: 'POST',
        data:
            {
                'data': data,
                action: 'get_cart_item'  // <-- WP action
            },
        dataType: 'json',
        success: function (response) {
            console.log(response);
            window.localStorage.setItem('ProductJSON', response );
            location.href = myAjax.configurator_page;
        },
        error: function(xhr, status, error) {

        }
    });
}
/**==============================================================================
 ||
 ||  get the URL of the theme
 ||
 *==============================================================================*/
function __getHomeUrl() {
  var href = window.location.href;
  var index = href.indexOf('/wp-admin');
  var homeUrl = href.substring(0, index);
  return homeUrl;
}

/**==============================================================================
||
||  get the URL of the theme
||
*==============================================================================*/
function __getThemeUrl() {
  var href = window.location.href;
  var ThemeUrl = href + '/wp-content/themes/ferdware generic theme/';
  console.log( ThemeUrl )
  return ThemeUrl;
}

/**==============================================================================
||  this function is the comments function
*==============================================================================*/
function clg( $textToDisplay, $type ){

  if ($type) {
    console.log($textToDisplay)
  }
}

/**==============================================================================
||  this displays the waiting_modal
*==============================================================================*/
function __waiting_modal(){

   $.LoadingOverlay("show");

}


/**==============================================================================
||  this attaches an event to a function
*==============================================================================*/
function addEvent(element, eventName, fn) {
   if (element.addEventListener)
      element.addEventListener(eventName, fn, false);
   else if (element.attachEvent)
      element.attachEvent('on' + eventName, fn);
}

/**==============================================================================
||
||  get the current ProductJSON
||
*==============================================================================*/
async function getAjaxProductJSON(){ //

   var data = {
      action: 'ajax_get_product_json', // php: function get_product_json()
      data: {
         item_guid: myAjax.item_guid,
      }
   };

   await $.post( myAjax.do_ajax, data, function (results) {

      window.localStorage.setItem('ProductJSON', results);
      
   }).done(function(){

         return 'ok';
         
   })
}

/**==============================================================================
||
||  get the current ProductJSON
||
*==============================================================================*/
function getAjaxProductJSON_load_konva( $render_type ){ //

   // this protects the page from being loaded from the back arrow key
   // from the shopping cart
   if( !myAjax.item_guid ){
      clg('OOPSIE DOOPSIE',1)
      $.LoadingOverlay("hide");
      $('#invalid-item-guid').modal('show'); // 
      return false;
   }

   //. . . . . . . . . . . . . . . . . . . . . . .
   //. . . . . . . . Ajax Routine . . . . . . . ..
   //. . . . . . . . . . . . . . . . . . . . . . .
   var data = {
      action: 'ajax_get_product_json',
      data: {
         item_guid: myAjax.item_guid,
      }
   };

   $.post( myAjax.do_ajax, data, function (results) {

      if (results) {

         // store the retrieved results into the localstorage
         window.localStorage.setItem('ProductJSON', results);
         // convert the results to an object
         $_tmpProductJSON = JSON.parse(results);

         //. . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . .
         // load the framed Konva product on screen
         //. . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . .
         function load_framed () {
            return new Promise(resolve => {

               // todo - load the default frame product from the frame JSON

               // set only if the default productJSON is being used?
               if (!$_tmpProductJSON.frame_guid) {
                  $_tmpProductJSON.mb1_color = '#ffffff';
                  $_tmpProductJSON.mb1_width = 144;
                  $_tmpProductJSON.mb1_width_text = '2 in';
                  $_tmpProductJSON.frame_width = 54;
                  $_tmpProductJSON.frame_rabbet_width = 36;
                  $_tmpProductJSON.mb1_color_name = 'White';
                  $_tmpProductJSON.mb1_color = '#ffffff';
                  $_tmpProductJSON.frame_border_img = '10771000-splice.jpg';
                  $_tmpProductJSON.frame_description = 'Black';
                  $_tmpProductJSON.frame_guid = 'bdeede50-c86a-4fe2-9531-a3586ec6626f';
               }

               // calculate the print dimensions and add it to the productJSON
               $_ProductJSON = get_print_dimension($_tmpProductJSON);

               // display the product details on the screen
               $('#list-item-price').html(formatter.format($_tmpProductJSON.print_price));
               $('#list-item-outer-dimension').html($_tmpProductJSON.outer_dimension);
               $('#list-printed-item-image-size').html($_tmpProductJSON.print_dimension);
               $('#list-item-frame').html($_tmpProductJSON.frame_description);
               $('#list-item-top-mat').html('Matting: ' + $_tmpProductJSON.mb1_width_text);
               $('#print-file-name').html($_tmpProductJSON.img_original_filename);

               // display the product details on the screen
               $('#list-item-price-mobile').html(formatter.format($_tmpProductJSON.print_price));
               $('#list-item-outer-dimension-mobile').html($_tmpProductJSON.outer_dimension);
               $('#list-printed-item-image-size-mobile').html($_tmpProductJSON.print_dimension);
               $('#list-item-frame-mobile').html($_tmpProductJSON.frame_description);
               $('#list-item-top-mat-mobile').html( 'Matting: ' + $_tmpProductJSON.mb1_width_text);
               $('#print-file-name-mobile').html($_tmpProductJSON.img_original_filename);

               // set the product values to the buttons
               set_button_text($_tmpProductJSON);

               // retrieve the current frame selected and load the preview images
               set_current_frame_preview($_tmpProductJSON);

               // this routine creates the sizing buttons
               display_sizes_configurator($_tmpProductJSON);

               if ($_tmpProductJSON.innerMBEnabled == 1) {
                  $("#switch-bottom").prop('checked', true); // set the bottom mat switch to on
               }

                if( $_ProductJSON.invisible_glass == '1' ){
                    $("#switch-invisible-glass").prop('checked', true);
                    console.log('invisible-glass UPDATED');
                }
                final_price();

                // hide matting option on express framing type
                if ($_ProductJSON.type == 'express') {
                    $("#matting-options").hide();
                }

               resolve('ok')
            });
         }

         function load_default_size() {
            return new Promise(resolve => {

               if( $_tmpProductJSON.print_price == 0 ){

                  $printSizesObj = $_tmpProductJSON.printSizes;

                  $printSizesObj_size = Object.keys($printSizesObj).length - 1

                  $default_printSize = $printSizesObj[$printSizesObj_size];

                  set_config_frame_size(
                     $default_printSize.width,
                     $default_printSize.height,
                     $default_printSize.print_width,
                     $default_printSize.print_height,
                     JSON.stringify($default_printSize),
                     'configurator'
                  )
               } else {

                  if( detector.mobile() ){
                     $( document.getElementById('mobile-pricing') ).html( '$' + $_tmpProductJSON.print_price )
                     $( document.getElementById('mobile-size-optons') ).html( $_tmpProductJSON.outer_dimension )
                     $( document.getElementById('mobile-frame-description') ).html( $_tmpProductJSON.frame_description )
                     if( $_tmpProductJSON.mb1_width == 0 ){
                        $( document.getElementById('mobile-matting') ).html( 'No Matting' )
                     } else {
                        $( document.getElementById('mobile-matting') ).html( '2 in' )
                     }
                  }
               }
               clg('######## LOAD DEFAULT SIZE ########',1);

               resolve('ok')
            });
         }

         function set_onload_product_options() {
            return new Promise(resolve => {

               resolve('ok')
            });
         }

         // returns a promise
         async function wrapperFunc() {
            try {
               let r1 = await load_framed();
               let r2 = await load_default_size();
               let r3 = await set_onload_product_options();
               // now process r2
               return r3;     // this will be resolved value of the returned promise
            } catch(e) {
               console.log(e);
               throw e;      // let caller know the promise rejected with this reason
            }
         }

         wrapperFunc().then(result => {

            // display the sizes panel only in desktop mode on inital load
            if( $('#product-detail-mobile').hasClass('hide') ){
               $('#collapseOne').collapse('toggle')
            }

            if( detector.mobile()) {
               $('html').animate({scrollTop: 150}, 'slow');//IE, FF
               $('body').animate({scrollTop: 150}, 'slow');//chrome, don't know if Safari works
            }

         }).catch(err => {
            // got error
         });

        }

   }).always(function(){

      clg('############################ DONE',1)
      $.LoadingOverlay("hide");
      
   });

}

/**=========================================================
* load the Konva stage
*----------------------------------------------------------*/
function load_konva_stage( $_tmpProductJSON ){

   $('#file-uploaded').html( 'Uploaded File:'+ $_tmpProductJSON.img_scaled_filename );

   load_images( $_tmpProductJSON );

};

/**==============================================================================
||
||  this sets the frame preview parameters on page load
||
*==============================================================================*/
function set_current_frame_preview( $_tmpProductJSON ){

   var $tmp_frame_data = frame_list;

   $.each( $tmp_frame_data, function(i, v) {
      if ( $tmp_frame_data[i].frame_guid == $_tmpProductJSON.frame_guid ) {

         clg('FOUND =='+$tmp_frame_data[i].frame_guid,1);
         change_frame( $tmp_frame_data[i].id );

         return;
      } else {

         clg('NOT FOUND =='+ $tmp_frame_data[i].frame_guid, 1);

      }
   });

}

function hide_options_mobile( $mode ){

}


/**==============================================================================
||
||  get the current ProductJSON
||
*==============================================================================*/
function __getAjaxProductJSON_load_fb_konva(){ //

   var data = {
      action: 'ajax_get_product_json',
      data: {
         item_guid: myAjax.item_guid,
      }
   };

   $.post( myAjax.ajaxurl, data, function (results) {

      if (results) {

         window.localStorage.setItem( 'ProductJSON', results);

         $_tmpProductJSON = JSON.parse(results);

         load_images( $_tmpProductJSON );

         $_ProductJSON = get_print_dimension( $_tmpProductJSON );
         $('#list-item-price').html( formatter.format( $_tmpProductJSON.print_price ));
         //$('#list-item-outer-dimension').html( $_tmpProductJSON.outer_dimension );
         $('#list-item-outer-dimension').html( $_tmpProductJSON.print_dimension );
         $('#list-item-frame').html( $_tmpProductJSON.frame_description );
         $('#list-item-top-mat').html( $_tmpProductJSON.mb1_color_name );
         $('#print-file-name').html( $_tmpProductJSON.img_original_filename );

         // set the top mat flag to the setting in productJson on load
         if( $_tmpProductJSON.mb1_width > 0 ){
            $("#switch-top").prop('checked', true);
         } else {
            $("#switch-top").prop('checked', false);
         }

         // set the product values to the buttons
         set_button_text( $_tmpProductJSON );

         display_sizes_configurator( $_tmpProductJSON ); // this routine creates the sizing buttons

         $('#file-uploaded').html( 'Uploaded File:'+ $_tmpProductJSON.img_scaled_filename );

      } else {

      }
   })
}

/**==============================================================================
||
||  set the current ProductJSON
||
*==============================================================================*/
function saveAjaxProductJSON( $tmp_ProductJSON ){

   var data = {
      action: 'ajax_save_product_json',
      data:{
         product_json : JSON.stringify( $tmp_ProductJSON ),
         item_guid: $tmp_ProductJSON.item_guid
      }
   }

   $.post( myAjax.ajaxurl , data, function (results) { // submit the ajax request

      if(results == 'fail'){

         clg('AJAX REQUEST FAILED',1)

      } else {

         clg( 'Results from the AJAX request ==' + results, 1);

      }

   })
   .then(function(){

   })
   .done(function(){
      clg('saveAjaxProductJSON Ajax - done',1);
   })
   .fail(function(){
      clg('saveAjaxProductJSON Ajax - error',1);
   })
   .always(function(){
      clg('saveAjaxProductJSON Ajax - finished',1);
   });

}

/**==============================================================================
 ||
 ||  set the current ProductJSON
 ||
 *==============================================================================*/
function saveAjaxProductJSON_go_configurator( $tmp_ProductJSON ){

   var data = {
      action: 'ajax_save_product_json',
      data:{
         product_json : JSON.stringify( $tmp_ProductJSON ),
         item_guid: $tmp_ProductJSON.item_guid
      }
   }

   $.post( myAjax.ajaxurl , data, function (results) { // submit the ajax request

      if(results == 'fail'){

         clg('AJAX REQUEST FAILED',1)

      } else {

         clg( 'Results from the AJAX request ==' + results, 1);

         if( _productMode == 'custom' ){
            // goto the custom configurator page
            location.href = myAjax.configurator_page;

         } else {
            // goto the express configurator page
            location.href = myAjax.configurator_express;

         }
      }

   })
      .then(function(){

      })
      .done(function(){
         clg('saveAjaxProductJSON Ajax - done',1);
      })
      .fail(function(){
         clg('saveAjaxProductJSON Ajax - error',1);
      })
      .always(function(){
         clg('saveAjaxProductJSON Ajax - finished',1);
      });

}

/**==============================================================================
||
||  save the ProductJSON to localstorage as JSON, no slashes
||
*==============================================================================*/
function saveProductJsonToLS( $tmp_ProductJSON ){

   window.localStorage.removeItem('ProductJSON');

   $tmp_ProductJSON = JSON.stringify( $tmp_ProductJSON );

   window.localStorage.setItem('ProductJSON', $tmp_ProductJSON );

   clg('>>> saveProductJsonToLS <<<',1);

}

/**==============================================================================
||
||  get the current ProductJSON from localstorage and return as an object
||
*==============================================================================*/
function getLsProductJSON()
{
   var $tmp_product_json = window.localStorage.getItem('ProductJSON');

   if( $tmp_product_json ){

      return JSON.parse( $tmp_product_json ); // return existing fc_JSON

   } else {

      // todo if frame or product

      return $FrameProductJSON; // return the blank fc_JSON var template i fnone currently exists

   }

}

/*======================================================================================================================
|=======================================================================================================================
|
|  Configurator Functions
|
|=======================================================================================================================
+======================================================================================================================*/






/*======================================================================================================================
|=======================================================================================================================
|
|  Ajax Functions
|
|=======================================================================================================================
+======================================================================================================================*/

/**==============================================================================
||
||  Process image Ajax request
||
*==============================================================================*/
function process_image(){

   var data = {
      action: 'ajax_process_upload_image',
      // this is the data to be sent to the ajax routine
      data: {
         qty:                 _qty,
         user_guid:           myAjax.user_guid,
         order_guid:          myAjax.order_guid,
         item_guid:           myAjax.item_guid,
         image_filename:      _filename_selected_for_upload,
         image_guid:          $globalUUID,
         productMode:         _productMode,
         image_width:         _image_width,
         image_height:        _image_height,
         orig_image_width:    _orig_image_width,
         orig_image_height:   _orig_image_height,
         image_max_screen_size: _image_max_screen_size,
      }
   };

    $.post( myAjax.do_ajax , data, function (results) { // submit the ajax request

      if(results == 'fail'){

         clg('PROCESS IMAGE FAILED',1)

      } else {

         $globalUUID = null;

         $_tmpProductJSON = getLsProductJSON();
         $_tmpProductJSON.print_price = 0; // resets the print price so item is treated as new with new image
         
         var $_new_ProductJSON;

         // save the returned data to the fc_json
         $_new_ProductJSON = onFileUploadAllComplete(results, $_tmpProductJSON);

         saveAjaxProductJSON_go_configurator($_new_ProductJSON);

      }

    }).done(function(){
    });

}

/**==============================================================================
||
||  Process the image Ajax request into the fc_json
||
||  this function loads the values returned from the image_processing
||  Ajax request into the ProductJSON
||
*==============================================================================*/
function onFileUploadAllComplete( $results, $_tmpProductJSON ) {

   var $tmp_results = JSON.parse( $results );

   Object.keys( $tmp_results ).forEach(function(key) {

      $_tmpProductJSON[key] = $tmp_results[key]

   });

   return $_tmpProductJSON; // return the newly modified tmpProductJSON

};

/**==============================================================================
|
| This calcs the available print sizes - post process
|
*==============================================================================*/
function uploader_calcPrintSizes( $_tmpProductJSON )
{
   clg('MODE === ' + $_tmpProductJSON.pricing_grid_mode, 1);

   // const aspect_ratio = $_tmpProductJSON.img_aspect_ratio

   var $new_long_side,$new_short_side,$tmp_price_category;
   var $tmp_reference_long_side, $tmp_reference_short_side;
   var $long_side,$short_side;
   var counter = 0;
   var print_obj;
   var print_obj_arr = [];

   // get the orientation of the file
   if( $_tmpProductJSON.img_orientation == 'landscape' ) { // LANDSCAPE

      $long_side = $_tmpProductJSON.original_img_width;
      $short_side = $_tmpProductJSON.original_img_height;

      // get the aspect ratio of the file
      var $aspect_ratio = $short_side / $long_side;

      // actual calculations
      $tmp_print_width = parseFloat($long_side / 72);
      $tmp_print_height = parseFloat($short_side / 72);

      $tmp_reference_long_side = $tmp_print_width;
      $tmp_reference_short_side = $tmp_print_height;

      if( $_tmpProductJSON.pricing_grid_mode == 'custom_custom' ){ // <
         $_tmpProductJSON.img_aspect_ratio = $tmp_print_height / $tmp_print_width;
         $tmp_cropper_aspect_ratio = $_tmpProductJSON.img_aspect_ratio;
         cropper.setAspectRatio(0);
      } else { // express
         $_tmpProductJSON.img_aspect_ratio = $frame_aspect_ratio_settings[$_tmpProductJSON.pricing_grid_mode].portrait;
         $tmp_cropper_aspect_ratio = $_tmpProductJSON.img_aspect_ratio;
         cropper.setAspectRatio( $frame_aspect_ratio_settings[$_tmpProductJSON.pricing_grid_mode].landscape );
      }
   } else { // PORTRAIT

      // switch the width and height
      $long_side = $_tmpProductJSON.original_img_height;
      $short_side = $_tmpProductJSON.original_img_width;

      // actual calculations
      $tmp_print_height = parseFloat($long_side / 72);
      $tmp_print_width = parseFloat($short_side / 72);

      $tmp_reference_long_side = $tmp_print_height;
      $tmp_reference_short_side = $tmp_print_height;

      if( $_tmpProductJSON.pricing_grid_mode == 'custom_custom' ){ // <
         $_tmpProductJSON.img_aspect_ratio = $tmp_print_width / $tmp_print_height;
         $tmp_cropper_aspect_ratio = $_tmpProductJSON.img_aspect_ratio;
         cropper.setAspectRatio(0);
      } else {
         $_tmpProductJSON.img_aspect_ratio = $frame_aspect_ratio_settings[$_tmpProductJSON.pricing_grid_mode].portrait;
         $tmp_cropper_aspect_ratio = $_tmpProductJSON.img_aspect_ratio;
         cropper.setAspectRatio( $frame_aspect_ratio_settings[$_tmpProductJSON.pricing_grid_mode].portrait );
      }
   }

   // force cropper aspect orientation
   //console.log('$_tmpProductJSON.cropper_orientation = ' + $_tmpProductJSON.cropper_orientation);
   if ($_tmpProductJSON.cropper_orientation !== 'undefined') {
       cropper.setAspectRatio($frame_aspect_ratio_settings[$_tmpProductJSON.pricing_grid_mode][$_tmpProductJSON.cropper_orientation]);
   }

   clg('**************************************************',1);
   clg('PRINT WIDTH: ' + $tmp_print_width + '  PRINT HEIGHT: ' + $tmp_print_height + '  ASPECT RATIO: ' + $aspect_ratio, 1);
   clg('**************************************************',1);

   // this is the added width of the frame and the matboard
    $tmp_default_added_width = (( $_tmpProductJSON.frame_width * 2 ) + ( $_tmpProductJSON.mb1_width * 2 )) / 72;

    $frame_pricing_grid = $paper[$_tmpProductJSON.pricing_grid_mode];

    console.log('________________________>' + $_tmpProductJSON.pricing_grid_mode);
    console.log('________________________>' + $_tmpProductJSON.type);
    console.log($frame_pricing_grid);


   // cycle through all the pricing grid json items
   $.each($frame_pricing_grid, function(key, innerjson)
   {
       $tmp_price_category = key; // key is the price

       // get the long side of the current price grid item
       $tmp_grid_longside = innerjson.long_side;
       //console.log('long ______________________________>' + $tmp_grid_longside);

       // get the short side of the current price grid item
       $tmp_grid_shortside = innerjson.short_side;
       //console.log('short ______________________________>' + $tmp_grid_shortside);

       if ($_tmpProductJSON.type == 'express') { // EXPRESS FRAMING

           print_obj = set_printObj(innerjson.long_side, innerjson.short_side, innerjson.invisible_glass_price);
           print_obj_arr[counter] = print_obj;
           counter++

       } else { // CUSTOM FRAMING

           // check to see if the longest side is within than current pricing grid long side limit
           if( $tmp_reference_long_side > $tmp_grid_longside ) {

               // set the new long side to the current grid width limit
               $new_long_side = $tmp_grid_longside;

               // calculate a new short side based on the new long side set by the previous line
               $new_short_side = Math.round( $tmp_grid_longside * $tmp_cropper_aspect_ratio ).toFixed(0);

               // check to make sure that the new short side does not exceed the grid limit short side
               if( $new_short_side <= $tmp_grid_shortside ) {
                   // the short side is within limit

                   // now check to make sure the short side is not too small
                   if( $new_short_side >= $minimum_print_length ) {

                       // GOOD TO GO - save the current size
                       print_obj =  set_printObj( $new_long_side, $new_short_side, innerjson.invisible_glass_price );
                       print_obj_arr[counter] = print_obj;

                   } else {
                       return; // short side is too small, exit the loop
                   }
               } else { // at this point the short side is longer than the grid short side limit

                   $new_short_side = $tmp_grid_shortside; // set the short side to the grid short side limit

                   $new_long_side = $tmp_grid_shortside / $tmp_cropper_aspect_ratio; // calculate the new long side

                   // save the new sizes
                   print_obj =  set_printObj( $new_long_side, $new_short_side, innerjson.invisible_glass_price );
                   print_obj_arr[counter] = print_obj;
               }
           } else { // at this point the longest side exceeds the long side grid limit

               // now check to make sure the short side is within the grid limit
               if( $tmp_reference_short_side <= $tmp_grid_shortside ) { // short side within limit

                   // check to make sure the short side is not too short
                   if( $tmp_reference_short_side >= $minimum_print_length ) {

                       // convert from px to in
                       $new_long_side = $long_side / $default_min_print_res;
                       $new_short_side = $short_side / $default_min_print_res;

                       // the current original size is good. send the current size to the printObj
                       print_obj =  set_printObj( $new_long_side, $new_short_side, innerjson.invisible_glass_price );
                       print_obj_arr[counter] = print_obj;
                   } else {
                       return; // short side is too small, exit the loop
                   }
               } else { // short side NOT within limit

                   // set the short side within the grid short size limit
                   $new_short_side = $tmp_grid_shortside;
                   // get the new long side value
                   $new_long_side = $tmp_grid_shortside / $_tmpProductJSON.img_aspect_ratio;

                   // send the current sizes to the printObj
                   print_obj =  set_printObj( $new_long_side, $new_short_side, innerjson.invisible_glass_price );
                   print_obj_arr[counter] = print_obj;
               }
               return false; // exit loop - important
           }
           counter++
       }
   });

   if ( print_obj_arr === undefined || print_obj_arr.length == 0 ) {

      // array empty or does not exist
      clg(' THE IMAGE IS TOO SMALL ',1)

      $( document.getElementById('cropper-image-wrapper') ).addClass( 'hide' );
      $( document.getElementById('lowres-file-modal') ).modal('show');

   } else {

      // save the printsizes into printSizes field
      $_tmpProductJSON.printSizes = print_obj_arr ;

      if(typeof $_tmpProductJSON.pricing_grid_mode == 'undefined') {
         // set the initial print size
         $_tmpProductJSON = initPrintSize( print_obj_arr[ counter - 1 ], $_tmpProductJSON);
      }
   }

   //===================================================================================
   //  save the size to the printObj array
   //===================================================================================
   function set_printObj( $tmp_new_long_side, $tmp_new_short_side, $invisible_glass_price )
   {
      var tmp_print_obj;

      if( $_tmpProductJSON.img_orientation == 'landscape' ){
         tmp_print_obj = { // set as landscape
             width : parseInt($tmp_new_long_side),
             height : parseFloat((Math.round( $tmp_new_short_side * 4) / 4).toFixed(2)),
             print_width : parseInt($tmp_new_long_side),
             print_height : parseFloat((Math.round( $tmp_new_short_side * 4) / 4).toFixed(2)),
             outer_width : parseInt( $tmp_new_long_side + 5 ),
             outer_height : parseFloat(( Math.round( ($tmp_new_short_side * 4) / 4) + 5).toFixed(2)),
             price_category: $tmp_price_category,
             invisible_glass_price: $invisible_glass_price
         }
      } else {
         tmp_print_obj = { // set as portrait
             height : parseInt($tmp_new_long_side),
             width : parseFloat((Math.round($tmp_new_short_side * 4) / 4).toFixed(2)),
             print_height : parseInt($tmp_new_long_side),
             print_width : parseFloat((Math.round( $tmp_new_short_side * 4) / 4).toFixed(2)),
             outer_height : parseInt($tmp_new_long_side + 5),
             outer_width : parseFloat((Math.round(($tmp_new_short_side * 4 ) / 4) + 5).toFixed(2)),
             price_category: $tmp_price_category,
             invisible_glass_price: $invisible_glass_price
         }
      }

      return tmp_print_obj;
   }

   // test whether a value is an Int or not
   function isInt(value) {
      return !isNaN(value) &&
         parseInt(Number(value)) == value &&
         !isNaN(parseInt(value, 10));
   }

   return $_tmpProductJSON
};

/**==============================================================================
|
|  this calculates the abreviated sizes
|
*==============================================================================*/
function __calcQuicksizes( $print_sizes ){

   var currentPrice = 0;
   var i;
   var quickSizes = [];

   for ( i = 0; i < $print_sizes.length; i++ ) {
      if (i === 0){
         quickSizes.push($print_sizes[i]) // push the first item into the printSizesArray
         currentPrice = $print_sizes[i].price_category
      }
      else {
         if ( $print_sizes[i].price_category != currentPrice ){
            quickSizes.push($print_sizes[i]); // push the first item into the printSizesArray
            currentPrice = $print_sizes[i].price_category
         }
      }
   }

   clg( quickSizes,1);

   return quickSizes;

};

/**==============================================================================
|
| This changes the size of the print to the selected size
|
*==============================================================================*/
function initPrintSize( $default_print_size, $_ProductJSON ) {

   var tmp_new_screen_scale_factor, newWidth, newHeight;

   var _pricing_json = {
       frame_price: 0,
       glass_price: 0,
       inner_mb_price: 0,
       mb_price: 0,
       mounting_price: 0,
       print_price: 0,
       print_sqFt: 0,
       total_price: 0,
       invisible_glass_price: 0
   };

   $_ProductJSON.print_width = $default_print_size.print_width;  // in inches
   $_ProductJSON.print_height = $default_print_size.print_height; // in inches

   if( $_ProductJSON.img_orientation == 'landscape' ){

      // new sizes based on landscape

      newWidth = parseInt( $default_print_size.width * 72 );
      $_ProductJSON.width = newWidth; // in px
      $_ProductJSON.img_width = newWidth; // in px

      // compute the new height in px
      newHeight = Math.round( parseFloat( newWidth * $_ProductJSON.img_aspect_ratio ));
      $_ProductJSON.height = newHeight; // in px
      $_ProductJSON.img_height = newHeight; // in px

      // compute the screen scale factor
      tmp_new_screen_scale_factor = _MOBILE_MAX_SCREEN_WIDTH / newWidth;
   }
   else {

      // new sizes based on portrait

      newHeight  = parseInt( $default_print_size.height * 72 );
      $_ProductJSON.height = newHeight;
      $_ProductJSON.img_height = newHeight;

      // compute the new width in px
      newWidth = Math.round( parseFloat( newHeight * $_ProductJSON.img_aspect_ratio ));
      $_ProductJSON.width = newWidth;
      $_ProductJSON.img_width = newWidth;

      // compute the screen scale factor
      tmp_new_screen_scale_factor = _MOBILE_MAX_SCREEN_HEIGHT / newHeight;
   }

   // save the scale factor to the state
   $_ProductJSON.img_scale_factor   = tmp_new_screen_scale_factor;

   // save the new screen width & height to state
   $_ProductJSON.img_scaled_width   = newWidth * tmp_new_screen_scale_factor;

   $_ProductJSON.img_scaled_height  = newHeight * tmp_new_screen_scale_factor;

   $_ProductJSON.print_price = $default_print_size.price_category;
   $_ProductJSON.invisible_glass_price = $default_print_size.invisible_glass_price;

   _pricing_json.print_price = $default_print_size.price_category;
   _pricing_json.invisible_glass_price = $default_print_size.invisible_glass_price;

   _pricing_json.print_sqFt = parseInt( $default_print_size.width * $default_print_size.height );

   $_ProductJSON.pricingJSON = _pricing_json;

   return $_ProductJSON;
};

/**==============================================================================
 |
 | This changes the size of the print to the selected size
 |
 *==============================================================================*/
function __set_image_size_uploader( $width, $height, $print_width, $print_height, $element )
{
   $tmp_productJSON = getLsProductJSON();

   $tmp_element = JSON.parse( $element );

   var print_obj = { // set as landscape
      width: $width,
      height: $height,
      print_width: $print_width,
      print_height: $print_height,
      price_category: $tmp_element.price_category
   };

   $tmp_productJSON = initPrintSize( print_obj, $tmp_productJSON );

   saveProductJsonToLS( $tmp_productJSON )
}

/**
   Recalculate and display final price
   based on ProductJSON data
 */
function final_price()
{
    var $_ProductJSON = getLsProductJSON();
    console.log($_ProductJSON);

    var final_price = Number($_ProductJSON.print_price);
    if ($_ProductJSON.invisible_glass == '1') {
        final_price = Number(final_price) + Number($_ProductJSON.invisible_glass_price);
    }
    //console.log('invisible_glass = ' + $_ProductJSON.invisible_glass);
    //console.log('invisible_glass_price = ' + $_ProductJSON.invisible_glass_price);
    //console.log('final_price = ' + final_price);

    $('#list-item-price').html(formatter.format(final_price));
    $('#list-item-price-mobile').html(formatter.format(final_price));

    console.log('final_price _______________________ ... .. . > ' + formatter.format(final_price));
}

/**==============================================================================
|
| This changes the size of the print to the selected size
|
*==============================================================================*/
function set_config_frame_size( $width, $height, $print_width, $print_height, $element, $condition )
{
   $_ProductJSON = getLsProductJSON();
   //console.log($_ProductJSON);

   $tmp_element = JSON.parse( $element );

   //console.log('.....................................');
   //console.log($tmp_element);
   //console.log('.....................................');

   var tmp_new_screen_scale_factor, newWidth, newHeight;

   var _pricing_json = {
      frame_price: 0,
      glass_price: 0,
      inner_mb_price: 0,
      mb_price: 0,
      mounting_price: 0,
      print_price: 0,
      print_sqFt: 0,
      total_price: 0,
      invisible_glass_price: 0,
   };

   $_ProductJSON.print_width = $print_width;
   $_ProductJSON.print_height = $print_height;

   if( $_ProductJSON.img_orientation == 'landscape' ){

      // new sizes based on landscape

      newWidth = parseInt( $width * 72 );
      $_ProductJSON.width = newWidth;
      $_ProductJSON.img_width = newWidth;

      // compute the new height in px
      newHeight = Math.round( parseFloat( newWidth * $_ProductJSON.img_aspect_ratio ));
      $_ProductJSON.height = newHeight;
      $_ProductJSON.img_height = newHeight;

      // compute the screen scale factor
      tmp_new_screen_scale_factor = _MOBILE_MAX_SCREEN_WIDTH / newWidth;
   }
   else {

      // new sizes based on portrait

      newHeight  = parseInt( $height * 72 );
      $_ProductJSON.height = newHeight;
      $_ProductJSON.img_height = newHeight;

      // compute the new width in px
      newWidth = Math.round( parseFloat( newHeight * $_ProductJSON.img_aspect_ratio ));
      $_ProductJSON.width = newWidth;
      $_ProductJSON.img_width = newWidth;

      // compute the screen scale factor
      tmp_new_screen_scale_factor = _MOBILE_MAX_SCREEN_HEIGHT / newHeight;
   }

   // save the scale factor to the state
   $_ProductJSON.img_scale_factor = tmp_new_screen_scale_factor;

   // save the new screen width & height to state
   $_ProductJSON.img_scaled_width   = newWidth * tmp_new_screen_scale_factor;

   $_ProductJSON.img_scaled_height  = newHeight * tmp_new_screen_scale_factor;

   $_ProductJSON.print_price = $tmp_element.price_category;
   $_ProductJSON.invisible_glass_price = $tmp_element.invisible_glass_price;

   _pricing_json.print_price = $tmp_element.price_category;
   _pricing_json.invisible_glass_price = $tmp_element.invisible_glass_price;

   _pricing_json.print_sqFt = parseInt( $width * $height );

   $_ProductJSON.pricingJSON = _pricing_json;

    saveProductJsonToLS( $_ProductJSON );

   // set the new outside dimensions to the ProductJSON

   // only required if this function is called by the configurator
   if( $condition == 'configurator' ){
      load_images( $_ProductJSON );
   }

   get_print_dimension( $_ProductJSON );

   saveProductJsonToLS( $_ProductJSON );

   $('#list-item-price').html( formatter.format( $tmp_element.price_category ));
   $('#list-item-price-mobile').html( formatter.format( $tmp_element.price_category ));
   final_price();

   $('#list-item-outer-dimension').html( $_ProductJSON.outer_dimension );
   $('#list-printed-item-image-size').html( $_ProductJSON.print_dimension );

   $('#list-item-outer-dimension-mobile').html( $_ProductJSON.outer_dimension );
   $('#list-printed-item-image-size-mobile').html( $_ProductJSON.print_dimension );

   if( detector.mobile() ){
      $( document.getElementById('mobile-pricing') ).html( '$' + $tmp_element.price_category ); // <--
       final_price();
      $( document.getElementById('mobile-size-optons') ).html( $_ProductJSON.outer_dimension );
      $( document.getElementById('mobile-frame-description') ).html( $_ProductJSON.frame_description );
      if( $tmp_productJSON.mb1_width == 0 ){
         $( document.getElementById('mobile-matting') ).html( 'No Matting' )
      } else {
         $( document.getElementById('mobile-matting') ).html( '2 in' )
      }
   }

   console.log($_ProductJSON);
};

/**==============================================================================
|
| This changes the size of the print to the selected size
|
*==============================================================================*/
function __display_sizes(){

   $tmp_productJSON = getLsProductJSON();

   $tmp_productJSON.quick_sizes.forEach( function( element ){

      var r = $('<li>width: '+element.width+'in x height: '+element.height+'in </li>').attr({
         class: 'list-group-item fware-list-item',
         type: "li",
         id: "field",
         onclick: "set_config_frame_size("+element.width+','+element.height+','+element.price_category+",'image_sizing')"
      });

     $("#sizing-options").append(r);

   })

}

/**==============================================================================
|
| This changes the size of the print to the selected size
|
*==============================================================================*/
function display_sizes_configurator( $tmp_productJSON ){

    console.log($tmp_productJSON);

    $tmp_productJSON.printSizes.forEach( function( element ){ // repeat for each size in the collection

        var print_size_element = "<div class='d-inline list-item-print-size' style='width: 120px;'>" + element.width + " x " + element.height + "</div>";
        var outer_size_element = "<div class='d-inline list-item-outer-size' style='width: 120px;'>" + element.outer_width + " x " + element.outer_height + "</div>";
        var price_element = "<div class='d-inline list-item-price' style='width: 100px;'>$" + element.price_category + "</div>";

        // create the button for each size
        $button_template = "<button class='sizer-button' type='button'"+
          " onclick=set_config_frame_size('" + element.width + "','" + element.height + "','" + element.print_width + "','" + element.print_height + "','" + JSON.stringify(element) +"','configurator')"
          + "><div style='font-size: 11pt !important; line-height: inherit !important;'>" + print_size_element + outer_size_element + price_element +"</div></button>";

        // build the button element
        var r = $('<li>'+$button_template+'</li>').attr({
            class: 'list-group-item fware-list-item mt-1',
            style: 'padding: 0px !important;',
            type: "li",
            id: "field"
        });

        $("#configurator-size-options").append(r); // add the new button to the option element

        setTimeout(()=>{
            $('#collapseOne').collapse({toggle: false});
        },400)
   });
}

/**==============================================================================
|
| set and save the top mat colors and run the configurator
|
*==============================================================================*/
function setTopMatColor( $top_mat_color, $top_mat_color_name ){

   var $tmp_productJSON = getLsProductJSON();

   if($('#switch-top').is(':checked') == false){

      $tmp_productJSON.mb1_width = 144;

   }

   $("#switch-top").prop('checked', true); // set regardless
   $tmp_productJSON.mb1_color = $top_mat_color;
   $tmp_productJSON.mb1_color_name = $top_mat_color_name;
   $('#list-item-top-mat').html( $top_mat_color_name );
   $('#list-item-top-mat-mobile').html( $top_mat_color_name );

   saveProductJsonToLS( $tmp_productJSON );

   set_frame();

   return;

}

/**==============================================================================
|
| set and save the bottom mat colors and run the configurator
|
*==============================================================================*/
function setBottomMatColor( $bottom_mat_color, $bottom_mat_color_name ){

   clg('########### BOTTOM MAT COLOR ====='+$bottom_mat_color+'  '+$bottom_mat_color_name,1 )

   var $tmp_productJSON = getLsProductJSON();

   // by setting the bottom mat, check to make sure the top mat is set.
   // if the top mat is off, set it with default settings
   if( $tmp_productJSON.mb1_width == 0 ){

      $tmp_productJSON.mb1_width = 144; // 2inch width
      $("#switch-top").prop('checked', true); // turn the top mat switch to enabled
      $tmp_productJSON.mb1_color = '#ffffff'; // set the default color to white
      $tmp_productJSON.mb1_color_name = 'White'; // set the default color name
      $('#list-item-top-mat').html( 'White' ); // display the mat color on screen
      $('#list-item-top-mat-mobile').html( 'White' ); // display the mat color on screen

   }

   saveProductJsonToLS( $tmp_productJSON );

   set_frame();
 
   return;

}

/**==============================================================================
|
|
*==============================================================================*/
function __get_order_cart_info(){

/*
   document.getElementById("cart-items-detail").innerHTML = null;

   var data = {
      action: 'ajax_get_order_info',
      // this is the data to be sent to the ajax routine
      // data: { user_guid: $ProductJSON.user_guid }
      data: { user_guid: myAjax.user_guid }
   };
   $.post( myAjax.do_ajax , data, function (results) { // submit the ajax request

      if(results == ''){

         document.getElementById("cart-items-detail").innerHTML = "<div>TEST</div>";

      } else {

         window.localStorage.setItem('CartJSON', results );
         get_cart_contents()

      }
   }).always(function(){
       clg('Ajax - finished',1);
   });

 */

}
/**==============================================================================
|
| set and save the bottom mat colors and run the configurator
|
*==============================================================================*/
function __get_cart_contents( $condition ){

/*
   var $tmp_cart_json;
   var $tmp_order_json;
   var $cart_items;
   var template;
   var templateHtml;
   var cart_item_product_json_obj;
   var cart_item_product_pricing_json_obj;
   var $ret_string = "";

   function step_a(){
      return new Promise((resolve)=>{

         // get the 'cart_itens' object of the CartJSON json
         $tmp_cart_json = JSON.parse(  window.localStorage.getItem('CartJSON') );

         // separate the user data into it's own object
         $tmp_order_json = $tmp_cart_json.user_order_data;

         // separate the cart_items into it's own object
         $cart_items = $tmp_cart_json.cart_items;

         resolve('ok')
      })
   }
   function step_b(){
      return new Promise((resolve)=>{

         // check to make sure that the cart is not empty
         if( $cart_items ){

            // cycle through all the items in the shopping cart
            $cart_items.forEach( function( element ){

               // convert the current cart_item into an Object
               cart_item_product_json_obj = JSON.parse(element.productJSON);
               // get the associated pricingJSON from the cartItem
               cart_item_product_pricing_json_obj = cart_item_product_json_obj.pricingJSON

               if( cart_item_product_json_obj.img_orientation == 'landscape' ){

                  img_preview_width = _CART_PREVIEW_LANDSCAPE + 'px';
                  img_preview_height = 'auto';

               } else {

                  img_preview_height = _CART_PREVIEW_PORTRAIT + 'px';
                  img_preview_width = 'auto';

               }

               if( cart_item_product_json_obj.mb1_width !== 0 ){
                  $tmp_mb_selection = cart_item_product_json_obj.mb1_color_name + ' (' + cart_item_product_json_obj.mb1_width_text + ')';
               } else {
                  $tmp_mb_selection = 'No Matting';
               }

               $ret_string += '<div class="card w-100" style="margin-bottom: 5px; border: none !important;">' +
                  '<img class="card-img-top mx-auto" src="../../../uploadhandler/uploads/'+cart_item_product_json_obj.img_guid+'/'+cart_item_product_json_obj.img_guid+'.png?'+(new Date()).getTime()+'" alt="Card image cap" style="margin-top: 10px; width: '+img_preview_width+'; height: '+img_preview_height+'">' +
                  '<div class="card-title">' +
                  '</div>' +
                  '<div class="card-body p-1">' +
                  '<button type="button" id="shopping-cart-edit" class="fware-button shopping-cart-edit" style="width: 120px !important; font-size: 10pt !important; letter-spacing: 0px !important; background-color: #79b6c0" onclick="edit_cart_item('+'\''+element.item_guid+'\''+','+'\''+element.product_type+'\''+')">Edit</button>' +
                  '<button type="button" id="shopping-cart-delete" class="fware-button shopping-cart-delete" style="width: 120px !important; font-size: 10pt !important; letter-spacing: 0px !important; background-color: #79b6c0" onclick="delete_cart_item('+'\''+element.item_guid+'\''+','+'\''+element.product_type+'\''+')">Delete</button>' +
                  '</div>' +
                  '<div class="list-group mx-auto text-left mb-3">' +
                  '<div class="list-group-item border-0 p-1 pl-3 d-block">' +
                  '<span>Qty: '+element.item_qty+'</span>' +
                  '<span style="margin-left: 10px;">Price: $'+element.item_price+'</span>' +
                  '</div>' +
                  '<div class="list-group-item border-0 p-1 pl-3">Outside Dimensions: <div style="display: inline">'+cart_item_product_json_obj.outer_dimension+'</div></div>'+
                  '<div class="list-group-item border-0 p-1 pl-3">Printed Image Size: <div style="display: inline">'+cart_item_product_json_obj.print_dimension+'</div></div>'+
                  '<div class="list-group-item border-0 p-1 pl-3">Frame Style: '+cart_item_product_json_obj.frame_description+'</div>' +
                  '<div class="list-group-item border-0 p-1 pl-3">Matting: '+$tmp_mb_selection+'</div>' +
                  '</div>';

            });
            $display_string = $ret_string;
            clg('display_string===='+$display_string);
            document.getElementById("cart-items-detail").innerHTML = $display_string;
         } else {

            $innerHTML = "<div class='h-100 pt-4' style='min-height: 175px'>" +
               "<a class='reset-a' href='"+ myAjax.product_options_page +"'>" +
               "<img class='d-inline-block' src='../../uploadhandler/uploads/image_assets/upload-img.png' alt='upload frame placeholder' height='100px' width='82px' ></br>" +
               "Click here to Start Framing" +
               "</a>"

            document.getElementById("cart-items-detail").innerHTML = $innerHTML;
         }

         resolve('ok')
      })
   }

   function runSteps(){
      return step_a()
         .then(step_b)
         .then(()=>{

            $('#items_in_order').val( $tmp_order_json.items_in_order );
            $('#order_final_subtotal').val( $tmp_order_json.order_subtotal );

            if( parseInt( $tmp_order_json.items_in_order ) > 0  ){
               $('#btn-continue-to-checkout').prop('disabled', false);
            } else {
               $('#btn-continue-to-checkout').prop('disabled', true);
            }

            $.LoadingOverlay( "hide" );

            // since this func is called two diff ways, use condition to hide buttons for use in order confirm
            if($condition){
               $('.shopping-cart-edit').addClass('hide');
               $('.shopping-cart-delete').addClass('hide');
            }
         })
   }

   runSteps()

 */

};

/**==============================================================================
|
| get the viewport size
|
*==============================================================================*/
function __dw_getWindowDims() {
   var doc = document, w = window;
   var docEl = (doc.compatMode && doc.compatMode === 'CSS1Compat')?
      doc.documentElement: doc.body;

   var width = docEl.clientWidth;
   var height = docEl.clientHeight;

   // mobile zoomed in?
   if ( w.innerWidth && width > w.innerWidth ) {
      width = w.innerWidth;
      height = w.innerHeight;
   }

   return {width: width, height: height};
}

/**==============================================================================
 ||
 ||  sets the params for the main Konva stage
 ||
 *==============================================================================*/
var _configKonvaStageObj = {
   x: 0,
   y: 0,
   container: '',
   width:  0,
   height: 0,
}

/**==============================================================================
 ||
 ||  sets the params for the main Konva stage
 ||
 *==============================================================================*/
function configKonvaStage( $configKonvaStageObj ){

   $('#konva-container').css({'height': $configKonvaStageObj.height - 7, 'width': $configKonvaStageObj.width });

   var stage = new Konva.Stage({

      x: $configKonvaStageObj.x,
      y: $configKonvaStageObj.y,
      container: $configKonvaStageObj.container,
      width:  $configKonvaStageObj.width,
      height: $configKonvaStageObj.height,
      scaleY: $configKonvaStageObj.scaleY,
      scaleX: $configKonvaStageObj.scaleX,
      preventDefault: false,
   })

   return stage;

}

/**==============================================================================
 ||
 ||  sets the params for the main Konva Layer
 ||
 *==============================================================================*/
var _configMainLayerObj = {
   x: 0,
   y: 0,
   width:  0,
   height: 0,
}

/**==============================================================================
 ||
 ||  sets the params for the main image
 ||
 *==============================================================================*/
function configMainLayer( $configMainLayerObj ){
   var layer = new Konva.Layer ({
      x: $configMainLayerObj.x,
      y: $configMainLayerObj.y,
      width:  $configMainLayerObj.width,
      height: $configMainLayerObj.height,
   })

   return layer;

}

/**==============================================================================
 ||
 ||  sets the params for the main image
 ||
 *==============================================================================*/
var _configImgMainObj = {
   x: 0,
   y: 0,
   stroke:         0,
   strokeWidth:    0,
   strokeEnabled:  false,
   width:          0,
   height:         0,
}

/**==============================================================================
 ||
 ||  this function will be used to configure the sides
 ||
 *==============================================================================*/
function configImgMain( $configImgMainObj ){

   var mainImage = new Konva.Image({

      image: _mainImage, // this is a global variable
      x: $configImgMainObj.x,
      y: $configImgMainObj.y,
      stroke:         $configImgMainObj.stroke,
      strokeWidth:    $configImgMainObj.strokeWidth,
      strokeEnabled:  $configImgMainObj.strokeEnabled,
      width:          $configImgMainObj.width,
      height:         $configImgMainObj.height,
      preventDefault: false

   })

   return mainImage;

}

/**==============================================================================
 ||
 ||  this function will be used to configure the sides
 ||
 *==============================================================================*/
var _configSideObj = {
   x: 0,
   y: 0,
   scaleX:   0,
   scaleY:   0,
   points: [],
   shadowColor:    '#000000',
   shadowOffsetX:  0,
   shadowOffsetY:  0,
   shadowBlur:     0,
   shadowOpacity:  0,
   fillPatternRotation: 0,
}

/**==============================================================================
 ||
 ||  this function will be used to configure the sides
 ||
 *==============================================================================*/
function configSide( $configSideObj ){

   var kvLine = new Konva.Line({
      x: $configSideObj.x,
      y: $configSideObj.y,
      points:   $configSideObj.points,
      scaleX:   $configSideObj.scaleX,
      scaleY:   $configSideObj.scaleY,
      closed:   true,
      rotation: $configSideObj.rotation,
      fillPatternImage:  _borderImage, // todo - replace this dynamically
      fillPatternScaleX:      $configSideObj.fillPatternScaleX,
      fillPatternScaleY:      $configSideObj.fillPatternScaleY,
      fillPatternRotation:    $configSideObj.fillPatternRotation,
      fillPatternOffsetY:     $configSideObj.fillPatternOffsetY,
      fillPatternOffsetX:     $configSideObj.fillPatternOffsetX,

      preventDefault: false
   })

   return kvLine;

}

/**==============================================================================
||
||  this function will be used to configure the sides
||
*==============================================================================*/
var _mbSideObj = {
   points:[],
   fill: null,
   rotation: 0,
   scaleY: 0,
   offsetX: 0,
   offsetY: 0,
}

/**==============================================================================
||
||  this function will be used to configure the sides
||
*==============================================================================*/
function mbSide( $mbSideObj ){

   var mbSide = new Konva.Line({

      points:   $mbSideObj.points,
      fill:     $_ProductJSON.mb1_color, // same for all sides therefore getting from producJSON
      rotation: 0,
      closed:   true,
      scaleX:   $mbSideObj.scaleX,
      scaleY:   $mbSideObj.scaleY,
      x:        $mbSideObj.offsetX,
      y:        $mbSideObj.offsetY,
   })

   return mbSide;

}

/**==============================================================================
||
||  this function will be used to configure the sides
||
*==============================================================================*/
var _innerShadow = {
   point: 0,
   stroke: 0,
   strokewidth: 0,
   rotation: 0,
   x: 0,
   y: 0,
   shadowColor: null,
   shadowOffsetX: 0,
   shadowOffsetY: 0,
}

/**==============================================================================
 ||
 ||  this function will be used to configure the sides
 ||
 *==============================================================================*/
function innerShadow( $innerShadow ){
   var innerShadow = new Konva.Line({
      points: [
         0,0,
         $innerShadow.point ,0
      ],
      stroke:           $innerShadow.stroke,
      strokewidth:      1,
      opacity:          1,
      rotation:         $innerShadow.rotation,
      x: $innerShadow.x,
      y: $innerShadow.y,
      shadowColor:      $innerShadow.shadowColor,
      shadowOffsetX:    $innerShadow.shadowOffsetX,
      shadowOffsetY:    $innerShadow.shadowOffsetY,
      shadowBlur:       4,
      blurRadius:       2,
      shadowOpacity:    $innerShadow.shadowOpacity,
      preventDefault:   false,
   })

   return innerShadow;

}

/**==============================================================================
 ||
 ||  this function will be used to configure the sides
 ||
 *==============================================================================*/
function set_frame_params( $ProductJSON ){

   _imageWidth   = $ProductJSON.img_scaled_width
   _imageHeight  = $ProductJSON.img_scaled_height

   _scale_factor = $ProductJSON.img_scale_factor

   _MBpointXA = 300
   _MBpointXB = 192
   _MBpointXC = 108
   _MBpointXD = 108
   _MBpointXE = 108

   _MBpointYA = 300
   _MBpointYB = 192
   _MBpointYC = 108
   _MBpointYD = 108
   _MBpointYE = 108

   _MBBottomSideOffsetX = 0

   _pointXA = 300
   _pointXB = 192
   _pointXC = 108
   _pointXD = 108
   _pointXE = 108

   _pointYA = 300
   _pointYB = 192
   _pointYC = 108
   _pointYD = 108
   _pointYE = 108

   _BottomSideOffsetX = 0

   if (($ProductJSON.mb1_width == 0) ){ // this hides the innerShadow if the matboard width = 0

      clg('##### MB1 WIDTH == 0',1)

      _innerShadowVisibility = false
      _innerMBEnabled = false
      $ProductJSON.innerMBEnabled = false
      $ProductJSON.innerMBColorName = 'none'
      $ProductJSON.mb1_color_name = 'no mat'
      $ProductJSON.mb1_width_text = null
      _textMBWidth = ''
      _btnInnerMat = 'disable'

   } else {

      clg('##### MB1 WIDTH !== 0',1)

      $ProductJSON.innerMBWidth = 36 * $ProductJSON.img_scale_factor // 18 = quarter inch
      _innerShadowVisibility = true
      _btnInnerMat = null

   }

   return

}


/**==============================================================================
 ||
 ||  this function will be used to configure the sides
 ||
 *==============================================================================*/
function setMB( $ProductJSON ){

   var ImageLengthX = _imageWidth // this is the (x) width of the rectangle

   // this is the width/thickness of the frame based on the scale factor
   _scaled_width_of_mb = parseFloat( $ProductJSON.mb1_width ) * parseFloat( _scale_factor )

   _MBpointXA =  parseFloat( ImageLengthX ) + ( _scaled_width_of_mb * 2 ) // this is the outer width of the frame
   _MBpointXB =  parseFloat( _MBpointXA ) - parseFloat(_scaled_width_of_mb ) // this is the inside width of the frame
   _MBpointXC =  $ProductJSON.mb1_width
   _MBpointXD =  ( $ProductJSON.mb1_width * _scale_factor ) // this is the scale factor based on 1.5 inches or 108 px
   _MBpointXE =  $ProductJSON.mb1_width

   clg('#### Inner MB Width == '+ _innerMBWidth, 1 )

   _MBLengthY = _imageHeight // this is the height of the rectangle

   _MBpointYA = parseFloat( _MBLengthY ) + ( _scaled_width_of_mb * 2 ) // this is the outer height of the frame
   _MBpointYB = parseFloat( _MBpointYA ) - parseFloat( _scaled_width_of_mb ) // this is the inner height of the frame
   _MBpointYC = $ProductJSON.mb1_width
   _MBpointYD = ( $ProductJSON.mb1_width * _scale_factor )
   _MBpointYE = $ProductJSON.mb1_width

   // this is the X coordinate of the inner rectangle
   _MBRectX = _scaled_width_of_mb
   // this is the Y coordinate of the inner rectangle
   _MBRectY = _scaled_width_of_mb

   // place the right frame side
   _MBRightSideX = parseFloat( ImageLengthX ) + parseFloat( _scaled_width_of_mb ) + _RightSideAdjustment

   // place the left side of the frame
   _MBLeftSideOffsetX = parseFloat( ImageLengthX ) + ( _scaled_width_of_mb * 2 )

   // place the bottom of the frame
   _MBBottomSideOffsetX = parseFloat( ImageLengthX ) + ( _scaled_width_of_mb * 2 )
   _MBBottomSideY = parseFloat( _MBpointYB )

   _MBTotalWidth = _MBpointXA
   _MBTotalHeight = _MBpointYA

   clg('### POINT mbXA =='+_MBpointXA,1);
   clg('### POINT mbXB =='+_MBpointXB,1);
   clg('### POINT mbXC =='+_MBpointXC,1);
   clg('### POINT mbXD =='+_MBpointXD,1);
   clg('### POINT mbXE =='+_MBpointXE,1);

   clg('### POINT mbYA =='+_MBpointYA,1);
   clg('### POINT mbYB =='+_MBpointYB,1);
   clg('### POINT mbYC =='+_MBpointYC,1);
   clg('### POINT mbYD =='+_MBpointYD,1);
   clg('### POINT mbYE =='+_MBpointYE,1);

   return

}

/**==============================================================================
 ||
 ||  this will set the frame parameters
 ||
 *==============================================================================*/
function setFrame( $ProductJSON ){

   var LengthX = _MBTotalWidth; // this is the (x) width of the rectangle
   clg( "LengthX = " + LengthX );

   // this is the width of the frame based on the scale factor
   var scaled_width_of_frame = parseFloat( $ProductJSON.frame_width ) * parseFloat( _scale_factor );
   clg( "Scaled Frame Width = " + scaled_width_of_frame );

   _pointXA = parseFloat( LengthX ) + ( scaled_width_of_frame * 2 ); // this is the outer width of the frame
   clg( "PointXA: "+ _pointXA );

   _pointXB = parseFloat( _pointXA ) - parseFloat( scaled_width_of_frame ); // this is the inside width of the frame
   clg( "Inside Frame Width: "+ LengthX );

   _pointXC = $ProductJSON.frame_width;
   _pointXD = ( $ProductJSON.frame_width * _scale_factor ); // this is the scale factor based on 1.5 inches or 108 px
   _pointXE = $ProductJSON.frame_width;

   var LengthY = _MBTotalHeight; // this is the height of the rectangle

   _pointYA = parseFloat( LengthY ) + ( scaled_width_of_frame * 2 ); // this is the outer height of the frame
   _pointYB = parseFloat( _pointYA ) - parseFloat( scaled_width_of_frame ); // this is the inner height of the frame
   _pointYC = $ProductJSON.frame_width;
   _pointYD = $ProductJSON.frame_width * _scale_factor;
   _pointYE = $ProductJSON.frame_width;

   _RectX = scaled_width_of_frame; // this is the X coordinate of the inner rectangle

   _RectY = scaled_width_of_frame; // this is the Y coordinate of the inner rectangle

   // place the right frame side
   _RightSideX = parseFloat( LengthX ) + parseFloat( scaled_width_of_frame ) + _RightSideAdjustment;

   // place the left side of the frame
   _LeftSideOffsetX = parseFloat( LengthX ) + ( scaled_width_of_frame * 2 );
   //_LeftSideOffsetX = parseFloat( _pointYA ); // Todo - Delete

   // place the bottom of the frame
   _BottomSideOffsetX = parseFloat( LengthX ) + ( scaled_width_of_frame * 2 );
   _BottomSideY = parseFloat( _pointYB ) - _BottomSideAdjustment;

   _MBOffsetX = scaled_width_of_frame;
   _MBOffsetY = scaled_width_of_frame;

   _SrcImageX = parseFloat( scaled_width_of_frame ) + parseFloat( _scaled_width_of_mb );
   _SrcImageY = parseFloat( scaled_width_of_frame ) + parseFloat( _scaled_width_of_mb );

   //getWindowWidth()

   //*7.0

   var $tmp_window_width = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);

   if ($tmp_window_width <= 550){
      _view_mode == 'mobile';
   } else {
      _view_mode == 'desktop';
   }

   _screenSizeAdj = get_screen_size_adjust( _pointXA, _pointYA );

   _configKonvaStageObj.height = (( _pointYA + _pointYA_Adjustment) * _screenSizeAdj); // *6.0
   _configKonvaStageObj.width = (( _pointXA + _pointXA_Adjustment) * _screenSizeAdj); // *6.0

   _configKonvaStageObj.scaleX = _screenSizeAdj;
   _configKonvaStageObj.scaleY = _screenSizeAdj;

   _configKonvaStageObj.x = 0;
   _configKonvaStageObj.y = 0;

   clg( "Total height:" + _pointYA * _screenSizeAdj);
   clg( "config Stage height =="+ _canvasDivHeight );
   clg('######==== SET FRAME - BEFORE RESOLVE() ===######')

   clg('### POINT XA =='+ _pointXA,1);
   clg('### POINT XB =='+ _pointXB,1);
   clg('### POINT XC =='+ _pointXC,1);
   clg('### POINT XD =='+ _pointXD,1);
   clg('### POINT XE =='+ _pointXE,1);

   clg('### POINT YA =='+ _pointYA,1);
   clg('### POINT YB =='+ _pointYB,1);
   clg('### POINT YC =='+ _pointYC,1);
   clg('### POINT YD =='+ _pointYD,1);
   clg('### POINT YE =='+ _pointYE,1);



   clg('###### _configKonvaStageObj.height==='+_configKonvaStageObj.height,1)
   clg('###### _configKonvaStageObj.width==='+_configKonvaStageObj.width,1)

   _configKonvaStageObj.container = 'konva-container'; // corresponds to the id of the dom element for the canvas
   tmp_configKonvaStage = configKonvaStage( _configKonvaStageObj ) // init the stage

   return

}

/**=============================================================================
 ||
 ||  sets the params for the Konva layer
 ||
 *==============================================================================*/
function set_layer(){

   clg( 'STAGE HEIGHT =='+ _stage_height,1 );

   var tmp_configLayerObj = _configMainLayerObj;

   tmp_configLayerObj.x = 0;
   tmp_configLayerObj.y = 0;
   tmp_configLayerObj.width = _stage_width;
   tmp_configLayerObj.height = _stage_height;

   tmp_configLayer = configMainLayer( tmp_configLayerObj ); // init the layer

}

/**=============================================================================
 ||
 ||  sets the params for the left frame side
 ||
 ||       1         4
 ||      0,0      YE,YD
 ||       +---------+
 ||       |         |
 ||       |         |
 ||       |         |
 ||       |         |
 ||       +---------+
 ||      0,YA     YC,YB
 ||       2         3
 ||
 *==============================================================================*/
function set_left_side(){

   clg('SCALE LEFT SIDE ====='+_scale_factor,1 )

   var left_sideObj = _configSideObj;

   left_sideObj.points = [
      0,0,
      0, _pointYA,
      _pointYC, _pointYB,
      _pointYE, _pointYD
   ];
   left_sideObj.x        = 0;
   left_sideObj.y        = 0;
   left_sideObj.scaleX   = _scale_factor;
   left_sideObj.scaleY   = 1;
   left_sideObj.rotation = 0;
   left_sideObj.fillPatternScaleX = 1;
   left_sideObj.fillPatternScaleY = 2;
   left_sideObj.fillPatternRotation = 90; // rotates the background fill image with the orientation of the side
   left_sideObj.fillPatternOffsetX = 0;
   left_sideObj.fillPatternOffsetY = 0;

   tmp_leftSide = configSide( left_sideObj )

}

/**=============================================================================
 ||
 ||   sets the params for the right frame side
 ||
 ||        4
 ||      XA-YC,      1
 ||       YD       XA,0
 ||        +---------+
 ||        |         |
 ||        |         |
 ||        |         |
 ||        |         |
 ||        +---------+
 ||      XA-YC,    XA,YA
 ||      YA-YD       2
 ||        3
 ||
 *==============================================================================*/
function set_right_side(){

   var right_sideObj = _configSideObj;

   right_sideObj.points = [
      0,0,
      0, _pointYA,
      _pointYC, _pointYB,
      _pointYE, _pointYD
   ];

   right_sideObj.x        = _pointXA;
   right_sideObj.y        = _pointYA;
   right_sideObj.rotation = 180;
   right_sideObj.scaleX   = _scale_factor;
   right_sideObj.scaleY   = 1;
   right_sideObj.fillPatternRotation = 90;

   right_sideObj.shadowColor   = '';
   right_sideObj.shadowOffsetX = 0;
   right_sideObj.shadowOffsetY = 0;
   right_sideObj.shadowBlur    = 0;
   right_sideObj.shadowOpacity = 0.5;

   right_sideObj.zIndex = 99;

   tmp_rightSide = configSide( right_sideObj )

}

/**=============================================================================
 ||
 ||    sets the params for the top frame side
 ||
 ||         1        2
 ||        0,0      XA,0
 ||         +---------+
 ||         |         |
 ||         |         |
 ||         |         |
 ||         |         |
 ||         +---------+
 ||       XD,YC     XB,YC
 ||         4         3
 ||
 *==============================================================================*/
function set_top_side(){

   var top_sideObj = _configSideObj;

   top_sideObj.points = [
      0,0,
      _pointXA, 0,
      _pointXB + 1, _pointYC,
      _pointXD - 1, _pointYC
   ];
   top_sideObj.x        = 0;
   top_sideObj.y        = 0;
   top_sideObj.rotation = 0;
   top_sideObj.scaleX   = 1;
   top_sideObj.scaleY   = _scale_factor;
   top_sideObj.fillPatternRotation = 0;
   top_sideObj.fillPatternOffsetY = 5;
   top_sideObj.fillPatternOffsetX = 0;

   top_sideObj.shadowColor   = '';
   top_sideObj.shadowOffsetX = 0;
   top_sideObj.shadowOffsetY = 0;
   top_sideObj.shadowBlur    = 0;
   top_sideObj.shadowOpacity = 0;

   tmp_topSide = configSide( top_sideObj )

}

/**=============================================================================
 ||
 ||     sets the params for the bottom frame side
 ||
 ||          4         3
 ||         XD,      XA-XD,
 ||        YA-YC     YA-YC
 ||          +---------+
 ||          |         |
 ||          |         |
 ||          |         |
 ||          |         |
 ||          +---------+
 ||         0,YA,    XA,YA
 ||          1         2
 ||
 *==============================================================================*/
function set_bottom_side(){

   var bottom_sideObj = _configSideObj;

   bottom_sideObj.points = [
      0,0,
      _pointXA, 0,
      _pointXB + 1, _pointYC,
      _pointXD - 1, _pointYC
   ];

   bottom_sideObj.x        = _pointXA;
   bottom_sideObj.y        = _pointYA;
   bottom_sideObj.rotation = 180;
   bottom_sideObj.scaleX   = 1;
   bottom_sideObj.scaleY   = _scale_factor;
   bottom_sideObj.fillPatternRotation = 0;
   bottom_sideObj.fillPatternOffsetY = 5;
   bottom_sideObj.fillPatternOffsetX = 0;
   tmp_bottomSide = configSide( bottom_sideObj );

}

/**=============================================================================
 ||
 ||   sets the params for the left matboard side
 ||
 ||        1         4
 ||       0,0      YE,YD
 ||        +---------+
 ||        |         |
 ||        |         |
 ||        |         |
 ||        |         |
 ||        +---------+
 ||       0,YA     YC,YB
 ||        2         3
 ||
 *==============================================================================*/
function set_mb_leftSide(){

   var left_mbSideObj = _mbSideObj;

   left_mbSideObj.points =[
      0,0,
      0, _MBpointYA,
      _MBpointYC, _MBpointYB,
      _MBpointYE, _MBpointYD,
   ];
   left_mbSideObj.scaleX = _scale_factor;
   left_mbSideObj.scaleY = 1;
   left_mbSideObj.offsetX = _MBOffsetX;
   left_mbSideObj.offsetY = _MBOffsetY;

   tmp_mbLeftSide = mbSide( left_mbSideObj );

}

/**=============================================================================
 ||
 ||    sets the params for the right matboard frame side
 ||
 ||         4
 ||       XA-YC,      1
 ||        YD       XA,0
 ||         +---------+
 ||         |         |
 ||         |         |
 ||         |         |
 ||         |         |
 ||         +---------+
 ||       XA-YC,    XA,YA
 ||       YA-YD       2
 ||         3
 ||
 *==============================================================================*/
function set_mb_rightSide(){

   var right_mbSideObj = _mbSideObj;

   right_mbSideObj.points =[
      _MBpointXA,0,
      _MBpointXA, _MBpointYA,
      _MBpointXA - _MBpointYC, _MBpointYA - _MBpointYD,
      _MBpointXA - _MBpointYC, _MBpointYD,
   ];
   right_mbSideObj.scaleX = _scale_factor;
   right_mbSideObj.scaleY = 1;
   right_mbSideObj.offsetX = _MBOffsetX + ( _MBpointXA - ( _MBpointXA * _scale_factor ));
   right_mbSideObj.offsetY = _MBOffsetY;

   tmp_mbRightSide = mbSide( right_mbSideObj );

}

/**=============================================================================
 ||
 ||     sets the params for the top matboard side
 ||
 ||          1        2
 ||         0,0      XA,0
 ||          +---------+
 ||          |         |
 ||          |         |
 ||          |         |
 ||          |         |
 ||          +---------+
 ||        XD,YC     XB,YC
 ||          4         3
 ||
 *==============================================================================*/
function set_mb_topSide(){

   clg('#### MATBOARD SCALE FACTOR ======'+_scale_factor,1);

   var top_mbSideObj = _mbSideObj;

   top_mbSideObj.points =[
      0,0,
      _MBpointXA,0,
      _MBpointXB, _MBpointYC,
      _MBpointXD, _MBpointYC,
   ];
   top_mbSideObj.scaleY  = _scale_factor;
   top_mbSideObj.scaleX  = 1;
   top_mbSideObj.offsetX = _MBOffsetX;
   top_mbSideObj.offsetY = _MBOffsetY;

   tmp_mbTopSide = mbSide( top_mbSideObj );

}

/**=============================================================================
 ||
 ||      sets the params for the bottom matboard side
 ||
 ||           4         3
 ||          XD,      XA-XD,
 ||         YA-YC     YA-YC
 ||           +---------+
 ||           |         |
 ||           |         |
 ||           |         |
 ||           |         |
 ||           +---------+
 ||          0,YA,    XA,YA
 ||           1         2
 ||
 *==============================================================================*/
function set_mb_bottomSide(){

   var bottom_mbSideObj = _mbSideObj;

   bottom_mbSideObj.points =[
      0, _MBpointYA,
      _MBpointXA, _MBpointYA,
      _MBpointXA - _MBpointXD, _MBpointYA - _MBpointXC,
      _MBpointXD, _MBpointYA - _MBpointYC,
   ];
   bottom_mbSideObj.scaleY  = _scale_factor;
   bottom_mbSideObj.scaleX  = 1;
   bottom_mbSideObj.offsetX = _MBOffsetX;
   bottom_mbSideObj.offsetY = _MBOffsetY + ( _MBpointYA - ( _MBpointYA * _scale_factor ));

   tmp_mbBottomSide = mbSide( bottom_mbSideObj );

}

/**==============================================================================
 ||
 ||      sets the params for the bottom matboard side
 ||
 *===============================================================================*/
function set_main_image( $ProductJSON ){

   var mainImageObj = _configImgMainObj;

   mainImageObj.x = _SrcImageX;
   mainImageObj.y = _SrcImageY;
   mainImageObj.stroke         = $ProductJSON.innerMBColor;
   mainImageObj.strokeWidth    = $ProductJSON.innerMBWidth;
   mainImageObj.strokeEnabled  = $ProductJSON.innerMBEnabled;
   mainImageObj.width          = $ProductJSON.img_scaled_width;
   mainImageObj.height         = $ProductJSON.img_scaled_height;

   tmp_mainImage = configImgMain( mainImageObj );

}

/**==============================================================================
||
||      sets the params for the bottom matboard side
||
*===============================================================================*/
function set_fb_main_image( $ProductJSON ){

   var mainImageObj = _configImgMainObj;

   mainImageObj.x = _SrcImageX;
   mainImageObj.y = _SrcImageY;
   mainImageObj.stroke         = $ProductJSON.innerMBColor;
   mainImageObj.strokeWidth    = $ProductJSON.innerMBWidth;
   mainImageObj.strokeEnabled  = $ProductJSON.innerMBEnabled;
   mainImageObj.width          = $ProductJSON.img_scaled_width;
   mainImageObj.height         = $ProductJSON.img_scaled_height;

   tmp_mainImage = configImgMain( mainImageObj );

}

/**==============================================================================
||
||      sets the params for the bottom matboard side
||
*===============================================================================*/
function set_top_inner_shadow(){

   var topInnerShadow = _innerShadow;

   topInnerShadow.point = _MBpointXA;
   topInnerShadow.x = _RectX;
   topInnerShadow.y = _RectY - 1;
   topInnerShadow.stroke         = '#8f8f8f';
   topInnerShadow.shadowColor    = '#6f6f6f';
   topInnerShadow.shadowOffsetX  = 0;
   topInnerShadow.shadowOffsetY  = 2;
   topInnerShadow.rotation       = 0;

   tmp_mbTopInnerShadow = innerShadow( topInnerShadow );

}

/**==============================================================================
 ||
 ||      sets the params for the bottom matboard side
 ||
 *===============================================================================*/
function set_bottom_inner_shadow(){

   var bottomInnerShadow = _innerShadow;

   bottomInnerShadow.point = _MBpointXA;
   bottomInnerShadow.x = _RectX;
   bottomInnerShadow.y = _pointYA - _RectY + 1;
   bottomInnerShadow.stroke         = '#8f8f8f';
   bottomInnerShadow.shadowColor    = '#6f6f6f';
   bottomInnerShadow.shadowOffsetX  = 0;
   bottomInnerShadow.shadowOffsetY  = - 1;
   bottomInnerShadow.rotation       = 0;

   tmp_mbBottomInnerShadow = innerShadow( bottomInnerShadow );

}

/**==============================================================================
 ||
 ||      sets the params for the bottom matboard side
 ||
 *===============================================================================*/
function set_left_inner_shadow(){

   var leftInnerShadow = _innerShadow;

   leftInnerShadow.point = _MBpointYA;
   leftInnerShadow.x = _RectX - 1;
   leftInnerShadow.y = _RectY;
   leftInnerShadow.stroke         = '#8f8f8f';
   leftInnerShadow.shadowColor    = '#6f6f6f';
   leftInnerShadow.shadowOffsetX  = 1;
   leftInnerShadow.shadowOffsetY  = 0;
   leftInnerShadow.rotation       = 90;

   tmp_mbLeftInnerShadow = innerShadow( leftInnerShadow );

}

/**==============================================================================
 ||
 ||      sets the params for the bottom matboard side
 ||
 *===============================================================================*/
function set_right_inner_shadow(){

   var rightInnerShadow = _innerShadow;

   rightInnerShadow.point = _MBpointYA;
   rightInnerShadow.x = _pointXA - _RectX + 1;
   rightInnerShadow.y = _RectY;
   rightInnerShadow.stroke         = '#8f8f8f';
   rightInnerShadow.shadowColor    = '#650253'; //'#6f6f6f';
   rightInnerShadow.shadowOffsetX  = - 1.5;
   rightInnerShadow.shadowOffsetY  = 0;
   rightInnerShadow.shadowOpacity  = .5;
   rightInnerShadow.rotation       = 90;

   tmp_mbRightInnerShadow = innerShadow( rightInnerShadow );

}

/**==============================================================================
 ||
 ||      sets the frame
 ||
 *===============================================================================*/
function set_frame(){

   $_ProductJSON = getLsProductJSON();

   set_frame_params( $_ProductJSON ) // set the frame params

   setMB( $_ProductJSON ) //  set the mat board params

   // the stage is initialized here
   setFrame( $_ProductJSON ) //  set the frame params

   set_left_side();

   set_right_side();

   set_top_side();

   set_bottom_side();

   set_mb_topSide();

   set_mb_bottomSide();

   set_mb_leftSide()

   set_mb_rightSide()

   set_top_inner_shadow()

   set_right_inner_shadow()

   //set_bottom_inner_shadow()

   //set_left_inner_shadow()

   set_main_image( $_ProductJSON )

   set_layer();

   tmp_configLayer.add(
      tmp_mbTopSide,
      tmp_mbBottomSide,
      tmp_mbLeftSide,
      tmp_mbRightSide,
      tmp_mbTopInnerShadow,
      tmp_mbRightInnerShadow,
      //tmp_mbBottomInnerShadow,
      //tmp_mbLeftInnerShadow,
      tmp_topSide,
      tmp_bottomSide,
      tmp_leftSide,
      tmp_rightSide,

      tmp_mainImage
   );

   tmp_configKonvaStage.removeChildren();

   clg('#### WIDTH AND HEIGHT OF OUTSIDE XA =='+_pointXA+'  YA=='+_pointYA,1)

   clg('#######SETTIMOUT INITIATED########',1);
   tmp_configKonvaStage.add( tmp_configLayer ); // add the layer to the stage
   tmp_configKonvaStage.draw();

   clg('**** SET FRAME COMPLETE *******',1);

   // this is here to make sure both sides of the configurator are even. its here because any change to
   // the height of the left side happens here.
   if( $('#product-card').height() > $('#product-description-card').height() ){
      $('#product-description-card').height( $('#product-card').height() )
   } else {
      $('#product-card').height( $('#product-description-card').height() )
   }

   return

}

/**================================================================================
||
||      sets the frame
||
*================================================================================*/
function get_screen_size_adjust( $width, $height ){

   var $screen_size_adjust;

   if( detector.mobile() ){

      if( $width >= $height){

         $screen_size_adjust = _adj_mobile_scale_width / $width // TODO - THIS IS THE BIG DEAL TO ADJUST FOR SCREEN SIZE
         clg( "############## MOBILE (L) screenSizeAdj "+ $screen_size_adjust);

      } else {

         $screen_size_adjust = _adj_mobile_scale_height / $height // TODO - THIS IS THE BIG DEAL TO ADJUST FOR SCREEN SIZE
         clg( "############## MOBILE (P) screenSizeAdj "+ $screen_size_adjust);

      }

   } else {

      if( $width >= $height){

         $screen_size_adjust = _adj_desktop_scale_width / $width // TODO - THIS IS THE BIG DEAL TO ADJUST FOR SCREEN SIZE
         clg( "############## DESKTOP (L) screenSizeAdj "+ $screen_size_adjust );

      } else {

         $screen_size_adjust = _adj_desktop_scale_height / $height // TODO - THIS IS THE BIG DEAL TO ADJUST FOR SCREEN SIZE
         clg( "############## DESKTOP (P) screenSizeAdj "+ $screen_size_adjust);

      }
   }

   return $screen_size_adjust

}

/**================================================================================
||
||      sets the frame
||
*================================================================================*/
function set_flatbed(){

   const gradient_color1 = '#AAC2CB'
   const gradient_color2 = '#cfcfcf'
   const gradient_color3 = '#7E929B'

   $_ProductJSON = getLsProductJSON();

   _screenSizeAdj = get_screen_size_adjust( $_ProductJSON.width, $_ProductJSON.height );

   const $tmp_width = $_ProductJSON.width * _screenSizeAdj;
   const $tmp_height = $_ProductJSON.height * _screenSizeAdj;

   var konva_flatbed_stage = new Konva.Stage({
      x: 0,
      y: 0,
      container: 'konva-container',
      width:  $tmp_width,
      height: $tmp_height,
      scaleY: 1,
      scaleX: 1,
      preventDefault: false,
   })

   const main_flatbed_layer = new Konva.Layer ({
      x: 0,
      y: 0,
      width: $tmp_width,
      height: $tmp_height,
   })

   /*--========== MAIN ==========--*/
   var main_fb_image_group = new Konva.Group();

   var main_fb_Image = new Konva.Image({
      image: _mainImage, // this is a global variable
      x: 5,
      y: 5,
      stroke:         $_ProductJSON.innerMBColor,
      strokeWidth:    $_ProductJSON.innerMBWidth,
      strokeEnabled:  $_ProductJSON.innerMBEnabled,
      width:          $tmp_width,
      height:         $tmp_height,
      preventDefault: false

   })

   main_fb_image_group.add( main_fb_Image );

   /*--========== LEFT ==========--*/
   const left_fb_clip_group = new Konva.Group({
      clipFunc: function(ctx){
         ctx.moveTo(0,0);
         ctx.lineTo( 7, 9);
         ctx.lineTo( 7, $tmp_height );
         ctx.lineTo(0, $tmp_height );
         ctx.closePath();
      },
      preventDefault: false,
      x: 0,
      y: 0,
   });

   const left_fb_imageObj = new Konva.Image({
      x: 0,
      y: 0,
      opacity: .5,
      skew:{ x: 0, y: 0 },
      fill: '#ffffff',
      image: _mainImage,
      width: $tmp_width,
      height: $tmp_height,
      fillRadialGradientEndRadius: 200,
      fillRadialGradientColorStops: [0, gradient_color1, 0.5, gradient_color2, 1, gradient_color3],
      preventDefault: false
   });

   const left_fb_lineObj = new Konva.Line({
      points: [
         0,0,
         5,5.5,
         5,$tmp_height + 4,
         0,$tmp_height - 1
      ],
      closed: true, fill: '', strokeWidth: .5, stroke: '#739090', opacity: .6, fill: '#A1C5C5',
      preventDefault: false,
      fillRadialGradientEndRadius: 200,
      fillRadialGradientColorStops: [0, gradient_color1, 0.5, gradient_color2, 1, gradient_color3],
   })

   /*--========== TOP ==========--*/
   const top_fb_clip_group = new Konva.Group({
      clipFunc: function(ctx){
         ctx.moveTo(0,0);
         ctx.lineTo( $tmp_width ,0);
         ctx.lineTo( $tmp_width + 7, 7);
         ctx.lineTo( 5, 7);
         ctx.closePath();
      },
      preventDefault: false,
      x: 0,
      y: 0,
   });

   const top_fb_imageObj = new Konva.Image({
      x: 0,
      y: 0,
      opacity: .4,
      skew: { x: 0, y: 0 },
      fill: '#ffffff',
      preventDefault: false,
      image: _mainImage,
      width: $tmp_width,
      height: $tmp_width,
      preventDefault: false
   });

   const top_fb_lineObj = new Konva.Line({
      points: [
         1,0,
         $tmp_width,0,
         $tmp_width + 5, 5,
         5.5,5
      ],
      closed: true, strokeWidth: .5, stroke: '#739090', opacity: .5, fill: '#A1C5C5',
      fillPatternImage: _mainImage,
      preventDefault: false,
      fillRadialGradientEndRadius: 200,
      fillRadialGradientColorStops: [0, gradient_color1, 0.5, gradient_color2, 1, gradient_color3],
   })

   left_fb_clip_group.add(
      left_fb_imageObj
   )

   top_fb_clip_group.add(
      top_fb_imageObj
   )

   main_flatbed_layer.add(
      left_fb_clip_group, left_fb_lineObj, top_fb_clip_group, top_fb_lineObj, main_fb_image_group
   );

   konva_flatbed_stage.add( main_flatbed_layer ); // add the layer to the stage
   tmp_configKonvaStage.draw();

   clg('**** SET FRAME COMPLETE *******',1);

   return

}

/**================================================================================
 ||
 ||      sets the frame
 ||
 *================================================================================*/
function set_canvas_wrapped(){

   $_ProductJSON = getLsProductJSON();

   _screenSizeAdj = get_screen_size_adjust( $_ProductJSON.width, $_ProductJSON.height );

   const $tmp_width = $_ProductJSON.width * _screenSizeAdj;
   const $tmp_height = $_ProductJSON.height * _screenSizeAdj;

   var konva_flatbed_stage = new Konva.Stage({
      x: 0,
      y: 0,
      container: 'konva-container',
      width:  $tmp_width,
      height: $tmp_height,
      scaleY: 1,
      scaleX: 1,
      preventDefault: false,
   })

   const main_flatbed_layer = new Konva.Layer ({
      x: 0,
      y: 0,
      width: $tmp_width,
      height: $tmp_height,
   })

   /*--========== MAIN ==========--*/
   
   const thickness_of_left_side  = 13
   const thickness_of_top_side   = 10
   const width_of_image          = $tmp_width
   const height_of_image         = $tmp_height
   const CanvasSidesColor        = '#ffffff'
   const CanvasSidesOpacity      = .6

   const FBMainImageObj_X        = 5
   const FBMainImageObj_Y        = 4

   const mainClipGroup = new Konva.Group({
      clipFunc: function (ctx) {
         ctx.moveTo(thickness_of_left_side - 1, thickness_of_top_side);
         ctx.lineTo(width_of_image, thickness_of_top_side);
         ctx.lineTo(width_of_image, height_of_image);
         ctx.lineTo(thickness_of_left_side - 1, height_of_image);
         ctx.closePath();
      },
      x: 1.5,
      y: .5,
      stroke: '#afafaf',
      strokeWidth: 1,
   })

   const mainImgObj = new Konva.Image({
      x: FBMainImageObj_X,
      y: FBMainImageObj_Y,
      image: _mainImage,
      width: $tmp_width,
      height: $tmp_height,
      preventDefault: false,
   })

   /*--========== LEFT ==========--*/

      const left_thickness_of_top_side   = 5
      const left_thickness_of_left_side  = 7
      const left_side_skew_adjust   = 6
      const left_height_of_image = $tmp_height
   
      const CanvasWrappedLeftClipGroup = new Konva.Group({
         clipFunc: function(ctx){
            ctx.moveTo(0, left_thickness_of_top_side);
            ctx.lineTo( left_thickness_of_left_side, left_thickness_of_top_side + left_side_skew_adjust - 1);
            ctx.lineTo( left_thickness_of_left_side, left_height_of_image);
            ctx.lineTo(0, left_height_of_image - 8);
            ctx.closePath();
         },
         preventDefault: false,
         x: 6,
         y: 0,
      })

      const CanvasWrappedLeftImageObj = new Konva.Image({
         x: 0,
         y: 0,
         skew:{ x: 0, y: .8 },
         image: _mainImage,
         width: $tmp_width,
         height: $tmp_height,
         preventDefault: false,
         fill: CanvasSidesColor,
         opacity: CanvasSidesOpacity,
      })


   /*--========== TOP ==========--*/

   const top_thickness_of_top_side   = 6
   const top_thickness_of_left_side  = 8

   const CanvasWrappedTopClipGroup = new Konva.Group({
      clipFunc: function(ctx){
         ctx.moveTo( top_thickness_of_left_side - 2,0);
         ctx.lineTo( width_of_image - top_thickness_of_left_side,0);
         ctx.lineTo( width_of_image, top_thickness_of_top_side);
         ctx.lineTo( top_thickness_of_left_side + 6, top_thickness_of_top_side);
         ctx.closePath();
      }, x:0, y:4
   })

   const CanvasWrappedTopImageObj = new Konva.Image({
      x: 0,
      y: 0,
      opacity: CanvasSidesOpacity,
      skew:{ x: 1.2, y: 0 },
      image: _mainImage,
      width: $tmp_width,
      height: $tmp_height,
      preventDefault: false,
      fill: CanvasSidesColor,
   })

   /*--========== DRAW FINAL PRODUCT ==========--*/

   CanvasWrappedLeftClipGroup.add( CanvasWrappedLeftImageObj )

   CanvasWrappedTopClipGroup.add( CanvasWrappedTopImageObj )

   mainClipGroup.add( mainImgObj )


   main_flatbed_layer.add(
      CanvasWrappedLeftClipGroup, CanvasWrappedTopClipGroup, mainClipGroup
   );

   konva_flatbed_stage.add( main_flatbed_layer ); // add the layer to the stage
//   tmp_configKonvaStage.draw();

   clg('**** SET FRAME COMPLETE *******',1);

   return

}

/**==============================================================================
 ||
 ||   create base64 image from Konva
 ||
 k===============================================================================*/
function image_tobase64( $image, $output_image ){

   var tmp_img = new Image();
   var $output_img = new Image();

   tmp_img.crossOrigin = 'Anonymous';

   tmp_img.onload = function( output_image_base64 ){

      var canvas = document.createElement('CANVAS');
      var ctx = canvas.getContext('2d');

      canvas.height = this.naturalHeight;

      canvas.width = this.naturalWidth;

      ctx.drawImage(this, 0, 0);

      $output_image.src = canvas.toDataURL();

   };

   tmp_img.src = $image;

   return

}

/**==============================================================================
||
||
||  add items to var
||
||
*==============================================================================*/
function put_product_config_details()
{
   $ProductJSON_obj = getLsProductJSON() // will come back as an object
   $ProductJSON_obj = get_print_dimension( $ProductJSON_obj ); // set the total width of the products

   var data = {
      action: 'ajax_put_product_config_details',

      // this is the data to be sent to the ajax routine
      data: {
         user_guid:          myAjax.user_guid,
         order_guid:         myAjax.order_guid,
         item_guid:          myAjax.item_guid,
         img_guid:           $ProductJSON_obj.img_guid,
         item_price:         $ProductJSON_obj.pricingJSON.print_price,
         item_qty:           1,
         ProductJSON:        JSON.stringify($ProductJSON_obj), // send as JSON string
         img_preview_blob:   'none',//_productPreviewImage,
         modified:           '',
      }
   };

   $.post( myAjax.do_ajax, data, function (results) { // submit the ajax request

      if(results == 'fail'){
         clg('Ajax return request - FAILED',1)
      } else {
         clg( 'Results from the AJAX core ==' + results);
         window.localStorage.removeItem('ProducJSON');
         if (myAjax.woocommerce_cart_redirect_after_add == 'yes') {
            //console.log('Redirect to Cart ...');
             location.href = myAjax.woocommerce_cart_url;
         } else {
             //console.log('Redirect to upload page ...');
             location.href = myAjax.upload_page + '&added=1';
         }
      }
   })
   .then(function(){

   })
   .done(function(){
      clg('Ajax - done',1);
   })
   .fail(function( jqxhr, status, exception ){
      clg('Ajax - error:==='+exception,1);
   })
   .always(function(){
      clg('Ajax - finished',1);
   });
}

/**==============================================================================
 ||  calculate the total width of the product with frame and matting
 *==============================================================================*/
function getImageWidth( $ProductJSON ){

   if ( $ProductJSON.img_width){ // check to see if there is a valid image_width
      var image_width = parseInt( $ProductJSON.img_width )
   } else {
      var image_width = 0 // otherwise initialize it
   }

   var mb_width = parseInt( $ProductJSON.mb1_width ) // get the current mat board width
   var frame_width = parseInt( $ProductJSON.frame_width ) // get the current frame width

   var final_total_width = ( image_width + ( mb_width * 2 ) + ( frame_width * 2 ) ) / 72 // total everything together

   var $final_total_width = final_total_width

   $ProductJSON.frame_size_width = $final_total_width

   return $ProductJSON

};

/**==============================================================================
 ||  calculate the total width of the product with frame and matting
 *==============================================================================*/
function getImageHeight( $ProductJSON ){

   if ( $ProductJSON.img_height){
      var image_height = parseInt( $ProductJSON.img_height )
   } else {
      var image_height = 0
   }

   var mb_width = parseInt( $ProductJSON.mb1_width ) // this.widthOfMB
   var frame_width = parseInt( $ProductJSON.frame_width )

   var _final_total_height = ( image_height + ( mb_width * 2 ) + ( frame_width * 2 ) ) / 72

   var _final_total_height = ( Math.round( _final_total_height * 4) / 4 ).toFixed(2)

   $ProductJSON.frame_size_height = Number( _final_total_height );

   return $ProductJSON

};

/**==============================================================================
 ||  calculate the total width of the product with frame and matting
 *==============================================================================*/
function get_print_dimension( $tmp_productJSON ){

   function _get_image_height(){

      if ($tmp_productJSON.img_height){
         var image_height = $tmp_productJSON.print_height * 72
      } else {
         var image_height = 0
      }

      var mb_width = parseInt( $tmp_productJSON.mb1_width ) // this.widthOfMB
      var frame_width = parseInt( $tmp_productJSON.frame_width )

      // calculates the outer dimension frame height
      var _final_total_height = ( image_height + ( mb_width * 2 ) + ( frame_width * 2 ) ) / 72
      _final_total_height = ( Math.round( _final_total_height * 4) / 4 ).toFixed(2)
      $tmp_productJSON.frame_size_height = Number( _final_total_height )

   }

   function _get_image_width(){

      if ( $tmp_productJSON.img_width){
         var image_width = $tmp_productJSON.print_width * 72
      } else {
         var image_width = 0
      }

      var mb_width = parseInt( $tmp_productJSON.mb1_width ) // this.widthOfMB
      var frame_width = parseInt( $tmp_productJSON.frame_width )

      // calculates the outer dimension frame width
      var _final_total_width = ( image_width + ( mb_width * 2 ) + ( frame_width * 2 ) ) / 72
      _final_total_width = ( Math.round( _final_total_width * 4) / 4 ).toFixed(2)
      $tmp_productJSON.frame_size_width = Number( _final_total_width )

   }

   _get_image_height();
   _get_image_width();

   $tmp_productJSON.outer_dimension = $tmp_productJSON.frame_size_width + ' in x ' + $tmp_productJSON.frame_size_height + ' in';
   $tmp_productJSON.print_dimension = $tmp_productJSON.print_width + ' in x '+$tmp_productJSON.print_height + ' in';
   $tmp_productJSON.glass_dimension = (Number($tmp_productJSON.print_width) + 4) + ' in x ' + (Number($tmp_productJSON.print_height) + 4) + ' in';

   saveProductJsonToLS( $tmp_productJSON );

   return $tmp_productJSON;

};

/**==============================================================================
 ||  add items to var
 *==============================================================================*/
function getDataURL(){

   _productPreviewImage = tmp_configKonvaStage.toDataURL();

   $('#converted-image').attr('src', _productPreviewImage);

};


/**==============================================================================
 ||  add items to var
 *==============================================================================*/
function change_frame( $tmp_id ){

   $tmp_productJSON = getLsProductJSON();

   var $tmp_frame_data = frame_list;

   var $tmp_data_obj = $tmp_frame_data[ $tmp_id ];

   $tmp_productJSON.frame_border_img = $tmp_data_obj.frame_border_img;
   $tmp_productJSON.frame_description = $tmp_data_obj.frame_description;
   $tmp_productJSON.frame_guid = $tmp_data_obj.frame_guid;
   $tmp_productJSON.frame_number = $tmp_data_obj.frame_number;

   load_images( $tmp_productJSON );

   // frame preview One
   $('#list-item-frame').html( $tmp_data_obj.frame_description );
   $('#list-item-frame-mobile').html( $tmp_data_obj.frame_description );

   $("#frame_preview_one").attr('src','../../../uploadhandler/uploads/image_assets/' + $tmp_data_obj.frame_preview_img_one);
   $("#frame_preview_one_sub").attr('src','../../../uploadhandler/uploads/image_assets/' + $tmp_data_obj.frame_preview_img_one);

   $("#carousel-image-one").attr('src','../../../uploadhandler/uploads/image_assets/' + $tmp_data_obj.frame_preview_img_one);
   $("#carousel-image-two").attr('src','../../../uploadhandler/uploads/image_assets/' + $tmp_data_obj.frame_preview_img_two);
   $("#carousel-image-three").attr('src','../../../uploadhandler/uploads/image_assets/' + $tmp_data_obj.frame_preview_img_three);

   $('#frame-preview-modal').data('frame-description', $tmp_data_obj.frame_description );

   if( detector.mobile() ){
      $( document.getElementById('mobile-frame-description') ).html( $tmp_data_obj.frame_description );
   }

   saveProductJsonToLS( $tmp_productJSON );

   clg('Frame == '+ $tmp_data_obj.frame_border_img,1 );

};

/**=====================================================================================================================
||
||  this function loads the images then calls set_frame to render the konva elements
||
*=====================================================================================================================*/
function __load_fb_images( $_productJSON ) {

   function load_main_image () {
      clg('################### load_main_image ################');

      return new Promise((resolve, reject)=>{
         _mainImage.onload = function(){

            clg('################### PROMISE MAIN IMAGE LOADED ################');
            resolve('ok')

         };

         _mainImage.src = '../../../uploadhandler/uploads/' + $_productJSON.img_scaled_filename;

         if(_mainImage.complete){

            clg('################### _mainImage.complete == MAIN IMAGE LOADED ################');

            resolve('ok')
         }
      })
   };

   function load_border_image () {
      return new Promise((resolve, reject)=>{
      })
   };

   function load_background_image () {
      return new Promise((resolve, reject)=>{
      })
   };

   // returns a promise
   async function wrapperFunc () {
      try {
         let r1 = await load_main_image();  // wait for main image to load
         //let r2 = await load_border_image();
         //let r3 = await load_background_image();
         // now process r2
         return r1;     // this will be resolved value of the returned promise
      } catch (e) {
         console.log(e);
         throw e;      // let caller know the promise rejected with this reason
      }
   }

   wrapperFunc().then(result => {

      clg('################### ALL DONE ################', 1);
      saveProductJsonToLS( $_productJSON );
      //set_flatbed();
      set_canvas_wrapped();

   }).catch(err => {

      clg('################### ERROR ################', 1);

   });
}

/**=====================================================================================================================
 ||
 ||  this function loads the images then calls set_frame to render the konva elements
 ||
 *=====================================================================================================================*/
function load_images( $_productJSON ) {

   function load_main_image () {
      clg('################### load_main_image ################');

      return new Promise((resolve, reject)=>{
         _mainImage.onload = function(){

            clg('################### PROMISE MAIN IMAGE LOADED ################');
            resolve('ok')

         };

         _mainImage.src = '../../../uploadhandler/uploads/' + $_productJSON.img_scaled_filename;

         if(_mainImage.complete){

            clg('################### _mainImage.complete == MAIN IMAGE LOADED ################');

            resolve('ok')
         }
      })
   };

   function load_border_image () {
      clg('################### load_border_image ################');

      return new Promise((resolve, reject)=>{
         _borderImage.onload = function(){

            clg('################### PROMISE BORDER IMAGE LOADED ################');
            resolve('ok')
         };
         _borderImage.src = '../../../uploadhandler/uploads/image_assets/'+ $_productJSON.frame_border_img;

         if(_borderImage.complete){

            clg('################### _borderImage.complete == BORDER IMAGE LOADED ################');
            resolve('ok')
         }
      })
   };

   function load_background_image () {
      clg('################### load_background_image ################');

      return new Promise((resolve, reject)=>{
         _backgroundImage.onload = function(){

            clg('################### PROMISE BACKGROUND IMAGE LOADED ################');
            resolve('ok')
         };
         _backgroundImage.src = '../../../uploadhandler/uploads/image_assets/background-wall-01.jpg';

         if(_backgroundImage.complete){

            clg('################### _backgroundImage.complete == BACKGROUND IMAGE LOADED ################');
            resolve('ok')
         }
      })
   };

   // returns a promise
   async function wrapperFunc () {
      try {
         let r1 = await load_main_image();  // wait for main image to load
         let r2 = await load_border_image();
         let r3 = await load_background_image();
         // now process r2
         return r1;     // this will be resolved value of the returned promise
      } catch (e) {
         console.log(e);
         throw e;      // let caller know the promise rejected with this reason
      }
   }

   wrapperFunc().then(result => {

      clg('################### ALL DONE ################', 1);
      // got final result
      saveProductJsonToLS( $_productJSON );

      set_frame(); // this must be run only after the load_images

   }).catch(err => {

      clg('################### ERROR ################', 1);
      // got error

   });
}

// ************ THESE FUNCTIONS ARE RELATED TO FILE UPLOADS ***************

/*************************************************************************
 ||
 ||  declare the uploader variable
 ||
 ||
 ||
 +*************************************************************************/
function createFineUploader() {

   $fineUploader = new qq.FineUploaderBasic({

      element: document.getElementById('fine-uploader-element'),

      autoUpload: false,

      debug: true,

      multiple: true,

      request: {

         endpoint: '../../../uploadhandler/endpoint.php',
         method: 'POST'

      },
      cors: {

         expected: true,
      },
      chunking: {

         enabled: true,

         concurrent: {enabled: false},

         partSize: 4000000,

         success: {endpoint: '../../../uploadhandler/endpoint.php?done'}
      },
      validation: {

         //acceptFiles: 'image/*',
         acceptFiles: ['jpg', 'jpeg', 'svg', 'gif', 'png', 'pdf', 'tif', 'tiff']
      },
      callbacks: {
         onSubmit: function ($id, $name) {

            onUploadSubmit($id, $name);

         },
         onSubmitted: function ($id, $name) {

            onUploadSubmitted($id, $name);

         },
         onUpload: function ($id, $name) {

            onUploadUpload($id, $name);

         },
         onStatusChange: function ($id, $oldStatus, $newStatus) {

            onUploadStatusChange($id, $oldStatus, $newStatus);

         },
         onProgress: function ($id, $name, $uploadedBytes, $totalBytes) {

            onUploadProgress($id, $name, $uploadedBytes, $totalBytes);

         },
         onTotalProgress: function ($totalUploadedBytes, $totalBytes) {

            onUploadTotalProgress($totalUploadedBytes, $totalBytes);

         },
         onComplete: function ($id, $name, $responseJSON) {

            onUploadComplete($id, $name, $responseJSON);

         },
         onAllComplete: function ($succeeded, $failed) {

            onUploadAllComplete($succeeded, $failed);

         },
         onCancel: function ($id, $name) {

            onUploadCancel($id, $name);

         },
         onAutoRetry: function ($id, $name, $attempts) {

            onUploadAutoRetry($id, $name, $attempts);

         },
         onError: function ($id, $name, $errorReason) {

            onUploadError($id, $name, $errorReason);

         }
      }
   })
};


/**===============================================================================
 ||
 ||   This is called when the uploader submits a file
 ||
 *===============================================================================*/
function onUploadSubmit( $id, $name ){

   clg('#############################################',1);
   clg('######## FineUploader Submit Event: file-id = '+$id+' file-name=='+$name,1);
   clg('#############################################',1);

   // this is here becuase it competes with save crop calling same func but needing diff results
   if( $('#crop-reset').css('display') == 'none' ){

      $('#begin-crop').css('display', 'inline-block');

   }

   // display the 'Begin Upload' button after a file is submitted to FineUploader
   $('#begin-upload').css('display','block'); //

   if( $globalUUID == null ){

      // this sets the UUID from the scaled file since it's always before the original file
      $globalUUID = get_upload_file_uuid($id);

   } else {

      // set the original file with the UUID established by it's scaled file.
      $fineUploader.setUuid($id, $globalUUID)

   }

   return;

}

/**===============================================================================
 ||
 ||   This is called when the uploader submits a file
 ||
 *===============================================================================*/
function onUploadSubmitted( $id, $name ){

   clg( '######## FineUploader Submited Event: file-id = '+$id+' file-name=='+$name,1);

   return;

}

/**===============================================================================
 ||
 ||   This is called when the uploader submits a file
 ||
 *===============================================================================*/
function onUploadUpload( $id, $name ){

   clg( '######## FineUploader Upload Event: file-id = '+$id+' file-name=='+$name,1);

   return;

}

/**===============================================================================
 ||
 ||   This is called when the uploader submits a file
 ||
 *===============================================================================*/
function onUploadStatusChange( $id, $oldStatus, $newStatus ){

   clg( '######## FineUploader Status Change Event',1);

   return;

}

/**===============================================================================
 ||
 ||   This is called when the uploader submits a file
 ||
 *===============================================================================*/
function onUploadProgress( $id, $name, $uploadedBytes, $totalBytes ){

   clg( '######## FineUploader Upload Progress Event: uploaded = '+$uploadedBytes+' total=='+$totalBytes,1);

//   if( $id == 0){

   var _percent_uploaded = parseInt(  ( $uploadedBytes / $totalBytes ) * 100  );

   setTimeout(()=>{

      $(".progress-bar").css("width", _percent_uploaded + "%").text( _percent_uploaded + " %");

   },100);

//   }

   return;

}

/**===============================================================================
 ||
 ||   This is called when the uploader submits a file
 ||
 *===============================================================================*/
function onUploadTotalProgress( $totalUploadedBytes, $totalBytes ){

   clg( '######## FineUploader Upload TotalProgress Event: uploaded = '+$totalUploadedBytes+' file-name=='+$totalBytes,1);

   return;

}

/**===============================================================================
 ||
 ||   This is called when the uploader submits a file
 ||
 *===============================================================================*/
function onUploadComplete( $id, $name, $responseJSON ){

   clg( '######## FineUploader Upload Complete Event: file-id = '+$id+' file-name=='+$name,1);
   clg( '######## FineUploader Upload Complete Event: responseJSON = '+ JSON.stringify($responseJSON,null,2),1);

   $setUUID = false;

   if(_filename_selected_for_upload == $name){

      process_image();

   }

   return;

}

/**===============================================================================
 ||
 ||   This is called when the uploader submits a file
 ||
 *===============================================================================*/
function onUploadAllComplete( $succeeded, $failed ){

   clg( '###################################################################',1);
   clg( '######## FineUploader UPLOAD-ALL-COMPLETE ##### Event: succeeded = '+$succeeded+' failed='+$failed,1);
   clg( '###################################################################',1);

   return;

}

/**===============================================================================
 ||
 ||   This is called when the uploader submits a file
 ||
 *===============================================================================*/
function onUploadCancel( $id, $name ){

   clg( '######## FineUploader Upload Cancel Event: file-id = '+$id+' file-name=='+$name,1);

   if(cropper){
      clg('##### CROPPER IS ENABLED #######',1);
      crop_reset();
   } else {
      clg('##### CROPPER IS NOT ENABLED #######',1);
   }

   // this clears the file input - file name
   var $el = $('#choose-Files');
   $el.wrap('<form>').closest('form').get(0).reset();
   $el.unwrap();

   // clear the image selected for upload
   $('#selected-upload-image').prop('src', _blankImgPlaceholder);

   // hide the following buttons
   $('#begin-crop').css('display', 'none');
   $('#begin-upload').css('display', 'none');


   return;

}

/**===============================================================================
 ||
 ||   This is called when the uploader submits a file
 ||
 *===============================================================================*/
function onUploadAutoRetry( $id, $name, $attempts ){

   clg( '######## FineUploader Upload onUploadAutoRetry Event',1);

   return;

}

/**===============================================================================
 ||
 ||   This is called when the uploader submits a file
 ||
 *===============================================================================*/
function onUploadError( $id, $name, $errorReason ){

   clg( '######## FineUploader Upload ERROR Event: file-id = '+$id+' file-name=='+$name,1);
   clg( '######## FineUploader Upload ERROR Event: reason = '+$errorReason,1);

   return;

}

/**===============================================================================
 ||
 ||   This function gets the UUID of the file being uploaded
 ||
 *===============================================================================*/
function get_upload_file_uuid( $id ){

   return $fineUploader.getUuid( $id );

}

/*************************************************************************
 ||
 ||  Attach the click event to begin the upload to the Dom element
 ||
 +*************************************************************************/
function create_scaled_filename( $tmp_filename, $flag ){

   if($flag == 1){

      // this creates the scaled filename

      var new_filename = $tmp_filename.replace(/\.[^/.]+$/, "") + ' (small).jpg';

   } else if( $flag == 2 ){

      // convererts the ordiginal with orig

      var $tmp_filename_only = $tmp_filename.replace(/\.[^/.]+$/, "");
      var $ext = $tmp_filename.substring($tmp_filename.lastIndexOf('.')+1, $tmp_filename.length) || $tmp_filename;

      var new_filename = $tmp_filename_only + ' (orig).' + $ext;

   } else if( $flag == 3 ) {

      var $tmp_filename_only = $tmp_filename.replace(/\.[^/.]+$/, "");
      var $ext = $tmp_filename.substring($tmp_filename.lastIndexOf('.') + 1, $tmp_filename.length) || $tmp_filename;

      var new_filename = $tmp_filename_only + ' (cropped).' + $ext;

   } else if( $flag == 4 ) {

      var $tmp_filename_only = $tmp_filename.replace(/\.[^/.]+$/, "");
      var $ext = $tmp_filename.substring($tmp_filename.lastIndexOf('.') + 1, $tmp_filename.length) || $tmp_filename;

      var new_filename = $tmp_filename_only + ' (orig-scaled).' + $ext;

   }

   return new_filename;

}

/*==============================================================================
 |   Rejects files that are not supported
 *==============================================================================*/
function getFileExtension(filename){

   var $allowedExts = [ 'jpg', 'jpeg', 'gif', 'png' ];
   var $allowedExtsSet = new Set( $allowedExts );

   var ext = /^.+\.([^.]+)$/.exec(filename);

   if( $allowedExtsSet.has( ext[1] )  ) {

      return

   } else {

      clg( 'INVALID FILE', 1 );
      return false
      
   }

}

/*==============================================================================
|
|   This method is invoked when the "Select File to Upload is selected" for LOCAL
|
*==============================================================================*/
function select_file_to_upload_framed(e, $mode){

    $tmp_productJSON = getLsProductJSON(); // get the productJSON from localStorage

    $file = e.files[0]; // get the file object from the passed in 'e' object

    $setUUID = false;
    $globalUUID = null;
    $cropped_image_upload = false;

    _cropped_offest_adjust = 0; // reset the offset adjust value
    _filename_selected_for_upload = $file.name; // assign the filename to the var

    // check if the file selected for upload has a file extension that is supported
    if ( getFileExtension( _filename_selected_for_upload.toLocaleLowerCase() ) == false ){
        // show modal that file is not supported
        $('#not-supported-file-modal').modal('show')
        // exit the function
        return false
    }

    // init the orientation var
    var $tmp_orientation = 0;

    //. . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . .
    // run the LoadImage JS external library
    // https://github.com/blueimp/JavaScript-Load-Image
    loadImage.parseMetaData( $file, function ( data ) {

        // checks to see if the image has a valid exif data
        if( typeof( data.exif ) !== 'undefined' ){

            // get the orientation value
            $tmp_orientation = data.exif[0x0112];

        }

        load_image() // call the load image func

        function load_image(){

            // run the load image library method
            loadImage( $file, function ( img ) {

                var canvas;

                if ($tmp_orientation == 6) { // if the orientation is off...

                    // run the loadimg routine to fix the orientation
                    // save the result to the canvas
                    canvas = loadImage.scale(img,
                        {orientation: $tmp_orientation, canvas: true}
                    );

                    // convert the canvas to a blob
                    $global_file_image_selected = canvas.toDataURL();

                    // attach the blob to the upload img element
                    $('#selected-upload-image').attr("src", $global_file_image_selected);
                    // attach the blob to the cropper element
                    $('#selected-upload-image-to-crop').attr("src", $global_file_image_selected);

                    // set the proper productJSON values
                    $tmp_productJSON.img_width = canvas.width;
                    $tmp_productJSON.img_height = canvas.height;
                    $tmp_productJSON.original_img_width = canvas.width;
                    $tmp_productJSON.original_img_height = canvas.height;
                    $tmp_productJSON.img_orientation = check_orientation( $tmp_productJSON );

                } else { // if no exif data or orientation good, run the old fashion way

                    // create the reader object
                    const reader = new FileReader();

                    // this is called after reading the file is done
                    reader.onload = function(e){

                        var $tmp_file_image_selected = reader.result;

                        // init an image obj to store the selected file
                        var $tmp_img = new Image();
                        // load the img object with the reader result
                        $tmp_img.src = e.target.result;
                        // set the proper productJSON values
                        $tmp_img.onload = function(){
                            $tmp_productJSON.img_width = this.width;
                            $tmp_productJSON.img_height = this.height;
                            $tmp_productJSON.original_img_width = this.width;
                            $tmp_productJSON.original_img_height = this.height;
                            $tmp_productJSON.img_orientation = check_orientation( $tmp_productJSON );
                        };

                        $global_file_image_selected = $tmp_file_image_selected;

                        $('#selected-upload-image').attr("src", $tmp_file_image_selected);
                        $('#selected-upload-image-to-crop').attr("src", $tmp_file_image_selected);

                    };
                    // read the file into the reader object
                    reader.readAsDataURL($file); // image will not display without this line
                }
            });

            // set the page elements
            $('#upload-crop-image-container').removeClass('hide');

            $('#image-upload-container').removeClass( 'col-md-6' );

            $('#local_picker').addClass('hide');

            // get the name of the file to upload
            $('#selected-file-to-upload-filename').text( _filename_selected_for_upload );

            $('#btn-begin-crop-wrapper').removeClass('hide');

            $('#btn-reset-upload-wrapper').removeClass('hide');

            $('#btn-begin-upload-wrapper').removeClass('hide');


            // clear out any old files loaded from a previous operation
            $fineUploader.clearStoredFiles();
            // add the uncropped file to the uploader
            $fineUploader.addFiles( $file );
        }
    });

    // ====================================================================================
    // because this is a load event - it will run independant of anything else
    $( document.getElementById( 'selected-upload-image' )).load( function(){

        //--------------------------------------------------------------------------------

        function func_a() {
            return new Promise(resolve => {

                //
                $tmp_cropper_wrapper_height = $('#cropper-image-wrapper').height();
                //
                $('#selected-image-upload-card-body').height( $tmp_cropper_wrapper_height + 20 )
                //
                resolve( $tmp_cropper_wrapper_height )
            });
        }

        function func_b() {
            return new Promise(resolve => {

                // init the cropper with the calculated image aspect ratio

                if( $mode == 'express'){

                    _productMode = 'express';
                    init_cropper( 1 )

                } else {

                    _productMode = 'custom'
                    init_cropper( null )

                }

                //
                resolve('ok')
            });
        }

        function func_c() {
            return new Promise(resolve => {
                //
                $tmp_productJSON = uploader_calcPrintSizes( $tmp_productJSON )
                //
                saveProductJsonToLS( $tmp_productJSON )
                //
                resolve('ok')
            });
        }

        function func_d() {
            return new Promise(resolve => {

                $('html').animate({scrollTop: 280}, 'slow');//IE, FF
                $('body').animate({scrollTop: 280}, 'slow');//chrome, don't know if Safari works

                resolve('ok')
            });
        }

        // returns a promise
        async function wrapperFunc() {
            try {
                let r1 = await func_a();
                let r2 = await func_b( r1 );
                let r3 = await func_c();
                let r4 = await func_d();
                // now process r2
                return r1;     // this will be resolved value of the returned promise
            } catch(e) {
                console.log(e);
                throw e;      // let caller know the promise rejected with this reason
            }
        }

        setTimeout(()=>{
            wrapperFunc().then(result => {
                //
            }).catch(err => {
                // got error
            });
        },500)

        //--------------------------------------------------------------------------------

    })// emd of the load function

}

/*==============================================================================
|
|   This method is invoked when the "Select File to Upload is selected" for FILESTACK
|
*==============================================================================*/
function select_file_to_upload_framed__filestack(data, $mode)
{
    console.log(data);

    $tmp_productJSON = getLsProductJSON(); // get the productJSON from localStorage

    $file = data.filesUploaded[0].url;
    $file = $file.replace('https://cdn.filestackcontent.com/', 'https://cdn.filestackcontent.com/rotate=deg:exif/'); // <-- rotate image per EXIF
    console.log($file);

    $filename = data.filesUploaded[0].filename;
    console.log($filename);

    $setUUID = false;
    $globalUUID = null;
    $cropped_image_upload = false;

    _cropped_offest_adjust = 0; // reset the offset adjust value
    _filename_selected_for_upload = $filename; // assign the filename to the var

   //. . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . .
   // run the LoadImage JS external library
   // https://github.com/blueimp/JavaScript-Load-Image
    load_image(); // call the load image function

    function load_image()
    {
        // run the load image library method
        loadImage(
            $file,
            function (img, data) {
                //document.body.appendChild(img); // test
                console.log(img);
                console.log(data);

                $('#selected-upload-image').attr("src", img.src);
                $('#selected-upload-image-to-crop').attr("src", img.src);

                $tmp_productJSON.img_width = img.width;
                $tmp_productJSON.img_height = img.height;
                $tmp_productJSON.original_img_width = img.naturalHeight;
                $tmp_productJSON.original_img_height = img.naturalWidth;
                $tmp_productJSON.img_orientation = check_orientation( $tmp_productJSON );

                $global_file_image_selected = img.src;

                // set the page elements
                $('#filestack_picker').hide();
                $('#upload-crop-image-container').removeClass('hide');
                $('#image-upload-container').removeClass( 'col-md-6' );
                $('#local_picker').addClass('hide');

                // get the name of the file to upload
                $('#selected-file-to-upload-filename').text( _filename_selected_for_upload );
                $('#btn-begin-crop-wrapper').removeClass('hide');
                $('#btn-reset-upload-wrapper').removeClass('hide');
                $('#btn-begin-upload-wrapper').removeClass('hide');

                // clear out any old files loaded from a previous operation
                $fineUploader.clearStoredFiles();
                // add the uncropped file to the uploader
                $fineUploader.addFiles( img.src )
            },
            { maxWidth: 600, meta: true }
        );
    }


   // ====================================================================================
   // because this is a load event - it will run independant of anything else
   $( document.getElementById( 'selected-upload-image' )).load( function(){

      //--------------------------------------------------------------------------------

      function func_a() {
         return new Promise(resolve => {

            //
            $tmp_cropper_wrapper_height = $('#cropper-image-wrapper').height();
            //
            $('#selected-image-upload-card-body').height( $tmp_cropper_wrapper_height + 20 )
            //
            resolve( $tmp_cropper_wrapper_height )
         });
      }

      function func_b() {
         return new Promise(resolve => {

            // init the cropper with the calculated image aspect ratio
            if( $mode == 'express'){
               _productMode = 'express';
               init_cropper( 1 )
            } else {
               _productMode = 'custom';
               init_cropper( null )
            }

            resolve('ok')
         });
      }

      function func_c() {
         return new Promise(resolve => {
            //
            $tmp_productJSON = uploader_calcPrintSizes( $tmp_productJSON )
            //
            saveProductJsonToLS( $tmp_productJSON )
            //
            resolve('ok')
         });
      }

      function func_d() {
         return new Promise(resolve => {

            $('html').animate({scrollTop: 280}, 'slow');//IE, FF
            $('body').animate({scrollTop: 280}, 'slow');//chrome, don't know if Safari works

            resolve('ok')
         });
      }

      // returns a promise
      async function wrapperFunc() {
         try {
            let r1 = await func_a();
            let r2 = await func_b( r1 );
            let r3 = await func_c();
            let r4 = await func_d();
            // now process r2
            return r1;     // this will be resolved value of the returned promise
         } catch(e) {
            console.log(e);
            throw e;      // let caller know the promise rejected with this reason
         }
      }

      setTimeout(()=>{
         wrapperFunc().then(result => {
            //
         }).catch(err => {
            // got error
         });
      },500)

      //--------------------------------------------------------------------------------

   })// emd of the load function

}

/**-------------------------------------------------------------------------------
|   begin the upload
*-------------------------------------------------------------------------------*/
function beginUpload(){

   $resizer = pica({ features: [ 'js', 'wasm', 'ww', 'cib' ] })

   $('#upload-in-process-modal').modal('show')

   if($cropped_image_upload == false){

      var $selected_file_to_upload = $('#selected-upload-image');

      $scaled_sizes = scale_for_preview( $selected_file_to_upload.width(), $selected_file_to_upload.height() );

      var offScreenCanvas = document.createElement('canvas')
      offScreenCanvas.width  = $scaled_sizes['width'];
      offScreenCanvas.height = $scaled_sizes['height'];

      var $tmp_image = new Image();

      $tmp_image.onload = function(){

         $resizer.resize( this , offScreenCanvas, {
            quality: 3,
            //unsharpRadius: 0.6,
            //unsharpThreshold: 2
         }).then(( result )=> $resizer.toBlob(result, 'image/jpeg',.75))
            .then((blob) => {

               $fineUploader.addFiles([
                  {
                     blob: blob,
                     name: create_scaled_filename( _filename_selected_for_upload, 1 ),
                  },
               ]);
               clg('############DATA URL TO BLOB COMPLETE###############',1)
            }).then(()=>{
            $fineUploader.uploadStoredFiles();
         })

      }

      $tmp_image.src = $global_file_image_selected;

   } else {

      $fineUploader.uploadStoredFiles();

   }

}


/**-------------------------------------------------------------------------------
 ||
 ||   Scale down
 ||
 *-------------------------------------------------------------------------------*/
function scale_for_preview( $nat_width, $nat_height ){

   if( $nat_width > $nat_height ){
      // landscape

      var $aspect_ratio = $nat_height / $nat_width

      var $new_height = 500 * $aspect_ratio;

      return { 'width' : 500, 'height': $new_height }

   } else {


      var $aspect_ratio = $nat_width / $nat_height

      var $new_width = 400 * $aspect_ratio;

      return { 'width' : $new_width, 'height':400 }

   }
}



/**-------------------------------------------------------------------------------
 ||
 ||   fhid
 ||
 *-------------------------------------------------------------------------------*/
function reset_upload() {

   // if there was a previous image - make sure to restore it to view mode.
   $fineUploader.clearStoredFiles();
   $fineUploader.reset();

   $('#local_picker').removeClass('hide');

   $('#upload-crop-image-container').addClass('hide');

   $('#selected-upload-image').removeAttr('src')

   $('#btn-begin-crop-wrapper').addClass('hide');

   $('#btn-reset-upload-wrapper').addClass('hide');

   $('#btn-begin-upload-wrapper').addClass('hide');

}

/**-------------------------------------------------------------------------------
 ||
 ||   This method converts the DataUrl to a Blob
 ||
 *-------------------------------------------------------------------------------*/
function dataURLtoBlob(dataurl) {

   var arr = dataurl.split(','), mime = arr[0].match(/:(.*?);/)[1],
      bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
   while(n--){
      u8arr[n] = bstr.charCodeAt(n);
   }
   return new Blob([u8arr], {type:mime});
}

/**------------------------------------------------------------------------------
 |
 |   This function adds the selected file to upload to FineUploader
 |
 *-------------------------------------------------------------------------------*/
function addFilesToFineUploader( $file_type, $file_to_upload ){

   $fineUploader.clearStoredFiles(); // clear out any old files loaded from a previous operation

   if( $file_type == 'non-cropped-file' ){

      // load the files selected - uncropped or rotated
      $fineUploader.addFiles( $file_to_upload )

   } else {
      // load cropped or rotated file.

      var blobImage = dataURLtoBlob( $croppedImage );

      $fineUploader.addFiles({blob:blobImage, name: _filename_selected_for_upload})

   }

   return

}

/**------------------------------------------------------------------------------
 |
 |   This function crops the file
 |
 *-------------------------------------------------------------------------------*/
function init_cropper( $aspect_ratio ) {

   $('#selected-upload-image-wrapper').addClass('hide')
   $('#cropper-image-wrapper').removeClass('hide')


   var $cropper_image = document.getElementById('selected-upload-image-to-crop');

   $('#btn-begin-upload').text('All Done and Upload');

   if( window.innerWidth < 500 ){

      _selected_upload_image_wrapper_display_width = 290;
      _selected_upload_image_wrapper_display_height = 290;

      $('#image-cropper-card-body').addClass( 'pl-0 pr-0');

   } else {

      _selected_upload_image_wrapper_display_width = 290;
      _selected_upload_image_wrapper_display_height = 290;

   }

   if( $cropper_image.width > $cropper_image.height ){
      // landscape
      _selected_image_for_upload_orig_orientation = 'landscape';
      $('#cropper-image-wrapper').width( _selected_upload_image_wrapper_display_width )
      $('#cropper-image-wrapper').height( _selected_upload_image_wrapper_display_width )

   } else {
      _selected_image_for_upload_orig_orientation = 'portrait';
      $('#cropper-image-wrapper').width( _selected_upload_image_wrapper_display_height )
      $('#cropper-image-wrapper').height( _selected_upload_image_wrapper_display_height )

   }

   cropper = new Cropper($cropper_image, {
      viewMode: 1,
      dragMode: 'move',
      movable: false,
      autoCropArea: 1,
      restore: false,
      modal: true,
      guides: true,
      highlight: true,
      cropBoxMovable: true,
      cropBoxResizable: true,
      zoomable: false,
      rotatable: true,
      background: true,
      toggleDragModeOnDblclick: false,
      initialAspectRatio: $aspect_ratio,
   });

   $('#inputWrapper').addClass('hide');
   $('#btn-begin-crop-wrapper').addClass('hide');
   $('#btn-begin-upload-wrapper').addClass('hide');
   $('#btn-reset-upload-wrapper').addClass('hide');
   $('#crop-action-buttons').removeClass('hide');

   $('#selected-image-upload-card-body').height( _selected_upload_image_wrapper_display_height )

}

/**------------------------------------------------------------------------------
 |
 |   This function rotates the file
 |
 *-------------------------------------------------------------------------------*/
function __re_rotateCrop() {

   $('#btn-begin-upload-crop-wrapper').addClass('hide');

   $('#cropper-image-wrapper').removeClass('hide')
   $('#cropper-result-wrapper').addClass('hide')
   $('#btn-rotate-image-wrapper').removeClass('hide')
   $('#btn-re-rotate-image-wrapper').addClass('hide')
   $('#btn-save-crop-wrapper').removeClass('hide')
   $('#btn-begin-upload-wrapper').addClass('hide')
}

/**------------------------------------------------------------------------------
 |
 |   This function rotates the file
 |
 *-------------------------------------------------------------------------------*/
function rotateCrop(){

   const cropData = cropper.getCropBoxData();

   $('#btn-begin-upload-crop-wrapper').addClass('hide');
   $('#cropper-image-wrapper').removeClass('hide');
   $('#cropper-result-wrapper').addClass('hide');


   if( _selected_image_for_upload_orig_orientation == 'landscape'){

      if( ( _cropped_offest_adjust == 0 ) && ( cropData.top !== 0 ) ){

         _cropped_offest_adjust = cropData.top; // this condition should the offset for the first and only time

      }
   } else {

      if( ( _cropped_offest_adjust == 0 ) && ( cropData.left !== 0 ) ){

         _cropped_offest_adjust = cropData.left; // this condition should the offset for the first and only time

      }
   }
   cropper.clear();
   cropper.enable();
   cropper.rotate(90);
   cropper.crop();

   switch_orientation();

   const imageData = cropper.getImageData();

   if( _selected_image_for_upload_orig_orientation == 'landscape') {

      if ((imageData.rotate == 90) || (imageData.rotate == 270)) {

         cropper.setCropBoxData({
            left: _cropped_offest_adjust,
            width: _selected_upload_image_wrapper_display_height,
            height: _selected_upload_image_wrapper_display_width
         });

      } else {

         cropper.setCropBoxData({
            top: _cropped_offest_adjust,
            height: _selected_upload_image_wrapper_display_width,
            width: _selected_upload_image_wrapper_display_height
         });

      }

   } else {

      if ((imageData.rotate == 90) || (imageData.rotate == 270)) {

         cropper.setCropBoxData({
            top: _cropped_offest_adjust,
            width: _selected_upload_image_wrapper_display_height,
            height: _selected_upload_image_wrapper_display_width
         });

      } else {

         cropper.setCropBoxData({
            left: _cropped_offest_adjust,
            height: _selected_upload_image_wrapper_display_width,
            width: _selected_upload_image_wrapper_display_height
         });

      }

      cropper.enable();
      cropper.crop();
   }

   function switch_orientation(){
      $productJSON = getLsProductJSON();

      $tmp_width = $productJSON.original_img_width;
      $tmp_height = $productJSON.original_img_height;

      // img size
      $productJSON.img_width = $tmp_height;
      $productJSON.img_height =  $tmp_width;
      $productJSON.original_img_width = $tmp_height;
      $productJSON.original_img_height =  $tmp_width;

      // change the orientation
      $productJSON.img_orientation = check_orientation( $productJSON );

      setTimeout(()=>{
         saveProductJsonToLS( $productJSON );
         uploader_calcPrintSizes( $productJSON )
      },200);
   }

}

/**------------------------------------------------------------------------------
 |
 |   This function saves the cropper data
 |
 *-------------------------------------------------------------------------------*/
function single_column_view(){

   $('#upload-crop-image-container').removeClass('hide');

   //$('#image-crop-container').addClass('hide');

   $('#image-upload-container').removeClass( 'col-md-6' );

}

/**------------------------------------------------------------------------------
 |
 |   This function saves the cropper data
 |
 *-------------------------------------------------------------------------------*/
function split_column_view(){

   $('#image-crop-container').removeClass('hide');

   $('#image-upload-container').addClass( 'col-md-6' );

}


/**------------------------------------------------------------------------------
 |
 |   This function saves the cropper data
 |
 *-------------------------------------------------------------------------------*/
// HERE
function saveCroppedCanvas(){

   $tmp_productJSON = getLsProductJSON()

   $cropped_image_upload = true;

   $('#cropper-image-wrapper').addClass('hide');
   $('#cropper-result-wrapper').removeClass('hide')
   $('#btn-rotate-image-wrapper').addClass('hide')
   $('#btn-re-rotate-image-wrapper').removeClass('hide')
   $('#btn-begin-upload-wrapper').removeClass('hide')
   $('#btn-save-crop-wrapper').addClass('hide')

   $resizer = pica({ features: [ 'js', 'wasm', 'ww', 'cib' ] })

   const $scaled_cropped_filename = create_scaled_filename( _filename_selected_for_upload, 1 );
   const $cropped_orig_filename = create_scaled_filename( _filename_selected_for_upload, 3 );

   var offScreenCanvas = document.createElement('canvas')
   offScreenCanvas.width  = 400;
   offScreenCanvas.height = 300;

   // show the upload button underneath the crop result
   $('#btn-begin-upload-crop-wrapper').removeClass('hide');

   $fineUploader.clearStoredFiles();

   //*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-
   // get the cropped image from the cropper
   function func_a() {
      return new Promise (function (resolved, rejected) {

         $croppedImage = cropper.getCroppedCanvas({ // get the details of the cropped image

            // these next two vars dictate the size of the result cropped image.
            // too low will result in a very low res image.
            maxWidth: 4096,
            maxHeight: 4096,
            imageSmoothingEnabled: false,
            imageSmoothingQuality: 'high',

         });
         console.log($croppedImage);
         resolved( $croppedImage )
      })
   }

   //*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-
   // save the cropped image to the uploader
   function func_b( $croppedImage ) {
      return new Promise (function (resolved, rejected) {

         $scaled_sizes = scale_for_preview( $croppedImage.width, $croppedImage.height );

         offScreenCanvas.height = $scaled_sizes['height']
         offScreenCanvas.width = $scaled_sizes['width']

         $resizer.resize( $croppedImage, offScreenCanvas, {
            quality: 3,
            unsharpRadius: 0.6,
            unsharpThreshold: 2
         }).then(( result )=>{

            $fineUploader.addFiles([
               {
                  canvas: result,
                  name: $scaled_cropped_filename,
                  quality: 100,
                  type: 'image/jpeg'
               },
               {
                  canvas: $croppedImage,
                  name: _filename_selected_for_upload,
                  quality: 100,
                  type: 'image/jpeg'
               },
            ]);
            resolved( 'ok' );
         })
      })
   }

   //*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-
   // set the dom elements
   function func_c(){
      return new Promise (function (resolved, rejected) {

         $( document.getElementById('aspect-ratio-buttons-wrapper') ).addClass('hide')
         $( document.getElementById('btn-crop-reset-wrapper') ).addClass('hide')
         $( document.getElementById('btn-save-crop-wrapper') ).addClass('hide')
         $( document.getElementById('btn-begin-upload-wrapper') ).addClass('hide')

         resolved( 'ok' )
      })
   }

   //*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-
   // save the results to screen
   function func_d( $offScreenCanvas, $_croppedImage ) {
      return new Promise (function (resolved, rejected) {

         var $image_src = $offScreenCanvas.toDataURL('image/jpeg');
         $('#cropper-result').attr('src', $image_src );

         _image_width = $_croppedImage.width;
         $tmp_productJSON.img_width = $_croppedImage.width;
         $tmp_productJSON.original_img_width = $_croppedImage.width;

         _image_height = $_croppedImage.height;
         $tmp_productJSON.img_height = $_croppedImage.height;
         $tmp_productJSON.original_img_height = $_croppedImage.height;

         _orig_image_width = $_croppedImage.width;
         _orig_image_height = $_croppedImage.height;

         $tmp_cropper_wrapper_height = $( document.getElementById('cropper-result') ).height();

         $('#selected-image-upload-card-body').height( '' );

         resolved( 'ok' )
      })
   }

   //*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-
   // save the results to screen
   function func_e() {
      return new Promise (function (resolved, rejected) {

         if( $tmp_productJSON.original_img_height > $tmp_productJSON.original_img_width ){
            $tmp_productJSON.img_orientation = 'portrait'
         } else {
            $tmp_productJSON.img_orientation = 'landscape'
         }

         $tmp_productJSON = uploader_calcPrintSizes( $tmp_productJSON )

         resolved( 'ok' )
      })
   }

   async function runSteps(){
      try {
         let r1 = await func_a();
         let r2 = await func_b( $croppedImage );
         let r3 = await func_c();
         let r4 = await func_d( offScreenCanvas, $croppedImage );
         let r5 = await func_e();
         return r4;     // this will be resolved value of the returned promise
      } catch(e) {
         console.log(e);
         throw e;      // let caller know the promise rejected with this reason
      }
   }

   $.LoadingOverlay("show");

   setTimeout(()=>{

      runSteps().then( result =>{

         saveProductJsonToLS( $tmp_productJSON )

         $.LoadingOverlay("hide");

         beginUpload()
         
      })
   },500)


   return

}

function saveCroppedCanvas_o(){

    $tmp_productJSON = getLsProductJSON()

    $cropped_image_upload = true;

    $('#cropper-image-wrapper').addClass('hide');
    $('#cropper-result-wrapper').removeClass('hide')
    $('#btn-rotate-image-wrapper').addClass('hide')
    $('#btn-re-rotate-image-wrapper').removeClass('hide')
    $('#btn-begin-upload-wrapper').removeClass('hide')
    $('#btn-save-crop-wrapper').addClass('hide')

    $resizer = pica({ features: [ 'js', 'wasm', 'ww', 'cib' ] })

    const $scaled_cropped_filename = create_scaled_filename( _filename_selected_for_upload, 1 );
    const $cropped_orig_filename = create_scaled_filename( _filename_selected_for_upload, 3 );

    var offScreenCanvas = document.createElement('canvas')
    offScreenCanvas.width  = 400;
    offScreenCanvas.height = 300;

    // show the upload button underneath the crop result
    $('#btn-begin-upload-crop-wrapper').removeClass('hide');

    $fineUploader.clearStoredFiles();

    //*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-
    // get the cropped image from the cropper
    function func_a() {
        return new Promise (function (resolved, rejected) {

            $croppedImage = cropper.getCroppedCanvas({ // get the details of the cropped image

                // these next two vars dictate the size of the result cropped image.
                // too low will result in a very low res image.
                maxWidth: 4096,
                maxHeight: 4096,
                imageSmoothingEnabled: false,
                imageSmoothingQuality: 'high',

            });
            console.log($croppedImage);
            resolved( $croppedImage )
        })
    }

    //*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-
    // save the cropped image to the uploader
    function func_b( $croppedImage ) {
        return new Promise (function (resolved, rejected) {

            $scaled_sizes = scale_for_preview( $croppedImage.width, $croppedImage.height );

            offScreenCanvas.height = $scaled_sizes['height']
            offScreenCanvas.width = $scaled_sizes['width']

            $resizer.resize( $croppedImage, offScreenCanvas, {
                quality: 3,
                unsharpRadius: 0.6,
                unsharpThreshold: 2
            }).then(( result )=>{

                $fineUploader.addFiles([
                    {
                        canvas: result,
                        name: $scaled_cropped_filename,
                        quality: 100,
                        type: 'image/jpeg'
                    },
                    {
                        canvas: $croppedImage,
                        name: _filename_selected_for_upload,
                        quality: 100,
                        type: 'image/jpeg'
                    },
                ]);
                resolved( 'ok' );
            })
        })
    }

    //*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-
    // set the dom elements
    function func_c(){
        return new Promise (function (resolved, rejected) {

            $( document.getElementById('aspect-ratio-buttons-wrapper') ).addClass('hide')
            $( document.getElementById('btn-crop-reset-wrapper') ).addClass('hide')
            $( document.getElementById('btn-save-crop-wrapper') ).addClass('hide')
            $( document.getElementById('btn-begin-upload-wrapper') ).addClass('hide')

            resolved( 'ok' )
        })
    }

    //*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-
    // save the results to screen
    function func_d( $offScreenCanvas, $_croppedImage ) {
        return new Promise (function (resolved, rejected) {

            var $image_src = $offScreenCanvas.toDataURL('image/jpeg');
            $('#cropper-result').attr('src', $image_src );

            _image_width = $_croppedImage.width;
            $tmp_productJSON.img_width = $_croppedImage.width;
            $tmp_productJSON.original_img_width = $_croppedImage.width;

            _image_height = $_croppedImage.height;
            $tmp_productJSON.img_height = $_croppedImage.height;
            $tmp_productJSON.original_img_height = $_croppedImage.height;

            _orig_image_width = $_croppedImage.width;
            _orig_image_height = $_croppedImage.height;

            $tmp_cropper_wrapper_height = $( document.getElementById('cropper-result') ).height();

            $('#selected-image-upload-card-body').height( '' );

            resolved( 'ok' )
        })
    }

    //*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-
    // save the results to screen
    function func_e() {
        return new Promise (function (resolved, rejected) {

            if( $tmp_productJSON.original_img_height > $tmp_productJSON.original_img_width ){
                $tmp_productJSON.img_orientation = 'portrait'
            } else {
                $tmp_productJSON.img_orientation = 'landscape'
            }

            $tmp_productJSON = uploader_calcPrintSizes( $tmp_productJSON )

            resolved( 'ok' )
        })
    }

    async function runSteps(){
        try {
            let r1 = await func_a()
            //let r2 = await func_b( $croppedImage )
            let r3 = await func_c()
            let r4 = await func_d( offScreenCanvas, $croppedImage )
            let r5 = await func_e()
            return r4;     // this will be resolved value of the returned promise
        } catch(e) {
            console.log(e);
            throw e;      // let caller know the promise rejected with this reason
        }
    }

    $.LoadingOverlay("show");

    setTimeout(()=>{

        runSteps().then( result =>{

            saveProductJsonToLS( $tmp_productJSON )

            $.LoadingOverlay("hide");

            beginUpload()

        })
    },500)


    return

}

/**-------------------------------------------------------------------------------
 |
 |   this fuction runs when the cropping is started
 |
 *-------------------------------------------------------------------------------*/
function crop_start(){

   $('#begin-crop-button').prop('disabled', true);

};

/**-------------------------------------------------------------------------------
 |
 |   this fuction runs when the cropping is started
 |
 *-------------------------------------------------------------------------------*/
function crop_reset(){

   single_column_view();

   cropper.clear();
   cropper.reset();
   cropper.destroy();

   $cropped_image_upload = false;

   $('#cropper-result').prop('src', _blankImgPlaceholder);

   $('#btn-begin-crop-wrapper').removeClass('hide');
   $('#btn-begin-upload-wrapper').removeClass('hide');
   $('#btn-reset-upload-wrapper').removeClass('hide');
   $('#crop-action-buttons').addClass('hide');
   $('#cropper-image-wrapper').addClass('hide');
   $('#cropper-result-wrapper').addClass('hide');
   $('#selected-upload-image-wrapper').removeClass('hide');

   $('#selected-image-upload-card-body').height( $('#selected-upload-image').height() );

};

/*======================================================================================================================
 |
 |  edit the cart
 |
 +=====================================================================================================================*/
function edit_cart_item( e, $item_guid, $product_type ){

   console.log('EDIT CART')

   //e.preventDefault();

   var data = {
      action: 'ajax_edit_cart_item',
      data:{
         item_guid: $item_guid
      }
   }

   $.post( myAjax.do_ajax , data, function (results) { // submit the ajax request

      if(results == 'fail'){

         clg('AJAX REQUEST FAILED',1)

      } else {

          window.localStorage.setItem('ProductJSON', results );

          location.href = myAjax.configurator_page;

         // TODO see about `express`
         /*
         if( $product_type == 'custom' ) {
            location.href = myAjax.configurator_page;
         } else {
            location.href = myAjax.configurator_express;
         }
         */
      }

   })
      .then(function(){

      })
      .done(function(){
         clg('saveAjaxProductJSON Ajax - done',1);
      })
      .fail(function(){
         clg('saveAjaxProductJSON Ajax - error',1);
      })
      .always(function(){
         clg('saveAjaxProductJSON Ajax - finished',1);
      });

}


/**=========================================================
 *
 *
 ----------------------------------------------------------*/
function change_image( $mode ){

   $('#change-image-button').addClass('active-change-image-button-cls');

   if( $mode == 'custom' ){

      window.location.href=myAjax.upload_page

   } else {

      window.location.href=myAjax.uploader_express

   }
}

/*======================================================================================================================
|
|  delete the item in the cart
|
+=====================================================================================================================*/
function delete_cart_item( $item_guid ){

   var data = {
      action: 'ajax_delete_cart_item',
      data:{
         item_guid: $item_guid,
      }
   }

   $.post( myAjax.do_ajax , data, function (results) { // submit the ajax request

      if(results == 'fail'){

         clg('AJAX REQUEST FAILED',1)

      } else {

         get_order_cart_info();

      }

   }).always(function(){
         clg('saveAjaxProductJSON Ajax - finished',1);
   });


}

/**-------------------------------------------------------------------------------
|
|   This method converts the DataUrl to a Blob
|
*-------------------------------------------------------------------------------*/
function init_fineuploader(){

   uploader = new qq.FineUploaderBasic({
      autoUpload: false,
      debug: true,
      multiple: false,
      request: {
         endpoint: '../../../uploadhandler/endpoint.php',
         method: 'POST'
      },
      cors: {
         expected: true,
      },
      chunking: {
         enabled: false,
      },
      validation: {
         acceptFiles: 'image/*',
         acceptFiles: ['jpg', 'jpeg', 'svg', 'gif', 'png']
      },
      callbacks: {
         onSubmit: function ($id, $name) {

            uploader.setUuid($id, $_ProductJSON.img_guid);
         },
         onSubmitted: function ($id, $name){},
         onUpload: function ($id, $name) {},
         onStatusChange: function ($id, $oldStatus, $newStatus) {},
         onProgress: function ($id, $name, $uploadedBytes, $totalBytes) {},
         onTotalProgress: function ($totalUploadedBytes, $totalBytes) {},
         onComplete: function ($id, $name, $responseJSON) {},
         onAllComplete: function ($succeeded, $failed) {

            // todo - trap for failed file upload

            uploader.reset()
            
            // save the product specifics to the db via ajax after the preview file is uploaded
            put_product_config_details();

         },
         onCancel: function ($id, $name) {},
         onError: function ($id, $name, $errorReason) {}
      }
   })
}

/**-------------------------------------------------------------------------------
|
|   set the top mat width
|
*-------------------------------------------------------------------------------*/
function set_top_mat_width( $this )
{
   //clg('******** MAT WIDTH OBJECT ==='+$this.selectedOptions[0].innerText, 1)

   //$('#list-item-top-mat').html( $this.selectedOptions[0].innerText );
   //$('#list-item-top-mat-mobile').html( $this.selectedOptions[0].innerText );

   var $tmp_productJSON = getLsProductJSON();

   $tmp_productJSON.mb1_width = parseInt( $this.value ); // set the mb1 width value
   $tmp_productJSON.mb1_width_text = parseInt( $this.value / 72 ) + ' in'; // set the mb1 width value
   $("#switch-top").prop('checked', true); // enable the top mat just in case it's not enabled

   $tmp_productJSON = get_print_dimension( $tmp_productJSON )

   $('#list-item-outer-dimension').html( $tmp_productJSON.outer_dimension);
   $('#list-printed-item-image-size').html( $tmp_productJSON.print_dimension);

   $('#list-item-outer-dimension-mobile').html( $tmp_productJSON.outer_dimension);
   $('#list-printed-item-image-size').html( $tmp_productJSON.print_dimension);

   if( detector.mobile() ){
      if( $tmp_productJSON.mb1_width == 0 ){
         $( document.getElementById('mobile-matting') ).html( 'No Matting' )
      } else {
         $( document.getElementById('mobile-matting') ).html( '2 in' )
      }
   }

   set_frame(); // show the new product without mat board

}

/**-------------------------------------------------------------------------------
|   This method is invoked when the "Select File to Upload is selected"
*-------------------------------------------------------------------------------*/
function get_printSizes( type, $tmp_pricing_grid_mode, cropper_orientation)
{
   $_tmpProductJSON = getLsProductJSON();

   $_tmpProductJSON.pricing_grid_mode = $tmp_pricing_grid_mode;

   $_tmpProductJSON.type = type;

   $_tmpProductJSON.cropper_orientation = cropper_orientation;

   $_tmpProductJSON = uploader_calcPrintSizes( $_tmpProductJSON );

   saveProductJsonToLS( $_tmpProductJSON );
}

/**-------------------------------------------------------------------------------
|
|  init the uploader
|
*-------------------------------------------------------------------------------*/
function init_uploader( $tmp_pricing_grid_mode ){

   function func_a(){
      return new Promise (function (resolved, rejected) {

         const $foo = getAjaxProductJSON(); // init by getting any json contents from the server
         
         resolved( $foo )
      })
   }

   function func_b( $__pricing_grid_mode ){
      return new Promise (function (resolved, rejected) {

         clg('***** Product Grid Mode *****=='+$__pricing_grid_mode,1);

         $tmp_productJSON = getLsProductJSON();
         $tmp_productJSON.pricing_grid_mode = $__pricing_grid_mode;

         saveProductJsonToLS( $tmp_productJSON );
         resolved( 'ok' )

      })
   }

   function func_c(){
      return new Promise (function (resolved, rejected) {

         createFineUploader(); // initialize the fineUploader component

         resolved( 'ok' )
      })
   }

   async function runSteps(){
      try {
         let r1 = await func_a()
         let r2 = await func_b( $tmp_pricing_grid_mode )
         let r3 = await func_c()
         return r3;     // this will be resolved value of the returned promise
      } catch(e) {
         console.log(e);
         throw e;      // let caller know the promise rejected with this reason
      }
   }

   runSteps();

}


/**-------------------------------------------------------------------------------
 |   this just checks the orientation based on the productJSON
 *-------------------------------------------------------------------------------*/
function check_orientation( $tmp_productJSON ){

   if( $tmp_productJSON.img_width >= $tmp_productJSON.img_height ){
      return "landscape";
   } else {
      return "portrait";
   }
}

/**-------------------------------------------------------------------------------
|   this just checks the orientation based on the productJSON
*-------------------------------------------------------------------------------*/
function reset_offset_from_top(){

   var scrollTop    = $(window).scrollTop();
   var elementOffset = $('#konva-container-wrapper').offset().top;
   var distance      = (elementOffset - scrollTop);

   if( ( distance > 5 ) || ( distance < 0 ) ){
      $('html').animate({scrollTop: 150}, 'slow');//IE, FF
      $('body').animate({scrollTop: 150}, 'slow');//chrome, don't know if Safari works
   }

}


/**-------------------------------------------------------------------------------
|
|   This function uploads the preview to the server with FineUploader
|
*-------------------------------------------------------------------------------*/
function display_visualizer_stage( $background_image ){

   var $tmp_productJSON = getLsProductJSON()

   var previewImgObj_width, previewImgObj_height;

   // calc the reference size against the background
   var view_mode = 'mobile'

   if ( view_mode == 'mobile' ){

      previewImgObj_width = $tmp_productJSON.img_width * .035
      previewImgObj_height = $tmp_productJSON.img_height * .035

   } else {

      previewImgObj_width = $tmp_productJSON.img_width * .05
      previewImgObj_height = $tmp_productJSON.img_height * .05

   }

   //=== Setup the parameters ===========================
   //
   //
   var visualization_main_stage = new Konva.Stage({
      container: 'visualizer-container',
   });

   var visualizeation_main_layer = new Konva.Layer();
   var imageObj = new Image();

   var product_object_layer = new Konva.Layer();
   var product_preview_obj = new Image();

   var $new_width,$new_height;

   //=== load the background layer ===========================
   //
   //

   function display_background(){

      var $scale = 330 / imageObj.naturalWidth;

      $new_width = imageObj.naturalWidth * $scale;
      $new_height = imageObj.naturalHeight * $scale;

      visualization_main_stage.width($new_width)
      visualization_main_stage.height($new_height)

      visualizeation_main_layer.width($new_width)
      visualizeation_main_layer.height($new_height)

      $('#visualization-container').css({'width': $new_width, 'height': $new_height});

      var background = new Konva.Image({
         x: 0,
         y: 0,
         image: imageObj,
         width: $new_width,
         height: $new_height,
      });

      // add the shape to the layer
      visualizeation_main_layer.add( background );

      // add the layer to the stage
      visualization_main_stage.add( visualizeation_main_layer );
      visualization_main_stage.draw
   };

   clg('### IMAGE SELECTED === '+$('#background_image_mode').innerText,1);

   imageObj.onload = function(){ display_background() }
   switch( $background_image ){
      case 1:
         imageObj.src = '../../../uploadhandler/uploads/image_assets/background-wall-01.jpg';
         break;
      case 2:
         imageObj.src = '../../../uploadhandler/uploads/image_assets/background-wall-03.jpg';
         break;
   }

   //=== load the preview object ===========================
   //
   //
   function display_preview_image_object(){

      var $centerX = ( $new_width / 2 ) - ( previewImgObj_width / 2 );
      var $offsetY = ( $new_height / 2 ) - ( previewImgObj_height );


      var product_preview = new Konva.Image({
         x: $centerX,
         y: $offsetY,
         width: previewImgObj_width,
         height: previewImgObj_height,
         image: product_preview_obj,
         shadowColor: '#888888',
         shadowOffsetX: 3,
         shadowOffsetY: 3,
         shadowBlur: 5,
         shadowOpacity: .3,
         draggable: true,
      });

      // add the preview to the layer
      product_object_layer.add( product_preview );

      // add the layer to the stage
      visualization_main_stage.add( product_object_layer );
   }

   product_preview_obj.onload = function(){ display_preview_image_object() }

   setTimeout(()=>{
      product_preview_obj.src = tmp_configKonvaStage.toDataURL();
   },750)

}

/**==============================================================================
 |
 | set and save the bottom mat colors and run the configurator
 |
 *==============================================================================*/
function __get_order_cartjson(){

   var data = {
      action: 'ajax_get_order_info',
      // this is the data to be sent to the ajax routine
      // data: { user_guid: $ProductJSON.user_guid }
      data: { user_guid: myAjax.user_guid }
   };
   $.post( myAjax.ajaxurl , data, function (results) { // submit the ajax request

      if( (results) && ( results !== '' )){

         $tmp_cart_json = JSON.parse( results );
         window.localStorage.setItem('CartJSON', results );

      }
   })
      .then(function(){

      })
      .done(function(){
         clg('Ajax - done',1);
      })
      .fail(function(){
         clg('Ajax - error',1);
      })
      .always(function(){
         clg('Ajax - finished',1);

      });

}

//***** CONFIGURATOR SPECIFIC FUNCTIONS ******//

/**=========================================================
 *
 *
 ----------------------------------------------------------*/
function check_offset_from_top() {

   var scrollTop    = $(window).scrollTop(),
      elementOffset = $('#konva-container-wrapper').offset().top,
      distance      = (elementOffset - scrollTop);
   clg('DISTANCE FROM THE TOP ======'+distance,1)

}

/**=========================================================
 *
 *
 ----------------------------------------------------------*/
function return_to_shopping_cart(){

   window.location = myAjax.shopping_cart

}

/**=========================================================
 *
 *
 ----------------------------------------------------------*/
function return_main_panel( $_tab ){

   $("#"+$_tab).addClass('hide');
   $('#config-options-div').removeClass('hide');

}

/**-------------------------------------------------------------------------------
 |   show the visualixer modal
 *-------------------------------------------------------------------------------*/
function show_modal( $image_selector ){

   $('#background_image_mode').innerText = $image_selector;

   $('#visualizer-modal').modal('show');

   display_visualizer_stage( $image_selector );

}

/**-------------------------------------------------------------------------------
 |
 |   This method converts the DataUrl to a Blob
 |
 *-------------------------------------------------------------------------------*/
function dataURLtoBlob(dataurl) {
   var arr = dataurl.split(','), mime = arr[0].match(/:(.*?);/)[1],
      bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
   while(n--){
      u8arr[n] = bstr.charCodeAt(n);
   }
   return new Blob([u8arr], {type:mime});
}

/**-------------------------------------------------------------------------------
 |
 |   this saves teh changed data and goes to cart
 |
 *-------------------------------------------------------------------------------*/
// HERE
function send_to_cart(){

   $("#btn-send-to-cart-wrapper").LoadingOverlay("show");

   // create the product thumbnail
   _productPreviewImage = tmp_configKonvaStage.toDataURL({
      mimeType: 'image/png',
      quality: 1,
      pixelRatio: 2
   });

   // convert to a blob
   var product_preview_blob = dataURLtoBlob( _productPreviewImage );

   uploader.addFiles({blob: product_preview_blob, name: $_ProductJSON.img_guid + ".png"})

   uploader.uploadStoredFiles()

}

/**=========================================================
 *  window ready - when images are loaded
 *----------------------------------------------------------*/
function set_button_text( $tmp_productJSON ){

   $('#frame-options-btn').text( 'Change Frame from: '+$tmp_productJSON.frame_description );

   $('#top-mat-options-btn').text( 'Change Top Mat from: '+$tmp_productJSON.mb1_width_text+' - '+$tmp_productJSON.mb1_color_name );

   $('#inner-mat-options-btn').text( 'Change Bottom Mat from: '+$tmp_productJSON.innerMBColorName );


};

/**=========================================================
 * Save the product details to cart
 *----------------------------------------------------------*/
function show_frame_preview(){

   $('#frame-preview-modal').modal('show')

   $('#carouselExampleSlidesOnly').carousel(0);

}

/**=========================================================
 *
 *
 ----------------------------------------------------------*/
function top_mat_setting( $e ){

   var $tmp_ProductJSON = getLsProductJSON();

   if(document.getElementById('switch-top').checked){

      $tmp_ProductJSON.mb1_width = 144;
      $tmp_ProductJSON.mb1_width_text = '2 in';
      $('#top_mat_width').val(144);

      load_images( $tmp_ProductJSON );

   }
   else {

      $tmp_ProductJSON.mb1_width = 0;
      $tmp_ProductJSON.mb1_width_text = '';
      $tmp_ProductJSON.innerMBEnabled = 0;
      $('#top_mat_width').val('');
      $("#switch-bottom").prop('checked', false); // set the bottom mat switch to off

      load_images( $tmp_ProductJSON );
   }

   saveProductJsonToLS( $tmp_ProductJSON );

   clg('####### TOP MAT OPTIONS CHANGED ########'+ document.getElementById('switch-top').checked ,1);

}

function invisible_glass_toggle__regular()
{
    $('#switch-invisible-glass').prop('checked', false);
    invisible_glass_toggle();
}

function invisible_glass_toggle__invisible()
{
    $('#switch-invisible-glass').prop('checked', true);
    invisible_glass_toggle();
}

function invisible_glass_toggle()
{
    if ($('#switch-invisible-glass').length) {
        var $tmp_ProductJSON = getLsProductJSON();
        //console.log('iiiiiiiiiiiiiiiiiiiiiiiiiii');
        console.log($tmp_ProductJSON);
        //console.log('iiiiiiiiiiiiiiiiiiiiiiiiiii');
        if ($('#switch-invisible-glass').prop('checked') == true) {
            $tmp_ProductJSON.invisible_glass = 1;
        } else {
            $tmp_ProductJSON.invisible_glass = 0;
        }
        console.log('invisible_glass = ' + $tmp_ProductJSON.invisible_glass);

        saveProductJsonToLS($tmp_ProductJSON);

        final_price();
    }
}